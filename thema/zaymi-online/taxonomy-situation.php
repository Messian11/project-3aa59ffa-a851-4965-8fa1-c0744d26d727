<?php
/**
 * Универсальный шаблон таксономий хабов: situation, summa, city.
 */
if (!defined('ABSPATH')) exit;
get_header();
$term = get_queried_object();
$tax  = $term->taxonomy ?? '';
$h1    = function_exists('get_field') ? get_field('hub_h1', $term) : '';
$intro = function_exists('get_field') ? get_field('hub_intro', $term) : '';
?>

<section class="px-6 pt-12 pb-6 bg-gradient-to-br from-slate-50 to-emerald-50/30">
  <div class="mx-auto max-w-7xl">
    <span class="text-xs font-extrabold uppercase tracking-[0.18em] text-blue-600">
      <?php echo $tax === 'city' ? 'Город' : ($tax === 'summa' ? 'Сумма' : 'Подборка'); ?>
    </span>
    <h1 class="mt-3 text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900">
      <?php echo esc_html($h1 ?: $term->name); ?>
    </h1>
    <?php if ($intro): ?>
      <div class="mt-4 max-w-3xl text-base md:text-lg text-slate-600 leading-relaxed"><?php echo wp_kses_post($intro); ?></div>
    <?php elseif ($term->description): ?>
      <p class="mt-4 max-w-3xl text-base md:text-lg text-slate-600 leading-relaxed"><?php echo esc_html($term->description); ?></p>
    <?php endif; ?>
  </div>
</section>

<?php echo do_shortcode('[zaymi_mfo_catalog limit="20" '.$tax.'="'.esc_attr($term->slug).'" title="" subtitle=""]'); ?>
<?php echo do_shortcode('[zaymi_comparison limit="8"]'); ?>
<?php echo do_shortcode('[zaymi_faq]'); ?>

<?php get_footer();
