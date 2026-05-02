<?php
/**
 * Шаблон одиночной статьи блога — точная копия дизайна превью credit-bridge-design.
 *
 * Структура:
 *  1. Breadcrumbs
 *  2. Article hero (категория, H1, лид, автор+дата+время чтения)
 *  3. Featured image
 *  4. TOC (sticky слева) + контент (the_content() в .prose-article)
 *  5. CTA-карточка
 *  6. Author bio (ACF user fields fallback)
 *  7. Related articles
 *  8. FAQ (из ACF post_faq)
 *  9. Newsletter
 *
 * Поддержка: автоматический TOC по H2 в контенте, расчёт времени чтения,
 * кнопки + / − аккордеона на чистом CSS (<details>).
 *
 * @package Zaymi
 */

if (!defined('ABSPATH')) exit;
get_header();

while (have_posts()): the_post();

    $post_id      = get_the_ID();
    $cats         = get_the_category();
    $primary_cat  = $cats ? $cats[0]->name : 'Блог';

    /* Автор: эксперт из CPT zaymi_author с фолбэком на WP-юзера */
    $author = function_exists('zaymi_get_post_author')
        ? zaymi_get_post_author($post_id)
        : ['name' => get_the_author(), 'role' => 'Автор', 'bio' => '', 'photo' => '', 'twitter' => '', 'linkedin' => '', 'email' => '', 'experience' => ''];
    $author_name  = $author['name'];
    $author_role  = $author['role'];
    $author_bio   = $author['bio'];
    $author_photo = $author['photo'];

    /* Время чтения: ~180 слов в мин для русского */
    $word_count   = str_word_count(wp_strip_all_tags(get_the_content()));
    $read_minutes = max(1, (int) ceil($word_count / 180));

    /* Парсим TOC из H2 контента и одновременно проставляем туда id */
    $raw_content  = apply_filters('the_content', get_the_content());
    $toc          = [];
    $content_html = preg_replace_callback(
        '/<h2(\s+[^>]*)?>(.*?)<\/h2>/i',
        function ($m) use (&$toc) {
            $attrs = $m[1] ?? '';
            $text  = trim(wp_strip_all_tags($m[2]));
            if ($text === '') return $m[0];
            // если id уже есть — используем его
            if (preg_match('/id=["\']([^"\']+)["\']/', $attrs, $idm)) {
                $id = $idm[1];
            } else {
                $id = 'h-' . sanitize_title($text);
                $attrs .= ' id="' . esc_attr($id) . '"';
            }
            $toc[] = ['id' => $id, 'title' => $text];
            return '<h2' . $attrs . ' class="zo-h2">' . $m[2] . '</h2>';
        },
        $raw_content
    );

    /* FAQ из ACF (если есть) */
    $faqs = function_exists('get_field') ? get_field('post_faq', $post_id) : [];
    if (!is_array($faqs)) $faqs = [];

    /* Excerpt = лид. Если пустой — берём первый абзац */
    $lead = trim(get_the_excerpt());
    if (!$lead) {
        if (preg_match('/<p[^>]*>(.+?)<\/p>/is', $raw_content, $pm)) {
            $lead = wp_strip_all_tags($pm[1]);
        }
    }
    $lead = wp_trim_words($lead, 45, '…');
?>

<div class="zo-article">

  <!-- 1. Breadcrumbs -->
  <nav class="zo-breadcrumbs" aria-label="Хлебные крошки">
    <ol>
      <li><a href="<?php echo esc_url(home_url('/')); ?>">Главная</a></li>
      <li><span class="sep">›</span></li>
      <li><a href="<?php echo esc_url(get_post_type_archive_link('post') ?: home_url('/blog/')); ?>">Блог</a></li>
      <li><span class="sep">›</span></li>
      <li class="current"><?php echo esc_html(wp_trim_words(get_the_title(), 6, '…')); ?></li>
    </ol>
  </nav>

  <!-- 2. Hero -->
  <header class="zo-art-hero">
    <div class="zo-container-narrow">
      <span class="zo-tag"><?php echo esc_html($primary_cat); ?></span>
      <h1 class="zo-art-title"><?php the_title(); ?></h1>
      <?php if ($lead): ?>
        <p class="zo-art-lead"><?php echo esc_html($lead); ?></p>
      <?php endif; ?>

      <div class="zo-art-meta">
        <div class="zo-author-mini">
          <img src="<?php echo esc_url($author_photo); ?>" alt="<?php echo esc_attr($author_name); ?>" width="48" height="48" loading="lazy">
          <div>
            <div class="name"><?php echo esc_html($author_name); ?></div>
            <div class="role"><?php echo esc_html($author_role); ?></div>
          </div>
        </div>
        <span class="divider"></span>
        <span class="meta-item">📅 <?php echo esc_html(get_the_date('j F Y')); ?></span>
        <span class="meta-item">⏱ <?php echo esc_html($read_minutes); ?> мин чтения</span>
      </div>
    </div>
  </header>

  <!-- 3. Featured image -->
  <?php if (has_post_thumbnail()): ?>
    <figure class="zo-art-featured">
      <div class="zo-container-wide">
        <?php the_post_thumbnail('full', ['class' => 'zo-art-img', 'loading' => 'eager']); ?>
        <?php
          $caption = get_the_post_thumbnail_caption($post_id);
          if ($caption): ?>
            <figcaption><?php echo esc_html($caption); ?></figcaption>
        <?php endif; ?>
      </div>
    </figure>
  <?php endif; ?>

  <!-- 4. TOC + Article -->
  <div class="zo-art-body">
    <div class="zo-container-medium zo-art-grid">

      <aside class="zo-toc-wrap">
        <?php if ($toc): ?>
          <div class="zo-toc">
            <details open>
              <summary>
                <span class="caption">Содержание</span>
                <span class="chev" aria-hidden="true">▾</span>
              </summary>
              <ul>
                <?php foreach ($toc as $i => $t): ?>
                  <li>
                    <a href="#<?php echo esc_attr($t['id']); ?>">
                      <span class="num"><?php echo ($i + 1); ?>.</span>
                      <span><?php echo esc_html($t['title']); ?></span>
                    </a>
                  </li>
                <?php endforeach; ?>
              </ul>
            </details>
          </div>
        <?php endif; ?>
      </aside>

      <article class="prose-article">
        <?php echo $content_html; // безопасно: вышел из the_content фильтра ?>
      </article>
    </div>
  </div>

  <!-- 5. CTA -->
  <section class="zo-art-cta">
    <div class="zo-container-medium">
      <div class="zo-cta-card">
        <div class="zo-cta-text">
          <h3>Готовы взять займ в проверенном МФО?</h3>
          <p>В нашем каталоге — 50+ МФО с лицензией ЦБ РФ, отсортированных по рейтингу и одобрению.</p>
        </div>
        <a href="<?php echo esc_url(get_post_type_archive_link('mfo')); ?>" class="zo-btn-white">
          Открыть каталог МФО →
        </a>
      </div>
    </div>
  </section>

  <!-- 6. Author bio -->
  <?php if ($author_bio || $author['experience']): ?>
  <section class="zo-art-author">
    <div class="zo-container-medium">
      <div class="zo-author-card">
        <img src="<?php echo esc_url($author_photo); ?>" alt="<?php echo esc_attr($author_name); ?>" width="80" height="80" loading="lazy">
        <div>
          <div class="kicker">Автор</div>
          <h3><?php echo esc_html($author_name); ?></h3>
          <div class="role"><?php echo esc_html($author_role); ?></div>
          <?php if ($author['experience']): ?><p class="exp"><?php echo esc_html($author['experience']); ?></p><?php endif; ?>
          <?php if ($author_bio): ?><p><?php echo esc_html(wp_strip_all_tags($author_bio)); ?></p><?php endif; ?>
          <?php if ($author['twitter'] || $author['linkedin'] || $author['email']): ?>
            <div class="zo-author-social">
              <?php if ($author['twitter']): ?><a href="<?php echo esc_url($author['twitter']); ?>" aria-label="Twitter" target="_blank" rel="nofollow noopener"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M22 5.8c-.7.3-1.5.6-2.4.7.9-.5 1.5-1.3 1.8-2.3-.8.5-1.7.8-2.6 1-1.5-1.6-4-1.7-5.6-.2-1 1-1.5 2.5-1.2 3.9C8.7 8.7 5.7 7.1 3.7 4.5c-1.1 1.9-.5 4.3 1.3 5.5-.7 0-1.3-.2-1.9-.5 0 2 1.4 3.7 3.3 4.1-.6.2-1.3.2-1.9.1.5 1.7 2.1 2.8 3.9 2.9-1.7 1.3-3.8 1.9-5.9 1.7C4.4 19.6 6.7 20.3 9 20.3c7.5 0 11.6-6.3 11.4-11.9.8-.6 1.5-1.3 2.1-2.1l-.5-.5z"/></svg></a><?php endif; ?>
              <?php if ($author['linkedin']): ?><a href="<?php echo esc_url($author['linkedin']); ?>" aria-label="LinkedIn" target="_blank" rel="nofollow noopener"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5C3.9 3 3 3.9 3 5v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM8.3 18.3H5.7V9.7h2.7v8.6zM7 8.5c-.9 0-1.5-.7-1.5-1.5s.7-1.5 1.5-1.5 1.5.7 1.5 1.5S7.8 8.5 7 8.5zm11.3 9.8h-2.7v-4.2c0-1 0-2.3-1.4-2.3s-1.6 1.1-1.6 2.2v4.3h-2.7V9.7h2.6v1.2c.4-.7 1.3-1.4 2.6-1.4 2.7 0 3.2 1.8 3.2 4.1v4.7z"/></svg></a><?php endif; ?>
              <?php if ($author['email']): ?><a href="mailto:<?php echo esc_attr($author['email']); ?>" aria-label="Email"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg></a><?php endif; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- 7. Related -->
  <?php
    $related = get_posts([
        'post_type'      => 'post',
        'posts_per_page' => 3,
        'post__not_in'   => [$post_id],
        'category__in'   => wp_list_pluck($cats, 'term_id'),
        'orderby'        => 'rand',
    ]);
    if ($related):
  ?>
  <section class="zo-art-related">
    <div class="zo-container-wide">
      <h2 class="zo-related-title">Читайте также</h2>
      <p class="zo-related-sub">Другие гиды и статьи на близкие темы.</p>

      <div class="zo-related-grid">
        <?php foreach ($related as $r):
          $r_cat = get_the_category($r->ID);
          $r_cat_name = $r_cat ? $r_cat[0]->name : 'Блог';
          $r_words = str_word_count(wp_strip_all_tags($r->post_content));
          $r_min = max(1, (int)ceil($r_words / 180));
        ?>
          <a href="<?php echo esc_url(get_permalink($r)); ?>" class="zo-related-card">
            <div class="zo-related-cover">
              <?php if (has_post_thumbnail($r->ID)): ?>
                <?php echo get_the_post_thumbnail($r->ID, 'medium_large', ['loading' => 'lazy']); ?>
              <?php endif; ?>
            </div>
            <div class="zo-related-meta">
              <span class="cat"><?php echo esc_html($r_cat_name); ?></span>
              <span class="dot"><?php echo esc_html(get_the_date('j M', $r)); ?> • <?php echo $r_min; ?> мин</span>
            </div>
            <h3><?php echo esc_html(get_the_title($r)); ?></h3>
            <p><?php echo esc_html(wp_trim_words(get_the_excerpt($r) ?: $r->post_content, 18, '…')); ?></p>
            <span class="more">Читать →</span>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- 8. FAQ -->
  <?php if ($faqs): ?>
  <section class="zo-art-faq">
    <div class="zo-container-narrow">
      <h2 class="zo-faq-title">Частые вопросы по теме</h2>
      <p class="zo-faq-sub">Если что-то осталось непонятным — ответы здесь.</p>
      <div class="zo-faq-list">
        <?php foreach ($faqs as $i => $f):
          $q = is_array($f) ? ($f['question'] ?? '') : '';
          $a = is_array($f) ? ($f['answer']   ?? '') : '';
          if (!$q) continue;
        ?>
          <details class="zo-faq-item" <?php echo $i === 0 ? 'open' : ''; ?>>
            <summary>
              <span class="q"><?php echo esc_html($q); ?></span>
              <span class="ico" aria-hidden="true"></span>
            </summary>
            <div class="a"><?php echo wp_kses_post(wpautop($a)); ?></div>
          </details>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- 9. Newsletter -->
  <section class="zo-art-newsletter">
    <div class="zo-container-medium">
      <div class="zo-news-card">
        <div>
          <div class="kicker">Рассылка</div>
          <h3>Новые гиды раз в неделю — без спама</h3>
          <p>Только полезные материалы про займы, финансы и кредитную историю. Отписаться можно в один клик.</p>
        </div>
        <form class="zo-news-form" method="post" action="<?php echo esc_url(rest_url('zaymi/v1/subscribe')); ?>">
          <input type="email" name="email" required placeholder="ваш@email.ru">
          <button type="submit">Подписаться</button>
        </form>
      </div>
    </div>
  </section>

</div>

<?php endwhile; get_footer();
