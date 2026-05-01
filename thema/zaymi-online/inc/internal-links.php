<?php
/**
 * Zaymi Online — Внутренняя перелинковка + LSI-блок.
 *
 * Шорткоды:
 *  [zaymi_related_mfo limit="6"]   — похожие МФО (на странице МФО)
 *  [zaymi_lsi]                     — блок "С этим ищут"
 *  [zaymi_internal_links]          — динамические ссылки на популярные суммы/города
 *
 * Также делает автозамену упоминаний МФО в тексте статьи на ссылки.
 *
 * @package Zaymi
 */

if (!defined('ABSPATH')) exit;

/* ---------- Похожие МФО ---------- */
add_shortcode('zaymi_related_mfo', function ($atts) {
    $a = shortcode_atts(['limit' => 6, 'title' => 'Похожие МФО'], $atts);
    if (!is_singular('mfo')) return '';
    $args = [
        'post_type'      => 'mfo',
        'posts_per_page' => (int)$a['limit'],
        'post__not_in'   => [get_the_ID()],
        'orderby'        => 'rand',
    ];
    $q = new WP_Query($args);
    if (!$q->have_posts()) return '';
    $out = '<section class="zaymi-related"><h3>'.esc_html($a['title']).'</h3><div class="zr-grid">';
    while ($q->have_posts()) { $q->the_post();
        $out .= '<a class="zr-card" href="'.get_permalink().'">';
        $out .= get_the_post_thumbnail(get_the_ID(),'thumbnail',['class'=>'zr-logo']);
        $out .= '<div><b>'.get_the_title().'</b>';
        if (function_exists('get_field')) {
            $r = get_field('rate_min'); if ($r) $out.='<span>от '.$r.'%/день</span>';
        }
        $out .= '</div></a>';
    }
    wp_reset_postdata();
    return $out . '</div></section>';
});

/* ---------- LSI-блок "С этим ищут" ---------- */
add_shortcode('zaymi_lsi', function () {
    $base = ['займ на карту','займ без отказа','займ онлайн','займ с плохой кредитной историей','займ без процентов','займ за 5 минут','микрозайм','быстрый займ'];
    $cities = wp_list_pluck(get_terms(['taxonomy'=>'city','number'=>5,'hide_empty'=>false]),'name');
    $sums   = ['5000 рублей','10000 рублей','15000 рублей','30000 рублей'];

    $links = [];
    foreach (array_slice($base,0,5) as $kw) {
        $links[] = '<a href="'.esc_url(home_url('/?s='.urlencode($kw))).'">'.esc_html($kw).'</a>';
    }
    foreach (array_slice($cities,0,3) as $c) {
        $links[] = '<a href="'.esc_url(home_url('/?s='.urlencode("займ в $c"))).'">займ в '.esc_html($c).'</a>';
    }
    foreach ($sums as $s) {
        $links[] = '<a href="'.esc_url(home_url('/?s='.urlencode($s))).'">займ '.esc_html($s).'</a>';
    }
    return '<section class="zaymi-lsi"><h3>С этим ищут</h3><div class="zl-cloud">'.implode(' ',$links).'</div></section>';
});

/* ---------- Внутренние ссылки на суммы/города/ситуации ---------- */
add_shortcode('zaymi_internal_links', function () {
    $sums = get_terms(['taxonomy'=>'summa','hide_empty'=>false]);
    $cities = get_terms(['taxonomy'=>'city','hide_empty'=>false,'number'=>15]);
    $sit = get_terms(['taxonomy'=>'situation','hide_empty'=>false]);
    $out = '<section class="zaymi-internal">';
    if ($sums) {
        $out .= '<h3>Займы по сумме</h3><div class="zi-row">';
        foreach($sums as $t) $out .= '<a href="'.get_term_link($t).'">'.esc_html($t->name).'</a>';
        $out .= '</div>';
    }
    if ($cities) {
        $out .= '<h3>Займы по городам</h3><div class="zi-row">';
        foreach($cities as $t) $out .= '<a href="'.get_term_link($t).'">'.esc_html($t->name).'</a>';
        $out .= '</div>';
    }
    if ($sit) {
        $out .= '<h3>Подборки</h3><div class="zi-row">';
        foreach($sit as $t) $out .= '<a href="'.get_term_link($t).'">'.esc_html($t->name).'</a>';
        $out .= '</div>';
    }
    return $out . '</section>';
});

/* ---------- Автозамена упоминаний МФО в тексте на ссылки ---------- */
add_filter('the_content', function ($content) {
    if (!is_singular('post')) return $content;
    $mfos = get_posts(['post_type'=>'mfo','posts_per_page'=>-1]);
    $used = [];
    foreach ($mfos as $m) {
        $name = $m->post_title;
        if (in_array($name, $used) || mb_strlen($name) < 3) continue;
        $url = get_permalink($m);
        // первое упоминание -> ссылка
        $pattern = '/(?<![>\/a-zа-яё])'. preg_quote($name,'/') .'(?![<\/a-zа-яё])/u';
        $count = 0;
        $content = preg_replace_callback($pattern, function ($m2) use ($url, &$count) {
            if ($count++) return $m2[0];
            return '<a href="'.esc_url($url).'" class="zaymi-auto-link">'.$m2[0].'</a>';
        }, $content, 1);
        $used[] = $name;
    }
    return $content;
});
