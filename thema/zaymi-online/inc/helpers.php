<?php
/**
 * Утилитарные функции темы.
 */
if (!defined('ABSPATH')) exit;

/**
 * Форматирование числа в рубли: 12345 → "12 345 ₽"
 */
function zaymi_money($n) {
    if ($n === '' || $n === null) return '';
    return number_format((float)$n, 0, ',', ' ') . ' ₽';
}

/**
 * Склонение «день / дня / дней»
 */
function zaymi_days($n) {
    $n = (int) $n;
    $mod10  = $n % 10;
    $mod100 = $n % 100;
    if ($mod10 === 1 && $mod100 !== 11)                     $w = 'день';
    elseif ($mod10 >= 2 && $mod10 <= 4 && ($mod100 < 10 || $mod100 >= 20)) $w = 'дня';
    else                                                    $w = 'дней';
    return $n . ' ' . $w;
}

/**
 * Получить значение опции темы (ACF options).
 */
function zaymi_opt($key, $default = '') {
    if (function_exists('get_field')) {
        $v = get_field($key, 'option');
        return $v !== null && $v !== '' ? $v : $default;
    }
    return $default;
}

/**
 * Меню в шапке/футере с защитой от отсутствия меню.
 */
function zaymi_nav($location, $args = []) {
    if (!has_nav_menu($location)) return;
    wp_nav_menu(array_merge([
        'theme_location' => $location,
        'container'      => false,
        'menu_class'     => 'flex flex-col gap-2 text-sm text-slate-600',
        'fallback_cb'    => false,
        'depth'          => 1,
    ], $args));
}

/**
 * Звёзды рейтинга 0-5 → HTML.
 */
function zaymi_stars($rating) {
    $rating = max(0, min(5, (float)$rating));
    $full = floor($rating);
    $half = ($rating - $full) >= 0.5;
    $out = '<span class="inline-flex items-center gap-0.5 text-amber-500">';
    for ($i = 0; $i < 5; $i++) {
        if ($i < $full)            $icon = 'star';
        elseif ($i === $full && $half) $icon = 'star-half';
        else                       $icon = 'star-outline';
        $out .= zaymi_icon($icon, 'w-4 h-4');
    }
    $out .= '</span>';
    return $out;
}

/**
 * Получить логотип МФО как URL.
 */
function zaymi_mfo_logo_url($post_id, $size = 'mfo-logo') {
    $logo = get_field('mfo_logo', $post_id);
    if (is_array($logo) && !empty($logo['ID'])) {
        $img = wp_get_attachment_image_src($logo['ID'], $size);
        if ($img) return $img[0];
    }
    if (has_post_thumbnail($post_id)) {
        return get_the_post_thumbnail_url($post_id, $size);
    }
    return ZAYMI_URI . '/assets/images/mfo-placeholder.png';
}

/**
 * Получить массив МФО по фильтрам.
 *
 * Особое поведение: если в args есть фильтр по таксономии city,
 * добавляются МФО с mfo_all_russia=1 (работают по всей РФ).
 */
function zaymi_query_mfo($args = []) {
    $defaults = [
        'post_type'      => 'mfo',
        'posts_per_page' => 12,
        'orderby'        => 'meta_value_num',
        'meta_key'       => 'mfo_rating',
        'order'          => 'DESC',
    ];
    $args = array_merge($defaults, $args);

    // Если фильтр по городу — подмешиваем "по всей России"
    $has_city_filter = false;
    if (!empty($args['tax_query']) && is_array($args['tax_query'])) {
        foreach ($args['tax_query'] as $tq) {
            if (isset($tq['taxonomy']) && $tq['taxonomy'] === 'city') { $has_city_filter = true; break; }
        }
    }
    if ($has_city_filter) {
        $all_russia_ids = get_posts([
            'post_type'      => 'mfo',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_query'     => [['key' => 'mfo_all_russia', 'value' => '1', 'compare' => '=']],
        ]);
        if (!empty($all_russia_ids)) {
            $city_args = $args;
            $city_args['posts_per_page'] = -1;
            $city_args['fields'] = 'ids';
            unset($city_args['orderby'], $city_args['meta_key'], $city_args['order']);
            $city_ids = get_posts($city_args);
            $merged_ids = array_values(array_unique(array_merge((array)$city_ids, (array)$all_russia_ids)));
            unset($args['tax_query']);
            $args['post__in'] = !empty($merged_ids) ? $merged_ids : [0];
        }
    }

    return new WP_Query($args);
}

/**
 * Аналогичный фильтр для архива city: подмешивает МФО "по всей РФ".
 * Срабатывает на основном запросе taxonomy-city.
 */
add_action('pre_get_posts', function ($q) {
    if (is_admin() || !$q->is_main_query()) return;
    if (!$q->is_tax('city')) return;
    $all_russia_ids = get_posts([
        'post_type'      => 'mfo',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'meta_query'     => [['key' => 'mfo_all_russia', 'value' => '1', 'compare' => '=']],
    ]);
    if (empty($all_russia_ids)) return;
    $term = $q->get_queried_object();
    if (!$term || empty($term->term_id)) return;
    $city_ids = get_posts([
        'post_type'      => 'mfo',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'tax_query'      => [['taxonomy' => 'city', 'field' => 'term_id', 'terms' => [$term->term_id]]],
    ]);
    $merged = array_values(array_unique(array_merge((array)$city_ids, (array)$all_russia_ids)));
    if (empty($merged)) $merged = [0];
    $q->set('tax_query', []);
    $q->set('post__in', $merged);
    $q->set('orderby', 'meta_value_num');
    $q->set('meta_key', 'mfo_rating');
    $q->set('order', 'DESC');
});

/**
 * Безопасный escape для аттрибутов с подстановкой по умолчанию.
 */
function zaymi_attr($v, $default = '') {
    return esc_attr($v !== '' && $v !== null ? $v : $default);
}
