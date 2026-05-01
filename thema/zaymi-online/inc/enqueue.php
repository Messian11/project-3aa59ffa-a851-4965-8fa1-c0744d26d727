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

    // Основной CSS темы (скомпилированный Tailwind v4 + дизайн-токены)
    wp_enqueue_style(
        'zaymi-main',
        ZAYMI_URI . '/assets/css/main.css',
        ['zaymi-inter'],
        ZAYMI_VERSION
    );

    // Дополнительные кастомные правила (хедер, футер, оверрайды)
    wp_enqueue_style(
        'zaymi-extra',
        ZAYMI_URI . '/assets/css/extra.css',
        ['zaymi-main'],
        ZAYMI_VERSION
    );

    // Скрипт мобильного меню и аккордеонов
    wp_enqueue_script(
        'zaymi-app',
        ZAYMI_URI . '/assets/js/app.js',
        [],
        ZAYMI_VERSION,
        true
    );
});

/* Удаляем emoji и oEmbed-мусор для чистого <head> */
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('wp_head', 'wp_oembed_add_discovery_links');
