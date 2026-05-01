<?php
/**
 * Zaymi Online — Программатик SEO.
 *
 * Виртуальные страницы (без записи в БД):
 *  /zaymy/{сумма}-rubley-na-{срок}-dney/
 *  /zaymy/{город}/bez-otkaza/
 *  /zaymy/{город}/s-plokhoy-kreditnoy-istoriey/
 *  /sravnenie/{мфо1}-vs-{мфо2}/
 *
 * Шаблоны: thema/zaymi-online/template-parts/seo-*.php
 *
 * @package Zaymi
 */

if (!defined('ABSPATH')) exit;

/* ---------- Rewrite-правила ---------- */
add_action('init', function () {
    // /zaymy/5000-rubley-na-7-dney/
    add_rewrite_rule(
        '^zaymy/([0-9]+)-rubley-na-([0-9]+)-dney/?$',
        'index.php?zaymi_seo=amount&zaymi_amount=$matches[1]&zaymi_term=$matches[2]',
        'top'
    );
    // /zaymy/{city-slug}/bez-otkaza/
    add_rewrite_rule(
        '^zaymy/([a-z0-9-]+)/bez-otkaza/?$',
        'index.php?zaymi_seo=city_noref&zaymi_city=$matches[1]',
        'top'
    );
    // /zaymy/{city-slug}/s-plokhoy-kreditnoy-istoriey/
    add_rewrite_rule(
        '^zaymy/([a-z0-9-]+)/s-plokhoy-kreditnoy-istoriey/?$',
        'index.php?zaymi_seo=city_bad&zaymi_city=$matches[1]',
        'top'
    );
    // /sravnenie/{mfo1}-vs-{mfo2}/
    add_rewrite_rule(
        '^sravnenie/([a-z0-9-]+)-vs-([a-z0-9-]+)/?$',
        'index.php?zaymi_seo=compare&zaymi_mfo1=$matches[1]&zaymi_mfo2=$matches[2]',
        'top'
    );
});
add_filter('query_vars', function ($v) {
    return array_merge($v, ['zaymi_seo','zaymi_amount','zaymi_term','zaymi_city','zaymi_mfo1','zaymi_mfo2']);
});

/* ---------- Подмена шаблона ---------- */
add_filter('template_include', function ($tpl) {
    $type = get_query_var('zaymi_seo');
    if (!$type) return $tpl;

    $map = [
        'amount'     => 'template-parts/seo-amount.php',
        'city_noref' => 'template-parts/seo-city.php',
        'city_bad'   => 'template-parts/seo-city.php',
        'compare'    => 'template-parts/seo-compare.php',
    ];
    if (isset($map[$type])) {
        $path = ZAYMI_DIR . '/' . $map[$type];
        if (file_exists($path)) return $path;
    }
    return $tpl;
});

/* ---------- Хелперы для шаблонов ---------- */
function zaymi_seo_meta() {
    $type = get_query_var('zaymi_seo');
    if (!$type) return null;

    $amount = (int) get_query_var('zaymi_amount');
    $term   = (int) get_query_var('zaymi_term');
    $city_s = get_query_var('zaymi_city');
    $mfo1_s = get_query_var('zaymi_mfo1');
    $mfo2_s = get_query_var('zaymi_mfo2');

    $city_term = $city_s ? get_term_by('slug', $city_s, 'city') : null;
    $city_name = $city_term ? $city_term->name : '';

    $mfo1 = $mfo1_s ? get_page_by_path($mfo1_s, OBJECT, 'mfo') : null;
    $mfo2 = $mfo2_s ? get_page_by_path($mfo2_s, OBJECT, 'mfo') : null;

    switch ($type) {
        case 'amount':
            $term_word = zaymi_decline_days($term);
            return [
                'h1'    => "Займ {$amount} рублей на {$term} {$term_word}",
                'title' => "Займ {$amount}₽ на {$term} {$term_word} онлайн на карту — ТОП МФО 2026",
                'desc'  => "Получите займ {$amount} рублей на {$term} {$term_word} без отказа на карту за 5 минут. Сравните условия лучших МФО, выберите минимальную ставку.",
                'amount'=> $amount, 'term' => $term,
            ];
        case 'city_noref':
            return [
                'h1'    => "Займы без отказа в городе {$city_name}",
                'title' => "Займ без отказа в {$city_name} — на карту онлайн срочно | 2026",
                'desc'  => "Срочные займы без отказа в {$city_name}: 25+ МФО, одобрение 99%, деньги на карту за 5 минут. Подберите подходящий займ онлайн.",
                'city_name' => $city_name, 'city_slug' => $city_s,
                'subtype' => 'noref',
            ];
        case 'city_bad':
            return [
                'h1'    => "Займы с плохой кредитной историей в {$city_name}",
                'title' => "Займ с плохой кредитной историей в {$city_name} онлайн — без отказа",
                'desc'  => "Займ с плохой кредитной историей в {$city_name}: МФО, которые одобряют даже с просрочками и черным списком. Деньги на карту за 5 минут.",
                'city_name' => $city_name, 'city_slug' => $city_s,
                'subtype' => 'bad',
            ];
        case 'compare':
            if (!$mfo1 || !$mfo2) return null;
            $n1 = get_the_title($mfo1); $n2 = get_the_title($mfo2);
            return [
                'h1'    => "Сравнение {$n1} и {$n2}",
                'title' => "{$n1} или {$n2} — что выбрать? Подробное сравнение условий 2026",
                'desc'  => "Сравниваем {$n1} и {$n2}: процентные ставки, суммы займов, требования к заёмщику, скорость одобрения. Выберите лучшее МФО.",
                'mfo1' => $mfo1, 'mfo2' => $mfo2,
            ];
    }
    return null;
}

function zaymi_decline_days($n) {
    $n = abs((int)$n) % 100;
    if ($n > 10 && $n < 20) return 'дней';
    $n %= 10;
    if ($n === 1) return 'день';
    if ($n >= 2 && $n <= 4) return 'дня';
    return 'дней';
}

/* ---------- Title тег ---------- */
add_filter('pre_get_document_title', function ($t) {
    $m = zaymi_seo_meta();
    return $m && !empty($m['title']) ? $m['title'] : $t;
});

/* ---------- Meta description ---------- */
add_action('wp_head', function () {
    $m = zaymi_seo_meta();
    if ($m && !empty($m['desc'])) {
        echo '<meta name="description" content="' . esc_attr($m['desc']) . '">' . "\n";
    }
}, 1);

/* ---------- Генератор уникального текста (спинтакс) ---------- */
function zaymi_spin($text) {
    return preg_replace_callback('/\{([^{}]+)\}/', function ($m) {
        $opts = explode('|', $m[1]);
        return $opts[array_rand($opts)];
    }, $text);
}
