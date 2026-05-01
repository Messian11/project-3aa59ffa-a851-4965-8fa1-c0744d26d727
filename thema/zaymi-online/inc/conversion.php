<?php
/**
 * Zaymi Online — Конверсионные виджеты.
 *
 * - Exit-intent поп-ап "ТОП-3 МФО"
 * - Виджет "Одобрено только что" (всплывашка снизу слева)
 * - Прогресс-бар одобрения (модалка после клика на "Получить")
 *
 * Все три виджета подключаются автоматически. Управляются ACF-полями
 * на странице настроек "Zaymi → Виджеты конверсии".
 *
 * @package Zaymi
 */

if (!defined('ABSPATH')) exit;

/* ---------- Подключение JS+CSS на каждой странице ---------- */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('zaymi-conv', ZAYMI_URI . '/assets/css/conversion.css', [], ZAYMI_VERSION);
    wp_enqueue_script('zaymi-conv', ZAYMI_URI . '/assets/js/conversion.js', [], ZAYMI_VERSION, true);

    $opt = function ($key, $default = '') {
        return function_exists('get_field') ? (get_field($key, 'option') ?: $default) : $default;
    };

    // ТОП-3 МФО для exit-intent (берём первые 3 опубликованных)
    $top_mfo = get_posts(['post_type' => 'mfo', 'posts_per_page' => 3, 'meta_key' => 'rating', 'orderby' => 'meta_value_num', 'order' => 'DESC']);
    $top_data = array_map(function ($p) {
        return [
            'name'   => get_the_title($p),
            'slug'   => $p->post_name,
            'logo'   => get_the_post_thumbnail_url($p, 'thumbnail'),
            'rate'   => function_exists('get_field') ? get_field('rate_min', $p->ID) : '',
            'url'    => function_exists('zaymi_go_url') ? zaymi_go_url($p->ID) : get_permalink($p),
        ];
    }, $top_mfo);

    wp_localize_script('zaymi-conv', 'ZaymiConv', [
        'restUrl'        => esc_url_raw(rest_url('zaymi/v1/lead')),
        'exitEnabled'    => (bool) $opt('exit_intent_enabled', true),
        'exitTitle'      => $opt('exit_intent_title', 'Подождите! ТОП-3 МФО с одобрением 99%'),
        'topMfo'         => $top_data,
        'noticeEnabled'  => (bool) $opt('notice_widget_enabled', true),
        'noticeInterval' => (int) $opt('notice_widget_interval', 25),
        'noticeNames'    => explode("\n", (string) $opt('notice_names', "Иван\nМария\nАлексей\nЕкатерина\nДмитрий\nОльга\nСергей\nАнна\nМихаил\nЮлия")),
        'noticeCities'   => explode("\n", (string) $opt('notice_cities', "Москва\nСанкт-Петербург\nКазань\nНовосибирск\nЕкатеринбург\nКраснодар\nРостов-на-Дону\nСамара")),
        'noticeMfo'      => array_map(fn($p) => $p['name'], $top_data) ?: ['Lime', 'Займер', 'MoneyMan'],
        'progressEnabled'=> (bool) $opt('progress_enabled', true),
        'progressDelay'  => (int) $opt('progress_delay', 2500),
    ]);
});
