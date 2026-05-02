<?php
/**
 * Zaymi Online — авто-SEO для таксономий city / summa / situation.
 *
 * — Уникальные <title> и meta description по шаблонам (если в ACF не заполнено вручную).
 * — Open Graph / Twitter теги.
 * — ACF-поля hub_meta_title / hub_meta_description / hub_intro / hub_seo_text / hub_faq.
 * — Заготовка SEO-текста (выводится через хелпер если hub_seo_text пуст).
 *
 * @package Zaymi
 */
if (!defined('ABSPATH')) exit;

/* ---------- ACF-поля для term'ов ---------- */
add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) return;
    acf_add_local_field_group([
        'key' => 'group_zaymi_term_seo',
        'title' => '🔍 SEO + контент термина',
        'fields' => [
            ['key'=>'field_zaymi_hub_meta_title', 'name'=>'hub_meta_title', 'label'=>'SEO: Title (≤60 символов)', 'type'=>'text', 'instructions'=>'Если пусто — генерируется автоматически.'],
            ['key'=>'field_zaymi_hub_meta_desc',  'name'=>'hub_meta_description', 'label'=>'SEO: Description (≤160)', 'type'=>'textarea', 'rows'=>2],
            ['key'=>'field_zaymi_hub_h1',         'name'=>'hub_h1', 'label'=>'H1 (необязательно)', 'type'=>'text'],
            ['key'=>'field_zaymi_hub_intro',      'name'=>'hub_intro', 'label'=>'Лид-абзац под H1', 'type'=>'textarea', 'rows'=>3],
            ['key'=>'field_zaymi_hub_seo_text',   'name'=>'hub_seo_text', 'label'=>'SEO-текст (низ страницы, HTML)', 'type'=>'wysiwyg', 'instructions'=>'Если оставить пустым — выведется автоматическая заготовка.'],
            ['key'=>'field_zaymi_hub_faq',        'name'=>'hub_faq', 'label'=>'FAQ (попадёт в JSON-LD)', 'type'=>'repeater',
                'sub_fields' => [
                    ['key'=>'field_zaymi_hub_faq_q','name'=>'question','label'=>'Вопрос','type'=>'text'],
                    ['key'=>'field_zaymi_hub_faq_a','name'=>'answer','label'=>'Ответ','type'=>'textarea','rows'=>3],
                ],
                'button_label' => 'Добавить вопрос',
            ],
        ],
        'location' => [
            [['param'=>'taxonomy','operator'=>'==','value'=>'city']],
            [['param'=>'taxonomy','operator'=>'==','value'=>'summa']],
            [['param'=>'taxonomy','operator'=>'==','value'=>'situation']],
        ],
    ]);
});

/* ---------- Генерация title/description ---------- */
function zaymi_term_seo_meta($term = null) {
    $term = $term ?: get_queried_object();
    if (!$term || !isset($term->taxonomy)) return null;
    $tax  = $term->taxonomy;
    $name = $term->name;

    $custom_title = function_exists('get_field') ? get_field('hub_meta_title', $term) : '';
    $custom_desc  = function_exists('get_field') ? get_field('hub_meta_description', $term) : '';

    $title = ''; $desc = '';
    switch ($tax) {
        case 'city':
            $title = "Займ онлайн в {$name} на карту — ТОП МФО " . date('Y');
            $desc  = "Срочный займ в городе {$name}: 25+ МФО, одобрение до 95%, деньги на карту любого банка за 5 минут. Без справок и поручителей. Сравните условия онлайн.";
            break;
        case 'summa':
            $title = "Займ {$name} на карту онлайн — без отказа за 5 минут";
            $desc  = "Возьмите займ {$name} онлайн на карту любого банка. Лучшие МФО с одобрением 90%+. Первый займ под 0%, без справок и поручителей.";
            break;
        case 'situation':
            $title = "{$name} — подборка лучших МФО " . date('Y');
            $desc  = "{$name}: проверенные МФО с лояльными условиями. Высокий процент одобрений, минимум документов, деньги на карту мгновенно.";
            break;
    }
    return [
        'title' => $custom_title ?: $title,
        'desc'  => $custom_desc  ?: $desc,
    ];
}

add_filter('pre_get_document_title', function ($t) {
    if (!is_tax(['city','summa','situation'])) return $t;
    $m = zaymi_term_seo_meta();
    return ($m && !empty($m['title'])) ? $m['title'] : $t;
}, 20);

add_action('wp_head', function () {
    if (!is_tax(['city','summa','situation'])) return;
    $m = zaymi_term_seo_meta();
    if (!$m) return;
    $url = get_term_link(get_queried_object());
    echo "\n<!-- Zaymi term SEO -->\n";
    if (!empty($m['desc']))  echo '<meta name="description" content="'.esc_attr($m['desc']).'">'."\n";
    echo '<link rel="canonical" href="'.esc_url($url).'">'."\n";
    echo '<meta property="og:type" content="website">'."\n";
    echo '<meta property="og:title" content="'.esc_attr($m['title']).'">'."\n";
    echo '<meta property="og:description" content="'.esc_attr($m['desc']).'">'."\n";
    echo '<meta property="og:url" content="'.esc_url($url).'">'."\n";
    echo '<meta name="twitter:card" content="summary_large_image">'."\n";
    echo '<meta name="twitter:title" content="'.esc_attr($m['title']).'">'."\n";
    echo '<meta name="twitter:description" content="'.esc_attr($m['desc']).'">'."\n";
}, 1);

/* ---------- Заготовка SEO-текста для term'ов ---------- */
function zaymi_term_seo_placeholder($term = null) {
    $term = $term ?: get_queried_object();
    if (!$term) return '';
    $name = $term->name;
    switch ($term->taxonomy) {
        case 'city':
            return '<h2>Займы онлайн в городе '.esc_html($name).'</h2>'
                .'<p>Жители '.esc_html($name).' могут оформить микрозайм в любой МФО из подборки полностью онлайн — без посещения офиса и поездок по городу. Деньги поступают на карту любого российского банка в течение 1–5 минут после одобрения, работа идёт круглосуточно, включая праздники и выходные.</p>'
                .'<h3>Условия в '.esc_html($name).'</h3>'
                .'<ul><li>Сумма от 1 000 до 100 000 ₽.</li><li>Срок от 7 до 365 дней.</li><li>Первый займ — под 0% при возврате в срок.</li><li>Только паспорт РФ и активная банковская карта.</li></ul>'
                .'<h3>Кому одобрят займ в '.esc_html($name).'?</h3>'
                .'<p>Гражданам РФ от 18 до 70 лет с действующим паспортом. Прописка в '.esc_html($name).' не обязательна — займы выдаются по всей России. Большинство МФО работают даже с заёмщиками, у которых были просрочки или отказы банков.</p>';
        case 'summa':
            return '<h2>Как получить займ '.esc_html($name).'</h2>'
                .'<p>Эта сумма входит в стандартный лимит большинства МФО, поэтому одобрение приходит более чем в 90% случаев. Главное — корректно заполнить анкету: правильный номер паспорта, действующий мобильный телефон и активная карта на ваше имя.</p>'
                .'<h3>Как быстро придут деньги?</h3>'
                .'<p>Решение по заявке поступает автоматически за 1–5 минут. После одобрения деньги мгновенно зачисляются на карту любого российского банка — Сбербанк, ВТБ, Тинькофф, Альфа-Банк и др.</p>'
                .'<h3>Что важно знать перед оформлением</h3>'
                .'<ul><li>Первый займ под 0% — переплата 0 ₽ при возврате в срок.</li><li>Возможна пролонгация на 7–30 дней.</li><li>Досрочное погашение без штрафов и комиссий.</li></ul>';
        case 'situation':
            return '<h2>'.esc_html($name).' — что важно знать</h2>'
                .'<p>В подборке представлены только проверенные МФО, которые специализируются на работе именно с этим типом заёмщиков. Все компании внесены в реестр Центробанка РФ, работают по федеральному закону №353-ФЗ и имеют высокий процент одобрений.</p>'
                .'<h3>Как повысить шансы на одобрение</h3>'
                .'<ul><li>Подайте заявку сразу в 3–5 МФО — алгоритмы скоринга у всех разные.</li><li>Указывайте только достоверные данные.</li><li>При отказе — уменьшите сумму займа в 2–3 раза.</li><li>Возвращайте первый займ в срок, чтобы открыть больший лимит.</li></ul>'
                .'<h3>Сколько можно получить</h3>'
                .'<p>Стартовый лимит — обычно 5 000–10 000 ₽. После успешного погашения первого займа лимит увеличивается до 30 000–100 000 ₽. Срок — от 7 до 365 дней, ставка от 0% (первый займ) до 1% в день.</p>';
    }
    return '';
}

/* ---------- Шорткод для удобства: [zaymi_term_seo_placeholder] ---------- */
add_shortcode('zaymi_term_seo_placeholder', function () {
    return zaymi_term_seo_placeholder();
});
