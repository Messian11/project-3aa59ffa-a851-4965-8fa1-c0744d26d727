<?php
/**
 * Шаблон одиночной страницы МФО.
 * Если пользователь сам поставил шорткоды в контент — рендерим контент,
 * иначе автоматически собираем стандартный набор блоков.
 */
if (!defined('ABSPATH')) exit;
get_header();
while (have_posts()): the_post(); $slug = get_post_field('post_name', get_the_ID()); ?>
  <?php echo do_shortcode('[zaymi_mfo_hero slug="'.esc_attr($slug).'"]'); ?>

  <?php $content = trim(get_the_content()); if ($content): ?>
    <article class="px-6 py-12">
      <div class="mx-auto max-w-7xl prose prose-lg max-w-none"><?php the_content(); ?></div>
    </article>
  <?php else: ?>
    <?php
      echo do_shortcode('[zaymi_mfo_conditions slug="'.esc_attr($slug).'"]');
      echo do_shortcode('[zaymi_mfo_pros_cons slug="'.esc_attr($slug).'"]');
      echo do_shortcode('[zaymi_mfo_rates slug="'.esc_attr($slug).'"]');
      echo do_shortcode('[zaymi_mfo_steps slug="'.esc_attr($slug).'"]');
      echo do_shortcode('[zaymi_mfo_reviews slug="'.esc_attr($slug).'"]');
      echo do_shortcode('[zaymi_mfo_faq slug="'.esc_attr($slug).'"]');
      $partner = get_field('mfo_partner_url', get_the_ID()) ?: '#';
      echo do_shortcode('[zaymi_final_cta title="Готовы получить займ в '.esc_attr(get_the_title()).'?" button="Перейти на сайт МФО" url="'.esc_attr($partner).'"]');
      echo do_shortcode('[zaymi_mfo_similar slug="'.esc_attr($slug).'" limit="3"]');
    ?>
  <?php endif; ?>
<?php endwhile;
get_footer();
