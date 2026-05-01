<?php
/**
 * Zaymi Online — Compliance Pages, robots.txt, reCAPTCHA settings (v2.3)
 *
 *  - Автосоздание страниц «Политика конфиденциальности» и «Согласие на обработку ПД» при активации
 *  - Динамический /robots.txt с Host, Clean-param (UTM/yclid/gclid/clickid), Sitemap
 *  - ACF-поля для reCAPTCHA v3 в Theme Options (site_key + secret)
 *  - Автоподключение скрипта reCAPTCHA на фронте, если ключи заданы
 *
 * @package Zaymi
 */
if (!defined('ABSPATH')) exit;

/* ---------- 1. Автосоздание страниц при активации темы ---------- */
add_action('after_switch_theme', function () {
    $pages = [
        'privacy' => [
            'title'   => 'Политика конфиденциальности',
            'content' => zaymi_pages_privacy_template(),
        ],
        'soglasie' => [
            'title'   => 'Согласие на обработку персональных данных',
            'content' => zaymi_pages_consent_template(),
        ],
        'polzovatelskoe-soglashenie' => [
            'title'   => 'Пользовательское соглашение',
            'content' => zaymi_pages_terms_template(),
        ],
    ];
    foreach ($pages as $slug => $data) {
        $existing = get_page_by_path($slug);
        if ($existing) continue;
        $page_id = wp_insert_post([
            'post_title'   => $data['title'],
            'post_content' => $data['content'],
            'post_name'    => $slug,
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_author'  => 1,
        ]);
        if ($slug === 'privacy' && $page_id && !get_option('wp_page_for_privacy_policy')) {
            update_option('wp_page_for_privacy_policy', $page_id);
        }
    }
});

function zaymi_pages_privacy_template() {
    $site = get_bloginfo('name'); $url = home_url();
    return <<<HTML
<h2>1. Общие положения</h2>
<p>Настоящая Политика конфиденциальности (далее — Политика) действует в отношении всей информации, которую сайт {$site} ({$url}) может получить о Пользователе во время использования сайта. Политика разработана в соответствии с Федеральным законом РФ от 27.07.2006 № 152-ФЗ «О персональных данных».</p>
<h2>2. Какие данные мы собираем</h2>
<ul>
<li>Имя, номер телефона, e-mail — добровольно при заполнении формы заявки.</li>
<li>Технические данные: IP-адрес, тип браузера, страница входа, источник перехода (UTM-метки, реферер).</li>
<li>Файлы cookie (включая аналитические Яндекс.Метрика, Google Analytics).</li>
</ul>
<h2>3. Цели обработки</h2>
<ul>
<li>Передача заявки в выбранную микрофинансовую организацию (МФО) для оформления займа.</li>
<li>Информирование о статусе заявки.</li>
<li>Аналитика и улучшение сервиса.</li>
</ul>
<h2>4. Передача третьим лицам</h2>
<p>Данные передаются исключительно партнёрам — лицензированным МФО — в объёме, необходимом для рассмотрения заявки. Полный перечень партнёров доступен по запросу.</p>
<h2>5. Сроки хранения</h2>
<p>Персональные данные хранятся в течение 5 лет с момента последнего обращения. По истечении срока — обезличиваются или удаляются.</p>
<h2>6. Права субъекта</h2>
<p>Пользователь вправе в любой момент отозвать согласие на обработку ПД, направив запрос на e-mail администрации сайта. Обработка прекращается в течение 30 дней.</p>
<h2>7. Контакты</h2>
<p>По вопросам обработки персональных данных обращайтесь на e-mail, указанный в подвале сайта.</p>
HTML;
}

function zaymi_pages_consent_template() {
    return <<<HTML
<p>Отправляя заявку на сайте, я даю своё согласие на обработку моих персональных данных (ФИО, номер телефона, e-mail, IP-адрес, файлы cookie) в соответствии с Федеральным законом № 152-ФЗ «О персональных данных» и <a href="/privacy/">Политикой конфиденциальности</a>.</p>
<h3>Цели обработки</h3>
<ul>
<li>передача заявки выбранной МФО для оформления займа;</li>
<li>информирование о результатах рассмотрения;</li>
<li>аналитика и улучшение сервиса.</li>
</ul>
<h3>Перечень действий</h3>
<p>Сбор, запись, систематизация, накопление, хранение, уточнение, извлечение, использование, передача (предоставление, доступ), обезличивание, блокирование, удаление, уничтожение — как с использованием средств автоматизации, так и без.</p>
<h3>Срок действия</h3>
<p>Согласие действует с момента отправки заявки и до момента его отзыва. Отзыв осуществляется письменным заявлением на e-mail администрации.</p>
HTML;
}

function zaymi_pages_terms_template() {
    $site = get_bloginfo('name');
    return <<<HTML
<h2>1. Общие положения</h2>
<p>Сайт {$site} является информационным сервисом по подбору микрофинансовых продуктов. Сайт не является кредитором, не выдаёт займы и не принимает решений об их выдаче.</p>
<h2>2. Услуги сайта</h2>
<p>Сайт предоставляет пользователю возможность ознакомиться с предложениями МФО-партнёров и направить заявку в выбранную организацию. Решение о выдаче займа и его условия принимаются МФО самостоятельно.</p>
<h2>3. Ответственность</h2>
<p>Администрация сайта не несёт ответственности за действия МФО-партнёров, условия их договоров и решения по заявкам пользователей.</p>
<h2>4. Согласие с условиями</h2>
<p>Используя сайт, пользователь подтверждает, что прочитал и принял условия настоящего соглашения и <a href="/privacy/">Политики конфиденциальности</a>.</p>
HTML;
}

/* ---------- 2. Динамический /robots.txt ---------- */
add_filter('robots_txt', function ($output, $public) {
    if (!$public) return $output;
    $host = preg_replace('#^https?://#', '', home_url());
    $sitemap = home_url('/sitemap.xml');
    $rules = [];
    $rules[] = "User-agent: *";
    $rules[] = "Disallow: /wp-admin/";
    $rules[] = "Disallow: /wp-includes/";
    $rules[] = "Disallow: /xmlrpc.php";
    $rules[] = "Disallow: /go/";              // партнёрские редиректы
    $rules[] = "Disallow: /*?s=";             // поиск
    $rules[] = "Disallow: /*?compare=";       // временные сравнения
    $rules[] = "Allow: /wp-admin/admin-ajax.php";
    $rules[] = "Allow: /wp-content/uploads/";
    $rules[] = "";
    $rules[] = "User-agent: Yandex";
    $rules[] = "Disallow: /wp-admin/";
    $rules[] = "Disallow: /go/";
    $rules[] = "Clean-param: utm_source&utm_medium&utm_campaign&utm_content&utm_term&yclid&gclid&clickid&fbclid&_openstat";
    $rules[] = "Host: {$host}";
    $rules[] = "";
    $rules[] = "Sitemap: {$sitemap}";
    return implode("\n", $rules) . "\n";
}, 10, 2);

/* ---------- 3. ACF: добавить reCAPTCHA в Theme Options ---------- */
add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) return;
    acf_add_local_field_group([
        'key' => 'group_zaymi_recaptcha',
        'title' => '🛡️ reCAPTCHA v3',
        'fields' => [
            ['key'=>'field_zaymi_rc_site',   'name'=>'recaptcha_site_key', 'label'=>'Site Key (публичный)',    'type'=>'text', 'instructions'=>'Получить на https://www.google.com/recaptcha/admin (выбрать reCAPTCHA v3)'],
            ['key'=>'field_zaymi_rc_secret', 'name'=>'recaptcha_secret',   'label'=>'Secret Key (приватный)',  'type'=>'text'],
        ],
        'location' => [[['param'=>'options_page','operator'=>'==','value'=>'zaymi-options']]],
    ]);
});

/* ---------- 4. Подключить reCAPTCHA-скрипт на фронте ---------- */
add_action('wp_enqueue_scripts', function () {
    if (!function_exists('get_field')) return;
    $site = get_field('recaptcha_site_key', 'option');
    if (!$site) return;
    wp_enqueue_script('google-recaptcha', "https://www.google.com/recaptcha/api.js?render={$site}", [], null, true);
    wp_add_inline_script('google-recaptcha', "window.ZAYMI_RECAPTCHA_SITE=" . wp_json_encode($site) . ";");
}, 30);
