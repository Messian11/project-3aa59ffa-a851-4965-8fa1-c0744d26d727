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
 */
function zaymi_query_mfo($args = []) {
    $defaults = [
        'post_type'      => 'mfo',
        'posts_per_page' => 12,
        'orderby'        => 'meta_value_num',
        'meta_key'       => 'mfo_rating',
        'order'          => 'DESC',
    ];
    return new WP_Query(array_merge($defaults, $args));
}

/**
 * Безопасный escape для аттрибутов с подстановкой по умолчанию.
 */
function zaymi_attr($v, $default = '') {
    return esc_attr($v !== '' && $v !== null ? $v : $default);
}
