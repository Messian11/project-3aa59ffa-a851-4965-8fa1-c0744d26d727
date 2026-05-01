<?php
/**
 * Архив каталога МФО.
 */
if (!defined('ABSPATH')) exit;
get_header(); ?>

<section class="px-6 pt-12 pb-6">
  <div class="mx-auto max-w-7xl text-center">
    <span class="text-xs font-extrabold uppercase tracking-[0.18em] text-emerald-600">Каталог</span>
    <h1 class="mt-3 text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900">Все МФО России</h1>
    <p class="mt-3 text-base md:text-lg text-slate-500 max-w-2xl mx-auto">Сравните условия 50+ микрофинансовых организаций с лицензией ЦБ РФ — отсортированы по нашему рейтингу.</p>
  </div>
</section>

<?php echo do_shortcode('[zaymi_mfo_catalog limit="50" title="" subtitle=""]'); ?>
<?php echo do_shortcode('[zaymi_comparison limit="12"]'); ?>
<?php echo do_shortcode('[zaymi_faq]'); ?>

<?php get_footer();
