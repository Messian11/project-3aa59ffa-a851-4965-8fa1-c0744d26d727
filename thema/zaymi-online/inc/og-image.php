<?php
/**
 * Zaymi Online — Open Graph + Twitter Cards с авто-генерацией картинок.
 *
 *  /og-image/{post_id}.jpg — динамическая OG-картинка через GD
 *  Кеш: uploads/zaymi-og/{id}.jpg
 *
 * @package Zaymi
 */

if (!defined('ABSPATH')) exit;

/* ---------- OG/Twitter теги ---------- */
add_action('wp_head', function () {
    if (is_admin()) return;

    $title = wp_get_document_title();
    $url   = home_url(add_query_arg(null, null));
    $desc  = '';
    $img   = '';

    if (is_singular()) {
        $id = get_the_ID();
        $desc = wp_strip_all_tags(get_the_excerpt() ?: get_the_content());
        $desc = mb_substr($desc, 0, 200);
        $img = get_the_post_thumbnail_url($id, 'large') ?: home_url('/og-image/'.$id.'.jpg');
    } else {
        $desc = get_bloginfo('description');
        $img = function_exists('get_field') ? (get_field('default_og_image','option') ?: '') : '';
    }

    echo "\n<!-- OG -->\n";
    echo '<meta property="og:type" content="website">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($desc) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
    if ($img) echo '<meta property="og:image" content="' . esc_url($img) . '">' . "\n";
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr($desc) . '">' . "\n";
    if ($img) echo '<meta name="twitter:image" content="' . esc_url($img) . '">' . "\n";
}, 2);

/* ---------- Динамическая OG-картинка ---------- */
add_action('init', function () {
    add_rewrite_rule('^og-image/([0-9]+)\.jpg$', 'index.php?zaymi_og=$matches[1]', 'top');
});
add_filter('query_vars', function ($v) { $v[] = 'zaymi_og'; return $v; });

add_action('template_redirect', function () {
    $id = (int) get_query_var('zaymi_og');
    if (!$id) return;
    if (!function_exists('imagecreatetruecolor')) {
        wp_redirect(home_url('/wp-content/themes/zaymi-online/screenshot.png')); exit;
    }

    $upload = wp_upload_dir();
    $cache_dir = $upload['basedir'] . '/zaymi-og';
    if (!file_exists($cache_dir)) wp_mkdir_p($cache_dir);
    $cache_file = $cache_dir . '/' . $id . '.jpg';

    if (file_exists($cache_file) && (time() - filemtime($cache_file) < DAY_IN_SECONDS)) {
        header('Content-Type: image/jpeg');
        readfile($cache_file); exit;
    }

    $W = 1200; $H = 630;
    $img = imagecreatetruecolor($W, $H);

    // градиент
    for ($y = 0; $y < $H; $y++) {
        $r = (int)(22  + ($y/$H) * 8);
        $g = (int)(163 + ($y/$H) * (-30));
        $b = (int)(74  + ($y/$H) * (-20));
        $c = imagecolorallocate($img, $r, $g, $b);
        imageline($img, 0, $y, $W, $y, $c);
    }

    $white = imagecolorallocate($img, 255, 255, 255);
    $light = imagecolorallocate($img, 220, 252, 231);

    $title = get_the_title($id);
    if (!$title) $title = get_bloginfo('name');
    $title = mb_substr($title, 0, 60);

    $font = ZAYMI_DIR . '/assets/fonts/Inter-Bold.ttf';
    if (file_exists($font)) {
        imagettftext($img, 56, 0, 60, 200, $white, $font, $title);
        $sub = '';
        if (get_post_type($id) === 'mfo' && function_exists('get_field')) {
            $rate = get_field('rate_min', $id);
            if ($rate) $sub = "от {$rate}% в день · до 100 000 ₽";
        }
        if ($sub) imagettftext($img, 32, 0, 60, 320, $light, $font, $sub);
        imagettftext($img, 24, 0, 60, $H - 60, $light, $font, get_bloginfo('name'));
    } else {
        imagestring($img, 5, 60, 60, $title, $white);
    }

    imagejpeg($img, $cache_file, 85);
    imagedestroy($img);
    header('Content-Type: image/jpeg');
    readfile($cache_file); exit;
});
