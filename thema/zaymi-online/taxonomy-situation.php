<?php
/**
 * Универсальный шаблон таксономий хабов: situation, summa, city.
 */
if (!defined('ABSPATH')) exit;
get_header();
$term = get_queried_object();
$tax  = $term->taxonomy ?? '';
$h1       = function_exists('get_field') ? get_field('hub_h1', $term) : '';
$intro    = function_exists('get_field') ? get_field('hub_intro', $term) : '';
$seo_text = function_exists('get_field') ? get_field('hub_seo_text', $term) : '';
$hub_faq  = function_exists('get_field') ? get_field('hub_faq', $term) : [];

$labels = ['city' => 'Город', 'summa' => 'Сумма', 'situation' => 'Подборка'];
$label  = $labels[$tax] ?? 'Подборка';
?>

<section class="px-6 pt-12 pb-8 bg-gradient-to-br from-slate-50 to-emerald-50/30">
  <div class="mx-auto max-w-7xl">
    <span class="text-xs font-extrabold uppercase tracking-[0.18em] text-blue-600"><?php echo esc_html($label); ?></span>
    <h1 class="mt-3 text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900">
      <?php echo esc_html($h1 ?: $term->name); ?>
    </h1>
    <?php if ($intro): ?>
      <div class="mt-4 max-w-3xl text-base md:text-lg text-slate-600 leading-relaxed"><?php echo wp_kses_post($intro); ?></div>
    <?php elseif ($term->description): ?>
      <p class="mt-4 max-w-3xl text-base md:text-lg text-slate-600 leading-relaxed"><?php echo esc_html($term->description); ?></p>
    <?php endif; ?>

    <!-- Бэйджи преимуществ подборки -->
    <div class="mt-6 flex flex-wrap gap-2">
      <?php foreach ([
        ['shield-check','Проверенные МФО'],
        ['zap','Решение за 5 минут'],
        ['percent','Низкие ставки'],
        ['wallet','На карту 24/7'],
      ] as $b): ?>
        <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white/80 px-3 py-1.5 text-xs font-bold text-slate-700">
          <span class="text-emerald-600"><?php echo zaymi_icon($b[0],'w-4 h-4'); ?></span>
          <?php echo esc_html($b[1]); ?>
        </span>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php echo do_shortcode('[zaymi_mfo_filter limit="50" '.$tax.'="'.esc_attr($term->slug).'" title="" subtitle="Сравните условия и выберите лучший займ"]'); ?>

<?php echo do_shortcode('[zaymi_comparison limit="10"]'); ?>

<?php if ($seo_text): ?>
<section class="px-6 py-16 bg-white">
  <div class="mx-auto max-w-3xl prose prose-lg prose-headings:font-extrabold prose-headings:text-slate-900 prose-a:text-blue-600">
    <?php echo wp_kses_post($seo_text); ?>
  </div>
</section>
<?php endif; ?>

<?php if (!empty($hub_faq)): ?>
<section class="px-6 py-16 bg-slate-50">
  <div class="mx-auto max-w-3xl">
    <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900 text-center">Частые вопросы</h2>
    <div class="mt-8 space-y-3" data-zaymi-accordion>
      <?php foreach ($hub_faq as $i => $item): ?>
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm" data-acc-item>
          <button class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left text-base font-bold text-slate-900" data-acc-trigger>
            <span><?php echo esc_html($item['question'] ?? ''); ?></span>
            <span class="text-blue-600"><?php echo zaymi_icon('chevron-down','w-5 h-5'); ?></span>
          </button>
          <div class="hidden px-5 pb-5 text-sm text-slate-600 leading-relaxed" data-acc-panel>
            <?php echo wp_kses_post($item['answer'] ?? ''); ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php else: ?>
<?php echo do_shortcode('[zaymi_faq]'); ?>
<?php endif; ?>

<?php
// Перекрёстные ссылки — другие подборки/суммы/города
echo do_shortcode('[zaymi_internal_links]');
echo do_shortcode('[zaymi_lsi]');
?>

<?php get_footer();
