<?php
/**
 * Theme setup: меню, image sizes, theme supports
 */
if (!defined('ABSPATH')) exit;

add_action('after_setup_theme', function () {
    load_theme_textdomain('zaymi', ZAYMI_DIR . '/languages');

    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('responsive-embeds');
    add_theme_support('editor-styles');
    add_theme_support('wp-block-styles');
    add_theme_support('align-wide');
    add_theme_support('automatic-feed-links');

    register_nav_menus([
        'primary'      => __('Главное меню (шапка)', 'zaymi'),
        'footer-cat'   => __('Футер: Каталог', 'zaymi'),
        'footer-info'  => __('Футер: Полезное', 'zaymi'),
        'footer-about' => __('Футер: Компания', 'zaymi'),
        'footer-docs'  => __('Футер: Документы', 'zaymi'),
    ]);

    add_image_size('mfo-logo',  200, 200, true);
    add_image_size('mfo-card',  400, 240, true);
    add_image_size('blog-thumb', 800, 480, true);
    add_image_size('og-share',  1200, 630, true);

    /* 🖼 Кастомный логотип через Customizer (Внешний вид → Настроить → Свойства сайта → Логотип) */
    add_theme_support('custom-logo', [
        'height'               => 80,
        'width'                => 320,
        'flex-height'          => true,
        'flex-width'           => true,
        'header-text'          => ['site-title', 'site-description'],
        'unlink-homepage-logo' => false,
    ]);

    /* 🎨 Фавикон / site icon — тоже через Customizer */
    add_theme_support('site-icon');
});

/* ------------------------------------------------------------------
 * Customizer: отдельная секция «Логотип Zaymi» для быстрой смены
 * ------------------------------------------------------------------ */
add_action('customize_register', function ($wp_customize) {
    $wp_customize->add_section('zaymi_branding', [
        'title'    => '🖼 Логотип Zaymi',
        'priority' => 25,
        'description' => 'Загрузите логотип здесь — он применится во всей теме (шапка, футер, OG-картинка). Рекомендуемый размер: 320×80, PNG/SVG.',
    ]);

    /* Высота логотипа в шапке (px) */
    $wp_customize->add_setting('zaymi_logo_height', [
        'default'           => 40,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ]);
    $wp_customize->add_control('zaymi_logo_height', [
        'label'       => 'Высота логотипа в шапке (px)',
        'section'     => 'zaymi_branding',
        'type'        => 'number',
        'input_attrs' => ['min' => 24, 'max' => 96, 'step' => 2],
    ]);

    /* Альтернативный логотип (для тёмного футера) */
    $wp_customize->add_setting('zaymi_logo_footer', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ]);
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'zaymi_logo_footer', [
        'label'   => 'Логотип для футера (необязательно)',
        'description' => 'Если у вас тёмный футер — загрузите белую/светлую версию.',
        'section' => 'zaymi_branding',
    ]));
});

/* ------------------------------------------------------------------
 * Хелпер: вывод логотипа Zaymi (Customizer → custom-logo → fallback)
 * ------------------------------------------------------------------ */
if (!function_exists('zaymi_logo')) {
    function zaymi_logo($args = []) {
        $h = (int) get_theme_mod('zaymi_logo_height', 40);
        $h = max(24, min(96, $h));
        $class = 'h-' . max(8, intval($h / 4)) . ' md:h-' . max(8, intval($h / 4)) . ' w-auto';
        $alt = get_bloginfo('name');

        // 1. Custom logo (Customizer → Site Identity)
        $logo_id = get_theme_mod('custom_logo');
        if ($logo_id) {
            $img = wp_get_attachment_image_src($logo_id, 'full');
            if ($img) {
                $url = $img[0];
                printf(
                    '<a href="%s" class="flex items-center select-none" aria-label="%s — главная"><img src="%s" alt="%s" style="height:%dpx;width:auto" decoding="async" /></a>',
                    esc_url(home_url('/')), esc_attr($alt), esc_url($url), esc_attr($alt), $h
                );
                return;
            }
        }

        // 2. Fallback: /assets/img/logo.png в теме
        $fallback = ZAYMI_URI . '/assets/img/logo.png';
        printf(
            '<a href="%s" class="flex items-center select-none" aria-label="%s — главная"><img src="%s" alt="%s" style="height:%dpx;width:auto" decoding="async" /></a>',
            esc_url(home_url('/')), esc_attr($alt), esc_url($fallback), esc_attr($alt), $h
        );
    }
}

if (!function_exists('zaymi_logo_footer_url')) {
    function zaymi_logo_footer_url() {
        $url = get_theme_mod('zaymi_logo_footer');
        if ($url) return $url;
        $logo_id = get_theme_mod('custom_logo');
        if ($logo_id) {
            $img = wp_get_attachment_image_src($logo_id, 'full');
            if ($img) return $img[0];
        }
        return ZAYMI_URI . '/assets/img/logo.png';
    }
}

/* Перерегистрация permalinks при активации */
add_action('after_switch_theme', function () {
    flush_rewrite_rules();
});

/* Скрываем admin bar для не-админов на фронте */
add_action('after_setup_theme', function () {
    if (!current_user_can('manage_options')) {
        show_admin_bar(false);
    }
});
