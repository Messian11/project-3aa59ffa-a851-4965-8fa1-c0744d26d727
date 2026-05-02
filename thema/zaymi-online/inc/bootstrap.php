<?php
/**
 * Zaymi Online — Bootstrap installer (v2.4)
 *
 * Один большой хук, который добивает за пользователя всё, что обычно настраивают руками:
 *   - permalink structure → /%postname%/
 *   - часовой пояс → Europe/Moscow, формат даты, неделя с понедельника
 *   - отключает комментарии глобально
 *   - чистит дефолтные «Hello world» / «Sample page» / «Uncategorized»
 *   - создаёт категории блога (Новости, Гайды, Сравнения, Финансовая грамотность)
 *   - собирает 4 меню (главное, футер ×3) из реально созданных страниц/CPT
 *   - наполняет сайдбар и футер виджетами по умолчанию
 *   - выставляет «название/описание» сайта, если они дефолтные
 *   - делает второй flush_rewrite_rules уже ПОСЛЕ всех CPT/seo-rewrites
 *   - показывает в админке нотис «Тема настроена, осталось N шагов» со ссылками
 *
 * Запускается один раз при активации (флаг zaymi_bootstrapped_v1).
 *
 * @package Zaymi
 */
if (!defined('ABSPATH')) exit;

const ZAYMI_BOOTSTRAP_FLAG = 'zaymi_bootstrapped_v1';

/* Запуск отложенно через admin_init, чтобы CPT и таксономии успели зарегистрироваться */
add_action('admin_init', function () {
    if (get_option(ZAYMI_BOOTSTRAP_FLAG)) return;
    // подождём, пока сидер отработает (он чистит свой флаг zaymi_seeder_pending)
    if (get_option('zaymi_seeder_pending')) return;
    zaymi_bootstrap_run();
    update_option(ZAYMI_BOOTSTRAP_FLAG, time());
}, 30);

function zaymi_bootstrap_run() {
    @set_time_limit(120);

    /* 1. Permalinks → /%postname%/ (без этого ЧПУ не работают) */
    if (get_option('permalink_structure') === '' || get_option('permalink_structure') === '/?p=%post_id%') {
        global $wp_rewrite;
        update_option('permalink_structure', '/%postname%/');
        if (isset($wp_rewrite)) $wp_rewrite->set_permalink_structure('/%postname%/');
    }

    /* 2. Локаль и часовой пояс */
    if (!get_option('timezone_string')) update_option('timezone_string', 'Europe/Moscow');
    update_option('date_format', 'j F Y');
    update_option('time_format', 'H:i');
    update_option('start_of_week', 1);
    update_option('blog_charset', 'UTF-8');

    /* 3. Глобально выключаем комментарии (они не нужны на финансовом портале) */
    update_option('default_comment_status', 'closed');
    update_option('default_ping_status', 'closed');
    // отключаем для уже созданных постов
    global $wpdb;
    $wpdb->query("UPDATE {$wpdb->posts} SET comment_status='closed', ping_status='closed' WHERE post_status='publish'");

    /* 4. Чистим дефолтный мусор WP */
    foreach (['Hello world!', 'Привет, мир!'] as $title) {
        $p = get_page_by_title($title, OBJECT, 'post');
        if ($p) wp_delete_post($p->ID, true);
    }
    foreach (['Sample Page', 'Пример страницы'] as $title) {
        $p = get_page_by_title($title, OBJECT, 'page');
        if ($p) wp_delete_post($p->ID, true);
    }

    /* 5. Категории блога */
    $blog_cats = [
        'Новости МФО'             => 'novosti',
        'Гайды по займам'         => 'gajdy',
        'Сравнения МФО'           => 'sravneniya',
        'Финансовая грамотность'  => 'finansovaya-gramotnost',
    ];
    foreach ($blog_cats as $name => $slug) {
        if (!term_exists($slug, 'category')) {
            wp_insert_term($name, 'category', ['slug' => $slug]);
        }
    }

    /* 6. Название и описание сайта */
    if (get_option('blogname') === 'My Site' || get_option('blogname') === 'Мой сайт') {
        update_option('blogname', 'Zaymi Online');
    }
    if (in_array(get_option('blogdescription'), ['Just another WordPress site', 'Ещё один сайт на WordPress', ''], true)) {
        update_option('blogdescription', 'Подбор займов от проверенных МФО');
    }

    /* 7. Меню — собираем из реальных страниц */
    zaymi_bootstrap_menus();

    /* 8. Виджеты сайдбара/футера */
    zaymi_bootstrap_widgets();

    /* 9. Финальный flush — теперь точно все правила зарегистрированы */
    flush_rewrite_rules(true);
}

/* ---------- Меню ---------- */
function zaymi_bootstrap_menus() {
    $menus = [
        'primary' => [
            'name'  => 'Главное меню',
            'items' => [
                ['title' => 'Все МФО',     'url' => get_post_type_archive_link('mfo') ?: home_url('/mfo/')],
                ['title' => 'По городам',  'url' => home_url('/city/moskva/')],
                ['title' => 'Калькулятор', 'url' => home_url('/kalkulyator/')],
                ['title' => 'Блог',        'url' => home_url('/blog/')],
                ['title' => 'О нас',       'url' => home_url('/about/')],
            ],
        ],
        'footer-cat' => [
            'name'  => 'Футер: Каталог',
            'items' => [
                ['title' => 'Все МФО',          'url' => home_url('/mfo/')],
                ['title' => 'Займы 5 000 ₽',    'url' => home_url('/zaymy/5000-rubley-na-7-dney/')],
                ['title' => 'Займы 30 000 ₽',   'url' => home_url('/zaymy/30000-rubley-na-30-dney/')],
                ['title' => 'Сравнить МФО',     'url' => home_url('/sravnenie/')],
            ],
        ],
        'footer-info' => [
            'name'  => 'Футер: Полезное',
            'items' => [
                ['title' => 'Калькулятор займа', 'url' => home_url('/kalkulyator/')],
                ['title' => 'Блог',              'url' => home_url('/blog/')],
                ['title' => 'Гайды',             'url' => home_url('/category/gajdy/')],
                ['title' => 'Новости',           'url' => home_url('/category/novosti/')],
            ],
        ],
        'footer-docs' => [
            'name'  => 'Футер: Документы',
            'items' => [
                ['title' => 'Политика конфиденциальности', 'url' => home_url('/privacy/')],
                ['title' => 'Согласие на обработку ПД',    'url' => home_url('/soglasie/')],
                ['title' => 'Пользовательское соглашение', 'url' => home_url('/polzovatelskoe-soglashenie/')],
                ['title' => 'Контакты',                    'url' => home_url('/contacts/')],
            ],
        ],
    ];

    $locations = (array) get_theme_mod('nav_menu_locations', []);
    foreach ($menus as $loc => $cfg) {
        $menu = wp_get_nav_menu_object($cfg['name']);
        $menu_id = $menu ? (int)$menu->term_id : wp_create_nav_menu($cfg['name']);
        if (is_wp_error($menu_id)) continue;
        $existing = wp_get_nav_menu_items($menu_id);
        if (!$existing) {
            foreach ($cfg['items'] as $item) {
                wp_update_nav_menu_item($menu_id, 0, [
                    'menu-item-title'  => $item['title'],
                    'menu-item-url'    => $item['url'],
                    'menu-item-status' => 'publish',
                ]);
            }
        }
        $locations[$loc] = (int) $menu_id;
    }
    /* Надёжно сохраняем locations: и через theme_mod, и напрямую в опции темы */
    set_theme_mod('nav_menu_locations', $locations);
    $stylesheet = get_stylesheet();
    $mods = get_option("theme_mods_{$stylesheet}", []);
    if (!is_array($mods)) $mods = [];
    $mods['nav_menu_locations'] = $locations;
    update_option("theme_mods_{$stylesheet}", $mods);
    wp_cache_delete('alloptions', 'options');
}

/* Авто-привязка primary при каждой загрузке (страховка, если кто-то снёс location) */
add_action('after_setup_theme', function () {
    $locations = (array) get_theme_mod('nav_menu_locations', []);
    if (empty($locations['primary'])) {
        $menu = wp_get_nav_menu_object('Главное меню');
        if ($menu) {
            $locations['primary'] = (int) $menu->term_id;
            set_theme_mod('nav_menu_locations', $locations);
        }
    }
}, 999);

/* ---------- Виджеты ---------- */
function zaymi_bootstrap_widgets() {
    // Сайдбар: поиск + последние посты + рубрики
    $sidebars = get_option('sidebars_widgets', []);
    $sidebars['sidebar-1'] = ['search-2', 'recent-posts-2', 'categories-2'];
    $sidebars['footer-1']  = ['text-2'];
    update_option('sidebars_widgets', $sidebars);

    update_option('widget_search', [2 => ['title' => 'Поиск'], '_multiwidget' => 1]);
    update_option('widget_recent-posts', [2 => ['title' => 'Свежие статьи', 'number' => 5], '_multiwidget' => 1]);
    update_option('widget_categories', [2 => ['title' => 'Рубрики', 'count' => 1, 'hierarchical' => 0, 'dropdown' => 0], '_multiwidget' => 1]);
    update_option('widget_text', [2 => [
        'title'  => 'Zaymi Online',
        'text'   => 'Подбор займов от проверенных МФО. Только лицензированные ЦБ РФ организации.',
        'filter' => true,
    ], '_multiwidget' => 1]);
}

/* ---------- Чек-лист в админке ---------- */
add_action('admin_notices', function () {
    if (!current_user_can('manage_options')) return;
    if (!get_option(ZAYMI_BOOTSTRAP_FLAG)) return;
    if (get_user_meta(get_current_user_id(), 'zaymi_checklist_dismissed', true)) return;

    $checks = [
        'ACF Pro установлен'  => class_exists('ACF'),
        'Permalinks ЧПУ'      => get_option('permalink_structure') !== '',
        'Демо-контент засеян' => (bool) get_option('zaymi_seeded_v2'),
        'Юр. страницы созданы' => (bool) get_page_by_path('privacy'),
        'Главное меню'         => has_nav_menu('primary'),
    ];
    $todo = [];
    if (!class_exists('ACF')) $todo[] = ['Установить ACF Pro', 'https://www.advancedcustomfields.com/pro/'];
    if (!function_exists('get_field') || !get_field('telegram_bot_token','option'))
        $todo[] = ['Подключить Telegram-бот для лидов', admin_url('admin.php?page=zaymi-options')];
    if (!function_exists('get_field') || !get_field('recaptcha_site_key','option'))
        $todo[] = ['Включить reCAPTCHA v3', admin_url('admin.php?page=zaymi-options')];

    echo '<div class="notice notice-info is-dismissible" style="border-left-color:#1a73e8"><p><strong>🎉 Zaymi Online настроена автоматически.</strong></p><ul style="margin:8px 0 8px 20px;list-style:disc">';
    foreach ($checks as $label => $ok) {
        echo '<li>' . ($ok ? '✅' : '❌') . ' ' . esc_html($label) . '</li>';
    }
    echo '</ul>';
    if ($todo) {
        echo '<p><strong>Осталось:</strong></p><ul style="margin:0 0 8px 20px;list-style:disc">';
        foreach ($todo as $t) echo '<li><a href="' . esc_url($t[1]) . '">' . esc_html($t[0]) . '</a></li>';
        echo '</ul>';
    }
    echo '<p><a href="' . esc_url(add_query_arg('zaymi_dismiss_checklist', 1)) . '">Скрыть это сообщение</a></p></div>';
});
add_action('admin_init', function () {
    if (isset($_GET['zaymi_dismiss_checklist']) && current_user_can('manage_options')) {
        update_user_meta(get_current_user_id(), 'zaymi_checklist_dismissed', 1);
        wp_safe_redirect(remove_query_arg('zaymi_dismiss_checklist'));
        exit;
    }
});
