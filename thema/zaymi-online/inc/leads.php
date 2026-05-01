<?php
/**
 * Zaymi Online — Лиды (сквозная заявка).
 *
 * Создаёт таблицу wp_zaymi_leads, REST-эндпоинт для приёма заявок,
 * админку для просмотра/экспорта в CSV, отправку в вебхуки
 * (Telegram / Bitrix24 / AmoCRM / любой URL).
 *
 * Шорткод: [zaymi_lead_form] — универсальная форма.
 *
 * @package Zaymi
 */

if (!defined('ABSPATH')) exit;

/* ------------------------------------------------------------------
 * 1. Создание таблицы при активации темы
 * ------------------------------------------------------------------ */
function zaymi_leads_install() {
    global $wpdb;
    $table = $wpdb->prefix . 'zaymi_leads';
    $charset = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table (
        id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        created_at      DATETIME        NOT NULL,
        status          VARCHAR(32)     NOT NULL DEFAULT 'new',
        name            VARCHAR(191)    NULL,
        phone           VARCHAR(64)     NULL,
        email           VARCHAR(191)    NULL,
        amount          INT             NULL,
        term            INT             NULL,
        mfo_id          BIGINT UNSIGNED NULL,
        mfo_slug        VARCHAR(191)    NULL,
        page_url        TEXT            NULL,
        referer         TEXT            NULL,
        utm_source      VARCHAR(191)    NULL,
        utm_medium      VARCHAR(191)    NULL,
        utm_campaign    VARCHAR(191)    NULL,
        utm_content     VARCHAR(191)    NULL,
        utm_term        VARCHAR(191)    NULL,
        gclid           VARCHAR(191)    NULL,
        yclid           VARCHAR(191)    NULL,
        ip              VARCHAR(64)     NULL,
        user_agent      TEXT            NULL,
        extra           LONGTEXT        NULL,
        PRIMARY KEY (id),
        KEY created_at (created_at),
        KEY mfo_id (mfo_id),
        KEY utm_source (utm_source)
    ) $charset;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}
add_action('after_switch_theme', 'zaymi_leads_install');

/* ------------------------------------------------------------------
 * 2. REST endpoint /wp-json/zaymi/v1/lead
 * ------------------------------------------------------------------ */
add_action('rest_api_init', function () {
    register_rest_route('zaymi/v1', '/lead', [
        'methods'             => 'POST',
        'callback'            => 'zaymi_leads_rest_create',
        'permission_callback' => '__return_true',
    ]);
});

function zaymi_leads_rest_create(WP_REST_Request $req) {
    global $wpdb;
    $p = $req->get_json_params() ?: $req->get_params();

    // honeypot против спама
    if (!empty($p['website'])) {
        return new WP_REST_Response(['ok' => true], 200);
    }

    // простой rate-limit: 5 заявок с одного IP / минуту
    $ip = zaymi_leads_client_ip();
    $rl_key = 'zaymi_rl_' . md5($ip);
    $count = (int) get_transient($rl_key);
    if ($count >= 5) {
        return new WP_REST_Response(['ok' => false, 'error' => 'rate_limited'], 429);
    }
    set_transient($rl_key, $count + 1, 60);

    $phone = preg_replace('/[^0-9+]/', '', (string)($p['phone'] ?? ''));
    if (strlen($phone) < 10) {
        return new WP_REST_Response(['ok' => false, 'error' => 'invalid_phone'], 400);
    }

    $data = [
        'created_at'   => current_time('mysql'),
        'status'       => 'new',
        'name'         => sanitize_text_field($p['name'] ?? ''),
        'phone'        => $phone,
        'email'        => sanitize_email($p['email'] ?? ''),
        'amount'       => isset($p['amount']) ? (int)$p['amount'] : null,
        'term'         => isset($p['term'])   ? (int)$p['term']   : null,
        'mfo_id'       => isset($p['mfo_id']) ? (int)$p['mfo_id'] : null,
        'mfo_slug'     => sanitize_text_field($p['mfo_slug'] ?? ''),
        'page_url'     => esc_url_raw($p['page_url'] ?? ''),
        'referer'      => esc_url_raw($p['referer'] ?? ''),
        'utm_source'   => sanitize_text_field($p['utm_source']   ?? ''),
        'utm_medium'   => sanitize_text_field($p['utm_medium']   ?? ''),
        'utm_campaign' => sanitize_text_field($p['utm_campaign'] ?? ''),
        'utm_content'  => sanitize_text_field($p['utm_content']  ?? ''),
        'utm_term'     => sanitize_text_field($p['utm_term']     ?? ''),
        'gclid'        => sanitize_text_field($p['gclid'] ?? ''),
        'yclid'        => sanitize_text_field($p['yclid'] ?? ''),
        'ip'           => $ip,
        'user_agent'   => substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 500),
        'extra'        => wp_json_encode($p['extra'] ?? []),
    ];

    $table = $wpdb->prefix . 'zaymi_leads';
    $wpdb->insert($table, $data);
    $lead_id = (int) $wpdb->insert_id;

    do_action('zaymi_lead_created', $lead_id, $data);

    return new WP_REST_Response([
        'ok' => true,
        'id' => $lead_id,
    ], 200);
}

function zaymi_leads_client_ip() {
    foreach (['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $h) {
        if (!empty($_SERVER[$h])) {
            $ip = explode(',', $_SERVER[$h])[0];
            return trim($ip);
        }
    }
    return '';
}

/* ------------------------------------------------------------------
 * 3. Вебхуки: Telegram / Bitrix24 / AmoCRM / Custom
 * ------------------------------------------------------------------ */
add_action('zaymi_lead_created', 'zaymi_leads_send_webhooks', 10, 2);

function zaymi_leads_send_webhooks($lead_id, $data) {
    if (!function_exists('get_field')) return;

    // -- Telegram --
    $tg_token = get_field('telegram_bot_token', 'option');
    $tg_chat  = get_field('telegram_chat_id',   'option');
    if ($tg_token && $tg_chat) {
        $msg = "🔥 <b>Новая заявка #{$lead_id}</b>\n";
        $msg .= "👤 " . htmlspecialchars($data['name'] ?: '—') . "\n";
        $msg .= "📞 " . htmlspecialchars($data['phone']) . "\n";
        if ($data['amount']) $msg .= "💰 Сумма: " . number_format($data['amount'], 0, '', ' ') . " ₽\n";
        if ($data['term'])   $msg .= "📅 Срок: {$data['term']} дн.\n";
        if ($data['mfo_slug']) $msg .= "🏦 МФО: " . htmlspecialchars($data['mfo_slug']) . "\n";
        if ($data['utm_source']) $msg .= "📣 Источник: " . htmlspecialchars($data['utm_source']) . "\n";
        if ($data['page_url']) $msg .= "🔗 " . esc_url($data['page_url']);

        wp_remote_post("https://api.telegram.org/bot{$tg_token}/sendMessage", [
            'timeout' => 5,
            'body'    => [
                'chat_id'    => $tg_chat,
                'text'       => $msg,
                'parse_mode' => 'HTML',
            ],
        ]);
    }

    // -- Bitrix24 webhook --
    $bx = get_field('bitrix24_webhook_url', 'option');
    if ($bx) {
        wp_remote_post(rtrim($bx, '/') . '/crm.lead.add.json', [
            'timeout' => 5,
            'body'    => [
                'fields' => [
                    'TITLE'   => "Заявка с сайта #{$lead_id}",
                    'NAME'    => $data['name'],
                    'PHONE'   => [['VALUE' => $data['phone'], 'VALUE_TYPE' => 'WORK']],
                    'EMAIL'   => $data['email'] ? [['VALUE' => $data['email'], 'VALUE_TYPE' => 'WORK']] : [],
                    'COMMENTS'=> "Сумма: {$data['amount']} ₽\nСрок: {$data['term']} дн.\nМФО: {$data['mfo_slug']}\nUTM: {$data['utm_source']}/{$data['utm_medium']}/{$data['utm_campaign']}\nСтраница: {$data['page_url']}",
                    'SOURCE_ID' => 'WEB',
                    'UTM_SOURCE'   => $data['utm_source'],
                    'UTM_MEDIUM'   => $data['utm_medium'],
                    'UTM_CAMPAIGN' => $data['utm_campaign'],
                ],
            ],
        ]);
    }

    // -- AmoCRM webhook (входящий вебхук в AmoCRM) --
    $amo = get_field('amocrm_webhook_url', 'option');
    if ($amo) {
        wp_remote_post($amo, [
            'timeout' => 5,
            'headers' => ['Content-Type' => 'application/json'],
            'body'    => wp_json_encode([
                'lead_id' => $lead_id,
                'data'    => $data,
            ]),
        ]);
    }

    // -- Custom webhook --
    $custom = get_field('custom_webhook_url', 'option');
    if ($custom) {
        wp_remote_post($custom, [
            'timeout' => 5,
            'headers' => ['Content-Type' => 'application/json'],
            'body'    => wp_json_encode(['lead_id' => $lead_id, 'data' => $data]),
        ]);
    }
}

/* ------------------------------------------------------------------
 * 4. Шорткод универсальной формы [zaymi_lead_form]
 * ------------------------------------------------------------------ */
add_shortcode('zaymi_lead_form', function ($atts) {
    $a = shortcode_atts([
        'title'    => 'Получить займ',
        'amount'   => 15000,
        'term'     => 14,
        'mfo_id'   => '',
        'mfo_slug' => '',
        'button'   => 'Отправить заявку',
        'compact'  => '0',
    ], $atts);

    if (is_singular('mfo') && empty($a['mfo_id'])) {
        $a['mfo_id']   = get_the_ID();
        $a['mfo_slug'] = get_post_field('post_name', get_the_ID());
    }

    ob_start(); ?>
    <form class="zaymi-lead-form <?php echo $a['compact'] === '1' ? 'is-compact' : ''; ?>"
          data-zaymi-lead
          data-mfo-id="<?php echo esc_attr($a['mfo_id']); ?>"
          data-mfo-slug="<?php echo esc_attr($a['mfo_slug']); ?>">
      <h3 class="zlf-title"><?php echo esc_html($a['title']); ?></h3>
      <div class="zlf-row">
        <label>Сумма, ₽
          <input type="number" name="amount" value="<?php echo (int)$a['amount']; ?>" min="1000" max="100000" step="1000" required>
        </label>
        <label>Срок, дн.
          <input type="number" name="term" value="<?php echo (int)$a['term']; ?>" min="1" max="365" required>
        </label>
      </div>
      <label>Ваше имя
        <input type="text" name="name" maxlength="100" required>
      </label>
      <label>Телефон
        <input type="tel" name="phone" placeholder="+7 (___) ___-__-__" required>
      </label>
      <input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px;" aria-hidden="true">
      <label class="zlf-agree">
        <input type="checkbox" required checked>
        <span>Согласен с <a href="/privacy/" target="_blank">политикой конфиденциальности</a></span>
      </label>
      <button type="submit" class="zlf-btn"><?php echo esc_html($a['button']); ?></button>
      <div class="zlf-result" hidden></div>
    </form>
    <?php
    return ob_get_clean();
});

/* ------------------------------------------------------------------
 * 5. Админ-страница: список + экспорт CSV
 * ------------------------------------------------------------------ */
add_action('admin_menu', function () {
    add_menu_page(
        'Заявки (Лиды)',
        'Заявки',
        'manage_options',
        'zaymi-leads',
        'zaymi_leads_admin_page',
        'dashicons-phone',
        26
    );
});

function zaymi_leads_admin_page() {
    global $wpdb;
    $table = $wpdb->prefix . 'zaymi_leads';

    // экспорт
    if (isset($_GET['export']) && current_user_can('manage_options')) {
        zaymi_leads_export_csv();
        exit;
    }

    // удаление
    if (!empty($_GET['delete']) && check_admin_referer('zaymi_lead_del')) {
        $wpdb->delete($table, ['id' => (int)$_GET['delete']]);
        echo '<div class="notice notice-success"><p>Заявка удалена.</p></div>';
    }

    $rows = $wpdb->get_results("SELECT * FROM $table ORDER BY id DESC LIMIT 200");
    $total = (int) $wpdb->get_var("SELECT COUNT(*) FROM $table");
    $today = (int) $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE DATE(created_at) = CURDATE()");
    ?>
    <div class="wrap">
      <h1>Заявки (Лиды) <a href="<?php echo esc_url(add_query_arg('export', '1')); ?>" class="page-title-action">Экспорт CSV</a></h1>
      <p>Всего: <b><?php echo $total; ?></b> · Сегодня: <b><?php echo $today; ?></b> · Показано последние 200.</p>
      <table class="widefat striped">
        <thead><tr>
          <th>#</th><th>Дата</th><th>Имя</th><th>Телефон</th><th>Сумма/срок</th><th>МФО</th><th>UTM</th><th>Страница</th><th></th>
        </tr></thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?php echo $r->id; ?></td>
            <td><?php echo esc_html($r->created_at); ?></td>
            <td><?php echo esc_html($r->name); ?></td>
            <td><a href="tel:<?php echo esc_attr($r->phone); ?>"><?php echo esc_html($r->phone); ?></a></td>
            <td><?php echo $r->amount ? number_format($r->amount,0,'',' ').' ₽ / '.$r->term.' дн.' : '—'; ?></td>
            <td><?php echo esc_html($r->mfo_slug ?: '—'); ?></td>
            <td><?php echo esc_html(trim(($r->utm_source ?: '—').'/'.$r->utm_medium.'/'.$r->utm_campaign, '/')); ?></td>
            <td><?php echo $r->page_url ? '<a href="'.esc_url($r->page_url).'" target="_blank">↗</a>' : '—'; ?></td>
            <td><a href="<?php echo esc_url(wp_nonce_url(add_query_arg('delete', $r->id), 'zaymi_lead_del')); ?>" onclick="return confirm('Удалить?')">✕</a></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php
}

function zaymi_leads_export_csv() {
    global $wpdb;
    $table = $wpdb->prefix . 'zaymi_leads';
    $rows = $wpdb->get_results("SELECT * FROM $table ORDER BY id DESC", ARRAY_A);

    nocache_headers();
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename=zaymi-leads-' . date('Y-m-d') . '.csv');

    $out = fopen('php://output', 'w');
    fputs($out, "\xEF\xBB\xBF"); // BOM для Excel
    if ($rows) {
        fputcsv($out, array_keys($rows[0]), ';');
        foreach ($rows as $r) fputcsv($out, $r, ';');
    }
    fclose($out);
}
