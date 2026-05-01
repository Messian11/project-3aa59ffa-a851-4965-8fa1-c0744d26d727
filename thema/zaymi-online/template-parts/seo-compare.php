<?php
/**
 * SEO-шаблон: /sravnenie/{мфо1}-vs-{мфо2}/
 */
if (!defined('ABSPATH')) exit;
$m = zaymi_seo_meta();
if (!$m) { get_header(); echo '<main class="container"><h1>Сравнение не найдено</h1></main>'; get_footer(); return; }
get_header();
$mfo1 = $m['mfo1']; $mfo2 = $m['mfo2'];
$f = function ($id, $key) { return function_exists('get_field') ? get_field($key, $id) : ''; };
?>
<main class="zaymi-seo-page">
  <div class="container">
    <?php echo do_shortcode('[zaymi_breadcrumbs]'); ?>
    <h1><?php echo esc_html($m['h1']); ?></h1>
    <p class="lead">Сравниваем условия двух МФО: ставки, суммы, сроки, требования. Выберите тот, который подходит вам больше.</p>

    <table class="zaymi-compare">
      <thead><tr><th></th><th><?php echo esc_html(get_the_title($mfo1)); ?></th><th><?php echo esc_html(get_the_title($mfo2)); ?></th></tr></thead>
      <tbody>
      <?php
      $rows = [
        ['Минимальная ставка', 'rate_min', '%/день'],
        ['Сумма от', 'sum_min', '₽'],
        ['Сумма до', 'sum_max', '₽'],
        ['Срок до', 'term_max', 'дн.'],
        ['Возраст от', 'age_min', 'лет'],
        ['Рейтинг', 'rating', '★'],
        ['Скорость', 'speed', ''],
      ];
      foreach ($rows as $r) {
          $v1 = $f($mfo1->ID, $r[1]); $v2 = $f($mfo2->ID, $r[1]);
          if (!$v1 && !$v2) continue;
          echo '<tr><td><b>'.esc_html($r[0]).'</b></td>';
          echo '<td>'.esc_html($v1 ? $v1.' '.$r[2] : '—').'</td>';
          echo '<td>'.esc_html($v2 ? $v2.' '.$r[2] : '—').'</td></tr>';
      }
      ?>
      </tbody>
    </table>

    <div class="zaymi-compare-cta">
      <a class="btn-primary" href="<?php echo esc_url(zaymi_go_url($mfo1->ID)); ?>">Получить в <?php echo esc_html(get_the_title($mfo1)); ?></a>
      <a class="btn-primary" href="<?php echo esc_url(zaymi_go_url($mfo2->ID)); ?>">Получить в <?php echo esc_html(get_the_title($mfo2)); ?></a>
    </div>

    <?php echo do_shortcode('[zaymi_lsi]'); ?>
  </div>
</main>
<?php get_footer();
