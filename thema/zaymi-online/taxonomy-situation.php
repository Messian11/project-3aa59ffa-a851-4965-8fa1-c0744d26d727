<?php
/**
 * Шаблон таксономии "Подборки" (situation) — 1:1 с React-превью /situations/{slug}.
 * Богатый контент с уникальными hero, чипсами, секциями "Почему" и "Что делать если отказали".
 */
if (!defined('ABSPATH')) exit;

$term = get_queried_object();
$slug = $term->slug ?? 'bez-otkaza';

/* ============================================================
 * Конфиги по slug — текст 1:1 с React /situations/{slug}.
 * Если в WP завели термин с другим slug — берётся дефолт.
 * ============================================================ */
$configs = [
    'bez-otkaza' => [
        'crumb'       => 'Займы без отказа',
        'eyebrow'     => 'Подборка МФО',
        'h1_main'     => 'Займы онлайн',
        'h1_accent'   => 'без отказа',
        'accent_color'=> '#dc2626',
        'subtitle'    => '24 МФО с самым высоким процентом одобрений. Деньги получат даже клиенты с плохой кредитной историей.',
        'chips' => [
            ['icon' => 'ban',           'label' => '95% одобрений', 'color' => '#dc2626'],
            ['icon' => 'check-circle',  'label' => 'С плохой КИ',    'color' => '#10b981'],
            ['icon' => 'zap',           'label' => 'Решение 5 мин',  'color' => '#b45309'],
            ['icon' => 'file-x',        'label' => 'Без справок',    'color' => '#2563eb'],
        ],
        'why_title'   => 'Почему именно эти МФО одобряют без отказа?',
        'cat_title'   => 'МФО с самым высоким одобрением',
        'cat_subtitle'=> 'Только компании с показателем одобрения 90%+ за 2025 год.',
        'refusal_title' => 'Что делать, если всё-таки отказали?',
        'seo_h2'      => 'Займы без отказа — что важно знать',
        'faq_title'   => 'Частые вопросы про займы без отказа',
    ],
    's-plohoy-ki' => [
        'crumb' => 'Займы с плохой КИ', 'eyebrow' => 'Подборка МФО',
        'h1_main' => 'Займы', 'h1_accent' => 'с плохой кредитной историей', 'accent_color' => '#dc2626',
        'subtitle' => '21 МФО, которые работают с заёмщиками с просрочками, отказами банков и испорченной КИ. Скоринг по текущей платёжеспособности, а не по прошлому.',
        'chips' => [
            ['icon' => 'shield-check', 'label' => 'Без проверки БКИ', 'color' => '#2563eb'],
            ['icon' => 'check-circle', 'label' => '92% одобрений',     'color' => '#10b981'],
            ['icon' => 'zap',          'label' => 'Решение 3 мин',     'color' => '#b45309'],
            ['icon' => 'file-x',       'label' => 'Любая КИ',          'color' => '#dc2626'],
        ],
        'why_title' => 'Почему эти МФО выдают займ с плохой КИ?',
        'cat_title' => 'МФО, лояльные к плохой кредитной истории',
        'cat_subtitle' => 'Компании, которые не делают жёсткий запрос в БКИ при первом обращении.',
        'refusal_title' => 'Что делать, если КИ совсем плохая?',
        'seo_h2' => 'Займы с плохой КИ — как и где получить',
        'faq_title' => 'Частые вопросы про займы с плохой кредитной историей',
    ],
    'pensioneram' => [
        'crumb' => 'Займы пенсионерам', 'eyebrow' => 'Подборка МФО',
        'h1_main' => 'Займы онлайн', 'h1_accent' => 'пенсионерам', 'accent_color' => '#2563eb',
        'subtitle' => '18 МФО, которые охотно работают с пенсионерами до 75 лет. Пенсия принимается как основной источник дохода — справки 2-НДФЛ не нужны.',
        'chips' => [
            ['icon' => 'shield-check', 'label' => 'Возраст до 75',  'color' => '#2563eb'],
            ['icon' => 'check-circle', 'label' => 'Пенсия = доход', 'color' => '#10b981'],
            ['icon' => 'zap',          'label' => 'Решение 5 мин',  'color' => '#b45309'],
            ['icon' => 'file-x',       'label' => 'Без 2-НДФЛ',     'color' => '#dc2626'],
        ],
        'why_title' => 'Почему эти МФО выдают займ пенсионерам?',
        'cat_title' => 'МФО для пенсионеров',
        'cat_subtitle' => 'Компании, для которых пенсия — полноценное подтверждение дохода.',
        'refusal_title' => 'Что делать, если отказали пенсионеру',
        'seo_h2' => 'Займы пенсионерам — особенности и условия',
        'faq_title' => 'Частые вопросы пенсионеров о займах',
    ],
    'studentam' => [
        'crumb' => 'Займы студентам', 'eyebrow' => 'Подборка МФО',
        'h1_main' => 'Займы онлайн', 'h1_accent' => 'студентам', 'accent_color' => '#10b981',
        'subtitle' => '15 МФО, которые выдают займы студентам от 18 лет. Без официальной работы, по паспорту и студенческому билету.',
        'chips' => [
            ['icon' => 'shield-check', 'label' => 'От 18 лет',      'color' => '#2563eb'],
            ['icon' => 'check-circle', 'label' => 'Без работы',     'color' => '#10b981'],
            ['icon' => 'zap',          'label' => 'Решение 5 мин',  'color' => '#b45309'],
            ['icon' => 'file-x',       'label' => 'Только паспорт', 'color' => '#dc2626'],
        ],
        'why_title' => 'Почему эти МФО выдают займ студентам?',
        'cat_title' => 'МФО для студентов',
        'cat_subtitle' => 'Лояльные компании, готовые работать с молодыми заёмщиками без КИ.',
        'refusal_title' => 'Что делать, если студенту отказали',
        'seo_h2' => 'Займы студентам — что важно знать',
        'faq_title' => 'Частые вопросы студентов о займах',
    ],
    'bezrabotnym' => [
        'crumb' => 'Займы безработным', 'eyebrow' => 'Подборка МФО',
        'h1_main' => 'Займы онлайн', 'h1_accent' => 'безработным', 'accent_color' => '#dc2626',
        'subtitle' => '16 МФО, которые выдают займы без официального трудоустройства. Принимают любой источник дохода — фриланс, подработку, пособие.',
        'chips' => [
            ['icon' => 'shield-check', 'label' => 'Без работы',    'color' => '#2563eb'],
            ['icon' => 'check-circle', 'label' => '90% одобрений', 'color' => '#10b981'],
            ['icon' => 'zap',          'label' => 'Решение 5 мин', 'color' => '#b45309'],
            ['icon' => 'file-x',       'label' => 'Без справок',   'color' => '#dc2626'],
        ],
        'why_title' => 'Почему эти МФО выдают займ безработным?',
        'cat_title' => 'МФО для безработных',
        'cat_subtitle' => 'Не требуют справку с работы и трудовую книжку.',
        'refusal_title' => 'Что делать безработному при отказе',
        'seo_h2' => 'Займы безработным — где взять и как получить',
        'faq_title' => 'Частые вопросы безработных о займах',
    ],
    'bez-spravok' => [
        'crumb' => 'Займы без справок', 'eyebrow' => 'Подборка МФО',
        'h1_main' => 'Займы онлайн', 'h1_accent' => 'без справок', 'accent_color' => '#2563eb',
        'subtitle' => '26 МФО, которые выдают займы только по паспорту. Без 2-НДФЛ, копий трудовой и поручителей — полностью онлайн.',
        'chips' => [
            ['icon' => 'file-x',       'label' => 'Только паспорт',  'color' => '#2563eb'],
            ['icon' => 'check-circle', 'label' => '94% одобрений',   'color' => '#10b981'],
            ['icon' => 'zap',          'label' => 'Решение 5 мин',   'color' => '#b45309'],
            ['icon' => 'shield-check', 'label' => 'Лицензия ЦБ РФ',  'color' => '#dc2626'],
        ],
        'why_title' => 'Почему эти МФО не требуют справки?',
        'cat_title' => 'МФО, выдающие займы без справок',
        'cat_subtitle' => 'Полная онлайн-проверка, никаких бумажных документов.',
        'refusal_title' => 'Что делать, если отказали без справок',
        'seo_h2' => 'Займы без справок — как это работает',
        'faq_title' => 'Частые вопросы про займы без справок',
    ],
    'srochno' => [
        'crumb' => 'Срочные займы', 'eyebrow' => 'Подборка МФО',
        'h1_main' => 'Займы онлайн', 'h1_accent' => 'срочно за 5 минут', 'accent_color' => '#b45309',
        'subtitle' => '19 МФО с самым быстрым решением и моментальным зачислением на карту 24/7. От заявки до денег — 5–10 минут.',
        'chips' => [
            ['icon' => 'zap',          'label' => '5 минут на всё', 'color' => '#b45309'],
            ['icon' => 'check-circle', 'label' => '93% одобрений',  'color' => '#10b981'],
            ['icon' => 'shield-check', 'label' => 'Карта 24/7',     'color' => '#2563eb'],
            ['icon' => 'file-x',       'label' => 'Без справок',    'color' => '#dc2626'],
        ],
        'why_title' => 'Почему эти МФО выдают деньги срочно?',
        'cat_title' => 'МФО с моментальным решением',
        'cat_subtitle' => 'Автоматический скоринг и зачисление в любое время суток.',
        'refusal_title' => 'Что делать, если деньги нужны срочно, а отказали?',
        'seo_h2' => 'Срочные займы — как получить деньги за 5 минут',
        'faq_title' => 'Частые вопросы про срочные займы',
    ],
];

$cfg = $configs[$slug] ?? $configs['bez-otkaza'];

/* Можно переопределить из ACF — если поля заполнены, они приоритетнее */
$acf_h1     = function_exists('get_field') ? get_field('hub_h1', $term) : '';
$acf_intro  = function_exists('get_field') ? get_field('hub_intro', $term) : '';
$acf_seo    = function_exists('get_field') ? get_field('hub_seo_text', $term) : '';
$acf_faq    = function_exists('get_field') ? get_field('hub_faq', $term) : [];

$why_reasons = [
    ['icon' => 'trending-up', 'title' => 'Высокий процент одобрений', 'desc' => 'В подборке только МФО, у которых одобрение 90% и выше — по реальной статистике за 2025 год.'],
    ['icon' => 'cpu',         'title' => 'Гибкий скоринг',            'desc' => 'Эти компании используют собственные алгоритмы оценки и не делают жёстких запросов в БКИ.'],
    ['icon' => 'shield-check','title' => 'Лояльны к КИ',              'desc' => 'Работают с заёмщиками, у которых были просрочки, отказы банков и испорченная кредитная история.'],
    ['icon' => 'sparkles',    'title' => 'Минимум требований',        'desc' => 'Только паспорт РФ и активная карта. Без справок 2-НДФЛ, поручителей и подтверждения дохода.'],
];

$refusal_advice = [
    ['icon' => 'refresh',      'title' => 'Подайте сразу в 3–5 МФО',         'desc' => 'Алгоритмы скоринга у всех разные. То, что отказала одна, не значит, что откажут все. При параллельной подаче в 5 компаний вероятность одобрения близка к 100%.'],
    ['icon' => 'list-checks',  'title' => 'Проверьте корректность данных',    'desc' => 'Опечатка в номере паспорта, неправильный СНИЛС или устаревший адрес — самые частые причины автоматического отказа. Перепроверьте всё перед подачей.'],
    ['icon' => 'wallet',       'title' => 'Уменьшите сумму займа',           'desc' => 'Если просили 30 000 ₽ — попробуйте 5 000–10 000 ₽. Малые суммы одобряют чаще, а после успешного погашения у вас откроется больший лимит.'],
    ['icon' => 'phone-call',   'title' => 'Позвоните в службу поддержки',     'desc' => 'У многих МФО можно уточнить причину отказа и попросить ручную проверку. Часто решение пересматривают в вашу пользу.'],
    ['icon' => 'check-circle', 'title' => 'Улучшите кредитный рейтинг',      'desc' => 'Закройте мелкие кредитки, оплатите старые долги, оформите карту рассрочки и пользуйтесь ей. Через 2–3 месяца ситуация заметно улучшится.'],
];

$default_faq = [
    ['question' => 'Существуют ли МФО со 100% одобрением?',                  'answer' => 'По закону ни одна МФО не может гарантировать 100% одобрение — это запрещено. Но реальный показатель одобрений у компаний из нашей подборки достигает 95–96%, что близко к гарантированному выдаче.'],
    ['question' => 'Дадут ли мне займ с открытыми просрочками?',             'answer' => 'С активными просрочками шансы ниже, но не нулевые. Лайм-Займ, ДоброЗайм и Webbankir работают даже с такими заёмщиками.'],
    ['question' => 'Берут ли эти МФО плату за рассмотрение?',                'answer' => 'Нет. Все МФО из подборки работают без комиссий за подачу заявки и рассмотрение. Если у вас просят деньги «за одобрение» — это мошенники.'],
    ['question' => 'Как быстро придёт ответ?',                                'answer' => 'Решение приходит автоматически за 1–5 минут. В редких случаях заявка отправляется на ручную проверку.'],
    ['question' => 'Что делать, если все МФО подряд отказывают?',            'answer' => 'Это сигнал, что проблема в ваших данных или КИ. Проверьте свою кредитную историю бесплатно через Госуслуги, исправьте ошибки и попробуйте снова через 30 дней.'],
    ['question' => 'Можно ли оформить займ без работы?',                      'answer' => 'Да, у большинства МФО из подборки официальная занятость не обязательна. Главное — указать стабильный источник дохода: пенсию, стипендию, фриланс или подработку.'],
];
$faq_items = !empty($acf_faq) ? $acf_faq : $default_faq;
$GLOBALS['zaymi_tax_faq'] = $faq_items;

/* Связанные подборки */
$related_terms = get_terms(['taxonomy' => 'situation', 'hide_empty' => false, 'exclude' => [$term->term_id], 'number' => 6]);

get_header();
?>

<!-- Хлебные крошки -->
<nav class="border-b border-slate-200 bg-white px-6 py-3" aria-label="Хлебные крошки">
  <ol class="mx-auto flex max-w-7xl items-center gap-1.5 text-xs font-medium text-slate-500">
    <li><a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-blue-600">Главная</a></li>
    <li><span class="text-slate-300">›</span></li>
    <li><a href="<?php echo esc_url(get_post_type_archive_link('mfo')); ?>" class="hover:text-blue-600">Подборки</a></li>
    <li><span class="text-slate-300">›</span></li>
    <li class="font-semibold text-slate-900"><?php echo esc_html($cfg['crumb']); ?></li>
  </ol>
</nav>

<!-- Hero -->
<section class="relative overflow-hidden px-6 py-16 md:py-20"
  style="background: radial-gradient(circle at 85% 15%, rgba(245,158,11,0.20), transparent 45%), radial-gradient(circle at 5% 90%, rgba(239,68,68,0.10), transparent 50%), linear-gradient(135deg, #fff7ed 0%, #fff1f2 100%);">
  <div class="mx-auto max-w-7xl">
    <div class="inline-flex items-center gap-2 rounded-full bg-white/90 px-3 py-1.5 text-xs font-extrabold uppercase tracking-[0.18em] text-amber-700 shadow-card backdrop-blur">
      <?php echo zaymi_icon('sparkles','w-3.5 h-3.5'); ?> <?php echo esc_html($cfg['eyebrow']); ?>
    </div>
    <h1 class="mt-4 max-w-3xl text-4xl font-extrabold tracking-tight text-slate-900 md:text-5xl">
      <?php if ($acf_h1): ?>
        <?php echo esc_html($acf_h1); ?>
      <?php else: ?>
        <?php echo esc_html($cfg['h1_main']); ?> <span style="color: <?php echo esc_attr($cfg['accent_color']); ?>"><?php echo esc_html($cfg['h1_accent']); ?></span>
      <?php endif; ?>
    </h1>
    <p class="mt-4 max-w-2xl text-base text-slate-700 md:text-lg">
      <?php echo $acf_intro ? wp_kses_post($acf_intro) : esc_html($cfg['subtitle']); ?>
    </p>
    <div class="mt-8 flex flex-wrap gap-2 md:gap-3">
      <?php foreach ($cfg['chips'] as $chip): ?>
        <span class="inline-flex items-center gap-2 rounded-full border border-white/60 bg-white/95 px-4 py-2 text-sm font-bold text-slate-900 shadow-card backdrop-blur">
          <span style="color: <?php echo esc_attr($chip['color']); ?>"><?php echo zaymi_icon($chip['icon'], 'w-4 h-4'); ?></span>
          <?php echo esc_html($chip['label']); ?>
        </span>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Почему именно эти МФО -->
<section class="px-6 py-16 md:py-20">
  <div class="mx-auto max-w-7xl">
    <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 md:text-4xl"><?php echo esc_html($cfg['why_title']); ?></h2>
    <p class="mt-3 max-w-2xl text-base text-slate-500 md:text-lg">Мы отбираем компании по 4 объективным критериям, а не по рекламным обещаниям.</p>
    <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
      <?php foreach ($why_reasons as $r): ?>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card transition-all hover:-translate-y-1 hover:shadow-hover">
          <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-500/10 text-emerald-600 ring-1 ring-inset ring-emerald-500/20">
            <?php echo zaymi_icon($r['icon'], 'w-6 h-6'); ?>
          </div>
          <h3 class="mt-4 text-lg font-extrabold text-slate-900"><?php echo esc_html($r['title']); ?></h3>
          <p class="mt-1.5 text-sm leading-relaxed text-slate-500"><?php echo esc_html($r['desc']); ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Каталог МФО (через шорткод фильтра) -->
<section class="bg-slate-50 px-6 py-16 md:py-20">
  <div class="mx-auto max-w-7xl">
    <div class="flex items-end justify-between gap-4">
      <div>
        <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 md:text-4xl"><?php echo esc_html($cfg['cat_title']); ?></h2>
        <p class="mt-3 max-w-2xl text-base text-slate-500 md:text-lg"><?php echo esc_html($cfg['cat_subtitle']); ?></p>
      </div>
      <a href="<?php echo esc_url(get_post_type_archive_link('mfo')); ?>" class="hidden text-sm font-bold text-blue-600 hover:underline md:inline">Все МФО →</a>
    </div>
  </div>
  <div class="mx-auto mt-10 max-w-7xl">
    <?php echo do_shortcode('[zaymi_mfo_filter limit="40" situation="' . esc_attr($term->slug) . '" title="" subtitle=""]'); ?>
  </div>
</section>

<!-- Что делать, если отказали -->
<section class="px-6 py-16 md:py-20">
  <div class="mx-auto max-w-5xl">
    <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 md:text-4xl"><?php echo esc_html($cfg['refusal_title']); ?></h2>
    <p class="mt-3 max-w-2xl text-base text-slate-500 md:text-lg">5 рабочих советов, которые повышают шансы на одобрение в разы.</p>
    <div class="mt-10 space-y-4">
      <?php foreach ($refusal_advice as $i => $a): ?>
        <div class="grid gap-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-card transition-all hover:shadow-hover md:grid-cols-[64px_56px_1fr] md:items-center md:p-7">
          <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-900 text-2xl font-black text-white shadow-card"><?php echo $i + 1; ?></div>
          <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-500/10 text-blue-600"><?php echo zaymi_icon($a['icon'], 'w-6 h-6'); ?></div>
          <div>
            <h3 class="text-lg font-extrabold text-slate-900"><?php echo esc_html($a['title']); ?></h3>
            <p class="mt-1.5 text-sm leading-relaxed text-slate-500"><?php echo esc_html($a['desc']); ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="mt-10 rounded-2xl border border-slate-200 bg-slate-50 p-6 text-sm leading-relaxed text-slate-900 md:p-7">
      <b>Совет:</b> воспользуйтесь нашим <a href="<?php echo esc_url(home_url('/')); ?>" class="font-bold text-blue-600 hover:underline">подбором займа</a> — одна заявка отправится сразу в 5 МФО с высоким процентом одобрения.
    </div>
  </div>
</section>

<!-- Похожие подборки -->
<?php if (!empty($related_terms) && !is_wp_error($related_terms)): ?>
<section class="bg-slate-50 px-6 py-16 md:py-20">
  <div class="mx-auto max-w-7xl">
    <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 md:text-4xl">Похожие подборки</h2>
    <p class="mt-3 max-w-2xl text-base text-slate-500 md:text-lg">Возможно, вам подойдёт одна из этих специализированных подборок МФО.</p>
    <div class="mt-8 flex flex-wrap gap-3">
      <?php foreach ($related_terms as $t): ?>
        <a href="<?php echo esc_url(get_term_link($t)); ?>" class="group inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-extrabold text-slate-900 shadow-card transition-all hover:-translate-y-0.5 hover:border-emerald-500 hover:text-emerald-600">
          <?php echo esc_html($t->name); ?>
          <?php echo zaymi_icon('arrow-right', 'w-4 h-4 transition-transform group-hover:translate-x-0.5'); ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- SEO-текст -->
<?php if ($acf_seo): ?>
<section class="px-6 py-20">
  <article class="mx-auto max-w-4xl prose prose-lg prose-headings:font-extrabold prose-headings:text-slate-900 prose-a:text-blue-600">
    <?php echo wp_kses_post($acf_seo); ?>
  </article>
</section>
<?php else: ?>
<section class="px-6 py-20">
  <article class="mx-auto max-w-4xl">
    <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 md:text-4xl"><?php echo esc_html($cfg['seo_h2']); ?></h2>
    <p class="mt-5 text-base leading-relaxed text-slate-600 md:text-lg">
      Подборка «<?php echo esc_html($cfg['crumb']); ?>» собрана из проверенных МФО с действующей лицензией ЦБ РФ. Все компании работают полностью онлайн, выдают деньги на карту любого банка, минимум требований к заёмщику.
    </p>
    <h3 class="mt-10 text-2xl font-extrabold text-slate-900">Кому подходят такие займы</h3>
    <ul class="mt-4 space-y-2 text-slate-600">
      <li class="flex gap-2"><span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-emerald-500"></span><span>Возраст от 18 лет (некоторые МФО — от 21).</span></li>
      <li class="flex gap-2"><span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-emerald-500"></span><span>Гражданство РФ и действующий паспорт.</span></li>
      <li class="flex gap-2"><span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-emerald-500"></span><span>Активная банковская карта на ваше имя.</span></li>
      <li class="flex gap-2"><span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-emerald-500"></span><span>Российский номер телефона.</span></li>
    </ul>
    <h3 class="mt-10 text-2xl font-extrabold text-slate-900">Как получить займ</h3>
    <p class="mt-4 leading-relaxed text-slate-600">Выберите подходящую МФО из списка выше, нажмите «Получить займ», заполните анкету за 3 минуты. Решение приходит автоматически в течение 5 минут, деньги поступают на карту мгновенно — 24/7, включая выходные и праздники.</p>
  </article>
</section>
<?php endif; ?>

<!-- FAQ -->
<section class="bg-slate-50 px-6 py-16 md:py-20">
  <div class="mx-auto max-w-3xl">
    <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 text-center md:text-4xl"><?php echo esc_html($cfg['faq_title']); ?></h2>
    <div class="mt-8 space-y-3" data-zaymi-accordion>
      <?php foreach ($faq_items as $item): ?>
        <div class="rounded-2xl border border-slate-200 bg-white shadow-card" data-acc-item>
          <button class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left text-base font-bold text-slate-900" data-acc-trigger>
            <span><?php echo esc_html($item['question']); ?></span>
            <span class="text-blue-600"><?php echo zaymi_icon('chevron-down', 'w-5 h-5'); ?></span>
          </button>
          <div class="hidden px-5 pb-5 text-sm text-slate-600 leading-relaxed" data-acc-panel>
            <?php echo wp_kses_post($item['answer']); ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Перекрёстные ссылки + LSI -->
<?php
echo do_shortcode('[zaymi_internal_links]');
echo do_shortcode('[zaymi_lsi]');

get_footer();
