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

/* ---------- Похожие статьи блога (по категориям/тегам) ---------- */
add_shortcode('zaymi_related_posts', function ($atts) {
    $a = shortcode_atts(['limit' => 3, 'title' => 'Читайте также'], $atts);
    if (!is_singular('post')) return '';
    $cur = get_the_ID();
    $cats = wp_get_post_categories($cur);
    $tags = wp_get_post_tags($cur, ['fields' => 'ids']);
    $args = [
        'post_type'      => 'post',
        'posts_per_page' => (int)$a['limit'],
        'post__not_in'   => [$cur],
        'orderby'        => 'rand',
    ];
    if ($cats || $tags) {
        $args['tax_query'] = [['relation' => 'OR']];
        if ($cats) $args['tax_query'][] = ['taxonomy' => 'category', 'field' => 'term_id', 'terms' => $cats];
        if ($tags) $args['tax_query'][] = ['taxonomy' => 'post_tag', 'field' => 'term_id', 'terms' => $tags];
    }
    $q = new WP_Query($args);
    if (!$q->have_posts()) {
        // fallback: просто свежие статьи
        $q = new WP_Query(['post_type'=>'post','posts_per_page'=>(int)$a['limit'],'post__not_in'=>[$cur]]);
    }
    if (!$q->have_posts()) return '';
    ob_start(); ?>
    <section class="px-6 py-16 bg-slate-50">
      <div class="mx-auto max-w-7xl">
        <div class="mx-auto max-w-2xl text-center">
          <span class="text-xs font-extrabold uppercase tracking-[0.18em] text-blue-600">Блог</span>
          <h2 class="mt-2 text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900"><?php echo esc_html($a['title']); ?></h2>
        </div>
        <div class="mt-10 grid gap-5 md:grid-cols-3">
          <?php while ($q->have_posts()): $q->the_post(); ?>
            <a href="<?php the_permalink(); ?>" class="group flex flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-md hover:-translate-y-1 hover:shadow-xl transition-all">
              <?php if (has_post_thumbnail()): ?>
                <div class="overflow-hidden rounded-xl mb-4"><?php the_post_thumbnail('medium', ['class' => 'w-full h-40 object-cover group-hover:scale-105 transition-transform duration-500']); ?></div>
              <?php endif; ?>
              <div class="text-xs font-bold text-slate-500"><?php echo get_the_date('j F Y'); ?></div>
              <h3 class="mt-2 text-lg font-extrabold leading-tight text-slate-900 group-hover:text-blue-600"><?php the_title(); ?></h3>
              <p class="mt-2 text-sm text-slate-600 line-clamp-3"><?php echo esc_html(wp_trim_words(get_the_excerpt() ?: strip_tags(get_the_content()), 18)); ?></p>
              <span class="mt-auto pt-3 text-sm font-bold text-emerald-600 inline-flex items-center gap-1">Читать →</span>
            </a>
          <?php endwhile; wp_reset_postdata(); ?>
        </div>
      </div>
    </section>
    <?php return ob_get_clean();
});
