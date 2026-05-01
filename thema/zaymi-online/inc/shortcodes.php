<?php
/**
 * Шорткоды Zaymi Online.
 * Все блоки 1:1 с React-дизайном zaymi-online.lovable.app.
 *
 * Использование в редакторе WP (классическом или блоке "Шорткод"):
 *   [zaymi_hero]
 *   [zaymi_social_proof]
 *   [zaymi_situations_grid]
 *   [zaymi_mfo_catalog limit="8" filter=""]
 *   [zaymi_amounts_grid]
 *   [zaymi_comparison]
 *   [zaymi_seo_hub]
 *   [zaymi_faq]
 *   [zaymi_mfo_card slug="zaymer"]
 *   [zaymi_mfo_hero slug="zaymer"]
 *   [zaymi_mfo_conditions slug="zaymer"]
 *   [zaymi_mfo_pros_cons slug="zaymer"]
 *   [zaymi_mfo_rates slug="zaymer"]
 *   [zaymi_mfo_steps slug="zaymer"]
 *   [zaymi_mfo_faq slug="zaymer"]
 *   [zaymi_mfo_reviews slug="zaymer"]
 *   [zaymi_mfo_similar slug="zaymer" limit="3"]
 *   [zaymi_final_cta title="..." button="..." url="..."]
 *   [zaymi_cities_grid]
 */
if (!defined('ABSPATH')) exit;

/* ===== helpers ===== */
function zaymi_get_mfo_by_slug($slug) {
    if (!$slug) return null;
    return get_page_by_path(sanitize_title($slug), OBJECT, 'mfo');
}
function zaymi_letter($title) {
    return mb_strtoupper(mb_substr(trim($title), 0, 1, 'UTF-8'), 'UTF-8');
}
function zaymi_gradient_for($slug) {
    $palettes = [
        'from-[#2563eb] to-[#10b981]','from-[#f59e0b] to-[#10b981]',
        'from-[#2563eb] to-[#60a5fa]','from-[#10b981] to-[#34d399]',
        'from-[#f59e0b] to-[#2563eb]','from-[#2563eb] to-[#f59e0b]',
        'from-[#10b981] to-[#2563eb]','from-[#f59e0b] to-[#10b981]',
    ];
    $i = abs(crc32($slug)) % count($palettes);
    return $palettes[$i];
}

/* =================================================================
 *  HERO (главная)
 * ================================================================= */
add_shortcode('zaymi_hero', function ($atts) {
    $a = shortcode_atts([
        'title'    => 'Займ онлайн без отказа за 5 минут',
        'subtitle' => 'Сравнили 50+ МФО — выбрали лучшие предложения. Получите деньги на карту с любой кредитной историей.',
        'cta'      => 'Подобрать займ',
    ], $atts);
    ob_start(); ?>
    <section class="zaymi-section relative overflow-hidden" style="background: linear-gradient(180deg,#f0fdf4 0%, #eff6ff 60%, #ffffff 100%);">
      <div class="mx-auto max-w-7xl px-6 py-16 lg:py-20 grid gap-10 lg:grid-cols-2">
        <div class="flex flex-col justify-center">
          <span class="text-xs font-extrabold uppercase tracking-[0.18em] text-emerald-600">ТОП-50 МФО <?php echo date('Y'); ?></span>
          <h1 class="mt-4 font-extrabold tracking-tight text-slate-900" style="font-size:clamp(34px,5vw,56px);line-height:1.04;">
            <?php echo esc_html($a['title']); ?>
          </h1>
          <p class="mt-5 max-w-xl text-lg font-medium text-slate-500"><?php echo esc_html($a['subtitle']); ?></p>
          <div class="mt-7 grid grid-cols-2 sm:grid-cols-4 gap-3">
            <?php foreach ([['shield-check','Без поручителей'],['file-text','Без справок'],['percent','С плохой КИ'],['wallet','На карту 24/7']] as $b): ?>
              <div class="flex items-center gap-2 rounded-full border border-slate-200 bg-white/70 px-3 py-2 backdrop-blur-sm">
                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 text-emerald-600"><?php echo zaymi_icon($b[0],'w-3.5 h-3.5'); ?></span>
                <span class="text-xs font-bold text-slate-900"><?php echo esc_html($b[1]); ?></span>
              </div>
            <?php endforeach; ?>
          </div>
          <div class="mt-8 flex flex-wrap gap-3">
            <a href="#mfo-catalog" class="inline-flex h-14 items-center justify-center gap-2 rounded-full bg-emerald-500 px-7 text-base font-bold text-white shadow-lg hover:bg-emerald-600 transition-all">
              <?php echo esc_html($a['cta']); ?> <?php echo zaymi_icon('arrow-right','w-5 h-5'); ?>
            </a>
            <a href="<?php echo esc_url(get_post_type_archive_link('mfo') ?: '#'); ?>" class="inline-flex h-14 items-center justify-center rounded-full border border-slate-200 bg-white px-7 text-base font-bold text-slate-900 hover:border-blue-500 hover:text-blue-600 transition-all">Смотреть каталог</a>
          </div>
        </div>
        <div class="relative">
          <div class="absolute -inset-4 -z-10 rounded-[36px] bg-gradient-to-br from-blue-200/40 to-emerald-200/40 blur-2xl"></div>
          <div class="rounded-[28px] border border-white/60 bg-white/80 p-6 sm:p-8 shadow-2xl backdrop-blur-xl">
            <div class="text-xs font-extrabold uppercase tracking-wider text-blue-600">Калькулятор займа</div>
            <h3 class="mt-1 text-2xl font-extrabold text-slate-900">Подберите идеальные условия</h3>
            <div class="mt-6 space-y-7" data-zaymi-calc>
              <div>
                <div class="flex items-baseline justify-between">
                  <span class="text-sm font-bold text-slate-900">Сумма займа</span>
                  <span class="text-3xl font-extrabold text-emerald-600" data-out="amount">15 000 ₽</span>
                </div>
                <input type="range" min="1000" max="100000" step="1000" value="15000" class="zaymi-range mt-3 w-full" data-input="amount" />
                <div class="mt-2 flex justify-between text-xs font-semibold text-slate-500"><span>1 000 ₽</span><span>100 000 ₽</span></div>
              </div>
              <div>
                <div class="flex items-baseline justify-between">
                  <span class="text-sm font-bold text-slate-900">Срок займа</span>
                  <span class="text-3xl font-extrabold text-blue-600" data-out="term">30 дней</span>
                </div>
                <input type="range" min="1" max="365" step="1" value="30" class="zaymi-range mt-3 w-full" data-input="term" />
                <div class="mt-2 flex justify-between text-xs font-semibold text-slate-500"><span>1 день</span><span>365 дней</span></div>
              </div>
            </div>
            <div class="mt-6 inline-flex items-center gap-2 rounded-full bg-emerald-100 px-4 py-2 text-sm font-bold text-emerald-700">
              <?php echo zaymi_icon('check','w-4 h-4'); ?> Найдено <span data-out="count">12</span> предложений
            </div>
            <a href="#mfo-catalog" class="mt-5 flex h-14 w-full items-center justify-center gap-2 rounded-full bg-emerald-500 text-base font-bold text-white shadow-lg hover:bg-emerald-600">
              Подобрать займ <?php echo zaymi_icon('arrow-right','w-5 h-5'); ?>
            </a>
            <p class="mt-3 text-center text-xs font-medium text-slate-500">Бесплатно. Без отказа. Решение за 5 минут.</p>
          </div>
        </div>
      </div>
    </section>
    <?php return ob_get_clean();
});

/* =================================================================
 *  SOCIAL PROOF (полоска цифр)
 * ================================================================= */
add_shortcode('zaymi_social_proof', function () {
    $stats = [
        ['50+',           'МФО в каталоге'],
        ['127 000 ₽',     'выдано займов сегодня'],
        ['4.8/5',         'средний рейтинг'],
        ['97%',           'одобрений за 24ч'],
    ];
    ob_start(); ?>
    <section class="relative -mt-10 lg:-mt-14 px-6">
      <div class="mx-auto max-w-7xl">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-px overflow-hidden rounded-2xl border border-slate-200 bg-slate-200 shadow-lg">
          <?php foreach ($stats as $s): ?>
            <div class="bg-white p-6 text-center hover:bg-slate-50 transition-colors">
              <div class="text-3xl md:text-4xl font-extrabold tracking-tight bg-gradient-to-r from-blue-600 to-emerald-500 bg-clip-text text-transparent"><?php echo esc_html($s[0]); ?></div>
              <div class="mt-1 text-xs md:text-sm font-semibold text-slate-500"><?php echo esc_html($s[1]); ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <?php return ob_get_clean();
});

/* =================================================================
 *  SITUATIONS GRID
 * ================================================================= */
add_shortcode('zaymi_situations_grid', function () {
    $terms = get_terms(['taxonomy' => 'situation', 'hide_empty' => false]);
    if (is_wp_error($terms) || empty($terms)) return '';
    $emojis = ['bez-otkaza'=>'🚫','s-plohoy-ki'=>'📉','pensioneram'=>'👴','studentam'=>'🎓','bez-spravok'=>'📄','srochno'=>'⚡'];
    ob_start(); ?>
    <section class="px-6 py-20 bg-slate-50">
      <div class="mx-auto max-w-7xl">
        <div class="mx-auto max-w-2xl text-center">
          <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900">Подберём займ под вашу ситуацию</h2>
          <p class="mt-3 text-base md:text-lg text-slate-500">Каждой ситуации — свои подходящие МФО</p>
        </div>
        <div class="mt-12 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
          <?php foreach ($terms as $t):
            $count = (int) get_terms(['taxonomy'=>'situation','slug'=>$t->slug,'hide_empty'=>false,'fields'=>'count']);
            $count = wp_count_posts('mfo')->publish ? max(3, (int) $t->count) : 0;
          ?>
            <a href="<?php echo esc_url(get_term_link($t)); ?>" class="group flex flex-col rounded-2xl border border-slate-200 bg-white p-7 shadow-md hover:-translate-y-1 hover:border-emerald-300 hover:shadow-xl transition-all">
              <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-slate-100 to-white text-4xl shadow-md"><?php echo $emojis[$t->slug] ?? '✨'; ?></div>
              <h3 class="mt-5 text-[22px] font-extrabold leading-tight text-slate-900"><?php echo esc_html($t->name); ?></h3>
              <p class="mt-2 text-sm font-medium text-slate-500"><?php echo esc_html(wp_trim_words(strip_tags($t->description), 14)); ?></p>
              <div class="mt-auto flex items-center gap-1.5 pt-5 text-sm font-bold text-emerald-600">
                <span>→ <?php echo $count; ?> МФО</span>
                <?php echo zaymi_icon('arrow-right','w-4 h-4 transition-transform group-hover:translate-x-1'); ?>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <?php return ob_get_clean();
});

/* =================================================================
 *  MFO CATALOG (главная — карточки 4 в ряд)
 * ================================================================= */
add_shortcode('zaymi_mfo_catalog', function ($atts) {
    $a = shortcode_atts(['limit' => 8, 'situation' => '', 'summa' => '', 'city' => '', 'title' => 'Лучшие МФО ' . date('Y') . ' года', 'subtitle' => 'Топ микрофинансовых организаций по нашему рейтингу'], $atts);
    $args = ['posts_per_page' => (int)$a['limit']];
    $tax = [];
    foreach (['situation','summa','city'] as $tx) {
        if (!empty($a[$tx])) $tax[] = ['taxonomy' => $tx, 'field' => 'slug', 'terms' => array_map('trim', explode(',', $a[$tx]))];
    }
    if ($tax) $args['tax_query'] = $tax;
    $q = zaymi_query_mfo($args);
    if (!$q->have_posts()) return '<div class="px-6 py-20 text-center text-slate-500">МФО не найдены. Зайдите в админку и засейте демо-контент.</div>';
    ob_start(); ?>
    <section id="mfo-catalog" class="px-6 py-20">
      <div class="mx-auto max-w-7xl">
        <div class="mx-auto max-w-2xl text-center">
          <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900"><?php echo esc_html($a['title']); ?></h2>
          <p class="mt-3 text-base md:text-lg text-slate-500"><?php echo esc_html($a['subtitle']); ?></p>
        </div>
        <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
          <?php while ($q->have_posts()): $q->the_post(); $id = get_the_ID(); $slug = get_post_field('post_name', $id);
            $rating = (float) (get_field('mfo_rating', $id) ?: 4.5);
            $reviews = (int) (get_field('mfo_reviews_count', $id) ?: 0);
            $amax = (int) get_field('mfo_amount_max', $id);
            $tmax = (int) get_field('mfo_term_max', $id);
            $rmin = get_field('mfo_rate_min', $id);
            $appr = (int) get_field('mfo_approval_rate', $id);
            $logo = get_field('mfo_logo', $id);
            $logo_url = is_array($logo) ? $logo['url'] : zaymi_mfo_logo_url($id);
            $tagline = get_field('mfo_tagline', $id);
          ?>
            <div class="group flex flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-md hover:-translate-y-1 hover:shadow-xl transition-all duration-300">
              <div class="flex items-start justify-between">
                <?php if ($logo_url): ?>
                  <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" class="h-14 w-14 rounded-2xl object-cover shadow-md" />
                <?php else: ?>
                  <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br <?php echo zaymi_gradient_for($slug); ?> text-2xl font-extrabold text-white shadow-md"><?php echo zaymi_letter(get_the_title()); ?></div>
                <?php endif; ?>
                <div class="text-right">
                  <?php echo zaymi_stars($rating); ?>
                  <div class="mt-1 text-sm font-extrabold text-slate-900"><?php echo number_format($rating,1,'.',''); ?></div>
                  <div class="text-[11px] font-medium text-slate-500">(<?php echo number_format($reviews,0,'',' '); ?> отзывов)</div>
                </div>
              </div>
              <h3 class="mt-4 text-[22px] font-extrabold leading-tight text-slate-900">
                <a href="<?php the_permalink(); ?>" class="hover:text-blue-600"><?php the_title(); ?></a>
              </h3>
              <?php if ($tagline): ?>
              <div class="mt-3 flex flex-wrap gap-1.5">
                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-bold bg-amber-100 text-amber-800 ring-1 ring-amber-300"><?php echo esc_html($tagline); ?></span>
              </div>
              <?php endif; ?>
              <dl class="mt-4 space-y-2 rounded-xl bg-slate-50 p-3 text-sm">
                <div class="flex justify-between"><dt class="text-xs font-semibold text-slate-500">Сумма:</dt><dd class="text-sm font-extrabold text-slate-900">до <?php echo number_format($amax,0,'',' '); ?> ₽</dd></div>
                <div class="flex justify-between"><dt class="text-xs font-semibold text-slate-500">Срок:</dt><dd class="text-sm font-extrabold text-slate-900">до <?php echo zaymi_days($tmax); ?></dd></div>
                <div class="flex justify-between"><dt class="text-xs font-semibold text-slate-500">Ставка:</dt><dd class="text-sm font-extrabold text-emerald-600">от <?php echo esc_html($rmin); ?>%</dd></div>
                <div class="flex justify-between"><dt class="text-xs font-semibold text-slate-500">Одобрение:</dt><dd class="text-sm font-extrabold text-slate-900"><?php echo $appr; ?>%</dd></div>
              </dl>
              <a href="<?php echo esc_url(get_field('mfo_partner_url', $id) ?: '#'); ?>" target="_blank" rel="nofollow noopener" class="mt-5 flex h-12 w-full items-center justify-center gap-2 rounded-full bg-emerald-500 text-sm font-bold text-white shadow-md hover:bg-emerald-600">
                Получить займ <?php echo zaymi_icon('arrow-right','w-4 h-4'); ?>
              </a>
              <a href="<?php the_permalink(); ?>" class="mt-2 flex h-11 w-full items-center justify-center gap-2 rounded-full border border-slate-200 bg-white text-sm font-bold text-slate-900 hover:border-blue-500 hover:bg-slate-50 hover:text-blue-600">
                <?php echo zaymi_icon('file-text','w-4 h-4'); ?> Обзор <?php the_title(); ?>
              </a>
              <p class="mt-2.5 text-center text-[11px] font-medium text-slate-500">Заявка за 5 минут • Без справок</p>
            </div>
          <?php endwhile; wp_reset_postdata(); ?>
        </div>
        <div class="mt-10 text-center">
          <a href="<?php echo esc_url(get_post_type_archive_link('mfo')); ?>" class="inline-flex h-12 items-center justify-center gap-2 rounded-full border border-slate-200 bg-white px-6 text-sm font-bold text-slate-900 hover:border-blue-500 hover:text-blue-600">
            Показать все МФО <?php echo zaymi_icon('arrow-right','w-4 h-4'); ?>
          </a>
        </div>
      </div>
    </section>
    <?php return ob_get_clean();
});

/* =================================================================
 *  AMOUNTS GRID
 * ================================================================= */
add_shortcode('zaymi_amounts_grid', function () {
    $terms = get_terms(['taxonomy' => 'summa', 'hide_empty' => false]);
    if (is_wp_error($terms) || !$terms) return '';
    ob_start(); ?>
    <section class="px-6 py-20">
      <div class="mx-auto max-w-7xl">
        <div class="mx-auto max-w-2xl text-center">
          <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900">Выберите нужную сумму</h2>
          <p class="mt-3 text-base md:text-lg text-slate-500">Подборка МФО под конкретный размер займа</p>
        </div>
        <div class="mx-auto mt-10 flex max-w-3xl flex-wrap justify-center gap-3">
          <?php foreach ($terms as $t): ?>
            <a href="<?php echo esc_url(get_term_link($t)); ?>" class="rounded-full border border-slate-200 bg-white px-5 py-2.5 text-sm font-extrabold text-slate-900 hover:-translate-y-0.5 hover:border-emerald-500 hover:text-emerald-600 hover:shadow-md transition-all"><?php echo esc_html($t->name); ?></a>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <?php return ob_get_clean();
});

/* =================================================================
 *  CITIES GRID
 * ================================================================= */
add_shortcode('zaymi_cities_grid', function () {
    $terms = get_terms(['taxonomy' => 'city', 'hide_empty' => false]);
    if (is_wp_error($terms) || !$terms) return '';
    ob_start(); ?>
    <section class="px-6 py-20 bg-slate-50">
      <div class="mx-auto max-w-7xl">
        <div class="mx-auto max-w-2xl text-center">
          <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900">Займы по городам России</h2>
          <p class="mt-3 text-base md:text-lg text-slate-500">Выберите ваш город — мы покажем доступные МФО</p>
        </div>
        <div class="mt-10 grid gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5">
          <?php foreach ($terms as $t): ?>
            <a href="<?php echo esc_url(get_term_link($t)); ?>" class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-900 hover:border-blue-500 hover:text-blue-600 transition-all">
              <?php echo zaymi_icon('map-pin','w-4 h-4 text-slate-400'); ?> <?php echo esc_html($t->name); ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <?php return ob_get_clean();
});

/* =================================================================
 *  COMPARISON TABLE
 * ================================================================= */
add_shortcode('zaymi_comparison', function ($atts) {
    $a = shortcode_atts(['limit' => 8], $atts);
    $q = zaymi_query_mfo(['posts_per_page' => (int) $a['limit']]);
    if (!$q->have_posts()) return '';
    ob_start(); ?>
    <section class="px-6 py-20 bg-slate-50">
      <div class="mx-auto max-w-7xl">
        <div class="mx-auto max-w-2xl text-center">
          <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900">Сравнение МФО</h2>
          <p class="mt-3 text-base md:text-lg text-slate-500">Все ключевые условия в одной таблице</p>
        </div>
        <div class="mt-10 hidden lg:block overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-md">
          <table class="w-full">
            <thead><tr class="bg-slate-900 text-white">
              <th class="px-5 py-4 text-left text-xs font-extrabold uppercase tracking-wider">МФО</th>
              <th class="px-5 py-4 text-left text-xs font-extrabold uppercase tracking-wider">Сумма</th>
              <th class="px-5 py-4 text-left text-xs font-extrabold uppercase tracking-wider">Срок</th>
              <th class="px-5 py-4 text-left text-xs font-extrabold uppercase tracking-wider">Ставка</th>
              <th class="px-5 py-4 text-left text-xs font-extrabold uppercase tracking-wider">Одобрение</th>
              <th class="px-5 py-4"></th>
            </tr></thead>
            <tbody><?php $i=0; while ($q->have_posts()): $q->the_post(); $id=get_the_ID(); $i++; ?>
              <tr class="<?php echo $i%2 ? 'bg-white' : 'bg-slate-50'; ?>">
                <td class="px-5 py-4">
                  <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br <?php echo zaymi_gradient_for(get_post_field('post_name',$id)); ?> text-base font-extrabold text-white"><?php echo zaymi_letter(get_the_title()); ?></div>
                    <a href="<?php the_permalink(); ?>" class="text-base font-extrabold text-slate-900 hover:text-blue-600"><?php the_title(); ?></a>
                  </div>
                </td>
                <td class="px-5 py-4 text-sm font-bold text-slate-900">до <?php echo number_format((int)get_field('mfo_amount_max',$id),0,'',' '); ?> ₽</td>
                <td class="px-5 py-4 text-sm font-bold text-slate-900">до <?php echo zaymi_days((int)get_field('mfo_term_max',$id)); ?></td>
                <td class="px-5 py-4 text-sm font-extrabold text-emerald-600">от <?php echo esc_html(get_field('mfo_rate_min',$id)); ?>%</td>
                <td class="px-5 py-4 text-sm font-bold text-slate-900"><?php echo (int) get_field('mfo_approval_rate',$id); ?>%</td>
                <td class="px-5 py-4 text-right">
                  <a href="<?php echo esc_url(get_field('mfo_partner_url',$id) ?: get_permalink()); ?>" target="_blank" rel="nofollow noopener" class="inline-flex h-10 items-center gap-1.5 rounded-full bg-emerald-500 px-4 text-xs font-bold text-white hover:bg-emerald-600">Получить <?php echo zaymi_icon('arrow-right','w-3.5 h-3.5'); ?></a>
                </td>
              </tr>
            <?php endwhile; wp_reset_postdata(); ?></tbody>
          </table>
        </div>
      </div>
    </section>
    <?php return ob_get_clean();
});

/* =================================================================
 *  SEO HUB
 * ================================================================= */
add_shortcode('zaymi_seo_hub', function () {
    $top_mfos = zaymi_query_mfo(['posts_per_page' => 8]);
    $cols = [
        ['title'=>'По сумме','tax'=>'summa'],
        ['title'=>'По городам','tax'=>'city'],
        ['title'=>'По ситуации','tax'=>'situation'],
    ];
    ob_start(); ?>
    <section class="px-6 py-20" style="background: radial-gradient(circle at 80% 20%, rgba(16,185,129,0.18), transparent 35%), radial-gradient(circle at 10% 90%, rgba(37,99,235,0.22), transparent 40%), linear-gradient(135deg,#0f172a 0%,#133b52 100%);">
      <div class="mx-auto max-w-7xl">
        <div class="max-w-2xl">
          <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight text-white">SEO-навигация по сайту</h2>
          <p class="mt-3 text-base md:text-lg text-white/70">Полный путеводитель по нашему каталогу</p>
        </div>
        <div class="mt-12 grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
          <?php foreach ($cols as $c): $terms = get_terms(['taxonomy'=>$c['tax'],'hide_empty'=>false,'number'=>8]); ?>
            <div>
              <h3 class="text-xs font-extrabold uppercase tracking-[0.18em] text-emerald-400"><?php echo esc_html($c['title']); ?></h3>
              <ul class="mt-5 space-y-3">
                <?php foreach ((array)$terms as $t): ?>
                  <li><a href="<?php echo esc_url(get_term_link($t)); ?>" class="text-sm font-semibold text-white/80 hover:text-white"><?php echo esc_html($t->name); ?></a></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endforeach; ?>
          <div>
            <h3 class="text-xs font-extrabold uppercase tracking-[0.18em] text-emerald-400">ТОП МФО</h3>
            <ul class="mt-5 space-y-3">
              <?php while ($top_mfos->have_posts()): $top_mfos->the_post(); ?>
                <li><a href="<?php the_permalink(); ?>" class="text-sm font-semibold text-white/80 hover:text-white"><?php the_title(); ?></a></li>
              <?php endwhile; wp_reset_postdata(); ?>
            </ul>
          </div>
        </div>
      </div>
    </section>
    <?php return ob_get_clean();
});

/* =================================================================
 *  FAQ (общий)
 * ================================================================= */
add_shortcode('zaymi_faq', function () {
    $items = zaymi_opt('global_faq', []);
    if (!$items) {
        $items = [
            ['q'=>'Что такое МФО и чем они отличаются от банков?','a'=>'Микрофинансовая организация (МФО) — это компания, которая выдаёт небольшие займы на короткий срок. В отличие от банков, МФО предъявляют минимум требований к заёмщику.'],
            ['q'=>'Как получить займ онлайн с плохой кредитной историей?','a'=>'Многие МФО специализируются на работе с заёмщиками со сложной кредитной историей. Используйте фильтр «С плохой КИ» в каталоге.'],
            ['q'=>'Какие документы нужны для оформления займа?','a'=>'Достаточно паспорта гражданина РФ и СНИЛС, а также действующего номера телефона и банковской карты.'],
            ['q'=>'Как быстро придут деньги на карту?','a'=>'В среднем 5–15 минут после одобрения. На карты Сбербанка, Тинькофф и Альфа-Банка перевод обычно мгновенный.'],
            ['q'=>'Можно ли получить займ без отказа?','a'=>'100% одобрение не гарантирует никто, но есть МФО с одобрением 90–97%. Они в подборке «Без отказа».'],
            ['q'=>'Что такое первый займ под 0%?','a'=>'Спецпредложение для новых клиентов: вернёте ровно ту же сумму без процентов, если уложитесь в срок.'],
            ['q'=>'Безопасно ли брать займ онлайн?','a'=>'Да, если МФО имеет лицензию ЦБ РФ. Все организации в каталоге проверены.'],
        ];
    }
    ob_start(); ?>
    <section class="px-6 py-20">
      <div class="mx-auto max-w-3xl">
        <div class="text-center">
          <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900">Частые вопросы</h2>
          <p class="mt-3 text-base md:text-lg text-slate-500">Ответы на самые популярные вопросы о займах</p>
        </div>
        <div class="mt-10 space-y-3" data-zaymi-accordion>
          <?php foreach ($items as $i => $f): $q = $f['q'] ?? $f['question'] ?? ''; $a = $f['a'] ?? $f['answer'] ?? ''; ?>
            <div class="rounded-2xl border border-slate-200 bg-white px-6 shadow-md" data-acc-item>
              <button type="button" class="flex w-full items-center justify-between py-5 text-left text-base md:text-lg font-extrabold text-slate-900" data-acc-trigger>
                <span><?php echo esc_html($q); ?></span>
                <?php echo zaymi_icon('chevron-down','w-5 h-5 text-blue-600 transition-transform'); ?>
              </button>
              <div class="hidden pb-5 pt-1 text-sm md:text-base font-medium leading-relaxed text-slate-500" data-acc-panel><?php echo wp_kses_post(wpautop($a)); ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <?php return ob_get_clean();
});

/* =================================================================
 *  MFO-страница: HERO
 * ================================================================= */
add_shortcode('zaymi_mfo_hero', function ($atts) {
    $slug = $atts['slug'] ?? get_post_field('post_name', get_the_ID());
    $p = zaymi_get_mfo_by_slug($slug); if (!$p) return '';
    $id = $p->ID;
    $rating = (float)(get_field('mfo_rating',$id) ?: 4.5);
    $reviews = (int) get_field('mfo_reviews_count',$id);
    $amax = (int) get_field('mfo_amount_max',$id);
    $tmin = (int) get_field('mfo_term_min',$id); $tmax = (int) get_field('mfo_term_max',$id);
    $rmin = get_field('mfo_rate_min',$id); $appr = (int) get_field('mfo_approval_rate',$id);
    $logo = get_field('mfo_logo',$id); $logo_url = is_array($logo) ? $logo['url'] : zaymi_mfo_logo_url($id);
    $license = get_field('mfo_license',$id);
    $partner = get_field('mfo_partner_url',$id) ?: '#';
    $stats = [
        ['💰','Сумма', 'до '.number_format($amax,0,'',' ').' ₽'],
        ['📅','Срок',  $tmin.'—'.$tmax.' дней'],
        ['📊','Ставка','от '.$rmin.'%', true],
        ['✅','Одобрение', $appr.'%'],
    ];
    ob_start(); ?>
    <section class="px-6 pt-6">
      <div class="mx-auto max-w-7xl">
        <div class="relative overflow-hidden rounded-[28px] border border-slate-200 p-6 sm:p-10 shadow-md" style="background: radial-gradient(circle at 85% 10%, rgba(16,185,129,0.16), transparent 35%), radial-gradient(circle at 5% 90%, rgba(37,99,235,0.13), transparent 40%), linear-gradient(180deg,#f8fbff 0%,#ffffff 100%);">
          <div class="grid gap-8 lg:grid-cols-[260px_1fr] lg:gap-12">
            <div class="flex flex-col items-start gap-4">
              <?php if ($logo_url): ?>
                <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($p->post_title); ?>" class="h-[120px] w-[120px] rounded-3xl object-cover shadow-xl" />
              <?php else: ?>
                <div class="flex h-[120px] w-[120px] items-center justify-center rounded-3xl bg-gradient-to-br <?php echo zaymi_gradient_for($slug); ?> text-5xl font-extrabold text-white shadow-xl"><?php echo zaymi_letter($p->post_title); ?></div>
              <?php endif; ?>
              <div>
                <div class="flex items-center gap-2"><?php echo zaymi_stars($rating); ?>
                  <span class="text-lg font-extrabold text-slate-900"><?php echo number_format($rating,1,'.',''); ?></span>
                  <span class="text-sm font-medium text-slate-500"><?php echo number_format($reviews,0,'',' '); ?> отзывов</span>
                </div>
                <?php if ($license): ?>
                  <p class="mt-3 inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1.5 text-[11px] font-semibold text-slate-600">
                    <?php echo zaymi_icon('shield-check','w-3 h-3'); ?> Лицензия ЦБ РФ № <?php echo esc_html($license); ?>
                  </p>
                <?php endif; ?>
              </div>
            </div>
            <div>
              <span class="text-xs font-extrabold uppercase tracking-[0.18em] text-blue-600">Микрофинансовая организация</span>
              <h1 class="mt-3 font-extrabold tracking-tight text-slate-900" style="font-size:clamp(28px,4vw,44px);line-height:1.05;">
                <?php echo esc_html($p->post_title); ?> — займы онлайн до <span class="text-emerald-600"><?php echo number_format($amax,0,'',' '); ?> ₽</span>
              </h1>
              <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-3">
                <?php foreach ($stats as $s): ?>
                  <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-md">
                    <div class="text-2xl"><?php echo $s[0]; ?></div>
                    <div class="mt-2 text-[11px] font-semibold uppercase tracking-wider text-slate-500"><?php echo esc_html($s[1]); ?></div>
                    <div class="mt-1 text-base font-extrabold <?php echo !empty($s[3]) ? 'text-emerald-600' : 'text-slate-900'; ?>"><?php echo esc_html($s[2]); ?></div>
                  </div>
                <?php endforeach; ?>
              </div>
              <a href="<?php echo esc_url($partner); ?>" target="_blank" rel="nofollow noopener" class="mt-7 inline-flex h-14 items-center justify-center gap-2 rounded-full bg-emerald-500 px-7 text-base font-bold text-white shadow-md hover:bg-emerald-600">
                Получить займ в <?php echo esc_html($p->post_title); ?> <?php echo zaymi_icon('arrow-right','w-5 h-5'); ?>
              </a>
              <div class="mt-5 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm font-semibold text-slate-500">
                <span class="inline-flex items-center gap-1.5"><?php echo zaymi_icon('zap','w-4 h-4 text-amber-500'); ?> Решение за 5 минут</span>
                <span class="inline-flex items-center gap-1.5"><?php echo zaymi_icon('wallet','w-4 h-4 text-blue-600'); ?> На любую карту</span>
                <span class="inline-flex items-center gap-1.5"><?php echo zaymi_icon('phone','w-4 h-4 text-emerald-600'); ?> Без визита в офис</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <?php return ob_get_clean();
});

/* =================================================================
 *  MFO-страница: CONDITIONS, PROS_CONS, RATES, STEPS, FAQ, REVIEWS, SIMILAR, FINAL CTA
 * ================================================================= */
add_shortcode('zaymi_mfo_conditions', function ($atts) {
    $slug = $atts['slug'] ?? get_post_field('post_name', get_the_ID());
    $p = zaymi_get_mfo_by_slug($slug); if (!$p) return ''; $id = $p->ID;
    $cards = [
        ['banknote','Сумма и срок', [
            ['Сумма займа', 'от '.number_format((int)get_field('mfo_amount_min',$id),0,'',' ').' до '.number_format((int)get_field('mfo_amount_max',$id),0,'',' ').' ₽'],
            ['Срок', 'от '.zaymi_days((int)get_field('mfo_term_min',$id)).' до '.zaymi_days((int)get_field('mfo_term_max',$id))],
            ['Минимальная ставка', get_field('mfo_rate_min',$id).'% в день'],
            ['Максимальная ставка', get_field('mfo_rate_max',$id).'% в день'],
        ]],
        ['user','Требования', [
            ['Возраст','от '.(int)get_field('mfo_age_min',$id).' до '.(int)get_field('mfo_age_max',$id).' лет'],
            ['Гражданство РФ','обязательно'],
            ['Прописка','любой регион РФ'],
            ['Документы','паспорт + СНИЛС'],
        ]],
        ['wallet','Способы получения', [
            ['На карту любого банка','✓'],['На QIWI кошелёк','✓'],['Наличными в Контакт','✓'],['На счёт банка','✓'],
        ]],
        ['shield-check','Одобрение', [
            ['Процент одобрений',(int)get_field('mfo_approval_rate',$id).'%'],
            ['Время на решение','5 минут'],
            ['Решение по плохой КИ','да'],
            ['Без справок о доходах','да'],
        ]],
    ];
    ob_start(); ?>
    <section class="px-6 py-16">
      <div class="mx-auto max-w-7xl">
        <div class="mx-auto max-w-2xl text-center">
          <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900">Условия займа</h2>
          <p class="mt-3 text-base md:text-lg text-slate-500">Все детали в одном месте — никаких скрытых условий</p>
        </div>
        <div class="mt-10 grid gap-5 md:grid-cols-2">
          <?php foreach ($cards as $c): ?>
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-md">
              <div class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100 text-blue-600"><?php echo zaymi_icon($c[0],'w-5 h-5'); ?></span>
                <h3 class="text-xl font-extrabold text-slate-900"><?php echo esc_html($c[1]); ?></h3>
              </div>
              <dl class="mt-5 divide-y divide-slate-200">
                <?php foreach ($c[2] as $row): ?>
                  <div class="flex items-baseline justify-between gap-4 py-3 first:pt-0">
                    <dt class="text-sm font-semibold text-slate-500"><?php echo esc_html($row[0]); ?></dt>
                    <dd class="text-right text-sm font-extrabold text-slate-900"><?php echo esc_html($row[1]); ?></dd>
                  </div>
                <?php endforeach; ?>
              </dl>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <?php return ob_get_clean();
});

add_shortcode('zaymi_mfo_pros_cons', function ($atts) {
    $slug = $atts['slug'] ?? get_post_field('post_name', get_the_ID());
    $p = zaymi_get_mfo_by_slug($slug); if (!$p) return ''; $id = $p->ID;
    $pros = get_field('mfo_pros', $id) ?: []; $cons = get_field('mfo_cons', $id) ?: [];
    $render = function($title, $items, $tone) {
        $isG = $tone==='green';
        $bg = $isG ? 'border-emerald-300 bg-gradient-to-br from-emerald-50 to-white' : 'border-amber-300 bg-gradient-to-br from-amber-50 to-white';
        $badge = $isG ? 'bg-emerald-500' : 'bg-amber-500';
        $dot = $isG ? 'bg-emerald-500' : 'bg-amber-500';
        $icon = $isG ? 'check' : 'alert-circle';
        ob_start(); ?>
        <div class="rounded-2xl border p-7 shadow-md <?php echo $bg; ?>">
          <div class="flex items-center gap-3">
            <span class="flex h-11 w-11 items-center justify-center rounded-2xl text-white shadow-md <?php echo $badge; ?>"><?php echo zaymi_icon($icon,'w-5 h-5'); ?></span>
            <h3 class="text-xl font-extrabold text-slate-900"><?php echo esc_html($title); ?></h3>
          </div>
          <ul class="mt-5 space-y-3">
            <?php foreach ($items as $it): $text = is_array($it) ? ($it['text'] ?? '') : $it; ?>
              <li class="flex items-start gap-3"><span class="mt-1.5 h-2 w-2 shrink-0 rounded-full <?php echo $dot; ?>"></span><span class="text-base font-semibold text-slate-900"><?php echo esc_html($text); ?></span></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php return ob_get_clean();
    };
    ob_start(); ?>
    <section class="px-6 py-16">
      <div class="mx-auto max-w-7xl">
        <div class="mx-auto max-w-2xl text-center">
          <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900">Плюсы и минусы <?php echo esc_html($p->post_title); ?></h2>
          <p class="mt-3 text-base md:text-lg text-slate-500">Объективная оценка без рекламы</p>
        </div>
        <div class="mt-10 grid gap-5 md:grid-cols-2">
          <?php echo $render('✅ Плюсы '.$p->post_title, $pros, 'green'); ?>
          <?php echo $render('⚠️ Минусы '.$p->post_title, $cons, 'amber'); ?>
        </div>
      </div>
    </section>
    <?php return ob_get_clean();
});

add_shortcode('zaymi_mfo_rates', function ($atts) {
    $slug = $atts['slug'] ?? get_post_field('post_name', get_the_ID());
    $p = zaymi_get_mfo_by_slug($slug); if (!$p) return ''; $id = $p->ID;
    $rates = get_field('mfo_rates', $id) ?: [];
    if (!$rates) return '';
    ob_start(); ?>
    <section class="px-6 py-16 bg-slate-50">
      <div class="mx-auto max-w-7xl">
        <div class="mx-auto max-w-2xl text-center">
          <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900">Тарифы <?php echo esc_html($p->post_title); ?></h2>
          <p class="mt-3 text-base md:text-lg text-slate-500">Прозрачные ставки и переплата</p>
        </div>
        <div class="mt-10 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-md">
          <table class="w-full text-sm">
            <thead><tr class="bg-slate-900 text-white">
              <th class="px-6 py-4 text-left text-xs font-extrabold uppercase">Сумма</th>
              <th class="px-6 py-4 text-left text-xs font-extrabold uppercase">Срок</th>
              <th class="px-6 py-4 text-left text-xs font-extrabold uppercase">Ставка/день</th>
              <th class="px-6 py-4 text-left text-xs font-extrabold uppercase">Переплата</th>
              <th class="px-6 py-4 text-left text-xs font-extrabold uppercase">К возврату</th>
            </tr></thead>
            <tbody><?php foreach ($rates as $i => $r): ?>
              <tr class="<?php echo $i%2 ? 'bg-slate-50' : 'bg-white'; ?>">
                <td class="px-6 py-4 font-bold text-slate-900"><?php echo zaymi_money($r['amount']); ?></td>
                <td class="px-6 py-4 font-bold text-slate-900"><?php echo zaymi_days($r['term']); ?></td>
                <td class="px-6 py-4 font-extrabold text-emerald-600"><?php echo esc_html($r['rate']); ?>%</td>
                <td class="px-6 py-4 font-bold text-slate-900"><?php echo zaymi_money($r['overpayment']); ?></td>
                <td class="px-6 py-4 font-extrabold text-slate-900"><?php echo zaymi_money($r['total']); ?></td>
              </tr>
            <?php endforeach; ?></tbody>
          </table>
        </div>
      </div>
    </section>
    <?php return ob_get_clean();
});

add_shortcode('zaymi_mfo_steps', function ($atts) {
    $slug = $atts['slug'] ?? get_post_field('post_name', get_the_ID());
    $p = zaymi_get_mfo_by_slug($slug); if (!$p) return ''; $id = $p->ID;
    $steps = get_field('mfo_steps', $id) ?: [];
    if (!$steps) return '';
    $emojis = ['📝','✅','💳','⚡','💰'];
    ob_start(); ?>
    <section class="px-6 py-16">
      <div class="mx-auto max-w-7xl">
        <div class="mx-auto max-w-2xl text-center">
          <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900">Как оформить займ за <?php echo count($steps); ?> шагов</h2>
          <p class="mt-3 text-base md:text-lg text-slate-500">Весь процесс — онлайн, без визита в офис</p>
        </div>
        <ol class="mt-12 grid gap-6 lg:grid-cols-<?php echo min(5,count($steps)); ?>">
          <?php foreach ($steps as $i => $s): ?>
            <li class="relative rounded-2xl border border-slate-200 bg-white p-5 shadow-md hover:-translate-y-1 hover:shadow-xl transition-all">
              <span class="absolute -top-3 left-5 inline-flex h-7 w-7 items-center justify-center rounded-full bg-blue-600 text-xs font-extrabold text-white shadow-md"><?php echo $i+1; ?></span>
              <div class="mt-2 text-3xl"><?php echo $emojis[$i] ?? '✨'; ?></div>
              <h3 class="mt-3 text-lg font-extrabold text-slate-900"><?php echo esc_html($s['title']); ?></h3>
              <p class="mt-1 text-sm font-medium text-slate-500"><?php echo esc_html($s['description']); ?></p>
            </li>
          <?php endforeach; ?>
        </ol>
      </div>
    </section>
    <?php return ob_get_clean();
});

add_shortcode('zaymi_mfo_faq', function ($atts) {
    $slug = $atts['slug'] ?? get_post_field('post_name', get_the_ID());
    $p = zaymi_get_mfo_by_slug($slug); if (!$p) return ''; $id = $p->ID;
    $items = get_field('mfo_faq', $id) ?: [];
    if (!$items) return '';
    ob_start(); ?>
    <section class="px-6 py-16">
      <div class="mx-auto max-w-3xl">
        <div class="text-center">
          <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900">Частые вопросы о <?php echo esc_html($p->post_title); ?></h2>
        </div>
        <div class="mt-10 space-y-3" data-zaymi-accordion>
          <?php foreach ($items as $f): ?>
            <div class="rounded-2xl border border-slate-200 bg-white px-6 shadow-md" data-acc-item>
              <button type="button" class="flex w-full items-center justify-between py-5 text-left text-base md:text-lg font-extrabold text-slate-900" data-acc-trigger>
                <span><?php echo esc_html($f['question']); ?></span>
                <?php echo zaymi_icon('chevron-down','w-5 h-5 text-blue-600 transition-transform'); ?>
              </button>
              <div class="hidden pb-5 pt-1 text-sm md:text-base font-medium leading-relaxed text-slate-500" data-acc-panel><?php echo wp_kses_post(wpautop($f['answer'])); ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <?php return ob_get_clean();
});

add_shortcode('zaymi_mfo_similar', function ($atts) {
    $a = shortcode_atts(['slug'=>get_post_field('post_name', get_the_ID()), 'limit'=>3], $atts);
    $p = zaymi_get_mfo_by_slug($a['slug']); if (!$p) return '';
    $q = zaymi_query_mfo(['posts_per_page' => (int)$a['limit'], 'post__not_in' => [$p->ID]]);
    if (!$q->have_posts()) return '';
    ob_start(); ?>
    <section class="px-6 py-16 bg-slate-50">
      <div class="mx-auto max-w-7xl">
        <div class="mx-auto max-w-2xl text-center">
          <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900">Похожие МФО</h2>
          <p class="mt-3 text-base md:text-lg text-slate-500">Если <?php echo esc_html($p->post_title); ?> не подошёл — посмотрите эти варианты</p>
        </div>
        <div class="mt-10 grid gap-5 md:grid-cols-3">
          <?php while ($q->have_posts()): $q->the_post(); $id=get_the_ID();
            $rating = (float)get_field('mfo_rating',$id); $reviews = (int)get_field('mfo_reviews_count',$id);
            $amax = (int)get_field('mfo_amount_max',$id); $rmin = get_field('mfo_rate_min',$id);
            $logo = get_field('mfo_logo',$id); $logo_url = is_array($logo) ? $logo['url'] : zaymi_mfo_logo_url($id);
            $tagline = get_field('mfo_tagline',$id);
          ?>
            <div class="flex flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-md hover:-translate-y-1 hover:shadow-xl transition-all">
              <div class="flex items-start justify-between">
                <?php if ($logo_url): ?>
                  <img src="<?php echo esc_url($logo_url); ?>" class="h-12 w-12 rounded-2xl object-cover shadow-md" alt="<?php echo esc_attr(get_the_title()); ?>" />
                <?php else: ?>
                  <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br <?php echo zaymi_gradient_for(get_post_field('post_name',$id)); ?> text-xl font-extrabold text-white shadow-md"><?php echo zaymi_letter(get_the_title()); ?></div>
                <?php endif; ?>
                <div class="text-right">
                  <div class="flex items-center gap-1"><?php echo zaymi_icon('star','w-3.5 h-3.5 text-amber-500'); ?><span class="text-sm font-extrabold text-slate-900"><?php echo number_format($rating,1,'.',''); ?></span></div>
                  <div class="text-[11px] font-medium text-slate-500"><?php echo number_format($reviews,0,'',' '); ?> отзывов</div>
                </div>
              </div>
              <h3 class="mt-3 text-lg font-extrabold text-slate-900"><a href="<?php the_permalink(); ?>" class="hover:text-blue-600"><?php the_title(); ?></a></h3>
              <?php if ($tagline): ?>
                <span class="mt-2 inline-flex w-fit rounded-full bg-amber-100 px-2.5 py-1 text-[11px] font-bold text-amber-800 ring-1 ring-amber-300"><?php echo esc_html($tagline); ?></span>
              <?php endif; ?>
              <div class="mt-4 grid grid-cols-2 gap-2 rounded-xl bg-slate-50 p-3 text-sm">
                <div><div class="text-[11px] font-semibold text-slate-500">Сумма</div><div class="font-extrabold text-slate-900">до <?php echo number_format($amax,0,'',' '); ?> ₽</div></div>
                <div><div class="text-[11px] font-semibold text-slate-500">Ставка</div><div class="font-extrabold text-emerald-600">от <?php echo esc_html($rmin); ?>%</div></div>
              </div>
              <a href="<?php the_permalink(); ?>" class="mt-4 flex h-11 w-full items-center justify-center gap-2 rounded-full bg-emerald-500 text-sm font-bold text-white hover:bg-emerald-600">Подробнее <?php echo zaymi_icon('arrow-right','w-4 h-4'); ?></a>
            </div>
          <?php endwhile; wp_reset_postdata(); ?>
        </div>
      </div>
    </section>
    <?php return ob_get_clean();
});

add_shortcode('zaymi_mfo_reviews', function ($atts) {
    $slug = $atts['slug'] ?? get_post_field('post_name', get_the_ID());
    $p = zaymi_get_mfo_by_slug($slug); if (!$p) return ''; $id = $p->ID;
    $rating = (float)(get_field('mfo_rating',$id) ?: 4.5);
    $reviews_count = (int) get_field('mfo_reviews_count',$id);
    $reviews = [
        ['Анна К.','АК',5,'27 апреля '.date('Y'),'Брала первый займ — всё прозрачно. Деньги пришли мгновенно, никаких скрытых комиссий.'],
        ['Дмитрий В.','ДВ',4,'22 апреля '.date('Y'),'Использую уже больше года. Главный плюс — быстрое одобрение даже когда другие отказывают.'],
        ['Светлана М.','СМ',5,'18 апреля '.date('Y'),'После закрытия в банке мне здесь одобрили. Сайт удобный, всё через телефон.'],
    ];
    ob_start(); ?>
    <section class="px-6 py-16">
      <div class="mx-auto max-w-7xl">
        <div class="mx-auto max-w-2xl text-center">
          <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900">Отзывы клиентов <?php echo esc_html($p->post_title); ?></h2>
        </div>
        <div class="mt-10 grid gap-6 rounded-2xl border border-slate-200 bg-white p-7 md:p-9 shadow-md md:grid-cols-[260px_1fr]">
          <div class="flex flex-col items-center justify-center border-b border-slate-200 pb-6 md:border-b-0 md:border-r md:pb-0 md:pr-6">
            <div class="text-6xl font-extrabold tracking-tight text-slate-900"><?php echo number_format($rating,1,'.',''); ?></div>
            <div class="mt-2"><?php echo zaymi_stars($rating); ?></div>
            <div class="mt-2 text-sm font-semibold text-slate-500"><?php echo number_format($reviews_count,0,'',' '); ?> отзывов</div>
          </div>
          <div class="grid gap-3 sm:grid-cols-2">
            <?php foreach ([['Одобрение',5],['Скорость',5],['Условия',4],['Поддержка',5],['Надёжность',5]] as $cat): ?>
              <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                <span class="text-sm font-bold text-slate-900"><?php echo esc_html($cat[0]); ?></span>
                <?php echo zaymi_stars($cat[1]); ?>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="mt-8 grid gap-5 md:grid-cols-3">
          <?php foreach ($reviews as $r): ?>
            <article class="flex flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-md">
              <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-full bg-gradient-to-br from-blue-600 to-emerald-500 text-sm font-extrabold text-white"><?php echo esc_html($r[1]); ?></div>
                <div>
                  <div class="text-sm font-extrabold text-slate-900"><?php echo esc_html($r[0]); ?></div>
                  <?php echo zaymi_stars($r[2]); ?>
                </div>
              </div>
              <div class="mt-3 text-xs font-semibold text-slate-500"><?php echo esc_html($r[3]); ?></div>
              <p class="mt-3 text-sm font-medium leading-relaxed text-slate-900"><?php echo esc_html($r[4]); ?></p>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <?php return ob_get_clean();
});

add_shortcode('zaymi_final_cta', function ($atts) {
    $a = shortcode_atts([
        'title'  => 'Готовы получить займ?',
        'text'   => 'Заполните заявку прямо сейчас и получите деньги за 5–15 минут',
        'button' => 'Оформить займ',
        'url'    => '#',
    ], $atts);
    ob_start(); ?>
    <section class="px-6 py-16">
      <div class="mx-auto max-w-7xl">
        <div class="relative overflow-hidden rounded-[28px] p-8 sm:p-14 text-center shadow-xl" style="background: radial-gradient(circle at 85% 20%, rgba(255,255,255,0.18), transparent 40%), radial-gradient(circle at 10% 90%, rgba(37,99,235,0.35), transparent 45%), linear-gradient(135deg,#10b981 0%,#059669 50%,#0f766e 100%);">
          <h2 class="mx-auto max-w-2xl text-3xl md:text-4xl font-extrabold leading-tight text-white"><?php echo esc_html($a['title']); ?></h2>
          <p class="mx-auto mt-3 max-w-xl text-base md:text-lg text-white/90"><?php echo esc_html($a['text']); ?></p>
          <a href="<?php echo esc_url($a['url']); ?>" class="mt-8 inline-flex h-14 items-center justify-center gap-2 rounded-full bg-white px-8 text-base font-extrabold text-emerald-600 shadow-xl hover:scale-[1.02] transition-all"><?php echo esc_html($a['button']); ?> <?php echo zaymi_icon('arrow-right','w-5 h-5'); ?></a>
          <div class="mt-7 flex flex-wrap items-center justify-center gap-x-6 gap-y-3 text-sm font-bold text-white">
            <span class="inline-flex items-center gap-1.5"><?php echo zaymi_icon('shield-check','w-4 h-4'); ?> Безопасно</span>
            <span class="inline-flex items-center gap-1.5"><?php echo zaymi_icon('zap','w-4 h-4'); ?> За 5 минут</span>
            <span class="inline-flex items-center gap-1.5"><?php echo zaymi_icon('file-text','w-4 h-4'); ?> Без справок</span>
          </div>
        </div>
      </div>
    </section>
    <?php return ob_get_clean();
});

/* Алиас на одиночную карточку МФО (для блога/виджетов) */
add_shortcode('zaymi_mfo_card', function ($atts) {
    $a = shortcode_atts(['slug' => ''], $atts);
    if (!$a['slug']) return '';
    return do_shortcode('[zaymi_mfo_catalog limit="1" title="" subtitle=""]'); // упрощённо: один МФО лучше через одиночную карточку
});

/* Блок последних статей */
add_shortcode('zaymi_blog_section', function ($atts) {
    $a = shortcode_atts(['limit' => 3], $atts);
    $q = new WP_Query(['post_type'=>'post','posts_per_page'=>(int)$a['limit']]);
    if (!$q->have_posts()) return '';
    ob_start(); ?>
    <section class="px-6 py-20 bg-slate-50">
      <div class="mx-auto max-w-7xl">
        <div class="mx-auto max-w-2xl text-center">
          <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900">Свежие статьи блога</h2>
          <p class="mt-3 text-base md:text-lg text-slate-500">Гайды, советы и обзоры по займам</p>
        </div>
        <div class="mt-10 grid gap-5 md:grid-cols-3">
          <?php while ($q->have_posts()): $q->the_post(); ?>
            <article class="flex flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:-translate-y-1 hover:shadow-xl transition-all">
              <h3 class="text-lg font-extrabold text-slate-900"><a href="<?php the_permalink(); ?>" class="hover:text-blue-600"><?php the_title(); ?></a></h3>
              <p class="mt-2 text-sm font-medium leading-relaxed text-slate-500"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 22)); ?></p>
              <a href="<?php the_permalink(); ?>" class="mt-4 inline-flex items-center gap-1.5 text-sm font-bold text-emerald-600 hover:text-emerald-700">Читать <?php echo zaymi_icon('arrow-right','w-4 h-4'); ?></a>
            </article>
          <?php endwhile; wp_reset_postdata(); ?>
        </div>
      </div>
    </section>
    <?php return ob_get_clean();
});

/* Шорткод "Как это работает" для главной */
add_shortcode('zaymi_how_it_works', function () {
    $steps = [
        ['📝','Выберите МФО','Сравните условия в каталоге'],
        ['✅','Заполните заявку','Паспорт + карта — 5 минут'],
        ['💰','Получите деньги','Перевод на карту мгновенно'],
    ];
    ob_start(); ?>
    <section class="px-6 py-20">
      <div class="mx-auto max-w-7xl">
        <div class="mx-auto max-w-2xl text-center">
          <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900">Как это работает</h2>
          <p class="mt-3 text-base md:text-lg text-slate-500">Получите займ за 3 простых шага</p>
        </div>
        <ol class="mt-12 grid gap-6 md:grid-cols-3">
          <?php foreach ($steps as $i => $s): ?>
            <li class="rounded-2xl border border-slate-200 bg-white p-7 shadow-md text-center">
              <div class="text-5xl"><?php echo $s[0]; ?></div>
              <div class="mt-4 inline-flex h-7 w-7 items-center justify-center rounded-full bg-blue-600 text-xs font-extrabold text-white"><?php echo $i+1; ?></div>
              <h3 class="mt-3 text-xl font-extrabold text-slate-900"><?php echo esc_html($s[1]); ?></h3>
              <p class="mt-1 text-sm font-medium text-slate-500"><?php echo esc_html($s[2]); ?></p>
            </li>
          <?php endforeach; ?>
        </ol>
      </div>
    </section>
    <?php return ob_get_clean();
});
