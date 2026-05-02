<?php
/**
 * Шаблон таксономии "Города" (city) — 1:1 с React /goroda/{slug}.
 */
if (!defined('ABSPATH')) exit;

$term = get_queried_object();
$name = $term->name ?? 'Город';

$acf_intro = function_exists('get_field') ? get_field('hub_intro', $term) : '';
$acf_seo   = function_exists('get_field') ? get_field('hub_seo_text', $term) : '';
$acf_faq   = function_exists('get_field') ? get_field('hub_faq', $term) : [];

$default_faq = [
    ['question' => 'Работают ли эти МФО в городе ' . esc_html($name) . '?',          'answer' => 'Да, все МФО подборки выдают займы по всей России, включая ' . esc_html($name) . '. Прописка значения не имеет — главное гражданство РФ.'],
    ['question' => 'На карту какого банка можно получить деньги?',                    'answer' => 'На карту любого российского банка: Сбербанк, ВТБ, Тинькофф, Альфа-Банк, Райффайзен, Газпромбанк и других.'],
    ['question' => 'Нужно ли идти в офис?',                                            'answer' => 'Нет. Все МФО подборки работают полностью онлайн — от подачи заявки до получения денег.'],
    ['question' => 'Какие документы нужны?',                                           'answer' => 'Только паспорт РФ и активная банковская карта на ваше имя. Никаких справок о доходах.'],
];
$faq_items = !empty($acf_faq) ? $acf_faq : $default_faq;
$GLOBALS['zaymi_tax_faq'] = $faq_items;

get_header();

$why = [
    ['icon' => 'building-2',   'title' => 'Работают в ' . esc_html($name), 'desc' => 'Все МФО подборки официально работают в вашем городе и выдают деньги на карту любого местного банка.'],
    ['icon' => 'zap',          'title' => 'Деньги за 5 минут',             'desc' => 'Онлайн-оформление 24/7 без посещения офиса. Зачисление на карту мгновенно.'],
    ['icon' => 'shield-check', 'title' => 'Лицензия ЦБ РФ',                'desc' => 'Все компании внесены в реестр Центробанка и работают по федеральному закону №353-ФЗ.'],
    ['icon' => 'check-circle', 'title' => '95% одобрений',                  'desc' => 'Высокий процент одобрений для жителей города ' . esc_html($name) . ' с любой кредитной историей.'],
];

$related_terms = get_terms(['taxonomy' => 'city', 'hide_empty' => false, 'exclude' => [$term->term_id], 'number' => 12]);
?>

<nav class="border-b border-slate-200 bg-white px-6 py-3" aria-label="Хлебные крошки">
  <ol class="mx-auto flex max-w-7xl items-center gap-1.5 text-xs font-medium text-slate-500">
    <li><a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-blue-600">Главная</a></li>
    <li><span class="text-slate-300">›</span></li>
    <li>Города</li>
    <li><span class="text-slate-300">›</span></li>
    <li class="font-semibold text-slate-900"><?php echo esc_html($name); ?></li>
  </ol>
</nav>

<!-- Hero -->
<section class="relative overflow-hidden px-6 py-16 md:py-20"
  style="background: radial-gradient(circle at 80% 15%, rgba(37,99,235,.14), transparent 45%), radial-gradient(circle at 10% 90%, rgba(16,185,129,.10), transparent 45%), linear-gradient(180deg, #eff6ff 0%, #fff 100%);">
  <div class="mx-auto max-w-7xl">
    <span class="inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-[0.18em] text-blue-600">
      <?php echo zaymi_icon('map-pin','w-4 h-4'); ?> Город
    </span>
    <h1 class="mt-4 max-w-3xl text-4xl font-extrabold tracking-tight text-slate-900 md:text-5xl">
      Займы онлайн в городе <span class="text-blue-600"><?php echo esc_html($name); ?></span>
    </h1>
    <p class="mt-4 max-w-2xl text-base text-slate-600 md:text-lg">
      <?php echo $acf_intro ? wp_kses_post($acf_intro) : 'Подборка МФО, которые работают в вашем городе. Деньги на карту любого банка за 5 минут, без посещения офиса. Доступно для жителей ' . esc_html($name) . ' и области.'; ?>
    </p>
    <div class="mt-8 flex flex-wrap gap-2 md:gap-3">
      <span class="inline-flex items-center gap-2 rounded-full bg-white/95 border border-slate-200 px-4 py-2 text-sm font-bold text-slate-900 shadow-card"><span class="text-emerald-600"><?php echo zaymi_icon('check-circle','w-4 h-4'); ?></span> Работают в <?php echo esc_html($name); ?></span>
      <span class="inline-flex items-center gap-2 rounded-full bg-white/95 border border-slate-200 px-4 py-2 text-sm font-bold text-slate-900 shadow-card"><span class="text-blue-600"><?php echo zaymi_icon('shield-check','w-4 h-4'); ?></span> Лицензия ЦБ РФ</span>
      <span class="inline-flex items-center gap-2 rounded-full bg-white/95 border border-slate-200 px-4 py-2 text-sm font-bold text-slate-900 shadow-card"><span class="text-amber-600"><?php echo zaymi_icon('zap','w-4 h-4'); ?></span> 24/7 онлайн</span>
      <span class="inline-flex items-center gap-2 rounded-full bg-white/95 border border-slate-200 px-4 py-2 text-sm font-bold text-slate-900 shadow-card"><span class="text-red-600"><?php echo zaymi_icon('file-x','w-4 h-4'); ?></span> Без справок</span>
    </div>
  </div>
</section>

<!-- Почему -->
<section class="px-6 py-16 md:py-20">
  <div class="mx-auto max-w-7xl">
    <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 md:text-4xl">Почему именно эти МФО подходят жителям <?php echo esc_html($name); ?></h2>
    <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
      <?php foreach ($why as $r): ?>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card transition-all hover:-translate-y-1 hover:shadow-hover">
          <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-500/10 text-blue-600 ring-1 ring-inset ring-blue-500/20"><?php echo zaymi_icon($r['icon'],'w-6 h-6'); ?></div>
          <h3 class="mt-4 text-lg font-extrabold text-slate-900"><?php echo esc_html($r['title']); ?></h3>
          <p class="mt-1.5 text-sm leading-relaxed text-slate-500"><?php echo wp_kses_post($r['desc']); ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Каталог МФО для города -->
<section class="bg-slate-50 px-6 py-16 md:py-20">
  <div class="mx-auto max-w-7xl">
    <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 md:text-4xl">МФО, которые работают в <?php echo esc_html($name); ?></h2>
    <p class="mt-3 max-w-2xl text-base text-slate-500 md:text-lg">Сравните условия и выберите лучший займ.</p>
  </div>
  <div class="mx-auto mt-10 max-w-7xl">
    <?php echo do_shortcode('[zaymi_mfo_filter limit="40" city="' . esc_attr($term->slug) . '" title="" subtitle=""]'); ?>
  </div>
</section>

<?php echo do_shortcode('[zaymi_comparison limit="10"]'); ?>

<!-- Другие города -->
<?php if (!empty($related_terms) && !is_wp_error($related_terms)): ?>
<section class="px-6 py-16">
  <div class="mx-auto max-w-7xl">
    <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 md:text-4xl">Займы в других городах</h2>
    <div class="mt-8 grid grid-cols-2 gap-3 md:grid-cols-4 lg:grid-cols-6">
      <?php foreach ($related_terms as $t): ?>
        <a href="<?php echo esc_url(get_term_link($t)); ?>" class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-900 shadow-card transition-all hover:-translate-y-0.5 hover:border-blue-500 hover:text-blue-600">
          <span class="text-blue-500"><?php echo zaymi_icon('map-pin','w-4 h-4'); ?></span>
          <?php echo esc_html($t->name); ?>
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
    <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 md:text-4xl">Займы в городе <?php echo esc_html($name); ?> — что нужно знать</h2>
    <p class="mt-5 text-base leading-relaxed text-slate-600 md:text-lg">Все микрофинансовые организации из подборки официально работают по всей территории России, включая город <?php echo esc_html($name); ?>. Это означает, что вы можете оформить займ онлайн, не выходя из дома, и получить деньги на карту любого российского банка за 5–10 минут.</p>
    <h3 class="mt-10 text-2xl font-extrabold text-slate-900">Преимущества онлайн-займа в <?php echo esc_html($name); ?></h3>
    <ul class="mt-4 space-y-2 text-slate-600">
      <li class="flex gap-2"><span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-blue-500"></span><span>Не нужно ехать в офис — всё оформляется через интернет.</span></li>
      <li class="flex gap-2"><span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-blue-500"></span><span>Работают 24/7, включая выходные и ночное время.</span></li>
      <li class="flex gap-2"><span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-blue-500"></span><span>Деньги поступают на карту мгновенно после одобрения.</span></li>
      <li class="flex gap-2"><span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-blue-500"></span><span>Минимум документов — только паспорт и карта.</span></li>
    </ul>
  </article>
</section>
<?php endif; ?>

<!-- FAQ -->
<section class="bg-slate-50 px-6 py-16 md:py-20">
  <div class="mx-auto max-w-3xl">
    <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 text-center md:text-4xl">Частые вопросы про займы в <?php echo esc_html($name); ?></h2>
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
