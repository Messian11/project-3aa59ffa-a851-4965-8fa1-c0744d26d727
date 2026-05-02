<?php
/**
 * Архив каталога МФО.
 * Структура 1-в-1 с превью /mfo (mfo.index.tsx):
 *  Breadcrumbs → Hero (с чипсами-статами) → [zaymi_mfo_filter] (фильтры+карточки) →
 *  [zaymi_comparison] → [zaymi_amounts_grid] → [zaymi_cities_grid] → [zaymi_faq] → [zaymi_lsi]
 */
if (!defined('ABSPATH')) exit;
get_header();

$total = wp_count_posts('mfo');
$count = (int)($total->publish ?? 0);

// средний рейтинг
$avg = 0;
$ids = get_posts(['post_type' => 'mfo', 'posts_per_page' => -1, 'fields' => 'ids', 'no_found_rows' => true]);
if ($ids) {
    $sum = 0; $n = 0;
    foreach ($ids as $id) {
        $r = (float) get_post_meta($id, 'mfo_rating', true);
        if ($r > 0) { $sum += $r; $n++; }
    }
    if ($n) $avg = round($sum / $n, 1);
}
?>

<!-- Breadcrumbs -->
<nav class="zo-blog-crumbs" aria-label="Хлебные крошки">
  <div class="zo-container-wide">
    <ol>
      <li><a href="<?php echo esc_url(home_url('/')); ?>">Главная</a></li>
      <li><span class="sep">›</span></li>
      <li class="current">Каталог МФО</li>
    </ol>
  </div>
</nav>

<!-- Hero -->
<section class="zo-mfo-archive-hero">
  <div class="zo-container-wide">
    <span class="kicker">Все МФО</span>
    <h1>Каталог микрофинансовых организаций</h1>
    <p>50+ проверенных МФО с лицензией ЦБ РФ. Сравните условия и выберите лучшее предложение.</p>

    <div class="chips">
      <span class="chip"><span aria-hidden>📊</span> <?php echo $count ?: '50+'; ?> МФО</span>
      <span class="chip"><span aria-hidden>✅</span> Все с лицензией</span>
      <span class="chip"><span aria-hidden>⭐</span> Средний рейтинг <?php echo $avg ?: '4.7'; ?></span>
      <span class="chip"><span aria-hidden>💯</span> Обновлено сегодня</span>
    </div>
  </div>
</section>

<?php echo do_shortcode('[zaymi_mfo_filter limit="60" title="" subtitle="Используйте фильтры слева, чтобы найти идеальное предложение"]'); ?>
<?php echo do_shortcode('[zaymi_comparison limit="12"]'); ?>
<?php echo do_shortcode('[zaymi_amounts_grid]'); ?>
<?php echo do_shortcode('[zaymi_cities_grid]'); ?>
<?php echo do_shortcode('[zaymi_faq]'); ?>
<?php echo do_shortcode('[zaymi_lsi]'); ?>

<style>
.zo-mfo-archive-hero{padding:64px 24px 56px;
  background:radial-gradient(circle at 90% 10%,rgba(16,185,129,.10),transparent 40%),
             radial-gradient(circle at 5% 90%,rgba(37,99,235,.10),transparent 45%),
             linear-gradient(180deg,#f8fafc 0%,#fff 100%);}
.zo-mfo-archive-hero .kicker{display:inline-block;font-size:12px;font-weight:800;color:#10b981;letter-spacing:.18em;text-transform:uppercase;}
.zo-mfo-archive-hero h1{margin:14px 0 0;max-width:780px;font-size:clamp(36px,5vw,52px);font-weight:800;color:#0f172a;letter-spacing:-.02em;line-height:1.1;}
.zo-mfo-archive-hero p{margin:18px 0 0;max-width:640px;font-size:18px;color:#64748b;}
.zo-mfo-archive-hero .chips{margin-top:32px;display:flex;flex-wrap:wrap;gap:10px;}
.zo-mfo-archive-hero .chip{display:inline-flex;align-items:center;gap:8px;padding:10px 18px;border-radius:999px;
  background:rgba(255,255,255,.92);border:1px solid #e2e8f0;color:#0f172a;font-size:14px;font-weight:700;
  box-shadow:0 4px 10px rgba(0,0,0,.04);backdrop-filter:blur(8px);}
</style>

<?php get_footer();
