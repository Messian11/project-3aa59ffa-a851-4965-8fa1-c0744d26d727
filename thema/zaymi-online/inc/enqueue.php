<?php
/**
 * Подключение CSS/JS
 */
if (!defined('ABSPATH')) exit;

add_action('wp_enqueue_scripts', function () {
    // Inter из Google Fonts
    wp_enqueue_style(
        'zaymi-inter',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap',
        [],
        null
    );

    // Tailwind CSS — локальная копия (без CDN, чтобы не блокировали плагины безопасности)
    wp_enqueue_style(
        'zaymi-tailwind',
        ZAYMI_URI . '/assets/css/tailwind.min.css',
        ['zaymi-inter'],
        ZAYMI_VERSION
    );

    // Основной CSS темы (дизайн-токены, кастомные классы, оверрайды)
    wp_enqueue_style(
        'zaymi-main',
        ZAYMI_URI . '/assets/css/main.css',
        ['zaymi-tailwind'],
        ZAYMI_VERSION
    );

    // Дополнительные кастомные правила (хедер, футер, оверрайды)
    wp_enqueue_style(
        'zaymi-extra',
        ZAYMI_URI . '/assets/css/extra.css',
        ['zaymi-main'],
        ZAYMI_VERSION
    );

    // Стили статьи блога — только на одиночных постах
    if (is_singular('post')) {
        wp_enqueue_style(
            'zaymi-article',
            ZAYMI_URI . '/assets/css/article.css',
            ['zaymi-extra'],
            ZAYMI_VERSION
        );
    }

    // Скрипт мобильного меню и аккордеонов
    wp_enqueue_script(
        'zaymi-app',
        ZAYMI_URI . '/assets/js/app.js',
        [],
        ZAYMI_VERSION,
        true
    );
    wp_localize_script('zaymi-app', 'zaymiData', [
        'restUrl' => esc_url_raw( rest_url() ),
        'homeUrl' => esc_url_raw( home_url('/') ),
    ]);
});

/* Удаляем emoji и oEmbed-мусор для чистого <head> */
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('wp_head', 'wp_oembed_add_discovery_links');
