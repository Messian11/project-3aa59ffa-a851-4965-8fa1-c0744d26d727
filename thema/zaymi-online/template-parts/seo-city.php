<?php
/**
 * SEO-шаблон: /zaymy/{город}/bez-otkaza/ и /s-plokhoy-kreditnoy-istoriey/
 */
if (!defined('ABSPATH')) exit;
$m = zaymi_seo_meta();
get_header();
$city = $m['city_name']; $slug = $m['city_slug']; $sub = $m['subtype'];
$intro = $sub === 'bad'
    ? zaymi_spin("Получить {займ|микрозайм} {с плохой кредитной историей|с просрочками|с черным списком} в {$city} {можно|реально|легко} в {проверенных|надежных|лояльных} МФО. Одобрение даже при низком {скоринге|кредитном рейтинге}.")
    : zaymi_spin("{Срочные|Быстрые|Моментальные} займы {без отказа|с гарантированным одобрением} в {$city}: {деньги|средства} на карту за 5 минут, {минимум документов|только паспорт}.");
?>
<main class="zaymi-seo-page">
  <div class="container">
    <?php echo do_shortcode('[zaymi_breadcrumbs]'); ?>
    <h1><?php echo esc_html($m['h1']); ?></h1>
    <p class="lead"><?php echo esc_html($intro); ?></p>

    <?php echo do_shortcode("[zaymi_mfo_catalog limit=15]"); ?>

    <section class="seo-text">
      <h2>Почему МФО в <?php echo esc_html($city); ?> одобряют <?php echo $sub === 'bad' ? 'с плохой КИ' : 'без отказа'; ?>?</h2>
      <p><?php echo $sub === 'bad'
          ? "В отличие от банков, МФО используют свои скоринговые модели и не запрашивают данные у БКИ так же строго. Главное — стабильный доход и действующая карта."
          : "МФО ориентированы на массовые быстрые займы и одобряют 95% заявок. Достаточно паспорта и карты любого банка."; ?></p>
    </section>

    <?php echo do_shortcode('[zaymi_lsi]'); ?>
    <?php echo do_shortcode('[zaymi_internal_links]'); ?>
  </div>
</main>
<?php get_footer();
