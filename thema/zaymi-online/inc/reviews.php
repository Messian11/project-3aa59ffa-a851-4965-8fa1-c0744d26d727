<?php
/**
 * Zaymi Online — Reviews module (v2.2)
 * Отзывы по МФО: своя таблица, шорткод формы и списка, модерация, агрегатный рейтинг,
 * Schema.org Review/AggregateRating, REST-эндпоинт для отправки.
 *
 * @package Zaymi
 */
if (!defined('ABSPATH')) exit;

/* ---------- 1. Таблица отзывов ---------- */
function zaymi_reviews_install() {
    global $wpdb;
    $table = $wpdb->prefix . 'zaymi_reviews';
    $charset = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        mfo_id BIGINT UNSIGNED NOT NULL,
        author VARCHAR(120) NOT NULL,
        city VARCHAR(120) DEFAULT '',
        rating TINYINT UNSIGNED NOT NULL DEFAULT 5,
        text TEXT NOT NULL,
        ip VARBINARY(16) NULL,
        status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
        created_at DATETIME NOT NULL,
        PRIMARY KEY (id),
        KEY mfo (mfo_id),
        KEY status (status)
    ) $charset;";
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}
add_action('after_switch_theme', 'zaymi_reviews_install');

/* ---------- 2. Helper: агрегат рейтинга ---------- */
function zaymi_reviews_aggregate($mfo_id) {
    global $wpdb;
    $t = $wpdb->prefix . 'zaymi_reviews';
    $row = $wpdb->get_row($wpdb->prepare(
        "SELECT COUNT(*) c, AVG(rating) avg FROM $t WHERE mfo_id=%d AND status='approved'",
        $mfo_id
    ));
    return ['count' => (int)($row->c ?? 0), 'avg' => round((float)($row->avg ?? 0), 1)];
}

/* ---------- 3. REST: приём отзыва ---------- */
add_action('rest_api_init', function () {
    register_rest_route('zaymi/v1', '/review', [
        'methods'  => 'POST',
        'permission_callback' => '__return_true',
        'callback' => function (WP_REST_Request $r) {
            global $wpdb;
            $mfo    = (int) $r->get_param('mfo_id');
            $author = sanitize_text_field($r->get_param('author') ?: 'Аноним');
            $city   = sanitize_text_field($r->get_param('city'));
            $rating = max(1, min(5, (int) $r->get_param('rating')));
            $text   = wp_kses_post($r->get_param('text'));
            $hp     = $r->get_param('website'); // honeypot
            if ($hp || !$mfo || mb_strlen($text) < 10) {
                return new WP_REST_Response(['ok' => false, 'error' => 'invalid'], 400);
            }
            // антифлуд: 1 отзыв с IP в час
            $ip = $_SERVER['REMOTE_ADDR'] ?? '';
            $bin = $ip ? @inet_pton($ip) : null;
            if ($bin) {
                $t = $wpdb->prefix . 'zaymi_reviews';
                $recent = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM $t WHERE ip=%s AND created_at > (NOW() - INTERVAL 1 HOUR)",
                    $bin
                ));
                if ($recent > 0) return new WP_REST_Response(['ok'=>false,'error'=>'rate_limit'], 429);
            }
            $wpdb->insert($wpdb->prefix . 'zaymi_reviews', [
                'mfo_id' => $mfo, 'author' => $author, 'city' => $city,
                'rating' => $rating, 'text' => $text, 'ip' => $bin,
                'status' => 'pending', 'created_at' => current_time('mysql'),
            ]);
            return ['ok' => true, 'message' => 'Отзыв отправлен на модерацию'];
        },
    ]);
});

/* ---------- 4. Шорткоды ---------- */
add_shortcode('zaymi_reviews', function ($atts) {
    $a = shortcode_atts(['mfo_id' => get_the_ID(), 'limit' => 10], $atts);
    global $wpdb;
    $t = $wpdb->prefix . 'zaymi_reviews';
    $rows = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $t WHERE mfo_id=%d AND status='approved' ORDER BY created_at DESC LIMIT %d",
        $a['mfo_id'], $a['limit']
    ));
    $agg = zaymi_reviews_aggregate($a['mfo_id']);
    ob_start(); ?>
    <div class="zaymi-reviews">
        <div class="zaymi-reviews__head">
            <div class="zaymi-reviews__rating">
                <span class="zaymi-reviews__rating-num"><?php echo $agg['avg']; ?></span>
                <span class="zaymi-reviews__stars" data-rating="<?php echo $agg['avg']; ?>">★★★★★</span>
                <span class="zaymi-reviews__count"><?php echo $agg['count']; ?> отзывов</span>
            </div>
        </div>
        <ul class="zaymi-reviews__list">
            <?php foreach ($rows as $r): ?>
            <li class="zaymi-reviews__item">
                <div class="zaymi-reviews__meta">
                    <strong><?php echo esc_html($r->author); ?></strong>
                    <?php if ($r->city): ?><span><?php echo esc_html($r->city); ?></span><?php endif; ?>
                    <span class="zaymi-reviews__stars" data-rating="<?php echo (int)$r->rating; ?>">★★★★★</span>
                </div>
                <p><?php echo wp_kses_post($r->text); ?></p>
                <time><?php echo esc_html(date_i18n('j F Y', strtotime($r->created_at))); ?></time>
            </li>
            <?php endforeach; ?>
            <?php if (!$rows): ?><li class="zaymi-reviews__empty">Будьте первым — оставьте отзыв.</li><?php endif; ?>
        </ul>
    </div>
    <?php return ob_get_clean();
});

add_shortcode('zaymi_review_form', function ($atts) {
    $a = shortcode_atts(['mfo_id' => get_the_ID()], $atts);
    ob_start(); ?>
    <form class="zaymi-review-form" data-mfo="<?php echo (int)$a['mfo_id']; ?>">
        <h3>Оставить отзыв</h3>
        <div class="zaymi-review-form__row">
            <input type="text" name="author" placeholder="Ваше имя" required>
            <input type="text" name="city" placeholder="Город">
        </div>
        <div class="zaymi-review-form__rating">
            <?php for ($i=5;$i>=1;$i--): ?>
                <input type="radio" name="rating" id="zr<?php echo $i;?>" value="<?php echo $i;?>" <?php echo $i===5?'checked':'';?>>
                <label for="zr<?php echo $i;?>">★</label>
            <?php endfor; ?>
        </div>
        <textarea name="text" placeholder="Ваш опыт с этой МФО..." minlength="10" required></textarea>
        <input type="text" name="website" class="zaymi-hp" tabindex="-1" autocomplete="off">
        <button type="submit">Отправить</button>
        <div class="zaymi-review-form__msg"></div>
    </form>
    <?php return ob_get_clean();
});

/* ---------- 5. Schema.org AggregateRating + Review ---------- */
add_filter('zaymi_schema_extra', function ($schema, $post_id) {
    if (get_post_type($post_id) !== 'mfo') return $schema;
    $agg = zaymi_reviews_aggregate($post_id);
    if ($agg['count'] > 0) {
        $schema['aggregateRating'] = [
            '@type' => 'AggregateRating',
            'ratingValue' => $agg['avg'],
            'reviewCount' => $agg['count'],
            'bestRating'  => 5,
        ];
    }
    return $schema;
}, 10, 2);

/* ---------- 6. Админ: модерация ---------- */
add_action('admin_menu', function () {
    add_submenu_page('zaymi-leads', 'Отзывы', '⭐ Отзывы', 'manage_options', 'zaymi-reviews', 'zaymi_reviews_admin_page');
});
function zaymi_reviews_admin_page() {
    global $wpdb;
    $t = $wpdb->prefix . 'zaymi_reviews';
    if (isset($_GET['act'], $_GET['id']) && check_admin_referer('zaymi_review_act')) {
        $id = (int)$_GET['id']; $act = $_GET['act'];
        if ($act === 'approve') $wpdb->update($t, ['status'=>'approved'], ['id'=>$id]);
        if ($act === 'reject')  $wpdb->update($t, ['status'=>'rejected'], ['id'=>$id]);
        if ($act === 'delete')  $wpdb->delete($t, ['id'=>$id]);
        echo '<div class="notice notice-success"><p>Готово.</p></div>';
    }
    $filter = $_GET['st'] ?? 'pending';
    $rows = $wpdb->get_results($wpdb->prepare("SELECT * FROM $t WHERE status=%s ORDER BY created_at DESC LIMIT 200", $filter));
    echo '<div class="wrap"><h1>Отзывы</h1>';
    foreach (['pending'=>'На модерации','approved'=>'Одобренные','rejected'=>'Отклонённые'] as $k=>$lbl) {
        $cur = $k===$filter ? 'style="font-weight:bold"' : '';
        echo "<a href='?page=zaymi-reviews&st=$k' $cur>$lbl</a> &nbsp;|&nbsp; ";
    }
    echo '<table class="widefat striped" style="margin-top:15px"><thead><tr><th>Дата</th><th>МФО</th><th>Автор</th><th>★</th><th>Текст</th><th>Действия</th></tr></thead><tbody>';
    foreach ($rows as $r) {
        $mfo = get_the_title($r->mfo_id);
        $nonce = wp_create_nonce('zaymi_review_act');
        echo '<tr>';
        echo '<td>'.esc_html($r->created_at).'</td>';
        echo '<td>'.esc_html($mfo).'</td>';
        echo '<td>'.esc_html($r->author).' '.esc_html($r->city).'</td>';
        echo '<td>'.(int)$r->rating.'</td>';
        echo '<td>'.esc_html(mb_substr($r->text,0,200)).'</td>';
        echo '<td>';
        echo "<a href='?page=zaymi-reviews&st=$filter&act=approve&id=$r->id&_wpnonce=$nonce'>✅</a> ";
        echo "<a href='?page=zaymi-reviews&st=$filter&act=reject&id=$r->id&_wpnonce=$nonce'>❌</a> ";
        echo "<a href='?page=zaymi-reviews&st=$filter&act=delete&id=$r->id&_wpnonce=$nonce' onclick='return confirm(\"Удалить?\")'>🗑</a>";
        echo '</td></tr>';
    }
    echo '</tbody></table></div>';
}
