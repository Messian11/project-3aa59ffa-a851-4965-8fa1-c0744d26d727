<?php
/**
 * Регистрация Custom Post Types и таксономий для Zaymi Online
 *
 * Подключение:
 *   require_once get_template_directory() . '/inc/cpt-register.php';
 *
 * Или установить как mu-plugin: wp-content/mu-plugins/zaymi-cpt.php
 */

if (!defined('ABSPATH')) exit;

/* ------------------------------------------------------------------
 * CPT: mfo (Микрофинансовая организация)
 * ------------------------------------------------------------------ */
add_action('init', function () {
    register_post_type('mfo', [
        'labels' => [
            'name'               => 'МФО',
            'singular_name'      => 'МФО',
            'menu_name'          => 'МФО',
            'add_new'            => 'Добавить МФО',
            'add_new_item'       => 'Добавить новое МФО',
            'edit_item'          => 'Редактировать МФО',
            'new_item'           => 'Новое МФО',
            'view_item'          => 'Просмотр МФО',
            'search_items'       => 'Найти МФО',
            'not_found'          => 'МФО не найдены',
            'all_items'          => 'Все МФО',
        ],
        'public'             => true,
        'has_archive'        => true,
        'rewrite'            => ['slug' => 'mfo', 'with_front' => false],
        'menu_icon'          => 'dashicons-money-alt',
        'menu_position'      => 20,
        'supports'           => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions'],
        'show_in_rest'       => true,
        'taxonomies'         => ['situation', 'summa', 'city'],
    ]);
});

/* ------------------------------------------------------------------
 * Таксономия: situation (Подборки)
 * URL: /situations/{slug}
 * ------------------------------------------------------------------ */
add_action('init', function () {
    register_taxonomy('situation', ['mfo'], [
        'labels' => [
            'name'          => 'Подборки',
            'singular_name' => 'Подборка',
            'menu_name'     => 'Подборки',
            'all_items'     => 'Все подборки',
            'edit_item'     => 'Редактировать подборку',
            'add_new_item'  => 'Добавить подборку',
        ],
        'public'            => true,
        'hierarchical'      => false,
        'show_admin_column' => true,
        'rewrite'           => ['slug' => 'situations', 'with_front' => false],
        'show_in_rest'      => true,
    ]);
});

/* ------------------------------------------------------------------
 * Таксономия: summa (По сумме займа)
 * URL: /summa/{slug}
 * ------------------------------------------------------------------ */
add_action('init', function () {
    register_taxonomy('summa', ['mfo'], [
        'labels' => [
            'name'          => 'По сумме',
            'singular_name' => 'Сумма',
            'menu_name'     => 'По сумме',
        ],
        'public'            => true,
        'hierarchical'      => false,
        'show_admin_column' => true,
        'rewrite'           => ['slug' => 'summa', 'with_front' => false],
        'show_in_rest'      => true,
    ]);
});

/* ------------------------------------------------------------------
 * Таксономия: city (Города)
 * URL: /goroda/{slug}
 * ------------------------------------------------------------------ */
add_action('init', function () {
    register_taxonomy('city', ['mfo'], [
        'labels' => [
            'name'          => 'Города',
            'singular_name' => 'Город',
            'menu_name'     => 'Города',
        ],
        'public'            => true,
        'hierarchical'      => false,
        'show_admin_column' => true,
        'rewrite'           => ['slug' => 'goroda', 'with_front' => false],
        'show_in_rest'      => true,
    ]);
});

/* ------------------------------------------------------------------
 * Перерегистрация permalinks при активации темы
 * ------------------------------------------------------------------ */
add_action('after_switch_theme', function () {
    flush_rewrite_rules();
});

/* ------------------------------------------------------------------
 * Опции темы (требует ACF Pro)
 * ------------------------------------------------------------------ */
if (function_exists('acf_add_options_page')) {
    acf_add_options_page([
        'page_title' => 'Настройки темы Zaymi',
        'menu_title' => 'Настройки Zaymi',
        'menu_slug'  => 'zaymi-theme-options',
        'capability' => 'edit_posts',
        'icon_url'   => 'dashicons-admin-customizer',
        'position'   => 60,
    ]);
}

/* ------------------------------------------------------------------
 * Регистрация локаций меню
 * ------------------------------------------------------------------ */
add_action('after_setup_theme', function () {
    register_nav_menus([
        'primary'      => 'Главное меню (шапка)',
        'footer-cat'   => 'Футер: Каталог',
        'footer-info'  => 'Футер: Полезное',
        'footer-about' => 'Футер: Компания',
        'footer-docs'  => 'Футер: Документы',
    ]);

    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
    add_theme_support('responsive-embeds');
    add_theme_support('editor-styles');
    add_theme_support('wp-block-styles');
    add_theme_support('align-wide');
});

/* ------------------------------------------------------------------
 * Размеры изображений
 * ------------------------------------------------------------------ */
add_action('after_setup_theme', function () {
    add_image_size('mfo-logo', 200, 200, true);
    add_image_size('mfo-card', 400, 240, true);
    add_image_size('blog-thumb', 800, 480, true);
    add_image_size('og-share', 1200, 630, true);
});
