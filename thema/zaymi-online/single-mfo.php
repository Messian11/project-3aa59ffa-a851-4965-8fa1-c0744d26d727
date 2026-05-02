<?php
/**
 * Шаблон одиночной страницы МФО.
 * Структура 1-в-1 с превью /mfo/$slug.tsx:
 *  Breadcrumbs → Hero → Conditions → Fit → Steps → ProsCons →
 *  Rates → Reviews → FAQ → Similar → FinalCta → SeoContent.
 */
if (!defined('ABSPATH')) exit;
get_header();
while (have_posts()): the_post(); $slug = get_post_field('post_name', get_the_ID()); ?>

  <?php echo do_shortcode('[zaymi_mfo_breadcrumbs slug="'.esc_attr($slug).'"]'); ?>
  <?php echo do_shortcode('[zaymi_mfo_hero slug="'.esc_attr($slug).'"]'); ?>
  <?php echo do_shortcode('[zaymi_mfo_conditions slug="'.esc_attr($slug).'"]'); ?>
  <?php echo do_shortcode('[zaymi_mfo_fit slug="'.esc_attr($slug).'"]'); ?>
  <?php echo do_shortcode('[zaymi_mfo_steps slug="'.esc_attr($slug).'"]'); ?>
  <?php echo do_shortcode('[zaymi_mfo_pros_cons slug="'.esc_attr($slug).'"]'); ?>
  <?php echo do_shortcode('[zaymi_mfo_rates slug="'.esc_attr($slug).'"]'); ?>
  <?php echo do_shortcode('[zaymi_mfo_reviews slug="'.esc_attr($slug).'"]'); ?>
  <?php echo do_shortcode('[zaymi_mfo_faq slug="'.esc_attr($slug).'"]'); ?>
  <?php echo do_shortcode('[zaymi_mfo_similar slug="'.esc_attr($slug).'" limit="3"]'); ?>

  <?php
    $partner = function_exists('get_field') ? (get_field('mfo_partner_url', get_the_ID()) ?: '#') : '#';
    echo do_shortcode('[zaymi_final_cta title="Готовы получить займ в '.esc_attr(get_the_title()).'?" button="Перейти на сайт МФО" url="'.esc_attr($partner).'"]');
  ?>

  <?php echo do_shortcode('[zaymi_mfo_seo_content slug="'.esc_attr($slug).'"]'); ?>

  <!-- Внутренняя перелинковка: города / суммы / подборки -->
  <?php echo do_shortcode('[zaymi_internal_links]'); ?>
  <?php echo do_shortcode('[zaymi_lsi]'); ?>

<?php endwhile;
get_footer();
