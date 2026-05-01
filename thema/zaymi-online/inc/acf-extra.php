<?php
/**
 * Zaymi Online — дополнительные ACF поля для интеграций SEO/CRM/виджетов.
 * Регистрируются отдельно от inc/acf.php чтобы не трогать существующий файл.
 *
 * @package Zaymi
 */

if (!defined('ABSPATH')) exit;

add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) return;

    /* === Дополнительные поля МФО === */
    acf_add_local_field_group([
        'key'    => 'group_mfo_extra',
        'title'  => 'Партнёрские настройки МФО',
        'fields' => [
            ['key' => 'field_mfo_aff_url', 'label' => 'Партнёрская ссылка (для /go/)',
             'name' => 'affiliate_url', 'type' => 'url',
             'instructions' => 'Можно использовать плейсхолдеры: {clickid}, {utm_source}, {utm_campaign}'],
            ['key' => 'field_mfo_site_url', 'label' => 'Прямой сайт МФО (fallback)',
             'name' => 'site_url', 'type' => 'url'],
            ['key' => 'field_mfo_reviews_count', 'label' => 'Количество отзывов', 'name' => 'reviews_count', 'type' => 'number', 'default_value' => 0],
            ['key' => 'field_mfo_term_max', 'label' => 'Срок до (дн.)', 'name' => 'term_max', 'type' => 'number'],
            ['key' => 'field_mfo_age_min', 'label' => 'Возраст от', 'name' => 'age_min', 'type' => 'number', 'default_value' => 18],
            ['key' => 'field_mfo_speed', 'label' => 'Скорость одобрения', 'name' => 'speed', 'type' => 'text', 'default_value' => '5 минут'],
        ],
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'mfo']]],
        'position' => 'side',
    ]);

    /* === Настройки SEO + интеграций (доп. опции) === */
    acf_add_local_field_group([
        'key'    => 'group_zaymi_integrations',
        'title'  => 'Интеграции и SEO',
        'fields' => [
            // CRM
            ['key' => 'field_int_tab_crm', 'label' => 'CRM / Уведомления', 'type' => 'tab'],
            ['key' => 'field_int_tg_token', 'label' => 'Telegram bot token', 'name' => 'telegram_bot_token', 'type' => 'text',
             'instructions' => 'Создайте бота через @BotFather, скопируйте токен сюда'],
            ['key' => 'field_int_tg_chat',  'label' => 'Telegram chat_id',   'name' => 'telegram_chat_id',   'type' => 'text',
             'instructions' => 'ID вашего чата или канала. Узнать: напишите боту, потом откройте https://api.telegram.org/bot<TOKEN>/getUpdates'],
            ['key' => 'field_int_bx',  'label' => 'Bitrix24 webhook URL',  'name' => 'bitrix24_webhook_url',  'type' => 'url',
             'instructions' => 'Входящий вебхук из Bitrix24, например https://your.bitrix24.ru/rest/1/xxxxx'],
            ['key' => 'field_int_amo', 'label' => 'AmoCRM webhook URL',    'name' => 'amocrm_webhook_url',    'type' => 'url'],
            ['key' => 'field_int_custom', 'label' => 'Свой Webhook URL',   'name' => 'custom_webhook_url',    'type' => 'url',
             'instructions' => 'Любой URL, который примет JSON с лидом'],

            // SEO
            ['key' => 'field_int_tab_seo', 'label' => 'SEO', 'type' => 'tab'],
            ['key' => 'field_int_indexnow', 'label' => 'IndexNow ключ', 'name' => 'indexnow_key', 'type' => 'text',
             'instructions' => 'Сгенерируйте ключ на indexnow.org. Файл подтверждения отдаётся автоматически по адресу /{key}.txt'],
            ['key' => 'field_int_ya_token', 'label' => 'Yandex Webmaster OAuth token', 'name' => 'yandex_oauth_token', 'type' => 'text'],
            ['key' => 'field_int_ya_host',  'label' => 'Yandex Webmaster Host ID',     'name' => 'yandex_host_id',     'type' => 'text'],
            ['key' => 'field_int_ga', 'label' => 'Google Analytics ID',    'name' => 'ga_id', 'type' => 'text', 'placeholder' => 'G-XXXXXXX'],
            ['key' => 'field_int_ym', 'label' => 'Яндекс.Метрика ID',      'name' => 'ym_id', 'type' => 'text'],
            ['key' => 'field_int_og_default', 'label' => 'OG-картинка по умолчанию', 'name' => 'default_og_image', 'type' => 'image', 'return_format' => 'url'],
            ['key' => 'field_int_logo', 'label' => 'Логотип сайта (URL)',  'name' => 'site_logo', 'type' => 'image', 'return_format' => 'url'],

            // Конверсионные виджеты
            ['key' => 'field_int_tab_conv', 'label' => 'Виджеты конверсии', 'type' => 'tab'],
            ['key' => 'field_int_exit_on', 'label' => 'Exit-intent попап', 'name' => 'exit_intent_enabled', 'type' => 'true_false', 'default_value' => 1, 'ui' => 1],
            ['key' => 'field_int_exit_t',  'label' => 'Заголовок попапа',  'name' => 'exit_intent_title', 'type' => 'text', 'default_value' => 'Подождите! ТОП-3 МФО с одобрением 99%'],
            ['key' => 'field_int_n_on', 'label' => 'Виджет «Одобрено только что»', 'name' => 'notice_widget_enabled', 'type' => 'true_false', 'default_value' => 1, 'ui' => 1],
            ['key' => 'field_int_n_int', 'label' => 'Интервал показа (сек)', 'name' => 'notice_widget_interval', 'type' => 'number', 'default_value' => 25],
            ['key' => 'field_int_n_names', 'label' => 'Имена (по одному на строку)', 'name' => 'notice_names', 'type' => 'textarea',
             'default_value' => "Иван\nМария\nАлексей\nЕкатерина\nДмитрий\nОльга\nСергей\nАнна\nМихаил\nЮлия"],
            ['key' => 'field_int_n_cities', 'label' => 'Города', 'name' => 'notice_cities', 'type' => 'textarea',
             'default_value' => "Москва\nСанкт-Петербург\nКазань\nНовосибирск\nЕкатеринбург\nКраснодар\nРостов-на-Дону\nСамара"],
            ['key' => 'field_int_pr_on', 'label' => 'Прогресс-бар одобрения', 'name' => 'progress_enabled', 'type' => 'true_false', 'default_value' => 1, 'ui' => 1],
            ['key' => 'field_int_pr_d',  'label' => 'Длительность анимации (мс)', 'name' => 'progress_delay', 'type' => 'number', 'default_value' => 2500],
        ],
        'location' => [[['param' => 'options_page', 'operator' => '==', 'value' => 'zaymi-theme-options']]],
    ]);
});

/* Подключаем GA + Метрику автоматически */
add_action('wp_head', function () {
    if (!function_exists('get_field')) return;
    $ga = get_field('ga_id', 'option');
    $ym = get_field('ym_id', 'option');
    if ($ga) {
        echo "\n<!-- GA -->\n<script async src='https://www.googletagmanager.com/gtag/js?id={$ga}'></script>\n";
        echo "<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{$ga}');</script>\n";
    }
    if ($ym) {
        echo "\n<!-- Yandex.Metrika -->\n<script>(function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})(window,document,'script','https://mc.yandex.ru/metrika/tag.js','ym');ym({$ym},'init',{clickmap:true,trackLinks:true,accurateTrackBounce:true});window._yaCounter={$ym};</script>\n";
    }
}, 3);
