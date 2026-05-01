<?php
/**
 * Zaymi Online — functions.php
 *
 * Главный загрузчик темы. Подключает все модули из /inc.
 * Не редактируйте этот файл напрямую — добавляйте логику в /inc/*.php
 *
 * @package Zaymi
 */

if (!defined('ABSPATH')) exit;

define('ZAYMI_VERSION', '1.0.0');
define('ZAYMI_DIR',     get_template_directory());
define('ZAYMI_URI',     get_template_directory_uri());
define('ZAYMI_DEMO_DIR', ZAYMI_DIR . '/demo-data');

/* ------------------------------------------------------------------
 * Подключение модулей
 * ------------------------------------------------------------------ */
require_once ZAYMI_DIR . '/inc/setup.php';          // theme support, menus, image sizes
require_once ZAYMI_DIR . '/inc/enqueue.php';        // CSS / JS / шрифты
require_once ZAYMI_DIR . '/inc/cpt.php';            // CPT mfo + таксономии
require_once ZAYMI_DIR . '/inc/acf.php';            // ACF поля и опции
require_once ZAYMI_DIR . '/inc/icons.php';          // SVG-иконки lucide
require_once ZAYMI_DIR . '/inc/helpers.php';        // утилиты (форматирование, рейтинг и т.д.)
require_once ZAYMI_DIR . '/inc/shortcodes.php';     // все шорткоды
require_once ZAYMI_DIR . '/inc/seeder.php';         // автозасев демо-контента
require_once ZAYMI_DIR . '/inc/admin.php';          // настройка админки

/* ------------------------------------------------------------------
 * Уведомление, если ACF Pro не установлен
 * ------------------------------------------------------------------ */
add_action('admin_notices', function () {
    if (!class_exists('ACF')) {
        echo '<div class="notice notice-error"><p><strong>Zaymi Online:</strong> требуется плагин <a href="https://www.advancedcustomfields.com/pro/" target="_blank">ACF Pro</a>. Без него поля МФО, тарифы, FAQ и настройки темы не будут работать.</p></div>';
    }
});
