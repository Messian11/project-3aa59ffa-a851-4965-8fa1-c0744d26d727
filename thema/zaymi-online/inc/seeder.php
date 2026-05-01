<?php
/**
 * Сидер демо-контента Zaymi.
 *
 * Запускается:
 *  1) Автоматически при активации темы.
 *  2) Вручную: Админка → МФО → «Инструменты» → «Засеять».
 *
 * Идемпотентно: повторный запуск ничего не дублирует.
 */
if (!defined('ABSPATH')) exit;

const ZAYMI_SEEDER_FLAG = 'zaymi_seeded_v2';

add_action('after_switch_theme', function () { update_option('zaymi_seeder_pending', 1); });
add_action('admin_init', function () {
    if (get_option('zaymi_seeder_pending')) {
        delete_option('zaymi_seeder_pending');
        if (!get_option(ZAYMI_SEEDER_FLAG)) {
            zaymi_run_seeder();
            update_option(ZAYMI_SEEDER_FLAG, time());
        }
    }
});
add_action('admin_post_zaymi_run_seeder', function () {
    if (!current_user_can('manage_options')) wp_die('Нет прав');
    check_admin_referer('zaymi_run_seeder');
    zaymi_run_seeder();
    update_option(ZAYMI_SEEDER_FLAG, time());
    wp_safe_redirect(admin_url('admin.php?page=zaymi-tools&seeded=1'));
    exit;
});

function zaymi_run_seeder() {
    @set_time_limit(300);
    zaymi_seed_terms();
    zaymi_seed_mfos();
    zaymi_seed_articles();
    zaymi_seed_pages();
    zaymi_set_homepage();
    flush_rewrite_rules();
}

/* ===== Термины: situation, summa, city ===== */
function zaymi_seed_terms() {
    foreach (['situations' => 'situation', 'amounts' => 'summa', 'cities' => 'city'] as $file => $tax) {
        $path = ZAYMI_DEMO_DIR . '/' . $file . '.json';
        if (!file_exists($path)) continue;
        $items = json_decode(file_get_contents($path), true) ?: [];
        foreach ($items as $it) {
            $slug = sanitize_title($it['slug']);
            $existing = get_term_by('slug', $slug, $tax);
            if ($existing) {
                $term_id = $existing->term_id;
            } else {
                $r = wp_insert_term($it['name'], $tax, ['slug' => $slug, 'description' => $it['intro'] ?? '']);
                if (is_wp_error($r)) continue;
                $term_id = $r['term_id'];
            }
            if (function_exists('update_field')) {
                if (!empty($it['h1']))    update_field('hub_h1',    $it['h1'],    'term_' . $term_id);
                if (!empty($it['intro'])) update_field('hub_intro', wpautop($it['intro']), 'term_' . $term_id);
            }
        }
    }
}

/* ===== МФО ===== */
function zaymi_seed_mfos() {
    $path = ZAYMI_DEMO_DIR . '/mfo.json';
    if (!file_exists($path)) return;
    $items = json_decode(file_get_contents($path), true) ?: [];

    foreach ($items as $m) {
        $slug = sanitize_title($m['slug']);
        $existing = get_page_by_path($slug, OBJECT, 'mfo');

        $post_id = $existing
            ? $existing->ID
            : wp_insert_post([
                'post_type'    => 'mfo',
                'post_title'   => $m['title'],
                'post_name'    => $slug,
                'post_status'  => 'publish',
                'post_content' => '',
            ]);

        if (!$post_id || is_wp_error($post_id)) continue;

        // Логотип: если файла в /assets/images/mfo/ нет — генерируем плейсхолдер на лету.
        $logo_id = zaymi_import_or_generate_logo($m, $post_id);
        if ($logo_id) {
            set_post_thumbnail($post_id, $logo_id);
            if (function_exists('update_field')) update_field('mfo_logo', $logo_id, $post_id);
        }

        if (function_exists('update_field')) {
            $map = [
                'mfo_tagline'       => $m['tagline']       ?? '',
                'mfo_rating'        => $m['rating']        ?? '',
                'mfo_reviews_count' => $m['reviews_count'] ?? '',
                'mfo_amount_min'    => $m['amount_min']    ?? '',
                'mfo_amount_max'    => $m['amount_max']    ?? '',
                'mfo_term_min'      => $m['term_min']      ?? '',
                'mfo_term_max'      => $m['term_max']      ?? '',
                'mfo_rate_min'      => $m['rate_min']      ?? '',
                'mfo_rate_max'      => $m['rate_max']      ?? '',
                'mfo_approval_rate' => $m['approval_rate'] ?? '',
                'mfo_age_min'       => $m['age_min']       ?? '',
                'mfo_age_max'       => $m['age_max']       ?? '',
                'mfo_partner_url'   => $m['partner_url']   ?? '',
                'mfo_license'       => $m['license']       ?? '',
                'mfo_documents'     => $m['documents']     ?? [],
            ];
            foreach ($map as $k => $v) update_field($k, $v, $post_id);

            update_field('mfo_pros', array_map(fn($t) => is_array($t) ? $t : ['text' => $t], $m['pros'] ?? []), $post_id);
            update_field('mfo_cons', array_map(fn($t) => is_array($t) ? $t : ['text' => $t], $m['cons'] ?? []), $post_id);
            update_field('mfo_fit_for', $m['fit_for'] ?? [], $post_id);
            update_field('mfo_steps',   $m['steps']   ?? [], $post_id);
            update_field('mfo_rates',   $m['rates']   ?? [], $post_id);
            update_field('mfo_faq',     $m['faq']     ?? [], $post_id);
        }

        if (!empty($m['taxonomies']) && is_array($m['taxonomies'])) {
            foreach ($m['taxonomies'] as $tax => $slugs) {
                wp_set_object_terms($post_id, (array) $slugs, $tax, false);
            }
        }
    }
}

/**
 * Импорт PNG из темы или авто-генерация плейсхолдера через GD.
 */
function zaymi_import_or_generate_logo($m, $post_id) {
    $filename = $m['logo'] ?? '';
    $title    = $m['title'] ?? '';
    if (!$filename) return 0;

    // 1. Проверка медиабиблиотеки по нашему meta-ключу
    $existing = get_posts([
        'post_type'   => 'attachment',
        'meta_key'    => '_zaymi_logo_src',
        'meta_value'  => $filename,
        'numberposts' => 1, 'fields' => 'ids',
    ]);
    if ($existing) return $existing[0];

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $upload = wp_upload_dir();
    $target = trailingslashit($upload['path']) . $filename;

    // 2. Готовый PNG в теме?
    $bundled = ZAYMI_DIR . '/assets/images/mfo/' . $filename;
    if (file_exists($bundled)) {
        @copy($bundled, $target);
    } elseif (function_exists('imagecreatetruecolor')) {
        // 3. Генерируем плейсхолдер через GD
        zaymi_generate_logo_placeholder($target, $title);
    } else {
        return 0;
    }

    if (!file_exists($target)) return 0;

    $att = [
        'post_mime_type' => 'image/png',
        'post_title'     => 'Логотип ' . $title,
        'post_status'    => 'inherit',
    ];
    $id = wp_insert_attachment($att, $target);
    if (!$id || is_wp_error($id)) return 0;

    $meta = wp_generate_attachment_metadata($id, $target);
    wp_update_attachment_metadata($id, $meta);
    update_post_meta($id, '_zaymi_logo_src', $filename);
    return $id;
}

/**
 * Генерация PNG-плейсхолдера 400x400: градиент + первая буква названия.
 */
function zaymi_generate_logo_placeholder($path, $title) {
    if (!function_exists('imagecreatetruecolor')) return false;
    $w = 400; $h = 400;
    $img = imagecreatetruecolor($w, $h);

    // Градиент (выбор по хешу названия)
    $palettes = [
        [[37,99,235],  [16,185,129]],
        [[245,158,11], [16,185,129]],
        [[37,99,235],  [96,165,250]],
        [[16,185,129], [52,211,153]],
        [[245,158,11], [37,99,235]],
        [[37,99,235],  [245,158,11]],
        [[16,185,129], [37,99,235]],
        [[245,158,11], [16,185,129]],
    ];
    $pi = abs(crc32($title)) % count($palettes);
    [$c1, $c2] = $palettes[$pi];

    for ($y = 0; $y < $h; $y++) {
        $t = $y / $h;
        $r = (int)($c1[0] + ($c2[0] - $c1[0]) * $t);
        $g = (int)($c1[1] + ($c2[1] - $c1[1]) * $t);
        $b = (int)($c1[2] + ($c2[2] - $c1[2]) * $t);
        $line = imagecolorallocate($img, $r, $g, $b);
        imageline($img, 0, $y, $w, $y, $line);
    }

    // Скруглённый квадрат — рисуем полупрозрачную рамку для эффекта объёма
    $shadow = imagecolorallocatealpha($img, 0, 0, 0, 110);
    imagefilledrectangle($img, 0, $h - 8, $w, $h, $shadow);

    // Буква
    $letter = mb_strtoupper(mb_substr(trim($title), 0, 1, 'UTF-8'), 'UTF-8');
    if (preg_match('/^[A-Z0-9]$/', $letter)) {
        $white = imagecolorallocate($img, 255, 255, 255);
        // Используем встроенный шрифт (без TTF), масштабируем растягиванием
        $font = 5;
        $fw = imagefontwidth($font); $fh = imagefontheight($font);
        $tmp = imagecreatetruecolor($fw, $fh);
        $tBg = imagecolorallocatealpha($tmp, 0, 0, 0, 127);
        imagefill($tmp, 0, 0, $tBg);
        imagesavealpha($tmp, true);
        $tWhite = imagecolorallocate($tmp, 255, 255, 255);
        imagestring($tmp, $font, 0, 0, $letter, $tWhite);
        // Растягиваем 10x10 → 220x220 с центрированием
        $size = 220;
        imagecopyresampled($img, $tmp, ($w - $size) / 2, ($h - $size) / 2, 0, 0, $size, $size, $fw, $fh);
        imagedestroy($tmp);
    } else {
        // Кириллица или иное: просто оставляем градиент с диагональю
        $white = imagecolorallocatealpha($img, 255, 255, 255, 90);
        for ($i = 0; $i < 80; $i++) imageline($img, 0, $i*8, $w, $i*8 + $w, $white);
    }

    $ok = imagepng($img, $path);
    imagedestroy($img);
    return $ok;
}

/* ===== Статьи блога ===== */
function zaymi_seed_articles() {
    $path = ZAYMI_DEMO_DIR . '/articles.json';
    if (!file_exists($path)) return;
    $items = json_decode(file_get_contents($path), true) ?: [];
    foreach ($items as $a) {
        $slug = sanitize_title($a['slug']);
        if (get_page_by_path($slug, OBJECT, 'post')) continue;
        wp_insert_post([
            'post_type'    => 'post',
            'post_status'  => 'publish',
            'post_title'   => $a['title'],
            'post_excerpt' => $a['excerpt'] ?? '',
            'post_content' => $a['content']  ?? '',
            'post_name'    => $slug,
        ]);
    }
}

/* ===== Страницы (главная + сервисные + хабы по суммам/городам/ситуациям) ===== */
function zaymi_seed_pages() {
    $pages = [
        ['main',         'Главная',                    '[zaymi_hero][zaymi_social_proof][zaymi_situations_grid][zaymi_mfo_catalog limit="8"][zaymi_amounts_grid][zaymi_comparison limit="8"][zaymi_how_it_works][zaymi_blog_section][zaymi_faq][zaymi_seo_hub]'],
        ['o-nas',        'О нас',                      "<h2>Кто мы</h2>\n<p>Zaymi Online — независимый каталог МФО России. Мы помогаем сравнить условия более 50 микрофинансовых организаций с лицензией ЦБ РФ и подобрать займ за пару кликов.</p>\n<h2>Как мы зарабатываем</h2>\n<p>Мы получаем вознаграждение от партнёров, когда пользователь оформляет займ через нашу площадку. Это не влияет на отзывы и рейтинги.</p>\n[zaymi_mfo_catalog limit=\"4\" title=\"Топ МФО 2026\" subtitle=\"\"]"],
        ['kontakty',     'Контакты',                   "<p>Email: hello@zaymi-online.example<br>Телефон: +7 (000) 000-00-00</p>"],
        ['privacy',      'Политика конфиденциальности', "<p>Здесь будет ваш текст политики конфиденциальности.</p>"],
        ['sravnenie-mfo','Сравнение МФО',              '[zaymi_comparison limit="20"]'],
        ['blog',         'Блог',                        ''], // используется как страница записей
    ];
    foreach ($pages as [$slug, $title, $content]) {
        $existing = get_page_by_path($slug);
        if ($existing) continue;
        wp_insert_post([
            'post_type'    => 'page',
            'post_status'  => 'publish',
            'post_title'   => $title,
            'post_name'    => $slug,
            'post_content' => $content,
        ]);
    }
}

/* Назначение главной страницы и страницы блога */
function zaymi_set_homepage() {
    $home = get_page_by_path('main');
    $blog = get_page_by_path('blog');
    if ($home) {
        update_option('show_on_front', 'page');
        update_option('page_on_front', $home->ID);
    }
    if ($blog) update_option('page_for_posts', $blog->ID);
}
