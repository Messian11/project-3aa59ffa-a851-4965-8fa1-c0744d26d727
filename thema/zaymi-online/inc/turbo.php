<?php
/**
 * Zaymi Online — Yandex Turbo RSS для блога.
 *
 *  /turbo.xml — RSS 2.0 с расширением <yandex:turbo>
 *
 * Подключи в Яндекс.Вебмастере → Турбо-страницы → RSS-источники.
 *
 * @package Zaymi
 */

if (!defined('ABSPATH')) exit;

add_action('init', function () {
    add_rewrite_rule('^turbo\.xml$', 'index.php?zaymi_turbo=1', 'top');
});
add_filter('query_vars', function ($v) { $v[] = 'zaymi_turbo'; return $v; });

add_action('template_redirect', function () {
    if (!get_query_var('zaymi_turbo')) return;

    nocache_headers();
    header('Content-Type: application/rss+xml; charset=UTF-8');
    $posts = get_posts(['post_type'=>'post','posts_per_page'=>50]);

    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<rss xmlns:yandex="http://news.yandex.ru" xmlns:media="http://search.yahoo.com/mrss/" xmlns:turbo="http://turbo.yandex.ru" version="2.0">';
    echo '<channel>';
    echo '<title>' . esc_html(get_bloginfo('name')) . '</title>';
    echo '<link>' . esc_url(home_url('/')) . '</link>';
    echo '<description>' . esc_html(get_bloginfo('description')) . '</description>';
    echo '<language>ru</language>';

    foreach ($posts as $p) {
        $thumb = get_the_post_thumbnail_url($p, 'large');
        $content = apply_filters('the_content', $p->post_content);
        // упрощаем HTML для Turbo (убираем неподдерживаемое)
        $content = preg_replace('/<(script|style|iframe)[^>]*>.*?<\/\1>/is', '', $content);

        echo '<item turbo="true">';
        echo '<link>' . esc_url(get_permalink($p)) . '</link>';
        echo '<title>' . esc_html($p->post_title) . '</title>';
        echo '<pubDate>' . mysql2date(DATE_RSS, $p->post_date_gmt) . '</pubDate>';
        echo '<author>' . esc_html(get_the_author_meta('display_name', $p->post_author)) . '</author>';
        if ($thumb) echo '<turbo:extendedHtml>true</turbo:extendedHtml>';
        echo '<turbo:content><![CDATA[';
        if ($thumb) echo '<figure><img src="'.esc_url($thumb).'"/></figure>';
        echo '<header><h1>'.esc_html($p->post_title).'</h1></header>';
        echo $content;
        echo ']]></turbo:content>';
        echo '</item>';
    }
    echo '</channel></rss>';
    exit;
});
