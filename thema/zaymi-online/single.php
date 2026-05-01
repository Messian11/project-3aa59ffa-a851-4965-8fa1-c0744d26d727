<?php
/** Одиночная статья блога. */
if (!defined('ABSPATH')) exit;
get_header(); ?>
<article class="px-6 py-12">
  <div class="mx-auto max-w-3xl">
    <?php while (have_posts()): the_post(); ?>
      <span class="text-xs font-extrabold uppercase tracking-[0.18em] text-emerald-600">Блог</span>
      <h1 class="mt-3 text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900"><?php the_title(); ?></h1>
      <div class="mt-3 text-sm text-slate-500"><?php echo get_the_date('j F Y'); ?></div>
      <?php if (has_post_thumbnail()): ?>
        <div class="mt-8 overflow-hidden rounded-2xl"><?php the_post_thumbnail('blog-thumb','class=w-full h-auto'); ?></div>
      <?php endif; ?>
      <div class="mt-8 prose prose-lg max-w-none prose-a:text-blue-600">
        <?php the_content(); ?>
      </div>
    <?php endwhile; ?>
  </div>
</article>
<?php echo do_shortcode('[zaymi_mfo_catalog limit="4" title="Подберите подходящий займ" subtitle="Топ МФО из нашего каталога"]'); ?>
<?php get_footer();
