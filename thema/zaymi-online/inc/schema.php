<?php
/**
 * Zaymi Online — Schema.org JSON-LD разметка.
 *
 * Автоматически добавляет structured data в <head>:
 *  - Organization + WebSite (на всех страницах)
 *  - BreadcrumbList (на всех)
 *  - FinancialProduct + AggregateRating + Review (на single-mfo)
 *  - FAQPage (если на странице есть FAQ)
 *  - LocalBusiness (на страницах городов)
 *
 * @package Zaymi
 */

if (!defined('ABSPATH')) exit;

add_action('wp_head', 'zaymi_schema_output', 5);

function zaymi_schema_output() {
    $items = [];

    /* ---- Organization + WebSite ---- */
    $items[] = [
        '@context' => 'https://schema.org',
        '@type'    => 'Organization',
        'name'     => get_bloginfo('name'),
        'url'      => home_url('/'),
        'logo'     => function_exists('get_field') ? (get_field('site_logo','option') ?: '') : '',
    ];
    $items[] = [
        '@context' => 'https://schema.org',
        '@type'    => 'WebSite',
        'name'     => get_bloginfo('name'),
        'url'      => home_url('/'),
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => home_url('/?s={search_term_string}'),
            'query-input' => 'required name=search_term_string',
        ],
    ];

    /* ---- BreadcrumbList ---- */
    $crumbs = zaymi_get_breadcrumbs();
    if (count($crumbs) > 1) {
        $items[] = [
            '@context' => 'https://schema.org',
            '@type'    => 'BreadcrumbList',
            'itemListElement' => array_map(function ($c, $i) {
                return ['@type' => 'ListItem', 'position' => $i+1, 'name' => $c['title'], 'item' => $c['url']];
            }, $crumbs, array_keys($crumbs)),
        ];
    }

    /* ---- FinancialService + FinancialProduct (single-mfo) — расширенные сниппеты ---- */
    if (is_singular('mfo')) {
        $id = get_the_ID();
        $rating        = function_exists('get_field') ? (float) get_field('rating', $id) : 0;
        $reviews_count = function_exists('get_field') ? (int)   get_field('reviews_count', $id) : 0;
        $rate_min      = function_exists('get_field') ? get_field('rate_min', $id) : '';
        $rate_max      = function_exists('get_field') ? get_field('rate_max', $id) : '';
        $sum_min       = function_exists('get_field') ? get_field('sum_min', $id)  : '';
        $sum_max       = function_exists('get_field') ? get_field('sum_max', $id)  : '';
        $term_min      = function_exists('get_field') ? get_field('term_min', $id) : '';
        $term_max      = function_exists('get_field') ? get_field('term_max', $id) : '';
        $logo_url      = get_the_post_thumbnail_url($id, 'medium') ?: '';

        /* 1. FinancialService — главный объект для расширенных сниппетов МФО */
        $fs = [
            '@context'    => 'https://schema.org',
            '@type'       => ['FinancialService', 'Organization'],
            '@id'         => get_permalink() . '#mfo',
            'name'        => get_the_title(),
            'url'         => get_permalink(),
            'description' => wp_strip_all_tags(get_the_excerpt() ?: get_the_content()),
            'image'       => $logo_url,
            'logo'        => $logo_url,
            'areaServed'  => ['@type' => 'Country', 'name' => 'Россия'],
            'currenciesAccepted' => 'RUB',
            'priceRange'  => '₽',
        ];
        if ($rating > 0 && $reviews_count > 0) {
            $fs['aggregateRating'] = [
                '@type'       => 'AggregateRating',
                'ratingValue' => $rating,
                'bestRating'  => 5,
                'worstRating' => 1,
                'reviewCount' => $reviews_count,
            ];
        }
        $items[] = $fs;

        /* 2. FinancialProduct — описание продукта (займ) */
        $fp = [
            '@context' => 'https://schema.org',
            '@type'    => 'FinancialProduct',
            'name'     => 'Займ ' . get_the_title(),
            'url'      => get_permalink(),
            'description' => wp_strip_all_tags(get_the_excerpt() ?: get_the_content()),
            'provider' => ['@type'=>'Organization','name'=>get_the_title(),'url'=>get_permalink()],
            'category' => 'Микрозайм',
        ];
        if ($rate_min || $rate_max) {
            $fp['interestRate'] = [
                '@type'    => 'QuantitativeValue',
                'minValue' => (float)($rate_min ?: 0),
                'maxValue' => (float)($rate_max ?: $rate_min),
                'unitText' => 'percent per day',
            ];
        }
        if ($sum_min || $sum_max) {
            $fp['amount'] = [
                '@type'    => 'MonetaryAmount',
                'currency' => 'RUB',
                'value'    => ['@type'=>'QuantitativeValue','minValue'=>(float)$sum_min,'maxValue'=>(float)$sum_max],
            ];
        }
        if ($term_min || $term_max) {
            $fp['loanTerm'] = [
                '@type'    => 'QuantitativeValue',
                'minValue' => (int)$term_min,
                'maxValue' => (int)$term_max,
                'unitText' => 'days',
            ];
        }
        if ($rating > 0 && $reviews_count > 0) {
            $fp['aggregateRating'] = [
                '@type'       => 'AggregateRating',
                'ratingValue' => $rating,
                'bestRating'  => 5,
                'reviewCount' => $reviews_count,
            ];
        }
        $items[] = $fp;
    }

    /* ---- FAQPage ---- */
    $faq = zaymi_collect_faq();
    if (!empty($faq)) {
        $items[] = [
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => array_map(function ($q) {
                return [
                    '@type' => 'Question',
                    'name'  => $q['q'],
                    'acceptedAnswer' => ['@type'=>'Answer', 'text'=>$q['a']],
                ];
            }, $faq),
        ];
    }

    /* ---- LocalBusiness (taxonomy city) ---- */
    if (is_tax('city')) {
        $term = get_queried_object();
        $items[] = [
            '@context' => 'https://schema.org',
            '@type'    => 'LocalBusiness',
            'name'     => 'Займы в городе ' . $term->name,
            'url'      => get_term_link($term),
            'areaServed' => ['@type' => 'City', 'name' => $term->name],
        ];
    }

    foreach ($items as $i) {
        echo '<script type="application/ld+json">' . wp_json_encode($i, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
    }
}

/* ---------- Сбор FAQ со страницы ---------- */
function zaymi_collect_faq() {
    if (!function_exists('get_field')) return [];
    $faq = [];
    if (is_singular()) {
        $rows = get_field('faq', get_the_ID());
        if ($rows) foreach ($rows as $r) {
            if (!empty($r['question']) && !empty($r['answer'])) {
                $faq[] = ['q' => $r['question'], 'a' => wp_strip_all_tags($r['answer'])];
            }
        }
    }
    return apply_filters('zaymi_faq_items', $faq);
}

/* ---------- Хлебные крошки ---------- */
function zaymi_get_breadcrumbs() {
    $crumbs = [['title' => 'Главная', 'url' => home_url('/')]];

    if (is_singular('mfo')) {
        $crumbs[] = ['title' => 'МФО', 'url' => get_post_type_archive_link('mfo')];
        $crumbs[] = ['title' => get_the_title(), 'url' => get_permalink()];
    } elseif (is_post_type_archive('mfo')) {
        $crumbs[] = ['title' => 'Каталог МФО', 'url' => get_post_type_archive_link('mfo')];
    } elseif (is_tax('city')) {
        $t = get_queried_object();
        $crumbs[] = ['title' => 'Города', 'url' => home_url('/goroda/')];
        $crumbs[] = ['title' => $t->name, 'url' => get_term_link($t)];
    } elseif (is_tax('summa')) {
        $t = get_queried_object();
        $crumbs[] = ['title' => 'По сумме', 'url' => home_url('/summa/')];
        $crumbs[] = ['title' => $t->name, 'url' => get_term_link($t)];
    } elseif (is_tax('situation')) {
        $t = get_queried_object();
        $crumbs[] = ['title' => 'Подборки', 'url' => home_url('/situations/')];
        $crumbs[] = ['title' => $t->name, 'url' => get_term_link($t)];
    } elseif (is_singular('post')) {
        $crumbs[] = ['title' => 'Блог', 'url' => home_url('/blog/')];
        $crumbs[] = ['title' => get_the_title(), 'url' => get_permalink()];
    } elseif (is_page()) {
        $crumbs[] = ['title' => get_the_title(), 'url' => get_permalink()];
    }
    return $crumbs;
}

/* ---------- Шорткод хлебных крошек [zaymi_breadcrumbs] ---------- */
add_shortcode('zaymi_breadcrumbs', function () {
    $c = zaymi_get_breadcrumbs();
    if (count($c) < 2) return '';
    $out = '<nav class="zaymi-crumbs" aria-label="Хлебные крошки"><ol>';
    foreach ($c as $i => $crumb) {
        $last = $i === count($c) - 1;
        $out .= '<li>' . ($last ? '<span>'.esc_html($crumb['title']).'</span>' : '<a href="'.esc_url($crumb['url']).'">'.esc_html($crumb['title']).'</a>') . '</li>';
    }
    $out .= '</ol></nav>';
    return $out;
});
