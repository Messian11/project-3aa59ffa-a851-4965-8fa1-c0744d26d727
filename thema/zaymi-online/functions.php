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

define('ZAYMI_VERSION', '1.0.2');
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
require_once ZAYMI_DIR . '/inc/shortcodes-extra.php'; // newsletter, mfo_fit, mfo_seo_content, mfo_breadcrumbs
require_once ZAYMI_DIR . '/inc/seeder.php';         // автозасев демо-контента
require_once ZAYMI_DIR . '/inc/admin.php';          // настройка админки
require_once ZAYMI_DIR . '/inc/mfo-editor.php';     // 🗂 удобный редактор МФО (табы, дублирование, bulk)

/* ----- Конверсия (Блок 1) ----- */
require_once ZAYMI_DIR . '/inc/leads.php';          // CPT-таблица лидов + REST + CRM webhooks
require_once ZAYMI_DIR . '/inc/cloaking.php';       // /go/{slug} + статистика кликов
require_once ZAYMI_DIR . '/inc/conversion.php';     // exit-intent, notice, progress

/* ----- SEO-машина (Блок 2) ----- */
require_once ZAYMI_DIR . '/inc/acf-extra.php';      // доп. ACF поля (CRM, SEO, виджеты)
require_once ZAYMI_DIR . '/inc/seo-pages.php';      // /zaymy/...-rubley-na-...-dney/, /sravnenie/...
require_once ZAYMI_DIR . '/inc/schema.php';         // JSON-LD: Organization, FinancialProduct, FAQ, Breadcrumbs
require_once ZAYMI_DIR . '/inc/sitemaps.php';       // /sitemap.xml + IndexNow + Yandex API
require_once ZAYMI_DIR . '/inc/internal-links.php'; // related, LSI, автозамена МФО на ссылки
require_once ZAYMI_DIR . '/inc/turbo.php';          // /turbo.xml для Яндекса
require_once ZAYMI_DIR . '/inc/og-image.php';       // OG/Twitter теги + динамическая картинка
require_once ZAYMI_DIR . '/inc/dashboard.php';      // 📊 Админ-дашборд с Chart.js

/* ----- Trust & Compliance (Блок 3, v2.2) ----- */
require_once ZAYMI_DIR . '/inc/reviews.php';        // отзывы + рейтинг + Schema Review
require_once ZAYMI_DIR . '/inc/calculator.php';     // калькулятор, сравнение, FAQ
require_once ZAYMI_DIR . '/inc/compliance.php';     // cookie-banner, согласие 152-ФЗ, гео по IP
require_once ZAYMI_DIR . '/inc/pages-legal.php';    // авто-страницы, robots.txt, reCAPTCHA
require_once ZAYMI_DIR . '/inc/bootstrap.php';      // 🚀 финальный авто-настройщик после активации

/* ----- AI Writer (Блок 4) ----- */
require_once ZAYMI_DIR . '/inc/ai-writer-core.php';   // ядро: провайдеры + пайплайн
require_once ZAYMI_DIR . '/inc/authors.php';          // CPT авторов + AI-генератор экспертов
require_once ZAYMI_DIR . '/inc/ai-writer-admin.php';  // админка: меню/очередь/расписание/лог
require_once ZAYMI_DIR . '/inc/ai-writer-cron.php';   // WP-крон автопубликации

/* SEO + Trust стили/скрипты */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('zaymi-seo',   ZAYMI_URI . '/assets/css/seo.css',   [], ZAYMI_VERSION);
    wp_enqueue_style('zaymi-trust', ZAYMI_URI . '/assets/css/trust.css', [], ZAYMI_VERSION);
    wp_enqueue_script('zaymi-trust', ZAYMI_URI . '/assets/js/trust.js', [], ZAYMI_VERSION, true);
}, 20);

/* Сброс rewrite-правил при активации темы (нужно для /go/, /sitemap.xml, программатик-страниц) */
add_action('after_switch_theme', function () {
    flush_rewrite_rules();
}, 99);

/* ------------------------------------------------------------------
 * Уведомление, если ACF Pro не установлен
 * ------------------------------------------------------------------ */
add_action('admin_notices', function () {
    if (!class_exists('ACF')) {
        echo '<div class="notice notice-error"><p><strong>Zaymi Online:</strong> требуется плагин <a href="https://www.advancedcustomfields.com/pro/" target="_blank">ACF Pro</a>. Без него поля МФО, тарифы, FAQ и настройки темы не будут работать.</p></div>';
    }
});
