<?php
/**
 * Zaymi Online — Авто-импорт офферов МФО из Leads.su (CPA-сеть).
 *
 * Что делает:
 *  - В админке появляется страница «МФО → Импорт из Leads.su».
 *  - Пользователь сохраняет API-токен и (опционально) ID канала-площадки.
 *  - По кнопке (или по cron каждые 24 часа) тянет список офферов из
 *    https://api.leads.su/webmaster/offers и создаёт/обновляет CPT mfo:
 *      • title       = оффер.name
 *      • slug        = sanitize_title(name) (стабильный)
 *      • thumbnail   = логотип оффера (sideload)
 *      • mfo_partner_url = персональная партнёрская ссылка
 *      • mfo_rate_min / mfo_amount_max / mfo_term_max — если приходят в API
 *      • meta _leadssu_offer_id — связь для последующих обновлений
 *  - Только категория «Микрофинансы» (category_id = 1 у Leads.su) или ручной фильтр.
 *  - Ничего не удаляет — повторный импорт обновляет существующие записи.
 *
 * API:
 *   GET https://api.leads.su/webmaster/offers?token=XXX&limit=500&offset=0&geo=1
 *
 * @package Zaymi
 */

if (!defined('ABSPATH')) exit;

const ZAYMI_LEADSSU_API     = 'https://api.leads.su/webmaster/';
const ZAYMI_LEADSSU_OPT     = 'zaymi_leadssu_settings';
const ZAYMI_LEADSSU_LOG     = 'zaymi_leadssu_log';
const ZAYMI_LEADSSU_CRON    = 'zaymi_leadssu_daily_sync';

/* ---------- Настройки по умолчанию ---------- */
function zaymi_leadssu_get_settings() {
    $defaults = [
        'token'      => '',
        'channel_id' => '', // platform_id (необязательно)
        'category'   => 'mikrofinansy', // фильтр по категории оффера
        'auto_sync'  => 1,
        'auto_publish' => 0, // 0 = создавать в draft, 1 = сразу publish
    ];
    return wp_parse_args(get_option(ZAYMI_LEADSSU_OPT, []), $defaults);
}

/* ---------- API-вызов ---------- */
function zaymi_leadssu_api($action, array $params = []) {
    $cfg = zaymi_leadssu_get_settings();
    if (empty($cfg['token'])) {
        return new WP_Error('no_token', 'Не указан токен Leads.su (Настройки → Импорт из Leads.su).');
    }
    $params['token'] = $cfg['token'];
    $url = trailingslashit(ZAYMI_LEADSSU_API) . ltrim($action, '/');
    $url = add_query_arg($params, $url);

    $resp = wp_remote_get($url, ['timeout' => 30]);
    if (is_wp_error($resp)) return $resp;
    $code = wp_remote_retrieve_response_code($resp);
    $body = wp_remote_retrieve_body($resp);
    $json = json_decode($body, true);
    if ($code !== 200 || empty($json) || !empty($json['error'])) {
        $msg = $json['error']['message'] ?? "HTTP {$code}";
        return new WP_Error('api_error', "Leads.su API: {$msg}");
    }
    return $json['data'] ?? [];
}

/* ---------- Получить все офферы (с пагинацией) ---------- */
function zaymi_leadssu_fetch_offers() {
    $cfg = zaymi_leadssu_get_settings();
    $offers = [];
    $offset = 0;
    $limit  = 200;
    do {
        $params = ['limit' => $limit, 'offset' => $offset, 'geo' => 1];
        if (!empty($cfg['channel_id'])) {
            $params['platform_id'] = (int) $cfg['channel_id'];
        }
        $action = !empty($cfg['channel_id']) ? 'offers/connectedPlatforms' : 'offers';
        $data = zaymi_leadssu_api($action, $params);
        if (is_wp_error($data)) return $data;
        if (empty($data)) break;
        $offers = array_merge($offers, $data);
        $offset += $limit;
        if (count($data) < $limit) break;
    } while ($offset < 5000);
    return $offers;
}

/* ---------- Импорт одного оффера в CPT mfo ---------- */
function zaymi_leadssu_import_offer($offer) {
    $cfg = zaymi_leadssu_get_settings();
    $oid = (string)($offer['id'] ?? '');
    if (!$oid) return null;

    $name = trim($offer['name'] ?? '');
    if (!$name) return null;

    // Фильтр по категории микрофинансы (если задано в настройках)
    if (!empty($cfg['category'])) {
        $cats = $offer['categories'] ?? $offer['category'] ?? [];
        $needle = mb_strtolower($cfg['category']);
        $hit = false;
        if (is_array($cats)) {
            foreach ($cats as $c) {
                $cn = is_array($c) ? mb_strtolower($c['name'] ?? $c['slug'] ?? '') : mb_strtolower((string)$c);
                if (str_contains($cn, $needle) || str_contains($cn, 'микрофин') || str_contains($cn, 'займ')) { $hit = true; break; }
            }
        } elseif (is_string($cats)) {
            $hit = str_contains(mb_strtolower($cats), $needle);
        }
        // если категории не пришли — пропускаем фильтр (доверяем подключённым офферам)
        if (!$hit && !empty($cats)) return ['skipped' => $name];
    }

    $slug = sanitize_title($name);

    // Существующая запись?
    $existing = get_posts([
        'post_type' => 'mfo', 'posts_per_page' => 1,
        'meta_key' => '_leadssu_offer_id', 'meta_value' => $oid,
        'post_status' => 'any', 'fields' => 'ids',
    ]);
    if (!$existing) {
        $by_slug = get_page_by_path($slug, OBJECT, 'mfo');
        if ($by_slug) $existing = [$by_slug->ID];
    }

    $status = $cfg['auto_publish'] ? 'publish' : 'draft';
    $postarr = [
        'post_type'   => 'mfo',
        'post_title'  => $name,
        'post_name'   => $slug,
        'post_status' => $existing ? get_post_status($existing[0]) : $status,
        'post_excerpt'=> wp_strip_all_tags(mb_substr($offer['description'] ?? '', 0, 280)),
    ];
    if ($existing) {
        $postarr['ID'] = $existing[0];
        $post_id = wp_update_post($postarr, true);
    } else {
        $post_id = wp_insert_post($postarr, true);
    }
    if (is_wp_error($post_id) || !$post_id) return null;

    /* --- Метаполя --- */
    update_post_meta($post_id, '_leadssu_offer_id', $oid);
    update_post_meta($post_id, '_leadssu_synced_at', time());

    // Партнёрская ссылка
    $aff_link = $offer['url'] ?? $offer['preview_url'] ?? '';
    if (empty($aff_link) && !empty($cfg['channel_id'])) {
        $aff_link = 'https://pxl.leads.su/aff_c?offer_id='.$oid.'&pltfm_id='.(int)$cfg['channel_id'];
    }
    if ($aff_link) {
        update_post_meta($post_id, 'mfo_partner_url', esc_url_raw($aff_link));
    }

    // Тарифы (если приходят)
    if (!empty($offer['terms']) && is_array($offer['terms'])) {
        $t = $offer['terms'];
        if (!empty($t['amount_max']))  update_post_meta($post_id, 'mfo_amount_max', (int)$t['amount_max']);
        if (!empty($t['amount_min']))  update_post_meta($post_id, 'mfo_amount_min', (int)$t['amount_min']);
        if (!empty($t['term_max']))    update_post_meta($post_id, 'mfo_term_max',   (int)$t['term_max']);
        if (!empty($t['rate_min']))    update_post_meta($post_id, 'mfo_rate_min',   (float)$t['rate_min']);
    }
    // Ставка (часто отдельным полем)
    if (!empty($offer['epc']))    update_post_meta($post_id, '_leadssu_epc', (float)$offer['epc']);
    if (!empty($offer['epl']))    update_post_meta($post_id, '_leadssu_epl', (float)$offer['epl']);
    if (!empty($offer['cr']))     update_post_meta($post_id, '_leadssu_cr',  (float)$offer['cr']);
    if (!empty($offer['ar']))     update_post_meta($post_id, '_leadssu_ar',  (float)$offer['ar']);

    /* --- Логотип → миниатюра поста --- */
    $logo = $offer['logo'] ?? $offer['image'] ?? '';
    if ($logo && !has_post_thumbnail($post_id)) {
        $logo = (str_starts_with($logo, 'http') ? $logo : 'https://leads.su' . $logo);
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
        $tmp = download_url($logo, 30);
        if (!is_wp_error($tmp)) {
            $file_array = [
                'name'     => sanitize_file_name($slug . '-' . basename(parse_url($logo, PHP_URL_PATH))),
                'tmp_name' => $tmp,
            ];
            $att_id = media_handle_sideload($file_array, $post_id);
            if (!is_wp_error($att_id)) set_post_thumbnail($post_id, $att_id);
            else @unlink($tmp);
        }
    }

    return ['id' => $post_id, 'name' => $name, 'updated' => (bool)$existing];
}

/* ---------- Полный синк ---------- */
function zaymi_leadssu_sync_now() {
    $offers = zaymi_leadssu_fetch_offers();
    if (is_wp_error($offers)) return $offers;
    $created = 0; $updated = 0; $skipped = 0; $errors = 0;
    foreach ($offers as $o) {
        $r = zaymi_leadssu_import_offer($o);
        if ($r === null) { $errors++; continue; }
        if (!empty($r['skipped'])) { $skipped++; continue; }
        $r['updated'] ? $updated++ : $created++;
    }
    $log = [
        'time'    => time(),
        'total'   => count($offers),
        'created' => $created,
        'updated' => $updated,
        'skipped' => $skipped,
        'errors'  => $errors,
    ];
    update_option(ZAYMI_LEADSSU_LOG, $log);
    return $log;
}

/* ---------- Cron 1/день ---------- */
add_action(ZAYMI_LEADSSU_CRON, function () {
    $cfg = zaymi_leadssu_get_settings();
    if (empty($cfg['token']) || empty($cfg['auto_sync'])) return;
    zaymi_leadssu_sync_now();
});

add_action('init', function () {
    $cfg = zaymi_leadssu_get_settings();
    if (!empty($cfg['token']) && !empty($cfg['auto_sync']) && !wp_next_scheduled(ZAYMI_LEADSSU_CRON)) {
        wp_schedule_event(time() + 3600, 'daily', ZAYMI_LEADSSU_CRON);
    }
    if ((empty($cfg['token']) || empty($cfg['auto_sync'])) && wp_next_scheduled(ZAYMI_LEADSSU_CRON)) {
        wp_clear_scheduled_hook(ZAYMI_LEADSSU_CRON);
    }
});

/* ---------- Админ-страница ---------- */
add_action('admin_menu', function () {
    add_submenu_page(
        'edit.php?post_type=mfo',
        'Импорт из Leads.su',
        '⚡ Импорт Leads.su',
        'manage_options',
        'zaymi-leadssu',
        'zaymi_leadssu_render_page'
    );
});

function zaymi_leadssu_render_page() {
    if (!current_user_can('manage_options')) return;

    if (!empty($_POST['zaymi_leadssu_save']) && check_admin_referer('zaymi_leadssu_save')) {
        $new = [
            'token'        => sanitize_text_field($_POST['token'] ?? ''),
            'channel_id'   => sanitize_text_field($_POST['channel_id'] ?? ''),
            'category'     => sanitize_text_field($_POST['category'] ?? 'mikrofinansy'),
            'auto_sync'    => empty($_POST['auto_sync']) ? 0 : 1,
            'auto_publish' => empty($_POST['auto_publish']) ? 0 : 1,
        ];
        update_option(ZAYMI_LEADSSU_OPT, $new);
        echo '<div class="notice notice-success is-dismissible"><p>Настройки сохранены.</p></div>';
    }

    if (!empty($_POST['zaymi_leadssu_run']) && check_admin_referer('zaymi_leadssu_run')) {
        $r = zaymi_leadssu_sync_now();
        if (is_wp_error($r)) {
            echo '<div class="notice notice-error"><p>'.esc_html($r->get_error_message()).'</p></div>';
        } else {
            echo '<div class="notice notice-success"><p>Готово. Всего офферов: <b>'.$r['total'].'</b>, создано: <b>'.$r['created'].'</b>, обновлено: <b>'.$r['updated'].'</b>, пропущено: <b>'.$r['skipped'].'</b>, ошибок: <b>'.$r['errors'].'</b>.</p></div>';
        }
    }

    $cfg = zaymi_leadssu_get_settings();
    $log = get_option(ZAYMI_LEADSSU_LOG, []);
    ?>
    <div class="wrap">
      <h1>⚡ Импорт МФО из Leads.su</h1>
      <p style="max-width:780px;color:#475569;font-size:14px;">
        Подключите ваш токен из <a href="https://webmaster.leads.su/account/default" target="_blank">личного кабинета Leads.su</a> —
        тема будет сама подтягивать ваши подключённые офферы (МФО), создавать страницы каталога и обновлять партнёрские ссылки.
        Запускается ежедневно по cron.
      </p>

      <form method="post" style="max-width:720px;background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:24px;margin-top:20px;">
        <?php wp_nonce_field('zaymi_leadssu_save'); ?>
        <table class="form-table">
          <tr>
            <th><label for="token">API-токен Leads.su</label></th>
            <td><input id="token" type="password" name="token" value="<?php echo esc_attr($cfg['token']); ?>" class="regular-text" placeholder="Скопируйте из webmaster.leads.su/account/default" />
            <p class="description">Хранится только в вашей БД. Не светится в логах.</p></td>
          </tr>
          <tr>
            <th><label for="channel_id">ID площадки (platform_id)</label></th>
            <td><input id="channel_id" type="text" name="channel_id" value="<?php echo esc_attr($cfg['channel_id']); ?>" class="regular-text" placeholder="напр. 12345 (необязательно)" />
            <p class="description">Если указан — используется ваша персональная партнёрская ссылка для этой площадки. Если пусто — берётся базовый URL оффера.</p></td>
          </tr>
          <tr>
            <th><label for="category">Фильтр по категории</label></th>
            <td><input id="category" type="text" name="category" value="<?php echo esc_attr($cfg['category']); ?>" class="regular-text" />
            <p class="description">Подстрока для фильтрации (по умолчанию <code>mikrofinansy</code>). Пустое поле = импортировать все.</p></td>
          </tr>
          <tr>
            <th>Автосинхронизация</th>
            <td><label><input type="checkbox" name="auto_sync" value="1" <?php checked($cfg['auto_sync']); ?> /> Запускать ежедневно (cron)</label></td>
          </tr>
          <tr>
            <th>Сразу публиковать</th>
            <td><label><input type="checkbox" name="auto_publish" value="1" <?php checked($cfg['auto_publish']); ?> /> Новые МФО сразу в публикацию (иначе — в черновик)</label></td>
          </tr>
        </table>
        <p><button type="submit" name="zaymi_leadssu_save" value="1" class="button button-primary">Сохранить</button></p>
      </form>

      <form method="post" style="max-width:720px;background:#0f172a;color:#fff;border-radius:12px;padding:24px;margin-top:20px;">
        <?php wp_nonce_field('zaymi_leadssu_run'); ?>
        <h2 style="color:#fff;margin-top:0;">Запустить импорт прямо сейчас</h2>
        <p style="opacity:.85;">Получит свежие офферы и обновит каталог. Может занять 30–90 секунд.</p>
        <button type="submit" name="zaymi_leadssu_run" value="1" class="button button-hero" style="background:#10b981;border:none;color:#fff;font-weight:700;">▶ Импортировать</button>
      </form>

      <?php if ($log): ?>
        <div style="max-width:720px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:18px;margin-top:20px;">
          <h3 style="margin-top:0;">Последний запуск</h3>
          <p>📅 <?php echo esc_html(date_i18n('d.m.Y H:i', $log['time'])); ?></p>
          <p>Всего офферов: <b><?php echo (int)$log['total']; ?></b>, создано: <b style="color:#10b981;"><?php echo (int)$log['created']; ?></b>,
            обновлено: <b style="color:#3b82f6;"><?php echo (int)$log['updated']; ?></b>,
            пропущено: <b><?php echo (int)$log['skipped']; ?></b>,
            ошибок: <b style="color:#ef4444;"><?php echo (int)$log['errors']; ?></b>.</p>
        </div>
      <?php endif; ?>

      <div style="max-width:720px;margin-top:24px;font-size:13px;color:#64748b;">
        <h3>Где взять токен</h3>
        <ol>
          <li>Зайдите в <a href="https://webmaster.leads.su/account/default" target="_blank">webmaster.leads.su/account/default</a>.</li>
          <li>Скопируйте API-токен (раздел «API»).</li>
          <li>Найдите ID площадки в разделе «Площадки» → нужен <code>platform_id</code>.</li>
          <li>Вставьте сюда → Сохранить → Импортировать.</li>
        </ol>
      </div>
    </div>
    <?php
}
