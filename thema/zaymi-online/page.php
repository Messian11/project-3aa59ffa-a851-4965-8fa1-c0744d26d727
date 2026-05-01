<?php
/** Универсальный шаблон страницы (выводит контент с обработкой шорткодов). */
if (!defined('ABSPATH')) exit;
get_header(); ?>
<article class="px-6 py-12">
  <div class="mx-auto max-w-5xl">
    <?php while (have_posts()): the_post(); ?>
      <?php $content = get_the_content(); ?>
      <?php if (!has_shortcode($content, 'zaymi_hero') && !has_shortcode($content, 'zaymi_mfo_catalog')): ?>
        <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900 mb-6"><?php the_title(); ?></h1>
      <?php endif; ?>
      <div class="prose prose-lg max-w-none prose-headings:font-extrabold prose-headings:text-slate-900 prose-a:text-blue-600">
        <?php the_content(); ?>
      </div>
    <?php endwhile; ?>
  </div>
</article>
<?php get_footer();
