<?php
/**
 * SEO-шаблон: /zaymy/{сумма}-rubley-na-{срок}-dney/
 */
if (!defined('ABSPATH')) exit;
$m = zaymi_seo_meta();
get_header();
$amount = $m['amount']; $term = $m['term'];
$intro = zaymi_spin("{Нужны|Понадобились|Срочно нужны} {$amount} рублей на {$term} ".zaymi_decline_days($term)."? {Подобрали|Собрали|Сравнили} {лучшие|выгодные|проверенные} МФО, которые {выдают|одобряют|перечисляют} такую сумму {за 5 минут|мгновенно|без отказа} прямо на карту.");
?>
<main class="zaymi-seo-page">
  <div class="container">
    <?php echo do_shortcode('[zaymi_breadcrumbs]'); ?>
    <h1><?php echo esc_html($m['h1']); ?></h1>
    <p class="lead"><?php echo esc_html($intro); ?></p>

    <?php echo do_shortcode("[zaymi_mfo_catalog limit=10 amount={$amount}]"); ?>

    <section class="seo-text">
      <h2>Условия займа <?php echo $amount; ?>₽ на <?php echo $term.' '.zaymi_decline_days($term); ?></h2>
      <ul>
        <li><b>Сумма:</b> <?php echo number_format($amount,0,'',' '); ?> ₽</li>
        <li><b>Срок:</b> <?php echo $term.' '.zaymi_decline_days($term); ?></li>
        <li><b>Ставка:</b> от 0% до 1% в день</li>
        <li><b>Одобрение:</b> 5–15 минут</li>
        <li><b>Возраст:</b> от 18 до 70 лет</li>
        <li><b>Документы:</b> только паспорт РФ</li>
      </ul>

      <h2>Как получить <?php echo $amount; ?>₽ на карту</h2>
      <ol>
        <li>Выберите МФО из списка выше</li>
        <li>Заполните анкету (5 минут)</li>
        <li>Дождитесь решения (обычно мгновенно)</li>
        <li>Подпишите договор и получите деньги</li>
      </ol>
    </section>

    <?php echo do_shortcode('[zaymi_lsi]'); ?>
    <?php echo do_shortcode('[zaymi_internal_links]'); ?>
  </div>
</main>
<?php get_footer();
