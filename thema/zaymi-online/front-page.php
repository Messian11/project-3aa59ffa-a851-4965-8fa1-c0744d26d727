<?php
/**
 * Главная страница (если пользователь не выбрал статическую страницу).
 */
if (!defined('ABSPATH')) exit;
get_header();
echo do_shortcode('[zaymi_hero]');
echo do_shortcode('[zaymi_social_proof]');
echo do_shortcode('[zaymi_situations_grid]');
echo do_shortcode('[zaymi_mfo_catalog limit="8"]');
echo do_shortcode('[zaymi_amounts_grid]');
echo do_shortcode('[zaymi_comparison limit="8"]');
echo do_shortcode('[zaymi_how_it_works]');
echo do_shortcode('[zaymi_blog_section]');
echo do_shortcode('[zaymi_faq]');
echo do_shortcode('[zaymi_seo_hub]');
get_footer();
