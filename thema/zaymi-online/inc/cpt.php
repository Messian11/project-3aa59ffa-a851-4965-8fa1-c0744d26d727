<?php
/**
 * Custom Post Type: mfo
 * Таксономии: situation, summa, city
 */
if (!defined('ABSPATH')) exit;

add_action('init', function () {
    register_post_type('mfo', [
        'labels' => [
            'name'          => 'МФО',
            'singular_name' => 'МФО',
            'menu_name'     => 'МФО',
            'add_new'       => 'Добавить МФО',
            'add_new_item'  => 'Добавить новое МФО',
            'edit_item'     => 'Редактировать МФО',
            'new_item'      => 'Новое МФО',
            'view_item'     => 'Просмотр МФО',
            'search_items'  => 'Найти МФО',
            'not_found'     => 'МФО не найдены',
            'all_items'     => 'Все МФО',
        ],
        'public'        => true,
        'has_archive'   => true,
        'rewrite'       => ['slug' => 'mfo', 'with_front' => false],
        'menu_icon'     => 'dashicons-money-alt',
        'menu_position' => 20,
        'supports'      => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions'],
        'show_in_rest'  => true,
        'taxonomies'    => ['situation', 'summa', 'city'],
    ]);

    register_taxonomy('situation', ['mfo'], [
        'labels'            => ['name' => 'Подборки', 'singular_name' => 'Подборка', 'menu_name' => 'Подборки'],
        'public'            => true,
        'hierarchical'      => false,
        'show_admin_column' => true,
        'rewrite'           => ['slug' => 'situations', 'with_front' => false],
        'show_in_rest'      => true,
    ]);

    register_taxonomy('summa', ['mfo'], [
        'labels'            => ['name' => 'По сумме', 'singular_name' => 'Сумма', 'menu_name' => 'По сумме'],
        'public'            => true,
        'hierarchical'      => false,
        'show_admin_column' => true,
        'rewrite'           => ['slug' => 'summa', 'with_front' => false],
        'show_in_rest'      => true,
    ]);

    register_taxonomy('city', ['mfo'], [
        'labels'            => ['name' => 'Города', 'singular_name' => 'Город', 'menu_name' => 'Города'],
        'public'            => true,
        'hierarchical'      => false,
        'show_admin_column' => true,
        'rewrite'           => ['slug' => 'goroda', 'with_front' => false],
        'show_in_rest'      => true,
    ]);
});

/* Архив МФО доступен по /mfo */
add_filter('post_type_archive_title', function ($title, $post_type) {
    if ($post_type === 'mfo') return 'Каталог МФО';
    return $title;
}, 10, 2);
