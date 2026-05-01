<?php
/** Архив блога. */
if (!defined('ABSPATH')) exit;
get_header(); ?>
<section class="px-6 pt-12 pb-6">
  <div class="mx-auto max-w-7xl text-center">
    <span class="text-xs font-extrabold uppercase tracking-[0.18em] text-emerald-600">Блог</span>
    <h1 class="mt-3 text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900">Полезные статьи о займах</h1>
  </div>
</section>
<section class="px-6 py-12">
  <div class="mx-auto max-w-7xl grid gap-5 md:grid-cols-3">
    <?php if (have_posts()): while (have_posts()): the_post(); ?>
      <article class="flex flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:-translate-y-1 hover:shadow-xl transition-all">
        <div class="text-xs font-semibold text-slate-500"><?php echo get_the_date('j F Y'); ?></div>
        <h3 class="mt-2 text-lg font-extrabold text-slate-900"><a href="<?php the_permalink(); ?>" class="hover:text-blue-600"><?php the_title(); ?></a></h3>
        <p class="mt-2 text-sm font-medium leading-relaxed text-slate-500"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 24)); ?></p>
        <a href="<?php the_permalink(); ?>" class="mt-4 inline-flex items-center gap-1.5 text-sm font-bold text-emerald-600">Читать <?php echo zaymi_icon('arrow-right','w-4 h-4'); ?></a>
      </article>
    <?php endwhile; else: ?>
      <p class="text-slate-500">Записей пока нет.</p>
    <?php endif; ?>
  </div>
</section>
<?php get_footer();
