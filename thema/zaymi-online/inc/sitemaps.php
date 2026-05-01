<?php
/**
 * Zaymi Online — XML-карты сайта по типам + auto-ping.
 *
 *  /sitemap.xml          — индексный
 *  /sitemap-mfo.xml
 *  /sitemap-cities.xml
 *  /sitemap-amounts.xml
 *  /sitemap-articles.xml
 *  /sitemap-pages.xml
 *  /sitemap-seo.xml      — программатик-страницы
 *
 * @package Zaymi
 */

if (!defined('ABSPATH')) exit;

/* ---------- Rewrite ---------- */
add_action('init', function () {
    add_rewrite_rule('^sitemap\.xml$', 'index.php?zaymi_sitemap=index', 'top');
    add_rewrite_rule('^sitemap-([a-z]+)\.xml$', 'index.php?zaymi_sitemap=$matches[1]', 'top');
});
add_filter('query_vars', function ($v) { $v[] = 'zaymi_sitemap'; return $v; });

// Отключаем штатный sitemap WP
add_filter('wp_sitemaps_enabled', '__return_false');

add_action('template_redirect', function () {
    $type = get_query_var('zaymi_sitemap');
    if (!$type) return;
    nocache_headers();
    header('Content-Type: application/xml; charset=UTF-8');
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    if ($type === 'index') {
        zaymi_sitemap_index();
    } else {
        zaymi_sitemap_urlset($type);
    }
    exit;
});

function zaymi_sitemap_index() {
    $maps = ['mfo', 'cities', 'amounts', 'articles', 'pages', 'seo'];
    echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    foreach ($maps as $m) {
        echo '<sitemap><loc>' . esc_url(home_url("/sitemap-$m.xml")) . '</loc><lastmod>' . date('c') . '</lastmod></sitemap>';
    }
    echo '</sitemapindex>';
}

function zaymi_sitemap_urlset($type) {
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    $urls = [];

    switch ($type) {
        case 'mfo':
            foreach (get_posts(['post_type'=>'mfo','posts_per_page'=>-1,'fields'=>'ids']) as $id) {
                $urls[] = ['loc'=>get_permalink($id), 'lastmod'=>get_post_modified_time('c', true, $id), 'priority'=>'0.9'];
            }
            $urls[] = ['loc' => get_post_type_archive_link('mfo'), 'priority'=>'1.0'];
            break;
        case 'cities':
            foreach (get_terms(['taxonomy'=>'city','hide_empty'=>false]) as $t) {
                $urls[] = ['loc'=>get_term_link($t),'priority'=>'0.7'];
            }
            break;
        case 'amounts':
            foreach (get_terms(['taxonomy'=>'summa','hide_empty'=>false]) as $t) {
                $urls[] = ['loc'=>get_term_link($t),'priority'=>'0.7'];
            }
            foreach (get_terms(['taxonomy'=>'situation','hide_empty'=>false]) as $t) {
                $urls[] = ['loc'=>get_term_link($t),'priority'=>'0.7'];
            }
            break;
        case 'articles':
            foreach (get_posts(['post_type'=>'post','posts_per_page'=>-1,'fields'=>'ids']) as $id) {
                $urls[] = ['loc'=>get_permalink($id),'lastmod'=>get_post_modified_time('c',true,$id),'priority'=>'0.6'];
            }
            break;
        case 'pages':
            foreach (get_posts(['post_type'=>'page','posts_per_page'=>-1,'fields'=>'ids']) as $id) {
                $urls[] = ['loc'=>get_permalink($id),'lastmod'=>get_post_modified_time('c',true,$id),'priority'=>'0.5'];
            }
            break;
        case 'seo':
            // программатик-страницы
            $amounts = [3000,5000,10000,15000,20000,30000,50000,100000];
            $terms_d = [7,14,21,30];
            foreach ($amounts as $a) foreach ($terms_d as $t) {
                $urls[] = ['loc'=>home_url("/zaymy/{$a}-rubley-na-{$t}-dney/"),'priority'=>'0.6'];
            }
            foreach (get_terms(['taxonomy'=>'city','hide_empty'=>false]) as $t) {
                $urls[] = ['loc'=>home_url("/zaymy/{$t->slug}/bez-otkaza/"),'priority'=>'0.6'];
                $urls[] = ['loc'=>home_url("/zaymy/{$t->slug}/s-plokhoy-kreditnoy-istoriey/"),'priority'=>'0.6'];
            }
            // парные сравнения МФО
            $mfos = get_posts(['post_type'=>'mfo','posts_per_page'=>-1]);
            for ($i=0; $i<count($mfos); $i++) {
                for ($j=$i+1; $j<count($mfos); $j++) {
                    $urls[] = ['loc'=>home_url("/sravnenie/{$mfos[$i]->post_name}-vs-{$mfos[$j]->post_name}/"),'priority'=>'0.5'];
                }
            }
            break;
    }

    foreach ($urls as $u) {
        echo '<url><loc>' . esc_url($u['loc']) . '</loc>';
        if (!empty($u['lastmod'])) echo '<lastmod>' . $u['lastmod'] . '</lastmod>';
        if (!empty($u['priority'])) echo '<priority>' . $u['priority'] . '</priority>';
        echo '</url>';
    }
    echo '</urlset>';
}

/* ---------- Auto-ping Google + IndexNow ---------- */
add_action('save_post', 'zaymi_sitemap_ping', 20, 2);
function zaymi_sitemap_ping($post_id, $post) {
    if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) return;
    if ($post->post_status !== 'publish') return;

    $url = get_permalink($post_id);

    // Google ping
    wp_remote_get('https://www.google.com/ping?sitemap=' . urlencode(home_url('/sitemap.xml')), ['timeout'=>3,'blocking'=>false]);

    // IndexNow (Bing/Yandex)
    $key = function_exists('get_field') ? get_field('indexnow_key','option') : '';
    if ($key) {
        wp_remote_post("https://api.indexnow.org/indexnow", [
            'timeout'=>3, 'blocking'=>false,
            'headers'=>['Content-Type'=>'application/json'],
            'body'=>wp_json_encode([
                'host'=>parse_url(home_url(),PHP_URL_HOST),
                'key'=>$key,
                'keyLocation'=>home_url('/'.$key.'.txt'),
                'urlList'=>[$url],
            ]),
        ]);
    }

    // Yandex Webmaster API (если настроен токен)
    $ya_token = function_exists('get_field') ? get_field('yandex_oauth_token','option') : '';
    $ya_host  = function_exists('get_field') ? get_field('yandex_host_id','option') : '';
    if ($ya_token && $ya_host) {
        wp_remote_post("https://api.webmaster.yandex.net/v4/user/{$ya_host}/recrawl/queue", [
            'timeout'=>3,'blocking'=>false,
            'headers'=>['Authorization'=>'OAuth '.$ya_token,'Content-Type'=>'application/json'],
            'body'=>wp_json_encode(['url'=>$url]),
        ]);
    }
}

/* ---------- Отдача файла ключа IndexNow ---------- */
add_action('init', function () {
    $key = function_exists('get_field') ? get_field('indexnow_key','option') : '';
    if (!$key) return;
    $req = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
    if ($req === $key . '.txt') {
        header('Content-Type: text/plain');
        echo $key; exit;
    }
});
