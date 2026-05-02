<?php
/**
 * Zaymi AI Writer — админка WP
 *
 * Меню "AI Writer" с тремя вкладками: Очередь / Расписание / Лог.
 *
 * @package Zaymi
 */

if (!defined('ABSPATH')) exit;

class Zaymi_AI_Writer_Admin {

    public static function init() {
        add_action('admin_menu', [__CLASS__, 'menu']);
        add_action('admin_post_zaymi_ai_add',     [__CLASS__, 'handle_add']);
        add_action('admin_post_zaymi_ai_bulk',    [__CLASS__, 'handle_bulk']);
        add_action('admin_post_zaymi_ai_remove',  [__CLASS__, 'handle_remove']);
        add_action('admin_post_zaymi_ai_run',     [__CLASS__, 'handle_run']);
        add_action('admin_post_zaymi_ai_save',    [__CLASS__, 'handle_save']);
        add_action('admin_post_zaymi_ai_clearlog',[__CLASS__, 'handle_clearlog']);
    }

    public static function menu() {
        add_menu_page('AI Writer', 'AI Writer', 'manage_options', 'zaymi-ai',
            [__CLASS__, 'render'], 'dashicons-edit-large', 27);
    }

    public static function render() {
        if (!current_user_can('manage_options')) return;
        $tab = $_GET['tab'] ?? 'queue';
        ?>
        <div class="wrap zaymi-ai-wrap">
            <h1>🪄 AI Writer — автогенерация статей</h1>

            <?php self::render_status_bar(); ?>

            <h2 class="nav-tab-wrapper">
                <a href="?page=zaymi-ai&tab=queue"    class="nav-tab <?= $tab==='queue'?'nav-tab-active':'' ?>">📋 Очередь тем</a>
                <a href="?page=zaymi-ai&tab=settings" class="nav-tab <?= $tab==='settings'?'nav-tab-active':'' ?>">⚙ Расписание</a>
                <a href="?page=zaymi-ai&tab=log"      class="nav-tab <?= $tab==='log'?'nav-tab-active':'' ?>">📜 Лог</a>
                <a href="?page=zaymi-ai&tab=help"     class="nav-tab <?= $tab==='help'?'nav-tab-active':'' ?>">❓ Как пользоваться</a>
            </h2>

            <?php
            if ($tab === 'settings') self::render_settings();
            elseif ($tab === 'log')  self::render_log();
            elseif ($tab === 'help') self::render_help();
            else self::render_queue();
            ?>
        </div>
        <style>
            .zaymi-ai-wrap .status-bar{display:flex;gap:12px;flex-wrap:wrap;margin:12px 0 20px;padding:12px 16px;background:#fff;border-radius:8px;border:1px solid #e2e8f0}
            .zaymi-ai-wrap .status-bar .pill{padding:4px 10px;border-radius:999px;font-size:13px;font-weight:600}
            .zaymi-ai-wrap .pill.ok{background:#dcfce7;color:#166534}
            .zaymi-ai-wrap .pill.warn{background:#fef3c7;color:#92400e}
            .zaymi-ai-wrap .pill.err{background:#fee2e2;color:#991b1b}
            .zaymi-ai-wrap .pill.info{background:#dbeafe;color:#1e40af}
            .zaymi-ai-wrap .card{background:#fff;padding:20px;border-radius:8px;border:1px solid #e2e8f0;margin-bottom:20px}
            .zaymi-ai-wrap table.queue{width:100%;border-collapse:collapse}
            .zaymi-ai-wrap table.queue th,.zaymi-ai-wrap table.queue td{padding:10px;border-bottom:1px solid #f1f5f9;text-align:left;vertical-align:top}
            .zaymi-ai-wrap .badge{padding:2px 8px;border-radius:4px;font-size:12px;font-weight:600}
            .zaymi-ai-wrap .badge.pending{background:#e0e7ff;color:#3730a3}
            .zaymi-ai-wrap .badge.processing{background:#fef3c7;color:#92400e}
            .zaymi-ai-wrap .badge.published{background:#dcfce7;color:#166534}
            .zaymi-ai-wrap .badge.failed{background:#fee2e2;color:#991b1b}
            .zaymi-ai-wrap .log-row{padding:8px 12px;border-bottom:1px solid #f1f5f9;font-family:ui-monospace,monospace;font-size:12px}
            .zaymi-ai-wrap .log-row .lvl{display:inline-block;width:70px;font-weight:700}
            .zaymi-ai-wrap .lvl.success{color:#166534}.zaymi-ai-wrap .lvl.info{color:#1e40af}
            .zaymi-ai-wrap .lvl.warn{color:#92400e}.zaymi-ai-wrap .lvl.error{color:#991b1b}
        </style>
        <?php
    }

    public static function render_status_bar() {
        $s = Zaymi_AI_Writer::get_settings();
        $key_ok = (bool)Zaymi_AI_Writer::openai_key();
        $unsplash = (bool)Zaymi_AI_Writer::unsplash_key();
        $today = Zaymi_AI_Writer::day_count();
        $queue = count(array_filter(Zaymi_AI_Writer::get_queue(), fn($t)=>$t['status']==='pending'));
        list($can, $why) = Zaymi_AI_Writer::can_publish_now();
        ?>
        <div class="status-bar">
            <span class="pill <?= $key_ok?'ok':'err' ?>">OpenAI ключ: <?= $key_ok?'✓ ОК':'✗ нет ZAYMI_OPENAI_KEY' ?></span>
            <span class="pill <?= $unsplash?'ok':'info' ?>">Unsplash: <?= $unsplash?'✓':'не задан (опц.)' ?></span>
            <span class="pill info">Автопубликация: <?= !empty($s['enabled'])?'✓ ВКЛ':'✗ ВЫКЛ' ?></span>
            <span class="pill info">Сегодня опубликовано: <?= $today ?> / <?= (int)$s['max_per_day'] ?></span>
            <span class="pill info">В очереди: <?= $queue ?> тем</span>
            <span class="pill <?= $can?'ok':'warn' ?>">Сейчас: <?= esc_html($why) ?></span>
        </div>
        <?php
    }

    /* =========================================================
     * ВКЛАДКА: ОЧЕРЕДЬ
     * ========================================================= */
    public static function render_queue() {
        $queue = Zaymi_AI_Writer::get_queue();
        ?>
        <div class="card">
            <h2>➕ Добавить тему</h2>
            <form method="post" action="<?= admin_url('admin-post.php') ?>">
                <?php wp_nonce_field('zaymi_ai_add'); ?>
                <input type="hidden" name="action" value="zaymi_ai_add">
                <table class="form-table">
                    <tr><th>Тема (заголовок)</th><td><input name="title" class="regular-text" required placeholder="Как взять займ с плохой кредитной историей" style="width:100%;max-width:600px"></td></tr>
                    <tr><th>Тип</th><td>
                        <select name="type">
                            <option value="guide">Руководство / гайд</option>
                            <option value="review">Обзор / отзыв</option>
                            <option value="comparison">Сравнение</option>
                            <option value="news">Новость / разбор</option>
                        </select>
                    </td></tr>
                    <tr><th>Ключевые слова</th><td><input name="keywords" class="regular-text" placeholder="займ онлайн, без отказа" style="width:100%;max-width:600px"></td></tr>
                    <tr><th>Город (опц.)</th><td><input name="city" class="regular-text" placeholder="Москва"></td></tr>
                </table>
                <p><button class="button button-primary">Добавить в очередь</button></p>
            </form>
        </div>

        <div class="card">
            <h2>📦 Массовый импорт</h2>
            <p>Одна тема на строку. Можно простой текст или формат с разделителем <code>|</code>:<br>
            <code>Заголовок | категория | тип (guide/review/comparison/news) | ключи | город</code></p>
            <form method="post" action="<?= admin_url('admin-post.php') ?>">
                <?php wp_nonce_field('zaymi_ai_bulk'); ?>
                <input type="hidden" name="action" value="zaymi_ai_bulk">
                <textarea name="bulk" rows="8" style="width:100%" placeholder="Как взять займ с плохой КИ
Топ-10 МФО для пенсионеров | финансы | comparison | пенсионерам, без отказа |
Срочный займ на карту за 5 минут | | guide | срочный займ |"></textarea>
                <p><button class="button button-primary">Импортировать</button></p>
            </form>
        </div>

        <div class="card">
            <h2>📋 Очередь (<?= count($queue) ?>)</h2>
            <?php if (!$queue): ?>
                <p>Очередь пуста. Добавьте темы выше ↑</p>
            <?php else: ?>
                <table class="queue">
                    <thead><tr><th>Тема</th><th>Тип</th><th>Статус</th><th>Опубликовано</th><th>Действия</th></tr></thead>
                    <tbody>
                    <?php foreach ($queue as $t): ?>
                        <tr>
                            <td>
                                <strong><?= esc_html($t['title']) ?></strong>
                                <?php if (!empty($t['keywords'])): ?><br><small style="color:#64748b">🔑 <?= esc_html($t['keywords']) ?></small><?php endif; ?>
                                <?php if (!empty($t['error'])): ?><br><small style="color:#991b1b">⚠ <?= esc_html($t['error']) ?></small><?php endif; ?>
                            </td>
                            <td><?= esc_html($t['type'] ?? 'guide') ?></td>
                            <td><span class="badge <?= esc_attr($t['status']) ?>"><?= esc_html($t['status']) ?></span></td>
                            <td>
                                <?php if (!empty($t['post_id'])): ?>
                                    <a href="<?= get_edit_post_link($t['post_id']) ?>" target="_blank">#<?= $t['post_id'] ?></a><br>
                                    <small><?= $t['published_at'] ? date_i18n('d.m H:i', $t['published_at']) : '' ?></small>
                                <?php else: ?>—<?php endif; ?>
                            </td>
                            <td>
                                <?php if (in_array($t['status'], ['pending','failed'])): ?>
                                    <a class="button button-primary" href="<?= self::nonce_url('zaymi_ai_run', $t['id']) ?>">🪄 Сгенерировать сейчас</a>
                                <?php endif; ?>
                                <a class="button button-link-delete" href="<?= self::nonce_url('zaymi_ai_remove', $t['id']) ?>" onclick="return confirm('Удалить тему?')">Удалить</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <?php
    }

    /* =========================================================
     * ВКЛАДКА: НАСТРОЙКИ
     * ========================================================= */
    public static function render_settings() {
        $s = Zaymi_AI_Writer::get_settings();
        $cats = get_categories(['hide_empty' => false]);
        $authors = get_users(['who' => 'authors', 'capability__in' => ['edit_posts']]);
        if (!$authors) $authors = get_users(['role__in' => ['administrator','editor','author']]);
        ?>
        <form method="post" action="<?= admin_url('admin-post.php') ?>" class="card">
            <?php wp_nonce_field('zaymi_ai_save'); ?>
            <input type="hidden" name="action" value="zaymi_ai_save">

            <h2>⚙ Расписание автопубликации</h2>
            <table class="form-table">
                <tr><th>Автопубликация</th><td>
                    <label><input type="checkbox" name="enabled" value="1" <?= !empty($s['enabled'])?'checked':'' ?>> Включить (крон будет публиковать сам)</label>
                </td></tr>
                <tr><th>Макс статей в день</th><td>
                    <input type="number" name="max_per_day" value="<?= (int)$s['max_per_day'] ?>" min="1" max="50" class="small-text">
                    <p class="description">Жёсткий лимит. После достижения — публикации прекращаются до следующих суток.</p>
                </td></tr>
                <tr><th>Окно публикаций</th><td>
                    с <input type="number" name="hour_from" value="<?= (int)$s['hour_from'] ?>" min="0" max="23" class="small-text">:00
                    до <input type="number" name="hour_to" value="<?= (int)$s['hour_to'] ?>" min="1" max="24" class="small-text">:00
                    <p class="description">Часовой пояс сайта: <?= esc_html(wp_timezone_string()) ?></p>
                </td></tr>
                <tr><th>Минимальный интервал</th><td>
                    <input type="number" name="min_gap_min" value="<?= (int)$s['min_gap_min'] ?>" min="10" max="1440" class="small-text"> мин
                    ± джиттер <input type="number" name="jitter_min" value="<?= (int)$s['jitter_min'] ?>" min="0" max="180" class="small-text"> мин (рандом, чтобы не палиться)
                </td></tr>
                <tr><th>Дни недели</th><td>
                    <?php $names = [1=>'Пн',2=>'Вт',3=>'Ср',4=>'Чт',5=>'Пт',6=>'Сб',7=>'Вс']; ?>
                    <?php foreach ($names as $d => $n): ?>
                        <label style="margin-right:12px"><input type="checkbox" name="days[]" value="<?= $d ?>" <?= in_array($d,(array)$s['days'])?'checked':'' ?>> <?= $n ?></label>
                    <?php endforeach; ?>
                </td></tr>
            </table>

            <h2>📝 Контент</h2>
            <table class="form-table">
                <tr><th>Рубрика</th><td>
                    <select name="category_id">
                        <option value="0">— по умолчанию —</option>
                        <?php foreach ($cats as $c): ?>
                            <option value="<?= $c->term_id ?>" <?= (int)$s['category_id']===$c->term_id?'selected':'' ?>><?= esc_html($c->name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </td></tr>
                <tr><th>Автор</th><td>
                    <select name="author_id">
                        <?php foreach ($authors as $u): ?>
                            <option value="<?= $u->ID ?>" <?= (int)$s['author_id']===$u->ID?'selected':'' ?>><?= esc_html($u->display_name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </td></tr>
                <tr><th>Статус после публикации</th><td>
                    <select name="status_after">
                        <option value="publish" <?= $s['status_after']==='publish'?'selected':'' ?>>Опубликовано</option>
                        <option value="draft"   <?= $s['status_after']==='draft'?'selected':'' ?>>Черновик (на проверку)</option>
                    </select>
                </td></tr>
                <tr><th>Картинки</th><td>
                    <label><input type="checkbox" name="add_hero_image" value="1" <?= !empty($s['add_hero_image'])?'checked':'' ?>> Hero (главная картинка)</label><br>
                    <label><input type="checkbox" name="add_inline_img" value="1" <?= !empty($s['add_inline_img'])?'checked':'' ?>> Иллюстрация в середине статьи</label><br>
                    Провайдер:
                    <select name="image_provider">
                        <option value="auto"     <?= $s['image_provider']==='auto'?'selected':'' ?>>Авто (Unsplash + DALL·E)</option>
                        <option value="openai"   <?= $s['image_provider']==='openai'?'selected':'' ?>>Только DALL·E</option>
                        <option value="unsplash" <?= $s['image_provider']==='unsplash'?'selected':'' ?>>Только Unsplash</option>
                    </select>
                </td></tr>
                <tr><th>Дополнительно</th><td>
                    <label><input type="checkbox" name="mfo_table_in_post" value="1" <?= !empty($s['mfo_table_in_post'])?'checked':'' ?>> Вставлять таблицу с топ-3 МФО</label><br>
                    <label><input type="checkbox" name="related_in_post" value="1" <?= !empty($s['related_in_post'])?'checked':'' ?>> Блок «читайте также»</label><br>
                    <label><input type="checkbox" name="antiAI_rewrite" value="1" <?= !empty($s['antiAI_rewrite'])?'checked':'' ?>> Анти-AI rewrite (второй проход — стоит х2 токенов, но круто помогает от детекта)</label>
                </td></tr>
            </table>
            <p><button class="button button-primary button-large">💾 Сохранить настройки</button></p>
        </form>
        <?php
    }

    /* =========================================================
     * ВКЛАДКА: ЛОГ
     * ========================================================= */
    public static function render_log() {
        $log = Zaymi_AI_Writer::get_log();
        ?>
        <div class="card">
            <h2>📜 Лог последних 200 событий</h2>
            <p>
                <a class="button" href="<?= self::nonce_url('zaymi_ai_clearlog', '') ?>">Очистить лог</a>
            </p>
            <?php if (!$log): ?><p>Лог пуст.</p><?php else: ?>
                <?php foreach ($log as $row): ?>
                    <div class="log-row">
                        <span style="color:#64748b"><?= date_i18n('d.m H:i:s', $row['time']) ?></span>
                        <span class="lvl <?= esc_attr($row['level']) ?>"><?= strtoupper($row['level']) ?></span>
                        <?= esc_html($row['msg']) ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php
    }

    /* =========================================================
     * ВКЛАДКА: ПОМОЩЬ
     * ========================================================= */
    public static function render_help() {
        ?>
        <div class="card">
            <h2>❓ Как пользоваться AI Writer</h2>

            <h3>1. Подключи API-ключи (один раз)</h3>
            <p>Открой <code>wp-config.php</code> на хостинге и добавь над строкой <code>/* That's all, stop editing! */</code>:</p>
<pre style="background:#0f172a;color:#e2e8f0;padding:16px;border-radius:8px;overflow:auto">
define('ZAYMI_OPENAI_KEY',   'sk-proj-...');     // обязательно — текст и DALL·E
define('ZAYMI_OPENAI_MODEL', 'gpt-4o');           // опц., по умолч. gpt-4o
define('ZAYMI_UNSPLASH_KEY', 'твой-unsplash-key'); // опц. — стоковые иллюстрации
</pre>
            <p>OpenAI ключ: <a href="https://platform.openai.com/api-keys" target="_blank">platform.openai.com/api-keys</a><br>
            Unsplash ключ (бесплатно 50 запросов/час): <a href="https://unsplash.com/oauth/applications" target="_blank">unsplash.com/oauth/applications</a></p>

            <h3>2. Настрой расписание</h3>
            <p>Вкладка «Расписание» → задай макс/день, окно времени (например 9-22), интервал. Поставь галку «Включить» — и можно идти спать.</p>

            <h3>3. Залей темы пачкой</h3>
            <p>Вкладка «Очередь» → блок «Массовый импорт». Можно вставить хоть 500 тем за раз — крон будет тянуть случайные и публиковать по расписанию.</p>

            <h3>4. Что делает движок чтобы Яндекс не палил AI:</h3>
            <ul style="list-style:disc;padding-left:24px">
                <li><strong>Двухпроходная генерация</strong> — сначала черновик, потом «человечный» rewrite</li>
                <li><strong>Системный промпт</strong> запрещает классические AI-фразы («в современном мире», «важно отметить» и т.д.)</li>
                <li><strong>Подмешивание реальных данных</strong> из твоей БД МФО — конкретные ставки, суммы, рейтинги</li>
                <li><strong>Случайная структура</strong> — каждая статья получает разное количество H2 и порядок блоков</li>
                <li><strong>Высокая температура</strong> (0.85-0.9) — больше вариативности</li>
                <li><strong>Джиттер времени публикации</strong> — никогда не публикует ровно по часам</li>
                <li><strong>Внутренняя перелинковка</strong> на твои МФО и соседние статьи (через шорткод)</li>
            </ul>

            <h3>5. Проверка работы крона</h3>
            <p>WordPress-крон работает только когда на сайт заходят. Если хочешь публикации даже ночью — попроси хостера настроить системный cron на:</p>
<pre style="background:#0f172a;color:#e2e8f0;padding:12px;border-radius:8px">*/5 * * * * wget -q -O - <?= esc_html(home_url('/wp-cron.php?doing_wp_cron')) ?> &gt;/dev/null 2&gt;&amp;1</pre>

            <h3>6. Тонкая настройка</h3>
            <p>Если статьи получаются не в твоём стиле — открой <code>thema/zaymi-online/inc/ai-writer-core.php</code>, найди методы <code>build_system_prompt()</code> и <code>build_user_prompt()</code> — там промпты, можно править под себя.</p>
        </div>
        <?php
    }

    /* =========================================================
     * HANDLERS
     * ========================================================= */
    private static function nonce_url($action, $id) {
        return wp_nonce_url(admin_url('admin-post.php?action=' . $action . '&id=' . urlencode($id)), $action);
    }

    public static function handle_add() {
        check_admin_referer('zaymi_ai_add');
        if (!current_user_can('manage_options')) wp_die('forbidden');
        Zaymi_AI_Writer::add_topic($_POST['title'] ?? '', [
            'type'     => sanitize_key($_POST['type'] ?? 'guide'),
            'keywords' => sanitize_text_field($_POST['keywords'] ?? ''),
            'city'     => sanitize_text_field($_POST['city'] ?? ''),
        ]);
        wp_safe_redirect(admin_url('admin.php?page=zaymi-ai&tab=queue&added=1'));
        exit;
    }

    public static function handle_bulk() {
        check_admin_referer('zaymi_ai_bulk');
        if (!current_user_can('manage_options')) wp_die('forbidden');
        $lines = preg_split('/\r\n|\r|\n/', $_POST['bulk'] ?? '');
        $n = Zaymi_AI_Writer::add_topics_bulk($lines);
        wp_safe_redirect(admin_url('admin.php?page=zaymi-ai&tab=queue&bulk=' . $n));
        exit;
    }

    public static function handle_remove() {
        $id = $_GET['id'] ?? '';
        check_admin_referer('zaymi_ai_remove');
        if (!current_user_can('manage_options')) wp_die('forbidden');
        Zaymi_AI_Writer::remove_topic($id);
        wp_safe_redirect(admin_url('admin.php?page=zaymi-ai&tab=queue'));
        exit;
    }

    public static function handle_run() {
        $id = $_GET['id'] ?? '';
        check_admin_referer('zaymi_ai_run');
        if (!current_user_can('manage_options')) wp_die('forbidden');
        try {
            $post_id = Zaymi_AI_Writer::generate_post($id);
            wp_safe_redirect(admin_url('admin.php?page=zaymi-ai&tab=queue&done=' . $post_id));
        } catch (Exception $e) {
            Zaymi_AI_Writer::update_topic($id, ['status' => 'failed', 'error' => $e->getMessage()]);
            Zaymi_AI_Writer::log('error', '✗ Генерация: ' . $e->getMessage(), ['topic' => $id]);
            wp_safe_redirect(admin_url('admin.php?page=zaymi-ai&tab=queue&err=' . urlencode($e->getMessage())));
        }
        exit;
    }

    public static function handle_save() {
        check_admin_referer('zaymi_ai_save');
        if (!current_user_can('manage_options')) wp_die('forbidden');
        $defaults = Zaymi_AI_Writer::default_settings();
        $s = [
            'enabled'        => !empty($_POST['enabled']) ? 1 : 0,
            'max_per_day'    => max(1, (int)($_POST['max_per_day'] ?? 3)),
            'hour_from'      => max(0, min(23, (int)($_POST['hour_from'] ?? 9))),
            'hour_to'        => max(1, min(24, (int)($_POST['hour_to'] ?? 22))),
            'min_gap_min'    => max(10, (int)($_POST['min_gap_min'] ?? 90)),
            'jitter_min'     => max(0, (int)($_POST['jitter_min'] ?? 45)),
            'days'           => array_map('intval', (array)($_POST['days'] ?? [1,2,3,4,5,6,7])),
            'category_id'    => (int)($_POST['category_id'] ?? 0),
            'author_id'      => (int)($_POST['author_id'] ?? 1),
            'status_after'   => in_array($_POST['status_after']??'',['publish','draft'])?$_POST['status_after']:'publish',
            'add_hero_image' => !empty($_POST['add_hero_image']) ? 1 : 0,
            'add_inline_img' => !empty($_POST['add_inline_img']) ? 1 : 0,
            'image_provider' => in_array($_POST['image_provider']??'',['auto','openai','unsplash'])?$_POST['image_provider']:'auto',
            'mfo_table_in_post' => !empty($_POST['mfo_table_in_post']) ? 1 : 0,
            'related_in_post'   => !empty($_POST['related_in_post']) ? 1 : 0,
            'antiAI_rewrite'    => !empty($_POST['antiAI_rewrite']) ? 1 : 0,
        ];
        Zaymi_AI_Writer::save_settings(wp_parse_args($s, $defaults));
        wp_safe_redirect(admin_url('admin.php?page=zaymi-ai&tab=settings&saved=1'));
        exit;
    }

    public static function handle_clearlog() {
        check_admin_referer('zaymi_ai_clearlog');
        if (!current_user_can('manage_options')) wp_die('forbidden');
        Zaymi_AI_Writer::clear_log();
        wp_safe_redirect(admin_url('admin.php?page=zaymi-ai&tab=log'));
        exit;
    }
}

Zaymi_AI_Writer_Admin::init();
