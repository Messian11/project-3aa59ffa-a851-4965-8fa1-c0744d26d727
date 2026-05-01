<?php
/**
 * Zaymi Online — Партнёрские ссылки с подменой (cloaking).
 *
 * URL вида /go/{slug-мфо}/ → редирект на партнёрскую ссылку из ACF
 * поля mfo.affiliate_url. Считает клики, источник, дату → таблица
 * wp_zaymi_clicks. Админка показывает CTR/EPC.
 *
 * @package Zaymi
 */

if (!defined('ABSPATH')) exit;

/* ---------- 1. Таблица кликов ---------- */
function zaymi_clicks_install() {
    global $wpdb;
    $table = $wpdb->prefix . 'zaymi_clicks';
    $charset = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table (
        id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        created_at   DATETIME        NOT NULL,
        mfo_id       BIGINT UNSIGNED NOT NULL,
        mfo_slug     VARCHAR(191)    NOT NULL,
        utm_source   VARCHAR(191)    NULL,
        utm_medium   VARCHAR(191)    NULL,
        utm_campaign VARCHAR(191)    NULL,
        referer      TEXT            NULL,
        ip           VARCHAR(64)     NULL,
        user_agent   TEXT            NULL,
        PRIMARY KEY (id),
        KEY mfo_id (mfo_id),
        KEY created_at (created_at)
    ) $charset;";
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}
add_action('after_switch_theme', 'zaymi_clicks_install');

/* ---------- 2. Rewrite-правило /go/{slug} ---------- */
add_action('init', function () {
    add_rewrite_rule('^go/([^/]+)/?$', 'index.php?zaymi_go=$matches[1]', 'top');
});
add_filter('query_vars', function ($vars) { $vars[] = 'zaymi_go'; return $vars; });

/* ---------- 3. Обработка редиректа ---------- */
add_action('template_redirect', function () {
    $slug = get_query_var('zaymi_go');
    if (!$slug) return;

    $mfo = get_page_by_path($slug, OBJECT, 'mfo');
    if (!$mfo) {
        wp_redirect(home_url('/mfo/'), 302);
        exit;
    }

    $url = function_exists('get_field') ? get_field('affiliate_url', $mfo->ID) : '';
    if (!$url) $url = function_exists('get_field') ? get_field('site_url', $mfo->ID) : '';
    if (!$url) $url = home_url('/mfo/' . $slug . '/');

    // Sub-id для трекинга в партнёрке (если URL содержит {clickid})
    global $wpdb;
    $table = $wpdb->prefix . 'zaymi_clicks';
    $wpdb->insert($table, [
        'created_at'   => current_time('mysql'),
        'mfo_id'       => $mfo->ID,
        'mfo_slug'     => $slug,
        'utm_source'   => sanitize_text_field($_GET['utm_source']   ?? ''),
        'utm_medium'   => sanitize_text_field($_GET['utm_medium']   ?? ''),
        'utm_campaign' => sanitize_text_field($_GET['utm_campaign'] ?? ''),
        'referer'      => esc_url_raw($_SERVER['HTTP_REFERER'] ?? ''),
        'ip'           => function_exists('zaymi_leads_client_ip') ? zaymi_leads_client_ip() : ($_SERVER['REMOTE_ADDR'] ?? ''),
        'user_agent'   => substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 500),
    ]);
    $click_id = (int) $wpdb->insert_id;

    // подстановка click_id в URL партнёрки
    $url = str_replace(['{clickid}', '{subid}', '{sub_id}'], $click_id, $url);

    // прокидываем UTM в URL партнёрки (если шаблон содержит {utm_source} и т.п.)
    foreach (['utm_source','utm_medium','utm_campaign','utm_content','utm_term'] as $k) {
        $url = str_replace('{'.$k.'}', urlencode((string)($_GET[$k] ?? '')), $url);
    }

    nocache_headers();
    wp_redirect($url, 302);
    exit;
});

/* ---------- 4. Хелпер: сгенерировать /go/ ссылку ---------- */
function zaymi_go_url($mfo_id_or_slug) {
    if (is_numeric($mfo_id_or_slug)) {
        $slug = get_post_field('post_name', (int)$mfo_id_or_slug);
    } else {
        $slug = $mfo_id_or_slug;
    }
    return home_url('/go/' . $slug . '/');
}

/* ---------- 5. Админ-страница со статистикой ---------- */
add_action('admin_menu', function () {
    add_submenu_page(
        'zaymi-leads',
        'Клики по партнёркам',
        'Клики (CR/EPC)',
        'manage_options',
        'zaymi-clicks',
        'zaymi_clicks_admin_page'
    );
});

function zaymi_clicks_admin_page() {
    global $wpdb;
    $clicks = $wpdb->prefix . 'zaymi_clicks';
    $leads  = $wpdb->prefix . 'zaymi_leads';

    $rows = $wpdb->get_results("
        SELECT mfo_slug,
               COUNT(*) AS clicks,
               (SELECT COUNT(*) FROM $leads l WHERE l.mfo_slug = c.mfo_slug) AS leads
        FROM $clicks c
        GROUP BY mfo_slug
        ORDER BY clicks DESC
    ");
    ?>
    <div class="wrap">
      <h1>Клики по партнёрским ссылкам</h1>
      <p>CR = заявки / клики · EPC рассчитайте после получения отчёта от партнёрки (поле «Доход за 1000 кликов» в настройках МФО).</p>
      <table class="widefat striped">
        <thead><tr><th>МФО (slug)</th><th>Клики</th><th>Заявки</th><th>CR, %</th><th>Ссылка</th></tr></thead>
        <tbody>
        <?php foreach ($rows as $r): $cr = $r->clicks ? round($r->leads / $r->clicks * 100, 2) : 0; ?>
          <tr>
            <td><?php echo esc_html($r->mfo_slug); ?></td>
            <td><?php echo (int)$r->clicks; ?></td>
            <td><?php echo (int)$r->leads; ?></td>
            <td><?php echo $cr; ?>%</td>
            <td><code><?php echo esc_html(home_url('/go/'.$r->mfo_slug.'/')); ?></code></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php
}
