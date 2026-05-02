<?php
/**
 * Шаблон страницы блога (Settings → Reading → Posts page).
 * Также используется для category.php и archive.php.
 *
 * Структура 1-в-1 с превью /blog (blog.index.tsx):
 *  Breadcrumbs → Hero (с поиском) → Категории-табы (sticky) →
 *  Featured post (только без фильтра) → Сетка статей → Пагинация.
 *
 * @package Zaymi
 */
if (!defined('ABSPATH')) exit;
get_header();

$is_cat   = is_category();
$is_tag   = is_tag();
$cur_cat  = $is_cat ? get_queried_object() : null;
$active_label = $is_cat ? $cur_cat->name : ($is_tag ? get_queried_object()->name : 'Все');
$search_q = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

// Категории для табов
$all_cats = get_categories(['hide_empty' => true]);

// Featured: последний пост (показываем только если фильтра нет)
$featured = (!$is_cat && !$is_tag && !$search_q && !is_paged())
    ? get_posts(['posts_per_page' => 1, 'post_status' => 'publish'])
    : [];
$featured_id = $featured ? $featured[0]->ID : 0;

// Основная выборка
$paged = max(1, get_query_var('paged') ?: get_query_var('page') ?: 1);
$args  = [
    'post_type'      => 'post',
    'posts_per_page' => 9,
    'paged'          => $paged,
    'post_status'    => 'publish',
];
if ($featured_id) $args['post__not_in'] = [$featured_id];
if ($is_cat) $args['cat'] = $cur_cat->term_id;
if ($is_tag) $args['tag_id'] = get_queried_object()->term_id;
if ($search_q) $args['s'] = $search_q;
$q = new WP_Query($args);

// Утилита градиента
function zaymi_blog_gradient($i) {
    $g = ['from-blue-500 to-emerald-500', 'from-amber-500 to-emerald-500', 'from-blue-500 to-blue-400',
          'from-emerald-500 to-emerald-400', 'from-amber-500 to-blue-500', 'from-blue-500 to-amber-500',
          'from-emerald-500 to-blue-500', 'from-amber-500 to-amber-400', 'from-blue-500 to-green-500'];
    return $g[$i % count($g)];
}
?>

<!-- Breadcrumbs -->
<nav class="zo-blog-crumbs" aria-label="Хлебные крошки">
  <div class="zo-container-wide">
    <ol>
      <li><a href="<?php echo esc_url(home_url('/')); ?>">Главная</a></li>
      <li><span class="sep">›</span></li>
      <?php if ($is_cat || $is_tag): ?>
        <li><a href="<?php echo esc_url(get_post_type_archive_link('post') ?: home_url('/blog/')); ?>">Блог</a></li>
        <li><span class="sep">›</span></li>
        <li class="current"><?php echo esc_html($active_label); ?></li>
      <?php else: ?>
        <li class="current">Блог</li>
      <?php endif; ?>
    </ol>
  </div>
</nav>

<!-- Hero -->
<section class="zo-blog-hero">
  <div class="zo-container-wide">
    <span class="kicker">📖 Блог</span>
    <h1>
      Полезные статьи <br>
      <span class="grad">о займах</span>
    </h1>
    <p>Гайды, инструкции, обзоры МФО и финансовая грамотность — от экспертов Zaymi Online.</p>
    <form class="zo-blog-search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
      <span class="ico">🔍</span>
      <input type="search" name="s" value="<?php echo esc_attr($search_q); ?>" placeholder="Поиск по статьям…" aria-label="Поиск">
      <input type="hidden" name="post_type" value="post">
    </form>
  </div>
</section>

<!-- Category tabs -->
<section class="zo-blog-tabs">
  <div class="zo-container-wide">
    <div class="tabs-scroll">
      <a href="<?php echo esc_url(get_post_type_archive_link('post') ?: home_url('/blog/')); ?>"
         class="tab <?php echo (!$is_cat && !$is_tag) ? 'active' : ''; ?>">✨ Все</a>
      <?php foreach ($all_cats as $c): ?>
        <a href="<?php echo esc_url(get_category_link($c)); ?>"
           class="tab <?php echo ($is_cat && $cur_cat->term_id === $c->term_id) ? 'active' : ''; ?>">
          <?php echo esc_html($c->name); ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Featured -->
<?php if ($featured_id):
    $f = $featured[0];
    $f_cat = get_the_category($f->ID);
    $f_cat_n = $f_cat ? $f_cat[0]->name : 'Гайды';
    $f_words = str_word_count(wp_strip_all_tags($f->post_content));
    $f_min   = max(1, (int)ceil($f_words / 180));
    $f_au    = function_exists('zaymi_get_post_author') ? zaymi_get_post_author($f->ID) : ['name' => get_the_author_meta('display_name', $f->post_author), 'photo' => ''];
?>
<section class="zo-featured-wrap">
  <div class="zo-container-wide">
    <a href="<?php echo esc_url(get_permalink($f)); ?>" class="zo-featured">
      <div class="cover gradient <?php echo esc_attr(zaymi_blog_gradient(0)); ?>">
        <?php if (has_post_thumbnail($f->ID)): ?>
          <?php echo get_the_post_thumbnail($f->ID, 'large', ['loading' => 'eager']); ?>
        <?php else: ?>
          <span class="emoji">📘</span>
        <?php endif; ?>
        <span class="featured-badge">✨ Главная статья</span>
      </div>
      <div class="body">
        <span class="cat"><?php echo esc_html($f_cat_n); ?></span>
        <h2><?php echo esc_html(get_the_title($f)); ?></h2>
        <p><?php echo esc_html(wp_trim_words(get_the_excerpt($f) ?: $f->post_content, 35, '…')); ?></p>
        <div class="meta">
          <?php if (!empty($f_au['photo'])): ?>
            <img class="ava" src="<?php echo esc_url($f_au['photo']); ?>" alt="<?php echo esc_attr($f_au['name']); ?>">
          <?php endif; ?>
          <span class="au"><?php echo esc_html($f_au['name']); ?></span>
          <span class="dot">📅 <?php echo esc_html(get_the_date('j F Y', $f)); ?></span>
          <span class="dot">⏱ <?php echo esc_html($f_min); ?> мин</span>
        </div>
        <span class="cta">Читать статью →</span>
      </div>
    </a>
  </div>
</section>
<?php endif; ?>

<!-- Articles grid -->
<section class="zo-blog-grid-wrap">
  <div class="zo-container-wide">
    <div class="zo-blog-grid-head">
      <h2><?php echo esc_html($active_label === 'Все' ? 'Все статьи' : $active_label); ?></h2>
      <p>Найдено: <?php echo (int)$q->found_posts; ?></p>
    </div>

    <?php if (!$q->have_posts()): ?>
      <div class="zo-blog-empty"><p>По вашему запросу ничего не найдено.</p></div>
    <?php else: ?>
      <div class="zo-blog-grid">
        <?php $i = 1; while ($q->have_posts()): $q->the_post();
          $cat = get_the_category(); $cat_n = $cat ? $cat[0]->name : 'Блог';
          $w = str_word_count(wp_strip_all_tags(get_the_content()));
          $m = max(1, (int)ceil($w / 180));
          $au = function_exists('zaymi_get_post_author') ? zaymi_get_post_author(get_the_ID()) : ['name' => get_the_author(), 'photo' => ''];
        ?>
          <a href="<?php the_permalink(); ?>" class="zo-blog-card">
            <div class="cover gradient <?php echo esc_attr(zaymi_blog_gradient($i)); ?>">
              <?php if (has_post_thumbnail()): ?>
                <?php the_post_thumbnail('medium_large', ['loading' => 'lazy']); ?>
              <?php else: ?>
                <span class="emoji">📰</span>
              <?php endif; ?>
              <span class="cat"><?php echo esc_html($cat_n); ?></span>
            </div>
            <div class="body">
              <h3><?php the_title(); ?></h3>
              <p><?php echo esc_html(wp_trim_words(get_the_excerpt() ?: get_the_content(), 22, '…')); ?></p>
              <div class="meta">
                <span class="au"><?php echo esc_html($au['name']); ?></span>
                <span class="time">⏱ <?php echo esc_html($m); ?> мин</span>
              </div>
            </div>
          </a>
        <?php $i++; endwhile; ?>
      </div>
    <?php endif; ?>

    <!-- Pagination -->
    <?php
    $big = 999999999;
    $pag = paginate_links([
        'base'      => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
        'format'    => '?paged=%#%',
        'current'   => $paged,
        'total'     => $q->max_num_pages,
        'prev_text' => '←',
        'next_text' => '→',
        'type'      => 'array',
        'mid_size'  => 1,
    ]);
    if ($pag): ?>
      <nav class="zo-blog-pag" aria-label="Пагинация">
        <?php foreach ($pag as $p) echo '<span>' . $p . '</span>'; ?>
      </nav>
    <?php endif; wp_reset_postdata(); ?>
  </div>
</section>

<?php get_footer();
