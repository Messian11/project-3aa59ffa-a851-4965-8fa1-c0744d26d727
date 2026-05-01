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
});

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
