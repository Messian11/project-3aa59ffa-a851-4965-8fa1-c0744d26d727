<?php
/**
 * Zaymi AI Writer — ядро генерации
 *
 * Провайдеры: OpenAI (текст + DALL·E 3), Unsplash (опц. для иллюстраций).
 * Ключи задаются в wp-config.php:
 *   define('ZAYMI_OPENAI_KEY', 'sk-...');
 *   define('ZAYMI_OPENAI_MODEL', 'gpt-4o');         // опц., по умолчанию gpt-4o
 *   define('ZAYMI_OPENAI_IMAGE_MODEL', 'dall-e-3'); // опц.
 *   define('ZAYMI_UNSPLASH_KEY', '...');            // опц., для стоковых иллюстраций
 *
 * Пайплайн: план → черновик → анти-AI rewrite → картинки → перелинковка → пост.
 *
 * @package Zaymi
 */

if (!defined('ABSPATH')) exit;

class Zaymi_AI_Writer {

    const OPT_QUEUE      = 'zaymi_ai_queue';      // массив тем
    const OPT_SETTINGS   = 'zaymi_ai_settings';   // настройки расписания
    const OPT_LOG        = 'zaymi_ai_log';        // последние 200 событий
    const OPT_LAST_RUN   = 'zaymi_ai_last_run';   // timestamp последнего запуска
    const OPT_DAY_COUNT  = 'zaymi_ai_day_count';  // ['date' => 'Y-m-d', 'n' => int]

    /* ============================================================
     * НАСТРОЙКИ ПО УМОЛЧАНИЮ
     * ============================================================ */
    public static function default_settings() {
        return [
            'enabled'        => 0,           // автопубликация вкл/выкл
            'max_per_day'    => 3,           // макс статей в сутки
            'hour_from'      => 9,           // окно публикаций — с
            'hour_to'        => 22,          // окно публикаций — до
            'min_gap_min'    => 90,          // минимальный интервал между публикациями (мин)
            'jitter_min'     => 45,          // разброс ±N минут
            'days'           => [1,2,3,4,5,6,7], // дни недели (1=Пн ... 7=Вс)
            'category_id'    => 0,           // рубрика по умолчанию
            'author_id'      => 1,           // автор по умолчанию
            'status_after'   => 'publish',   // publish|draft|future
            'add_hero_image' => 1,           // генерить hero-картинку
            'add_inline_img' => 1,           // вставлять иллюстрацию в середину
            'image_provider' => 'auto',      // auto|openai|unsplash
            'mfo_table_in_post' => 1,        // вставлять таблицу с топ-3 МФО
            'related_in_post'   => 1,        // блок "читайте также"
            'antiAI_rewrite' => 1,           // включить второй проход переписывания
        ];
    }

    public static function get_settings() {
        $s = get_option(self::OPT_SETTINGS, []);
        return wp_parse_args($s, self::default_settings());
    }

    public static function save_settings($s) {
        update_option(self::OPT_SETTINGS, $s, false);
    }

    /* ============================================================
     * ОЧЕРЕДЬ ТЕМ
     * ============================================================ */
    public static function get_queue() {
        $q = get_option(self::OPT_QUEUE, []);
        return is_array($q) ? $q : [];
    }

    public static function save_queue($q) {
        update_option(self::OPT_QUEUE, array_values($q), false);
    }

    public static function add_topic($title, $meta = []) {
        $title = trim(wp_strip_all_tags($title));
        if ($title === '') return false;

        $q = self::get_queue();
        $q[] = wp_parse_args($meta, [
            'id'        => uniqid('t_', true),
            'title'     => $title,
            'category'  => '',          // подсказка для AI
            'city'      => '',          // опц. город
            'type'      => 'guide',     // guide|review|comparison|news
            'keywords'  => '',          // через запятую
            'status'    => 'pending',   // pending|processing|published|failed
            'post_id'   => 0,
            'error'     => '',
            'added_at'  => time(),
            'published_at' => 0,
        ]);
        self::save_queue($q);
        return true;
    }

    public static function add_topics_bulk($lines) {
        $added = 0;
        foreach ((array)$lines as $line) {
            $line = trim($line);
            if ($line === '') continue;
            // CSV: title|category|type|keywords|city
            $parts = array_map('trim', explode('|', $line));
            if (self::add_topic($parts[0], [
                'category' => $parts[1] ?? '',
                'type'     => $parts[2] ?? 'guide',
                'keywords' => $parts[3] ?? '',
                'city'     => $parts[4] ?? '',
            ])) $added++;
        }
        return $added;
    }

    public static function update_topic($id, $patch) {
        $q = self::get_queue();
        foreach ($q as &$t) {
            if ($t['id'] === $id) { $t = array_merge($t, $patch); break; }
        }
        self::save_queue($q);
    }

    public static function remove_topic($id) {
        $q = array_values(array_filter(self::get_queue(), fn($t) => $t['id'] !== $id));
        self::save_queue($q);
    }

    public static function pick_random_pending() {
        $pending = array_values(array_filter(self::get_queue(), fn($t) => $t['status'] === 'pending'));
        if (!$pending) return null;
        return $pending[array_rand($pending)];
    }

    /* ============================================================
     * ЛОГИРОВАНИЕ
     * ============================================================ */
    public static function log($level, $msg, $ctx = []) {
        $log = get_option(self::OPT_LOG, []);
        if (!is_array($log)) $log = [];
        $log[] = [
            'time'  => time(),
            'level' => $level, // info|success|warn|error
            'msg'   => $msg,
            'ctx'   => $ctx,
        ];
        if (count($log) > 200) $log = array_slice($log, -200);
        update_option(self::OPT_LOG, $log, false);
    }

    public static function get_log() {
        $log = get_option(self::OPT_LOG, []);
        return is_array($log) ? array_reverse($log) : [];
    }

    public static function clear_log() {
        update_option(self::OPT_LOG, [], false);
    }

    /* ============================================================
     * ЛИМИТЫ ДНЯ
     * ============================================================ */
    public static function day_count() {
        $d = get_option(self::OPT_DAY_COUNT, ['date' => '', 'n' => 0]);
        $today = current_time('Y-m-d');
        if (($d['date'] ?? '') !== $today) return 0;
        return (int)($d['n'] ?? 0);
    }

    public static function increment_day_count() {
        $today = current_time('Y-m-d');
        $d = get_option(self::OPT_DAY_COUNT, ['date' => '', 'n' => 0]);
        if (($d['date'] ?? '') !== $today) $d = ['date' => $today, 'n' => 0];
        $d['n']++;
        update_option(self::OPT_DAY_COUNT, $d, false);
    }

    /* ============================================================
     * ПРОВАЙДЕР: OpenAI Chat
     * ============================================================ */
    public static function openai_key() {
        if (defined('ZAYMI_OPENAI_KEY') && ZAYMI_OPENAI_KEY) return ZAYMI_OPENAI_KEY;
        return '';
    }

    public static function openai_model() {
        return defined('ZAYMI_OPENAI_MODEL') ? ZAYMI_OPENAI_MODEL : 'gpt-4o';
    }

    public static function openai_image_model() {
        return defined('ZAYMI_OPENAI_IMAGE_MODEL') ? ZAYMI_OPENAI_IMAGE_MODEL : 'dall-e-3';
    }

    public static function unsplash_key() {
        if (defined('ZAYMI_UNSPLASH_KEY') && ZAYMI_UNSPLASH_KEY) return ZAYMI_UNSPLASH_KEY;
        return '';
    }

    /**
     * Запрос к OpenAI Chat Completions.
     */
    public static function openai_chat($messages, $opts = []) {
        $key = self::openai_key();
        if (!$key) throw new Exception('ZAYMI_OPENAI_KEY не определён в wp-config.php');

        $body = array_merge([
            'model'       => self::openai_model(),
            'messages'    => $messages,
            'temperature' => 0.85,
            'max_tokens'  => 4000,
        ], $opts);

        $resp = wp_remote_post('https://api.openai.com/v1/chat/completions', [
            'timeout' => 120,
            'headers' => [
                'Authorization' => 'Bearer ' . $key,
                'Content-Type'  => 'application/json',
            ],
            'body' => wp_json_encode($body),
        ]);

        if (is_wp_error($resp)) throw new Exception('OpenAI: ' . $resp->get_error_message());

        $code = wp_remote_retrieve_response_code($resp);
        $data = json_decode(wp_remote_retrieve_body($resp), true);

        if ($code !== 200) {
            $err = $data['error']['message'] ?? ('HTTP ' . $code);
            throw new Exception('OpenAI ' . $code . ': ' . $err);
        }

        return $data['choices'][0]['message']['content'] ?? '';
    }

    /**
     * Генерация изображения через DALL·E 3.
     */
    public static function openai_image($prompt) {
        $key = self::openai_key();
        if (!$key) throw new Exception('ZAYMI_OPENAI_KEY не определён');

        $resp = wp_remote_post('https://api.openai.com/v1/images/generations', [
            'timeout' => 120,
            'headers' => [
                'Authorization' => 'Bearer ' . $key,
                'Content-Type'  => 'application/json',
            ],
            'body' => wp_json_encode([
                'model'   => self::openai_image_model(),
                'prompt'  => $prompt,
                'size'    => '1792x1024',
                'quality' => 'standard',
                'n'       => 1,
            ]),
        ]);

        if (is_wp_error($resp)) throw new Exception('DALL·E: ' . $resp->get_error_message());
        $data = json_decode(wp_remote_retrieve_body($resp), true);
        $url = $data['data'][0]['url'] ?? '';
        if (!$url) throw new Exception('DALL·E: пустой ответ');
        return $url;
    }

    /**
     * Поиск стоковой картинки на Unsplash.
     */
    public static function unsplash_search($query) {
        $key = self::unsplash_key();
        if (!$key) return '';

        $resp = wp_remote_get('https://api.unsplash.com/search/photos?per_page=10&orientation=landscape&query=' . urlencode($query), [
            'timeout' => 20,
            'headers' => [
                'Authorization' => 'Client-ID ' . $key,
                'Accept-Version' => 'v1',
            ],
        ]);
        if (is_wp_error($resp)) return '';
        $data = json_decode(wp_remote_retrieve_body($resp), true);
        $results = $data['results'] ?? [];
        if (!$results) return '';
        $pick = $results[array_rand($results)];
        return $pick['urls']['regular'] ?? '';
    }

    /**
     * Загрузка картинки в медиабиблиотеку и возврат attachment_id.
     */
    public static function sideload_image($url, $post_id, $alt = '') {
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $tmp = download_url($url, 60);
        if (is_wp_error($tmp)) return 0;

        $file_array = [
            'name'     => 'ai-' . time() . '-' . wp_generate_password(6, false) . '.jpg',
            'tmp_name' => $tmp,
        ];
        $att_id = media_handle_sideload($file_array, $post_id, $alt);
        if (is_wp_error($att_id)) {
            @unlink($tmp);
            return 0;
        }
        if ($alt) update_post_meta($att_id, '_wp_attachment_image_alt', $alt);
        return (int)$att_id;
    }

    /* ============================================================
     * КОНТЕКСТ ИЗ БАЗЫ (для уникальности и анти-AI-детекта)
     * ============================================================ */
    public static function get_top_mfo($limit = 5) {
        $q = new WP_Query([
            'post_type'      => 'mfo',
            'posts_per_page' => $limit,
            'orderby'        => 'meta_value_num',
            'meta_key'       => 'mfo_rating',
            'order'          => 'DESC',
            'no_found_rows'  => true,
        ]);
        $out = [];
        foreach ($q->posts as $p) {
            $out[] = [
                'id'         => $p->ID,
                'title'      => get_the_title($p),
                'url'        => get_permalink($p),
                'rating'     => get_field('mfo_rating', $p->ID),
                'rate_min'   => get_field('mfo_rate_min', $p->ID),
                'amount_max' => get_field('mfo_amount_max', $p->ID),
                'term_max'   => get_field('mfo_term_max', $p->ID),
                'approval'   => get_field('mfo_approval_rate', $p->ID),
                'features'   => get_field('mfo_features', $p->ID),
            ];
        }
        return $out;
    }

    /* ============================================================
     * ПРОМПТЫ
     * ============================================================ */
    public static function build_system_prompt() {
        return "Ты — опытный российский финансовый журналист и редактор сайта о микрозаймах. " .
               "Пишешь живым человеческим языком, как для друга. Используешь короткие и длинные предложения вперемешку, " .
               "иногда разговорные обороты, риторические вопросы. " .
               "ИЗБЕГАЙ канцелярита и AI-маркеров: 'в современном мире', 'важно отметить', 'в заключение', 'стоит подчеркнуть', " .
               "'необходимо учитывать', 'таким образом', 'играет важную роль', 'неотъемлемой частью'. " .
               "Не используй смайлики и эмодзи. Опирайся на конкретные цифры и реальные продукты. " .
               "Ответ строго валидный JSON без markdown-обёртки.";
    }

    public static function build_user_prompt($topic, $mfos) {
        $type_hints = [
            'guide'      => 'практическое руководство со списками шагов',
            'review'     => 'обзор/сравнение конкретного продукта или категории',
            'comparison' => 'сравнительный материал с таблицами',
            'news'       => 'новостной разбор с экспертным мнением',
        ];
        $type = $type_hints[$topic['type'] ?? 'guide'] ?? 'практическое руководство';

        $mfo_block = '';
        if ($mfos) {
            $mfo_block = "\n\nДоступные МФО на сайте (используй 2-3 в тексте, ссылки оставлю сам по slug):\n";
            foreach ($mfos as $m) {
                $mfo_block .= sprintf("- %s: ставка от %s%%/день, до %s ₽, срок до %s дней, одобрение %s%%, рейтинг %s\n",
                    $m['title'],
                    $m['rate_min'] ?: '0.8',
                    number_format($m['amount_max'] ?: 100000, 0, '', ' '),
                    $m['term_max'] ?: 30,
                    $m['approval'] ?: 85,
                    $m['rating'] ?: 4.5
                );
            }
        }

        $kw = $topic['keywords'] ? "\nКлючевые слова (вписать естественно): {$topic['keywords']}" : '';
        $city = !empty($topic['city']) ? "\nГород/регион: {$topic['city']}" : '';

        return "Напиши {$type} на тему: «{$topic['title']}».{$kw}{$city}{$mfo_block}\n\n" .
               "Структура — верни JSON следующего формата:\n" .
               "{\n" .
               "  \"h1\": \"короткий H1, до 70 символов\",\n" .
               "  \"meta_description\": \"описание до 160 символов с ключом\",\n" .
               "  \"intro\": \"2-3 абзаца лида, цепляющий вход без воды (HTML-абзацы <p>)\",\n" .
               "  \"sections\": [\n" .
               "     { \"h2\": \"...\", \"html\": \"<p>...</p><ul><li>...</li></ul>\" },\n" .
               "     // 4-7 разделов\n" .
               "  ],\n" .
               "  \"conclusion\": \"<p>финальный абзац без слов 'в заключение'</p>\",\n" .
               "  \"faq\": [ {\"q\":\"...\",\"a\":\"...\"}, ... 5-7 пунктов ],\n" .
               "  \"image_prompt\": \"короткий англ. prompt для DALL·E без людей и текста, иллюстрация по теме\",\n" .
               "  \"image_query\": \"2-4 англ. слова для поиска на Unsplash\",\n" .
               "  \"mention_mfo\": [\"slug-mfo-1\", \"slug-mfo-2\"]\n" .
               "}\n\n" .
               "Объём: 1200-1800 слов. HTML только в полях html/intro/conclusion. Без markdown.";
    }

    public static function build_rewrite_prompt() {
        return "Перепиши следующий HTML-контент так, чтобы он звучал как написанный живым человеком, а не AI. " .
               "Сохрани смысл, цифры, факты, структуру (H2, списки, таблицы остаются). " .
               "Меняй: ритм предложений (короткие/длинные вперемешку), убирай канцелярит, добавляй живые обороты, " .
               "иногда риторические вопросы. УБЕРИ типичные AI-фразы. Не добавляй смайлики. " .
               "Верни только готовый HTML без комментариев.";
    }

    /* ============================================================
     * ГЛАВНЫЙ ПАЙПЛАЙН ГЕНЕРАЦИИ
     * ============================================================ */
    public static function generate_post($topic_id) {
        $q = self::get_queue();
        $topic = null;
        foreach ($q as $t) if ($t['id'] === $topic_id) { $topic = $t; break; }
        if (!$topic) throw new Exception('Тема не найдена');

        self::update_topic($topic_id, ['status' => 'processing', 'error' => '']);
        self::log('info', "▶ Старт генерации: {$topic['title']}");

        $settings = self::get_settings();
        $mfos = self::get_top_mfo(5);

        // 1. Генерация структуры через OpenAI
        $sys = self::build_system_prompt();
        $usr = self::build_user_prompt($topic, $mfos);

        $raw = self::openai_chat([
            ['role' => 'system', 'content' => $sys],
            ['role' => 'user',   'content' => $usr],
        ], ['response_format' => ['type' => 'json_object'], 'temperature' => 0.85]);

        $data = json_decode($raw, true);
        if (!$data || empty($data['h1'])) throw new Exception('OpenAI вернул невалидный JSON');
        self::log('info', "✓ Получен черновик: {$data['h1']}");

        // 2. Сборка HTML
        $html = self::assemble_html($data, $mfos, $settings);

        // 3. Анти-AI rewrite (опц.)
        if (!empty($settings['antiAI_rewrite'])) {
            try {
                $rewritten = self::openai_chat([
                    ['role' => 'system', 'content' => self::build_rewrite_prompt()],
                    ['role' => 'user',   'content' => $html],
                ], ['temperature' => 0.9, 'max_tokens' => 4000]);
                if ($rewritten && strlen($rewritten) > 500) {
                    $html = $rewritten;
                    self::log('info', '✓ Переписано человеческим стилем');
                }
            } catch (Exception $e) {
                self::log('warn', 'Rewrite не выполнен: ' . $e->getMessage());
            }
        }

        // 4. Создание поста
        $post_id = wp_insert_post([
            'post_title'    => $data['h1'],
            'post_content'  => $html,
            'post_status'   => $settings['status_after'] ?: 'publish',
            'post_author'   => (int)$settings['author_id'] ?: 1,
            'post_category' => $settings['category_id'] ? [(int)$settings['category_id']] : [],
            'meta_input'    => [
                '_zaymi_ai_generated' => 1,
                '_zaymi_ai_topic'     => $topic['title'],
                '_zaymi_ai_model'     => self::openai_model(),
                '_yoast_wpseo_metadesc' => $data['meta_description'] ?? '',
            ],
        ], true);

        if (is_wp_error($post_id)) throw new Exception('wp_insert_post: ' . $post_id->get_error_message());

        // 5. FAQ — сохраняем в ACF (если поле есть) и в meta
        if (!empty($data['faq']) && is_array($data['faq'])) {
            update_post_meta($post_id, '_zaymi_ai_faq', wp_json_encode($data['faq']));
            if (function_exists('update_field')) {
                $faq_acf = array_map(fn($f) => ['question' => $f['q'] ?? '', 'answer' => $f['a'] ?? ''], $data['faq']);
                update_field('post_faq', $faq_acf, $post_id);
            }
        }

        // 6. Hero-картинка
        if (!empty($settings['add_hero_image'])) {
            $img_url = self::pick_image($data['image_prompt'] ?? $topic['title'], $data['image_query'] ?? $topic['title'], $settings['image_provider']);
            if ($img_url) {
                $att = self::sideload_image($img_url, $post_id, $data['h1']);
                if ($att) {
                    set_post_thumbnail($post_id, $att);
                    self::log('info', '✓ Hero-картинка установлена');
                }
            }
        }

        self::update_topic($topic_id, [
            'status'       => 'published',
            'post_id'      => $post_id,
            'published_at' => time(),
        ]);
        self::increment_day_count();
        update_option(self::OPT_LAST_RUN, time(), false);
        self::log('success', "✓ Опубликовано: {$data['h1']}", ['post_id' => $post_id]);

        return $post_id;
    }

    /* ============================================================
     * СБОРКА HTML
     * ============================================================ */
    public static function assemble_html($data, $mfos, $settings) {
        $html = '';
        $html .= $data['intro'] ?? '';

        // Опц. таблица топ-3 МФО после интро
        if (!empty($settings['mfo_table_in_post']) && $mfos) {
            $html .= self::render_mfo_table(array_slice($mfos, 0, 3));
        }

        $sections = $data['sections'] ?? [];
        $mid = (int)floor(count($sections) / 2);
        foreach ($sections as $i => $s) {
            $html .= '<h2>' . esc_html($s['h2'] ?? '') . '</h2>' . ($s['html'] ?? '');
            // Маркер для middle-картинки
            if ($i === $mid && !empty($settings['add_inline_img'])) {
                $html .= '<!--ZAYMI_INLINE_IMG-->';
            }
        }

        $html .= $data['conclusion'] ?? '';

        // FAQ блок (видимый, разметка Schema добавится отдельно)
        if (!empty($data['faq'])) {
            $html .= '<h2>Частые вопросы</h2><div class="zaymi-ai-faq">';
            foreach ($data['faq'] as $f) {
                $html .= '<details><summary>' . esc_html($f['q'] ?? '') . '</summary><div>' . wp_kses_post($f['a'] ?? '') . '</div></details>';
            }
            $html .= '</div>';
        }

        // Шорткоды для "читайте также"
        if (!empty($settings['related_in_post']) && shortcode_exists('zaymi_related_posts')) {
            $html .= "\n[zaymi_related_posts]\n";
        }

        return $html;
    }

    public static function render_mfo_table($mfos) {
        $h  = '<div class="zaymi-ai-mfo-table"><h3>Подходящие предложения</h3><table><thead><tr>';
        $h .= '<th>МФО</th><th>Сумма</th><th>Срок</th><th>Ставка</th><th>Одобрение</th><th></th></tr></thead><tbody>';
        foreach ($mfos as $m) {
            $h .= '<tr>';
            $h .= '<td><a href="' . esc_url($m['url']) . '">' . esc_html($m['title']) . '</a></td>';
            $h .= '<td>до ' . number_format($m['amount_max'] ?: 100000, 0, '', ' ') . ' ₽</td>';
            $h .= '<td>до ' . esc_html($m['term_max'] ?: 30) . ' дн.</td>';
            $h .= '<td>от ' . esc_html($m['rate_min'] ?: '0.8') . '%/день</td>';
            $h .= '<td>' . esc_html($m['approval'] ?: 85) . '%</td>';
            $h .= '<td><a class="btn-go" href="' . esc_url($m['url']) . '">Подробнее</a></td>';
            $h .= '</tr>';
        }
        $h .= '</tbody></table></div>';
        return $h;
    }

    public static function pick_image($dalle_prompt, $unsplash_query, $provider) {
        if ($provider === 'unsplash' || ($provider === 'auto' && self::unsplash_key() && rand(0,1))) {
            $u = self::unsplash_search($unsplash_query ?: 'finance money');
            if ($u) return $u;
        }
        if (self::openai_key()) {
            try {
                return self::openai_image('Editorial-style illustration, no text, no people faces, cinematic lighting: ' . ($dalle_prompt ?: 'finance concept'));
            } catch (Exception $e) {
                self::log('warn', 'DALL·E: ' . $e->getMessage());
            }
        }
        return '';
    }

    /* ============================================================
     * ПРОВЕРКА: МОЖНО ЛИ ПУБЛИКОВАТЬ СЕЙЧАС
     * ============================================================ */
    public static function can_publish_now() {
        $s = self::get_settings();
        if (empty($s['enabled'])) return [false, 'автопубликация выключена'];

        $now = current_time('timestamp');
        $hour = (int)date('G', $now);
        if ($hour < (int)$s['hour_from'] || $hour >= (int)$s['hour_to']) {
            return [false, "вне окна {$s['hour_from']}-{$s['hour_to']}ч"];
        }

        $dow = (int)date('N', $now);
        if (!in_array($dow, (array)$s['days'])) return [false, 'выключенный день недели'];

        if (self::day_count() >= (int)$s['max_per_day']) {
            return [false, "лимит {$s['max_per_day']}/день достигнут"];
        }

        $last = (int)get_option(self::OPT_LAST_RUN, 0);
        $gap_min = (int)$s['min_gap_min'];
        $jitter = rand(-(int)$s['jitter_min'], (int)$s['jitter_min']);
        $required = ($gap_min + $jitter) * 60;
        if ($last && ($now - $last) < $required) {
            return [false, 'слишком рано после прошлой публикации'];
        }

        if (!self::pick_random_pending()) return [false, 'нет тем в очереди'];

        return [true, 'ok'];
    }
}
