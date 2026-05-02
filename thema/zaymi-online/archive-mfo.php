<?php
/**
 * Архив каталога МФО.
 */
if (!defined('ABSPATH')) exit;
get_header(); ?>

<section class="px-6 pt-12 pb-6 bg-gradient-to-br from-slate-50 to-emerald-50/30">
  <div class="mx-auto max-w-7xl text-center">
    <span class="text-xs font-extrabold uppercase tracking-[0.18em] text-emerald-600">Каталог 2026</span>
    <h1 class="mt-3 text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900">Все МФО России</h1>
    <p class="mt-3 text-base md:text-lg text-slate-500 max-w-2xl mx-auto">Сравните условия 50+ микрофинансовых организаций с лицензией ЦБ РФ — отфильтруйте по сумме, сроку и особенностям.</p>
  </div>
</section>

<?php echo do_shortcode('[zaymi_mfo_filter limit="60" title="" subtitle="Используйте фильтры слева, чтобы найти идеальное предложение"]'); ?>
<?php echo do_shortcode('[zaymi_comparison limit="12"]'); ?>
<?php echo do_shortcode('[zaymi_amounts_grid]'); ?>
<?php echo do_shortcode('[zaymi_cities_grid]'); ?>
<?php echo do_shortcode('[zaymi_faq]'); ?>
<?php echo do_shortcode('[zaymi_lsi]'); ?>

<?php get_footer();
