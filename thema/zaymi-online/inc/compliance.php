<?php
/**
 * Zaymi Online — Compliance & Geo (v2.2)
 *
 *  - Cookie-banner (152-ФЗ + РКН)
 *  - Логирование согласия на обработку ПД при отправке формы
 *  - Гео-определение города по IP (ip-api.com, бесплатно, без ключа)
 *  - Антифрод: лимит на отправку формы с одного IP
 *
 * @package Zaymi
 */
if (!defined('ABSPATH')) exit;

/* ---------- Cookie-banner (вывод в футер) ---------- */
add_action('wp_footer', function () { ?>
    <div id="zaymi-cookie" class="zaymi-cookie" hidden>
        <div class="zaymi-cookie__inner">
            <p>Мы используем cookie для аналитики и улучшения сервиса. Продолжая, вы соглашаетесь с
                <a href="/privacy/">Политикой конфиденциальности</a> и обработкой персональных данных
                согласно <a href="http://www.consultant.ru/document/cons_doc_LAW_61801/" rel="nofollow" target="_blank">152-ФЗ</a>.</p>
            <button type="button" id="zaymi-cookie-ok" class="zaymi-btn zaymi-btn--primary">Принять</button>
        </div>
    </div>
    <script>
    (function(){
        if (localStorage.getItem('zaymi_cookie_ok')) return;
        var el = document.getElementById('zaymi-cookie'); if(!el) return;
        el.hidden = false;
        document.getElementById('zaymi-cookie-ok').onclick = function(){
            localStorage.setItem('zaymi_cookie_ok','1'); el.hidden = true;
        };
    })();
    </script>
<?php }, 99);

/* ---------- Таблица согласий на ПД ---------- */
function zaymi_consent_install() {
    global $wpdb;
    $table = $wpdb->prefix . 'zaymi_consents';
    $charset = $wpdb->get_charset_collate();
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta("CREATE TABLE IF NOT EXISTS $table (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        lead_id BIGINT UNSIGNED NULL,
        ip VARBINARY(16) NULL,
        user_agent VARCHAR(255) DEFAULT '',
        page_url VARCHAR(500) DEFAULT '',
        consent_text TEXT,
        created_at DATETIME NOT NULL,
        PRIMARY KEY (id), KEY lead (lead_id)
    ) $charset;");
}
add_action('after_switch_theme', 'zaymi_consent_install');

/* Логируем согласие при создании лида (хук из inc/leads.php) */
add_action('zaymi_lead_created', function ($lead_id, $data) {
    global $wpdb;
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $wpdb->insert($wpdb->prefix . 'zaymi_consents', [
        'lead_id' => $lead_id,
        'ip' => $ip ? @inet_pton($ip) : null,
        'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 250),
        'page_url' => esc_url_raw($_SERVER['HTTP_REFERER'] ?? ''),
        'consent_text' => 'Согласие на обработку персональных данных согласно ФЗ-152 от ' . current_time('mysql'),
        'created_at' => current_time('mysql'),
    ]);
}, 10, 2);

/* ---------- Гео по IP (с кэшем в transient на 30 дней) ---------- */
function zaymi_detect_city() {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
    $ip = trim(explode(',', $ip)[0]);
    if (!$ip || $ip === '127.0.0.1' || strpos($ip, '192.168.') === 0) return null;
    $key = 'zaymi_geo_' . md5($ip);
    $cached = get_transient($key);
    if ($cached !== false) return $cached;
    $resp = wp_remote_get("http://ip-api.com/json/{$ip}?lang=ru&fields=city,regionName,status", ['timeout'=>3]);
    if (is_wp_error($resp)) { set_transient($key, null, DAY_IN_SECONDS); return null; }
    $data = json_decode(wp_remote_retrieve_body($resp), true);
    $city = ($data['status'] ?? '') === 'success' ? ($data['city'] ?? null) : null;
    set_transient($key, $city, 30 * DAY_IN_SECONDS);
    return $city;
}

/* Шорткод: вывод города */
add_shortcode('zaymi_geo_city', function () {
    $c = zaymi_detect_city();
    return $c ? esc_html($c) : 'вашем городе';
});

/* JS-переменная для фронта */
add_action('wp_head', function () {
    $c = zaymi_detect_city();
    if ($c) echo "<script>window.ZAYMI_CITY=" . wp_json_encode($c) . ";</script>\n";
}, 5);

/* ---------- Антифрод: лимит лидов с IP (5 / час) ---------- */
add_filter('zaymi_lead_pre_insert', function ($data) {
    global $wpdb;
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    if (!$ip) return $data;
    $t = $wpdb->prefix . 'zaymi_leads';
    $count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $t WHERE ip=%s AND created_at > (NOW() - INTERVAL 1 HOUR)",
        $ip
    ));
    if ((int)$count >= 5) {
        return new WP_Error('rate_limit', 'Слишком много заявок с вашего IP, попробуйте позже.');
    }
    return $data;
});
