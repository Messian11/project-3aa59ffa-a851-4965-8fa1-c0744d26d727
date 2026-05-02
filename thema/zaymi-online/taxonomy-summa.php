<?php
/**
 * Шаблон таксономии "По сумме" (summa) — 1:1 с React /summa/{slug}.
 */
if (!defined('ABSPATH')) exit;

$term = get_queried_object();
$slug = $term->slug ?? '';
$name = $term->name ?? 'Займ';

/* Извлекаем сумму из slug (5000-rub, 50000, 100000) */
$amount = (int) preg_replace('/\D+/', '', $slug);
if (!$amount) $amount = 5000;
$amount_fmt = number_format($amount, 0, '.', ' ') . ' ₽';

$acf_intro = function_exists('get_field') ? get_field('hub_intro', $term) : '';
$acf_seo   = function_exists('get_field') ? get_field('hub_seo_text', $term) : '';
$acf_faq   = function_exists('get_field') ? get_field('hub_faq', $term) : [];

$default_faq = [
    ['question' => 'Точно ли одобрят ' . esc_html($amount_fmt) . '?', 'answer' => 'Эта сумма входит в стандартный лимит большинства МФО. Реальное одобрение — 90%+ при условии корректных данных в анкете.'],
    ['question' => 'Какая будет переплата?',                          'answer' => 'При первом займе под 0% — переплата 0 ₽ при возврате в срок. Со второго займа — стандартная ставка от 0,8% до 1% в день.'],
    ['question' => 'Как быстро придут деньги?',                       'answer' => 'На карту любого банка России — мгновенно (1–5 минут после одобрения), 24/7.'],
    ['question' => 'Можно ли продлить срок?',                         'answer' => 'Да, у большинства МФО есть платная пролонгация на 7–30 дней. Стоимость и условия зависят от компании.'],
];
$faq_items = !empty($acf_faq) ? $acf_faq : $default_faq;
$GLOBALS['zaymi_tax_faq'] = $faq_items;

get_header();

$why = [
    ['icon' => 'check-circle', 'title' => 'Высокое одобрение',  'desc' => 'Сумма ' . esc_html($amount_fmt) . ' одобряется в 90%+ случаев — это базовый лимит для большинства МФО.'],
    ['icon' => 'zap',          'title' => 'Решение за 5 минут', 'desc' => 'Автоматический скоринг — деньги поступают на карту мгновенно после одобрения.'],
    ['icon' => 'percent',      'title' => 'Первый займ под 0%', 'desc' => 'Большинство МФО предлагают первый займ под 0% при возврате в срок до 30 дней.'],
    ['icon' => 'shield-check', 'title' => 'Без справок и поручителей', 'desc' => 'Только паспорт и активная карта на ваше имя. Никаких 2-НДФЛ.'],
];

$related_terms = get_terms(['taxonomy' => 'summa', 'hide_empty' => false, 'exclude' => [$term->term_id], 'number' => 8]);
?>

<nav class="border-b border-slate-200 bg-white px-6 py-3" aria-label="Хлебные крошки">
  <ol class="mx-auto flex max-w-7xl items-center gap-1.5 text-xs font-medium text-slate-500">
    <li><a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-blue-600">Главная</a></li>
    <li><span class="text-slate-300">›</span></li>
    <li>По сумме</li>
    <li><span class="text-slate-300">›</span></li>
    <li class="font-semibold text-slate-900"><?php echo esc_html($name); ?></li>
  </ol>
</nav>

<!-- Hero -->
<section class="relative overflow-hidden px-6 py-16 md:py-20"
  style="background: radial-gradient(circle at 90% 10%, rgba(16,185,129,.15), transparent 40%), radial-gradient(circle at 5% 90%, rgba(37,99,235,.10), transparent 45%), linear-gradient(180deg, #f8fafc 0%, #fff 100%);">
  <div class="mx-auto max-w-7xl">
    <span class="inline-block text-xs font-extrabold uppercase tracking-[0.18em] text-emerald-600">По сумме</span>
    <h1 class="mt-4 max-w-3xl text-4xl font-extrabold tracking-tight text-slate-900 md:text-5xl">
      Займ <span class="text-emerald-600"><?php echo esc_html($amount_fmt); ?></span> на карту
    </h1>
    <p class="mt-4 max-w-2xl text-base text-slate-600 md:text-lg">
      <?php echo $acf_intro ? wp_kses_post($acf_intro) : 'Подборка МФО, которые гарантированно выдают сумму ' . esc_html($amount_fmt) . '. Решение за 5 минут, на карту любого банка 24/7. Первый займ — под 0%.'; ?>
    </p>
    <div class="mt-8 flex flex-wrap gap-2 md:gap-3">
      <span class="inline-flex items-center gap-2 rounded-full bg-white/95 border border-slate-200 px-4 py-2 text-sm font-bold text-slate-900 shadow-card"><span class="text-emerald-600"><?php echo zaymi_icon('check-circle','w-4 h-4'); ?></span> Одобрение 95%</span>
      <span class="inline-flex items-center gap-2 rounded-full bg-white/95 border border-slate-200 px-4 py-2 text-sm font-bold text-slate-900 shadow-card"><span class="text-amber-600"><?php echo zaymi_icon('zap','w-4 h-4'); ?></span> Решение 5 мин</span>
      <span class="inline-flex items-center gap-2 rounded-full bg-white/95 border border-slate-200 px-4 py-2 text-sm font-bold text-slate-900 shadow-card"><span class="text-blue-600"><?php echo zaymi_icon('percent','w-4 h-4'); ?></span> Первый под 0%</span>
      <span class="inline-flex items-center gap-2 rounded-full bg-white/95 border border-slate-200 px-4 py-2 text-sm font-bold text-slate-900 shadow-card"><span class="text-red-600"><?php echo zaymi_icon('file-x','w-4 h-4'); ?></span> Без справок</span>
    </div>
  </div>
</section>

<!-- Почему -->
<section class="px-6 py-16 md:py-20">
  <div class="mx-auto max-w-7xl">
    <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 md:text-4xl">Почему именно эти МФО выдают <?php echo esc_html($amount_fmt); ?>?</h2>
    <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
      <?php foreach ($why as $r): ?>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card transition-all hover:-translate-y-1 hover:shadow-hover">
          <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-500/10 text-emerald-600 ring-1 ring-inset ring-emerald-500/20"><?php echo zaymi_icon($r['icon'],'w-6 h-6'); ?></div>
          <h3 class="mt-4 text-lg font-extrabold text-slate-900"><?php echo esc_html($r['title']); ?></h3>
          <p class="mt-1.5 text-sm leading-relaxed text-slate-500"><?php echo wp_kses_post($r['desc']); ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Каталог -->
<section class="bg-slate-50 px-6 py-16 md:py-20">
  <div class="mx-auto max-w-7xl">
    <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 md:text-4xl">МФО, выдающие <?php echo esc_html($amount_fmt); ?></h2>
    <p class="mt-3 max-w-2xl text-base text-slate-500 md:text-lg">Сравните условия и выберите лучший займ.</p>
  </div>
  <div class="mx-auto mt-10 max-w-7xl">
    <?php echo do_shortcode('[zaymi_mfo_filter limit="40" summa="' . esc_attr($term->slug) . '" title="" subtitle=""]'); ?>
  </div>
</section>

<!-- Сравнение -->
<?php echo do_shortcode('[zaymi_comparison limit="10"]'); ?>

<!-- Связанные суммы -->
<?php if (!empty($related_terms) && !is_wp_error($related_terms)): ?>
<section class="px-6 py-16">
  <div class="mx-auto max-w-7xl">
    <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 md:text-4xl">Другие популярные суммы</h2>
    <div class="mt-8 grid grid-cols-2 gap-3 md:grid-cols-4 lg:grid-cols-6">
      <?php foreach ($related_terms as $t): ?>
        <a href="<?php echo esc_url(get_term_link($t)); ?>" class="rounded-2xl border border-slate-200 bg-white px-4 py-5 text-center shadow-card transition-all hover:-translate-y-1 hover:border-emerald-500 hover:shadow-hover">
          <div class="text-lg font-extrabold text-slate-900"><?php echo esc_html($t->name); ?></div>
          <div class="mt-1 text-xs font-semibold text-slate-500">подобрать</div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- SEO -->
<?php if ($acf_seo): ?>
<section class="px-6 py-20 bg-white"><article class="mx-auto max-w-4xl prose prose-lg prose-headings:font-extrabold prose-a:text-blue-600"><?php echo wp_kses_post($acf_seo); ?></article></section>
<?php else: ?>
<section class="px-6 py-20 bg-white">
  <article class="mx-auto max-w-4xl">
    <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 md:text-4xl">Как получить займ <?php echo esc_html($amount_fmt); ?></h2>
    <p class="mt-5 text-base leading-relaxed text-slate-600 md:text-lg">Сумма <?php echo esc_html($amount_fmt); ?> — самая популярная среди заёмщиков. Её одобряют практически все МФО даже клиентам без идеальной кредитной истории. Главное — корректно заполнить анкету и иметь активную банковскую карту на своё имя.</p>
    <h3 class="mt-10 text-2xl font-extrabold text-slate-900">Условия получения</h3>
    <ul class="mt-4 space-y-2 text-slate-600">
      <li class="flex gap-2"><span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-emerald-500"></span><span>Возраст от 18 до 70 лет, гражданство РФ.</span></li>
      <li class="flex gap-2"><span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-emerald-500"></span><span>Действующий паспорт и банковская карта.</span></li>
      <li class="flex gap-2"><span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-emerald-500"></span><span>Постоянный источник дохода (необязательно официальный).</span></li>
    </ul>
  </article>
</section>
<?php endif; ?>

<!-- FAQ -->
<section class="bg-slate-50 px-6 py-16 md:py-20">
  <div class="mx-auto max-w-3xl">
    <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 text-center md:text-4xl">Частые вопросы про займ <?php echo esc_html($amount_fmt); ?></h2>
    <div class="mt-8 space-y-3" data-zaymi-accordion>
      <?php foreach ($faq_items as $item): ?>
        <div class="rounded-2xl border border-slate-200 bg-white shadow-card" data-acc-item>
          <button class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left text-base font-bold text-slate-900" data-acc-trigger>
            <span><?php echo esc_html($item['question']); ?></span>
            <span class="text-blue-600"><?php echo zaymi_icon('chevron-down','w-5 h-5'); ?></span>
          </button>
          <div class="hidden px-5 pb-5 text-sm text-slate-600 leading-relaxed" data-acc-panel><?php echo wp_kses_post($item['answer']); ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php
echo do_shortcode('[zaymi_internal_links]');
echo do_shortcode('[zaymi_lsi]');
get_footer();
