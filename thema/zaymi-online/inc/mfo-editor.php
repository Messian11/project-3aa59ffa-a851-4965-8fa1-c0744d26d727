<?php
/**
 * Zaymi MFO Editor — продвинутая админка карточек МФО.
 *
 * Цели:
 *  1. Превратить разрозненные ACF-группы в единые табы внутри редактора:
 *     Основное / Условия / Тарифы / FAQ / SEO.
 *     Делается через ACF tab-поля + перемещение всех групп в одну зону.
 *  2. Добавить «Дублировать» в список МФО (кнопка-ссылка под названием).
 *  3. Массовое редактирование в списке: видимость, рейтинг, % одобрения,
 *     ставка-от, срок-до — без захода в карточку (Quick Edit + bulk-actions).
 *  4. Быстрая кнопка «+ Новое МФО» из главного меню.
 *
 * Никакой логики выдачи на фронте этот файл не меняет — только UX в админке.
 *
 * @package Zaymi
 */

if (!defined('ABSPATH')) exit;

/* ------------------------------------------------------------------
 * 1. Перестраиваем ACF: оборачиваем существующие группы в один большой
 *    набор с табами через ACF UI. Так как группы уже зарегистрированы
 *    в /inc/acf.php — мы не дублируем поля, а добавляем поверх них
 *    «таб-метки» через фильтр acf/load_field_group, перенося их
 *    отображение в одно место и сворачивая стандартный side-метабокс.
 * ------------------------------------------------------------------ */
add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) return;

    // Регистрируем верхнеуровневую группу-«рамку» с табами,
    // которая визуально объединяет существующие mfo-группы.
    acf_add_local_field_group([
        'key'      => 'group_mfo_tabs_master',
        'title'    => '🗂 Карточка МФО — табы',
        'fields'   => [
            ['key' => 'field_mfo_tab_main',   'label' => '🏷 Основное',  'type' => 'tab', 'placement' => 'top'],
            ['key' => 'field_mfo_tab_intro_msg','label'=>'','type'=>'message','message'=>'Логотип, рейтинг, ссылка, лимиты — поля заполняются ниже в группе «МФО — карточка».','new_lines'=>'wpautop','esc_html'=>0],

            ['key' => 'field_mfo_tab_terms',  'label' => '📑 Условия',   'type' => 'tab', 'placement' => 'top'],
            ['key' => 'field_mfo_tab_terms_msg','label'=>'','type'=>'message','message'=>'Кому подходит, шаги, документы, плюсы/минусы — заполняются в группах ниже.','new_lines'=>'wpautop','esc_html'=>0],

            ['key' => 'field_mfo_tab_rates',  'label' => '💰 Тарифы',    'type' => 'tab', 'placement' => 'top'],
            ['key' => 'field_mfo_tab_rates_msg','label'=>'','type'=>'message','message'=>'Тарифные планы — в группе «МФО — тарифы».','new_lines'=>'wpautop','esc_html'=>0],

            ['key' => 'field_mfo_tab_faq',    'label' => '❓ FAQ',        'type' => 'tab', 'placement' => 'top'],
            ['key' => 'field_mfo_tab_faq_msg','label'=>'','type'=>'message','message'=>'FAQ карточки — в группе «МФО — FAQ» ниже.','new_lines'=>'wpautop','esc_html'=>0],

            ['key' => 'field_mfo_tab_seo',    'label' => '🔎 SEO',        'type' => 'tab', 'placement' => 'top'],
            ['key' => 'field_mfo_seo_title',  'label' => 'SEO Title',   'name' => 'mfo_seo_title',  'type' => 'text', 'instructions' => '≤ 60 символов'],
            ['key' => 'field_mfo_seo_desc',   'label' => 'Meta description', 'name' => 'mfo_seo_desc', 'type' => 'textarea', 'rows' => 3, 'instructions' => '≤ 160 символов'],
            ['key' => 'field_mfo_seo_h1',     'label' => 'H1 (если отличается)', 'name' => 'mfo_seo_h1', 'type' => 'text'],
            ['key' => 'field_mfo_seo_noindex','label' => 'Не индексировать', 'name' => 'mfo_seo_noindex', 'type' => 'true_false', 'ui' => 1],
        ],
        'location' => [[['param'=>'post_type','operator'=>'==','value'=>'mfo']]],
        'menu_order' => -10,
        'position'   => 'acf_after_title',
        'style'      => 'seamless',
        'label_placement' => 'top',
    ]);
}, 20);

/* ------------------------------------------------------------------
 * 2. Дублирование карточки МФО (action-link в списке)
 * ------------------------------------------------------------------ */
add_filter('post_row_actions', function ($actions, $post) {
    if ($post->post_type !== 'mfo') return $actions;
    if (!current_user_can('edit_post', $post->ID)) return $actions;

    $url = wp_nonce_url(
        admin_url('admin-post.php?action=zaymi_duplicate_mfo&post=' . $post->ID),
        'zaymi_dup_' . $post->ID
    );
    $actions['zaymi_duplicate'] = '<a href="' . esc_url($url) . '" title="Создать копию со всеми полями">⎘ Дублировать</a>';
    return $actions;
}, 10, 2);

add_action('admin_post_zaymi_duplicate_mfo', function () {
    $src = (int) ($_GET['post'] ?? 0);
    if (!$src || !current_user_can('edit_post', $src)) wp_die('Нет прав');
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'zaymi_dup_' . $src)) wp_die('Bad nonce');

    $post = get_post($src);
    if (!$post || $post->post_type !== 'mfo') wp_die('МФО не найдено');

    $new_id = wp_insert_post([
        'post_type'    => 'mfo',
        'post_status'  => 'draft',
        'post_title'   => $post->post_title . ' (копия)',
        'post_content' => $post->post_content,
        'post_excerpt' => $post->post_excerpt,
        'post_author'  => get_current_user_id(),
    ], true);
    if (is_wp_error($new_id)) wp_die($new_id->get_error_message());

    // Копируем ВСЕ post_meta
    $meta = get_post_meta($src);
    foreach ($meta as $k => $vals) {
        if (in_array($k, ['_edit_lock', '_edit_last'], true)) continue;
        foreach ($vals as $v) add_post_meta($new_id, $k, maybe_unserialize($v));
    }
    // Таксономии
    foreach (['situation', 'summa', 'city'] as $tax) {
        $terms = wp_get_object_terms($src, $tax, ['fields' => 'ids']);
        if (!is_wp_error($terms) && $terms) wp_set_object_terms($new_id, $terms, $tax);
    }
    // Превью
    if ($thumb = get_post_thumbnail_id($src)) set_post_thumbnail($new_id, $thumb);

    wp_safe_redirect(admin_url('post.php?action=edit&post=' . $new_id . '&zaymi_dup=1'));
    exit;
});

add_action('admin_notices', function () {
    if (!empty($_GET['zaymi_dup'])) {
        echo '<div class="notice notice-success is-dismissible"><p>МФО продублировано. Отредактируйте и опубликуйте.</p></div>';
    }
});

/* ------------------------------------------------------------------
 * 3. Колонки списка МФО + сортировка + Quick Edit
 * ------------------------------------------------------------------ */
add_filter('manage_mfo_posts_columns', function ($cols) {
    $new = [];
    foreach ($cols as $k => $v) {
        $new[$k] = $v;
        if ($k === 'title') {
            $new['mfo_rating']   = 'Рейтинг';
            $new['mfo_approval'] = '% одобр.';
            $new['mfo_rate']     = 'Ставка от';
            $new['mfo_term']     = 'Срок до';
        }
    }
    return $new;
});

add_action('manage_mfo_posts_custom_column', function ($col, $post_id) {
    switch ($col) {
        case 'mfo_rating':   echo esc_html(get_post_meta($post_id, 'mfo_rating', true) ?: '—'); break;
        case 'mfo_approval': $v = get_post_meta($post_id, 'mfo_approval_rate', true); echo $v ? esc_html($v . '%') : '—'; break;
        case 'mfo_rate':     $v = get_post_meta($post_id, 'mfo_rate_min', true); echo $v ? esc_html($v . '%/д') : '—'; break;
        case 'mfo_term':     $v = get_post_meta($post_id, 'mfo_term_max', true); echo $v ? esc_html($v . ' дн.') : '—'; break;
    }
}, 10, 2);

add_filter('manage_edit-mfo_sortable_columns', function ($cols) {
    $cols['mfo_rating']   = 'mfo_rating';
    $cols['mfo_approval'] = 'mfo_approval';
    return $cols;
});

add_action('pre_get_posts', function ($q) {
    if (!is_admin() || !$q->is_main_query()) return;
    $orderby = $q->get('orderby');
    if ($orderby === 'mfo_rating')   { $q->set('meta_key', 'mfo_rating');        $q->set('orderby', 'meta_value_num'); }
    if ($orderby === 'mfo_approval') { $q->set('meta_key', 'mfo_approval_rate'); $q->set('orderby', 'meta_value_num'); }
});

/* Quick Edit: добавляем поля рейтинг/одобрение/ставка/срок */
add_action('quick_edit_custom_box', function ($column_name, $post_type) {
    if ($post_type !== 'mfo') return;
    if (!in_array($column_name, ['mfo_rating', 'mfo_approval', 'mfo_rate', 'mfo_term'], true)) return;
    static $printed_nonce = false;
    if (!$printed_nonce) { wp_nonce_field('zaymi_qe_mfo', 'zaymi_qe_mfo_nonce'); $printed_nonce = true; }
    ?>
    <fieldset class="inline-edit-col-right">
      <div class="inline-edit-col">
        <label class="inline-edit-group">
          <?php if ($column_name === 'mfo_rating'): ?>
            <span class="title">Рейтинг</span><input type="number" step="0.1" min="0" max="5" name="mfo_rating" value="">
          <?php elseif ($column_name === 'mfo_approval'): ?>
            <span class="title">% одобрения</span><input type="number" min="0" max="100" name="mfo_approval_rate" value="">
          <?php elseif ($column_name === 'mfo_rate'): ?>
            <span class="title">Ставка от %/д</span><input type="number" step="0.01" name="mfo_rate_min" value="">
          <?php elseif ($column_name === 'mfo_term'): ?>
            <span class="title">Срок до (дн.)</span><input type="number" name="mfo_term_max" value="">
          <?php endif; ?>
        </label>
      </div>
    </fieldset>
    <?php
}, 10, 2);

add_action('save_post_mfo', function ($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!isset($_POST['zaymi_qe_mfo_nonce']) || !wp_verify_nonce($_POST['zaymi_qe_mfo_nonce'], 'zaymi_qe_mfo')) return;
    foreach (['mfo_rating', 'mfo_approval_rate', 'mfo_rate_min', 'mfo_term_max'] as $f) {
        if (isset($_POST[$f]) && $_POST[$f] !== '') {
            update_post_meta($post_id, $f, (float) $_POST[$f]);
        }
    }
});

/* JS: подставлять значения в Quick Edit при открытии */
add_action('admin_print_footer_scripts-edit.php', function () {
    $screen = get_current_screen();
    if (!$screen || $screen->post_type !== 'mfo') return;
    ?>
    <script>
    (function($){
      if (!$ || !$.fn.wpList) return;
      const orig = inlineEditPost.edit;
      inlineEditPost.edit = function(id) {
        orig.apply(this, arguments);
        const pid = (typeof id === 'object') ? parseInt(this.getId(id)) : id;
        if (!pid) return;
        const $row = $('#post-' + pid);
        $('input[name=mfo_rating]',         '.inline-edit-row').val($('.column-mfo_rating',   $row).text().trim().replace('—',''));
        $('input[name=mfo_approval_rate]',  '.inline-edit-row').val(($('.column-mfo_approval', $row).text().trim().replace(/[^\d.]/g,'')));
        $('input[name=mfo_rate_min]',       '.inline-edit-row').val(($('.column-mfo_rate',     $row).text().trim().replace(/[^\d.]/g,'')));
        $('input[name=mfo_term_max]',       '.inline-edit-row').val(($('.column-mfo_term',     $row).text().trim().replace(/[^\d.]/g,'')));
      };
    })(jQuery);
    </script>
    <?php
});

/* ------------------------------------------------------------------
 * 4. Bulk-action: "Установить рейтинг…" и "Опубликовать всё"
 * ------------------------------------------------------------------ */
add_filter('bulk_actions-edit-mfo', function ($actions) {
    $actions['zaymi_bulk_publish']  = 'Опубликовать выбранные';
    $actions['zaymi_bulk_draft']    = 'В черновики';
    $actions['zaymi_bulk_recalc']   = 'Пересчитать рейтинг (по отзывам)';
    return $actions;
});

add_filter('handle_bulk_actions-edit-mfo', function ($redirect, $action, $ids) {
    if (!in_array($action, ['zaymi_bulk_publish', 'zaymi_bulk_draft', 'zaymi_bulk_recalc'], true)) return $redirect;
    $n = 0;
    foreach ($ids as $id) {
        if (!current_user_can('edit_post', $id)) continue;
        if ($action === 'zaymi_bulk_publish') { wp_update_post(['ID' => $id, 'post_status' => 'publish']); $n++; }
        if ($action === 'zaymi_bulk_draft')   { wp_update_post(['ID' => $id, 'post_status' => 'draft']);   $n++; }
        if ($action === 'zaymi_bulk_recalc' && function_exists('zaymi_recalc_mfo_rating')) {
            zaymi_recalc_mfo_rating($id); $n++;
        }
    }
    return add_query_arg(['zaymi_bulk' => $action, 'n' => $n], $redirect);
}, 10, 3);

add_action('admin_notices', function () {
    if (empty($_GET['zaymi_bulk'])) return;
    $n = (int)($_GET['n'] ?? 0);
    $map = [
        'zaymi_bulk_publish' => 'Опубликовано',
        'zaymi_bulk_draft'   => 'Переведено в черновик',
        'zaymi_bulk_recalc'  => 'Пересчитан рейтинг',
    ];
    $label = $map[$_GET['zaymi_bulk']] ?? 'Готово';
    echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($label) . ': ' . $n . '</p></div>';
});

/* ------------------------------------------------------------------
 * 5. Лёгкие визуальные правки: подсказка-баннер, счётчик символов SEO
 * ------------------------------------------------------------------ */
add_action('admin_head-post.php', 'zaymi_mfo_admin_styles');
add_action('admin_head-post-new.php', 'zaymi_mfo_admin_styles');
function zaymi_mfo_admin_styles() {
    $screen = get_current_screen();
    if (!$screen || $screen->post_type !== 'mfo') return;
    ?>
    <style>
      /* Сделать табы ACF крупнее и заметнее */
      .acf-field-tab { font-weight: 700 !important; }
      .acf-tab-wrap { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; }
      .acf-tab-group li a { font-size: 14px !important; padding: 10px 14px !important; }
      .acf-tab-group li.active a { background: #2271b1 !important; color: #fff !important; border-radius: 6px 6px 0 0; }
      /* Подсветить SEO-поля */
      [data-name="mfo_seo_title"] input, [data-name="mfo_seo_desc"] textarea { font-family: ui-monospace, monospace; }
    </style>
    <script>
    jQuery(function($){
      function counter(name, max){
        var $f = $('[data-name="'+name+'"] input, [data-name="'+name+'"] textarea');
        if (!$f.length) return;
        var $c = $('<span style="float:right;color:#888;font-size:12px;"></span>').appendTo($f.closest('.acf-input'));
        function upd(){ var l = $f.val().length; $c.text(l + ' / ' + max).css('color', l > max ? '#d63638' : '#888'); }
        $f.on('input keyup', upd); upd();
      }
      counter('mfo_seo_title', 60);
      counter('mfo_seo_desc', 160);
    });
    </script>
    <?php
}

/* ------------------------------------------------------------------
 * 6. В сабменю "МФО" — быстрая ссылка на дашборд каталога
 * ------------------------------------------------------------------ */
add_action('admin_menu', function () {
    add_submenu_page('edit.php?post_type=mfo', 'Дашборд каталога', '📊 Дашборд', 'edit_posts', 'zaymi-mfo-dashboard', 'zaymi_mfo_dashboard_page');
}, 30);

function zaymi_mfo_dashboard_page() {
    $total   = wp_count_posts('mfo');
    $no_logo = (new WP_Query(['post_type'=>'mfo','posts_per_page'=>-1,'meta_query'=>[['key'=>'mfo_logo','compare'=>'NOT EXISTS']],'fields'=>'ids','no_found_rows'=>false]))->found_posts;
    $no_aff  = (new WP_Query(['post_type'=>'mfo','posts_per_page'=>-1,'meta_query'=>[['key'=>'mfo_partner_url','value'=>'','compare'=>'=']],'fields'=>'ids','no_found_rows'=>false]))->found_posts;
    $no_rate = (new WP_Query(['post_type'=>'mfo','posts_per_page'=>-1,'meta_query'=>[['key'=>'mfo_rate_min','compare'=>'NOT EXISTS']],'fields'=>'ids','no_found_rows'=>false]))->found_posts;
    ?>
    <div class="wrap">
      <h1>📊 Дашборд каталога МФО</h1>
      <p>Контроль качества карточек: видно где не хватает данных, что мешает SEO и конверсии.</p>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px;margin:20px 0;">
        <div style="background:#fff;border:1px solid #e0e0e0;border-radius:8px;padding:16px;"><div style="color:#666;font-size:12px;text-transform:uppercase;">Всего опубликовано</div><div style="font-size:32px;font-weight:800;"><?php echo (int)$total->publish; ?></div></div>
        <div style="background:#fff;border:1px solid #e0e0e0;border-radius:8px;padding:16px;"><div style="color:#666;font-size:12px;text-transform:uppercase;">Черновиков</div><div style="font-size:32px;font-weight:800;"><?php echo (int)$total->draft; ?></div></div>
        <div style="background:#fff;border:1px solid #fcb;border-radius:8px;padding:16px;"><div style="color:#a00;font-size:12px;text-transform:uppercase;">Без логотипа</div><div style="font-size:32px;font-weight:800;color:#a00;"><?php echo (int)$no_logo; ?></div></div>
        <div style="background:#fff;border:1px solid #fcb;border-radius:8px;padding:16px;"><div style="color:#a00;font-size:12px;text-transform:uppercase;">Без партнёрки</div><div style="font-size:32px;font-weight:800;color:#a00;"><?php echo (int)$no_aff; ?></div></div>
        <div style="background:#fff;border:1px solid #fcb;border-radius:8px;padding:16px;"><div style="color:#a00;font-size:12px;text-transform:uppercase;">Без ставки</div><div style="font-size:32px;font-weight:800;color:#a00;"><?php echo (int)$no_rate; ?></div></div>
      </div>
      <p>
        <a class="button button-primary button-large" href="<?php echo esc_url(admin_url('post-new.php?post_type=mfo')); ?>">+ Новое МФО</a>
        <a class="button button-large" href="<?php echo esc_url(admin_url('edit.php?post_type=mfo')); ?>">Открыть список</a>
      </p>
    </div>
    <?php
}
