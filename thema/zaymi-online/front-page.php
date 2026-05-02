<?php
/**
 * Главная страница (front-page).
 * Структура 1-в-1 с превью credit-bridge-design /index.tsx.
 */
if (!defined('ABSPATH')) exit;
get_header();
echo do_shortcode('[zaymi_hero]');
echo do_shortcode('[zaymi_social_proof]');
echo do_shortcode('[zaymi_mfo_catalog limit="8"]');
echo do_shortcode('[zaymi_situations_grid]');
echo do_shortcode('[zaymi_amounts_grid]');
echo do_shortcode('[zaymi_comparison limit="8"]');
echo do_shortcode('[zaymi_how_it_works]');
echo do_shortcode('[zaymi_blog_section]');
echo do_shortcode('[zaymi_seo_hub]');
echo do_shortcode('[zaymi_faq]');
echo do_shortcode('[zaymi_newsletter]');
get_footer();
