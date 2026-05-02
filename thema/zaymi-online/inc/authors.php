<?php
/**
 * Zaymi Authors — собственная сущность авторов статей.
 *
 * Решает задачу превью: у каждой статьи свой эксперт с фото, должностью,
 * био, соцсетями. Никак не зависит от WP-юзеров — это полноценный CPT.
 *
 * Возможности:
 *  - CPT `zaymi_author` с полями: должность, био, twitter/linkedin/email, фото.
 *  - Привязка к статье: meta `_zaymi_author_id` (post-object).
 *  - Метабокс на экране редактирования поста.
 *  - Хелперы: zaymi_get_post_author($post_id) → массив (name, role, bio, photo, links)
 *    с фолбэком на штатного WP-автора.
 *  - Админ-страница "AI Авторы" → генерирует имя/должность/био + аватар через
 *    DALL·E одной кнопкой, импортирует пачкой.
 *
 * @package Zaymi
 */

if (!defined('ABSPATH')) exit;

/* ------------------------------------------------------------------
 * 1. CPT
 * ------------------------------------------------------------------ */
add_action('init', function () {
    register_post_type('zaymi_author', [
        'labels' => [
            'name'          => 'Авторы',
            'singular_name' => 'Автор',
            'menu_name'     => 'Авторы',
            'add_new'       => 'Добавить автора',
            'add_new_item'  => 'Новый автор',
            'edit_item'     => 'Редактировать автора',
            'all_items'     => 'Все авторы',
            'search_items'  => 'Найти автора',
            'not_found'     => 'Авторы не найдены',
        ],
        'public'        => false,
        'show_ui'       => true,
        'show_in_menu'  => true, // отдельный top-level пункт «Авторы»
        'menu_position' => 26,
        'menu_icon'     => 'dashicons-admin-users',
        'supports'      => ['title', 'thumbnail', 'editor'],
        'capability_type' => 'post',
    ]);
}, 5);

/* ------------------------------------------------------------------
 * 2. Поля метабокса автора (должность, био, соцсети)
 * ------------------------------------------------------------------ */
add_action('add_meta_boxes', function () {
    add_meta_box('zaymi_author_fields', 'Профиль эксперта', 'zaymi_author_fields_box', 'zaymi_author', 'normal', 'high');
    add_meta_box('zaymi_post_author_pick', 'Автор статьи', 'zaymi_post_author_pick_box', 'post', 'side', 'high');
});

function zaymi_author_fields_box($post) {
    $role  = get_post_meta($post->ID, '_role', true);
    $bio   = get_post_meta($post->ID, '_bio', true);
    $tw    = get_post_meta($post->ID, '_twitter', true);
    $li    = get_post_meta($post->ID, '_linkedin', true);
    $em    = get_post_meta($post->ID, '_email', true);
    $exp   = get_post_meta($post->ID, '_experience', true);
    wp_nonce_field('zaymi_author_save', 'zaymi_author_nonce');
    ?>
    <style>.zaymi-af{display:grid;gap:12px;}.zaymi-af label{font-weight:600;display:block;margin-bottom:4px;}.zaymi-af input,.zaymi-af textarea{width:100%;}</style>
    <div class="zaymi-af">
        <div><label>Должность / экспертиза</label><input type="text" name="zaymi_author_role" value="<?php echo esc_attr($role); ?>" placeholder="Финансовый эксперт, 12 лет в банковской сфере"></div>
        <div><label>Био (3-5 предложений)</label><textarea name="zaymi_author_bio" rows="5"><?php echo esc_textarea($bio); ?></textarea></div>
        <div><label>Опыт (короткой строкой)</label><input type="text" name="zaymi_author_experience" value="<?php echo esc_attr($exp); ?>" placeholder="Бывший руководитель отдела розничного кредитования Альфа-Банка"></div>
        <div><label>Twitter URL</label><input type="url" name="zaymi_author_twitter" value="<?php echo esc_attr($tw); ?>"></div>
        <div><label>LinkedIn URL</label><input type="url" name="zaymi_author_linkedin" value="<?php echo esc_attr($li); ?>"></div>
        <div><label>Email</label><input type="email" name="zaymi_author_email" value="<?php echo esc_attr($em); ?>"></div>
        <p style="color:#666;font-size:12px;">Фото эксперта — задайте через «Изображение записи» справа. Можно сгенерировать через AI на странице «Авторы → AI Авторы».</p>
    </div>
    <?php
}

add_action('save_post_zaymi_author', function ($post_id) {
    if (!isset($_POST['zaymi_author_nonce']) || !wp_verify_nonce($_POST['zaymi_author_nonce'], 'zaymi_author_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    $map = [
        '_role'       => 'zaymi_author_role',
        '_bio'        => 'zaymi_author_bio',
        '_experience' => 'zaymi_author_experience',
        '_twitter'    => 'zaymi_author_twitter',
        '_linkedin'   => 'zaymi_author_linkedin',
        '_email'      => 'zaymi_author_email',
    ];
    foreach ($map as $meta => $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $meta, sanitize_text_field(wp_unslash($_POST[$field])));
        }
    }
});

/* ------------------------------------------------------------------
 * 3. Метабокс выбора автора в посте
 * ------------------------------------------------------------------ */
function zaymi_post_author_pick_box($post) {
    $cur = (int) get_post_meta($post->ID, '_zaymi_author_id', true);
    $authors = get_posts(['post_type' => 'zaymi_author', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC']);
    wp_nonce_field('zaymi_post_author_save', 'zaymi_post_author_nonce');
    echo '<select name="zaymi_post_author_id" style="width:100%;">';
    echo '<option value="0">— штатный WP-автор —</option>';
    foreach ($authors as $a) {
        printf('<option value="%d" %s>%s</option>', $a->ID, selected($cur, $a->ID, false), esc_html($a->post_title));
    }
    echo '</select>';
    echo '<p style="color:#666;margin-top:6px;">Эксперт-автор отображается в шапке статьи и блоке «Об авторе».</p>';
}

add_action('save_post_post', function ($post_id) {
    if (!isset($_POST['zaymi_post_author_nonce']) || !wp_verify_nonce($_POST['zaymi_post_author_nonce'], 'zaymi_post_author_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    $aid = isset($_POST['zaymi_post_author_id']) ? (int) $_POST['zaymi_post_author_id'] : 0;
    if ($aid > 0) update_post_meta($post_id, '_zaymi_author_id', $aid);
    else delete_post_meta($post_id, '_zaymi_author_id');
});

/* ------------------------------------------------------------------
 * 4. Хелпер для шаблона
 * ------------------------------------------------------------------ */
if (!function_exists('zaymi_get_post_author')) {
    function zaymi_get_post_author($post_id) {
        $aid = (int) get_post_meta($post_id, '_zaymi_author_id', true);
        if ($aid && get_post_status($aid) === 'publish') {
            return [
                'source'     => 'zaymi',
                'id'         => $aid,
                'name'       => get_the_title($aid),
                'role'       => get_post_meta($aid, '_role', true) ?: 'Автор Zaymi Online',
                'bio'        => get_post_meta($aid, '_bio', true),
                'experience' => get_post_meta($aid, '_experience', true),
                'twitter'    => get_post_meta($aid, '_twitter', true),
                'linkedin'   => get_post_meta($aid, '_linkedin', true),
                'email'      => get_post_meta($aid, '_email', true),
                'photo'      => get_the_post_thumbnail_url($aid, 'medium') ?: 'https://i.pravatar.cc/200?u=' . $aid,
            ];
        }
        // фолбэк на штатного WP-автора
        $wp_aid = (int) get_post_field('post_author', $post_id);
        return [
            'source'     => 'wp',
            'id'         => $wp_aid,
            'name'       => get_the_author_meta('display_name', $wp_aid),
            'role'       => function_exists('get_field') ? (get_field('user_role', 'user_' . $wp_aid) ?: 'Автор Zaymi Online') : 'Автор Zaymi Online',
            'bio'        => get_the_author_meta('description', $wp_aid),
            'experience' => '',
            'twitter'    => '',
            'linkedin'   => '',
            'email'      => '',
            'photo'      => get_avatar_url($wp_aid, ['size' => 160]),
        ];
    }
}

/* ------------------------------------------------------------------
 * 5. AI-генератор авторов (страница "AI Авторы")
 * ------------------------------------------------------------------ */
add_action('admin_menu', function () {
    add_submenu_page('edit.php', 'AI Авторы', '✨ AI Авторы', 'manage_options', 'zaymi-ai-authors', 'zaymi_ai_authors_page');
});

function zaymi_ai_authors_page() {
    if (!current_user_can('manage_options')) return;

    // Обработка
    if (isset($_POST['zaymi_ai_author_nonce']) && wp_verify_nonce($_POST['zaymi_ai_author_nonce'], 'zaymi_ai_authors')) {
        $count = max(1, min(10, (int)($_POST['count'] ?? 1)));
        $hint  = sanitize_text_field(wp_unslash($_POST['hint'] ?? ''));
        $gender = in_array($_POST['gender'] ?? 'mix', ['male','female','mix'], true) ? $_POST['gender'] : 'mix';
        $with_photo = !empty($_POST['with_photo']);
        $created = []; $errors = [];
        for ($i = 0; $i < $count; $i++) {
            try {
                $g = $gender === 'mix' ? (rand(0,1) ? 'male' : 'female') : $gender;
                $created[] = zaymi_ai_generate_author($hint, $g, $with_photo);
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }
        echo '<div class="notice notice-success"><p>Создано авторов: ' . count($created) . '</p></div>';
        if ($errors) echo '<div class="notice notice-error"><p>' . esc_html(implode(' • ', $errors)) . '</p></div>';
    }

    $authors = get_posts(['post_type' => 'zaymi_author', 'posts_per_page' => 50, 'orderby' => 'date', 'order' => 'DESC']);
    ?>
    <div class="wrap">
        <h1>✨ AI-Авторы</h1>
        <p>Генерируйте экспертов-авторов для статей: имя, должность, био и фото-аватар одной кнопкой. Используется ваш ключ <code>ZAYMI_OPENAI_KEY</code>.</p>

        <form method="post" style="background:#fff;padding:16px;border:1px solid #e0e0e0;border-radius:8px;max-width:720px;">
            <?php wp_nonce_field('zaymi_ai_authors', 'zaymi_ai_author_nonce'); ?>
            <h2 style="margin-top:0;">Сгенерировать новых</h2>
            <table class="form-table">
                <tr><th>Сколько</th><td><input type="number" name="count" value="3" min="1" max="10"></td></tr>
                <tr><th>Пол</th><td>
                    <select name="gender">
                        <option value="mix">Микс</option>
                        <option value="female">Женщины</option>
                        <option value="male">Мужчины</option>
                    </select>
                </td></tr>
                <tr><th>Подсказка</th><td><input type="text" name="hint" placeholder="напр. финансовый журналист, бывший банкир" style="width:100%;"></td></tr>
                <tr><th>Аватарки</th><td><label><input type="checkbox" name="with_photo" value="1" checked> Сгенерировать фото-аватары через DALL·E (медленнее, но круче)</label></td></tr>
            </table>
            <p><button type="submit" class="button button-primary button-large">Сгенерировать</button></p>
        </form>

        <h2 style="margin-top:32px;">Текущие авторы (<?php echo count($authors); ?>)</h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">
            <?php foreach ($authors as $a):
                $role = get_post_meta($a->ID, '_role', true);
                $photo = get_the_post_thumbnail_url($a->ID, 'medium') ?: 'https://i.pravatar.cc/200?u=' . $a->ID;
                $used  = (int) (new WP_Query(['post_type' => 'post', 'meta_key' => '_zaymi_author_id', 'meta_value' => $a->ID, 'fields' => 'ids', 'posts_per_page' => -1, 'no_found_rows' => false]))->found_posts;
            ?>
            <div style="background:#fff;border:1px solid #e0e0e0;border-radius:8px;padding:14px;display:flex;gap:12px;">
                <img src="<?php echo esc_url($photo); ?>" style="width:64px;height:64px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                <div style="min-width:0;">
                    <div style="font-weight:600;"><?php echo esc_html($a->post_title); ?></div>
                    <div style="color:#666;font-size:12px;margin:2px 0;"><?php echo esc_html($role); ?></div>
                    <div style="color:#888;font-size:11px;">Статей: <?php echo $used; ?></div>
                    <a href="<?php echo esc_url(get_edit_post_link($a->ID)); ?>" class="button button-small" style="margin-top:6px;">Редактировать</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

/**
 * Сгенерировать одного автора через OpenAI.
 */
function zaymi_ai_generate_author($hint = '', $gender = 'female', $with_photo = true) {
    if (!class_exists('Zaymi_AI_Writer')) throw new Exception('AI Writer ядро не загружено');
    $key = Zaymi_AI_Writer::openai_key();
    if (!$key) throw new Exception('ZAYMI_OPENAI_KEY не задан в wp-config.php');

    $sys = 'Ты создаёшь правдоподобный профиль российского финансового эксперта-автора для сайта о займах. Только русский язык. Ответ строго JSON.';
    $genTxt = $gender === 'male' ? 'мужчина' : 'женщина';
    $usr = "Сгенерируй профиль автора ({$genTxt})." . ($hint ? " Подсказка: {$hint}." : '') . " Верни JSON: {\n  \"name\": \"Имя Фамилия\",\n  \"role\": \"должность с цифрой опыта, до 60 символов\",\n  \"bio\": \"3-4 предложения о специализации, без воды, человеческим языком\",\n  \"experience\": \"одна короткая строка с прошлым местом работы (банк/МФО/издание)\",\n  \"avatar_prompt\": \"english DALL-E prompt for a realistic professional headshot photo of a {$genTxt} russian financial expert in business attire, neutral background, soft lighting, photo realistic, no text\"\n}";

    $raw = Zaymi_AI_Writer::openai_chat([
        ['role' => 'system', 'content' => $sys],
        ['role' => 'user',   'content' => $usr],
    ], ['response_format' => ['type' => 'json_object'], 'temperature' => 0.95, 'max_tokens' => 600]);

    $d = json_decode($raw, true);
    if (!$d || empty($d['name'])) throw new Exception('AI вернул пустой профиль');

    $author_id = wp_insert_post([
        'post_type'   => 'zaymi_author',
        'post_status' => 'publish',
        'post_title'  => $d['name'],
        'post_content'=> $d['bio'] ?? '',
    ], true);
    if (is_wp_error($author_id)) throw new Exception($author_id->get_error_message());

    update_post_meta($author_id, '_role', $d['role'] ?? '');
    update_post_meta($author_id, '_bio',  $d['bio']  ?? '');
    update_post_meta($author_id, '_experience', $d['experience'] ?? '');
    update_post_meta($author_id, '_zaymi_ai_generated', 1);

    // Аватар
    if ($with_photo && !empty($d['avatar_prompt'])) {
        try {
            // квадратный портрет
            $key2 = Zaymi_AI_Writer::openai_key();
            $resp = wp_remote_post('https://api.openai.com/v1/images/generations', [
                'timeout' => 120,
                'headers' => ['Authorization' => 'Bearer ' . $key2, 'Content-Type' => 'application/json'],
                'body'    => wp_json_encode([
                    'model'  => Zaymi_AI_Writer::openai_image_model(),
                    'prompt' => $d['avatar_prompt'],
                    'size'   => '1024x1024',
                    'quality'=> 'standard',
                    'n'      => 1,
                ]),
            ]);
            if (!is_wp_error($resp)) {
                $body = json_decode(wp_remote_retrieve_body($resp), true);
                $url  = $body['data'][0]['url'] ?? '';
                if ($url) {
                    $att = Zaymi_AI_Writer::sideload_image($url, $author_id, $d['name']);
                    if ($att) set_post_thumbnail($author_id, $att);
                }
            }
        } catch (Exception $e) { /* без фото — не критично */ }
    }

    return $author_id;
}
