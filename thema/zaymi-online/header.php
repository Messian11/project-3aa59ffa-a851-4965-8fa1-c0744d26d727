<?php if (!defined('ABSPATH')) exit; ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class('min-h-screen flex flex-col bg-white text-slate-900'); ?>>
<?php wp_body_open(); ?>

<?php
$situations = get_terms(['taxonomy'=>'situation','hide_empty'=>false,'number'=>12]);
$amounts    = get_terms(['taxonomy'=>'summa','hide_empty'=>false,'number'=>12]);
$cities     = get_terms(['taxonomy'=>'city','hide_empty'=>false,'number'=>20]);
$blog_url   = get_permalink(get_option('page_for_posts')) ?: home_url('/blog');
$catalog_url= get_post_type_archive_link('mfo');
?>

<header class="sticky top-0 z-40 border-b border-slate-200 bg-white/85 backdrop-blur-lg shadow-sm">
  <div class="mx-auto max-w-7xl flex items-center gap-4 h-16 px-4 md:h-[72px] md:px-6">
    <button class="md:hidden -ml-1 flex h-10 w-10 items-center justify-center rounded-md text-slate-900 hover:bg-slate-100" data-zaymi-toggle="mobile-menu" aria-label="Меню">
      <?php echo zaymi_icon('menu','w-6 h-6'); ?>
    </button>

    <?php if (function_exists('zaymi_logo')) { zaymi_logo(); } else { ?>
      <a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center select-none flex-1 md:flex-none" aria-label="Zaymi Online — главная">
        <img src="<?php echo esc_url(get_template_directory_uri().'/assets/img/logo.png'); ?>" alt="Zaymi Online" width="180" height="44" class="h-9 md:h-10 w-auto" decoding="async" />
      </a>
    <?php } ?>

    <nav class="hidden md:flex flex-1 items-center justify-center gap-1">
      <a href="<?php echo esc_url(home_url('/')); ?>" class="rounded-md px-3 py-2 text-sm font-semibold text-slate-900 hover:bg-blue-50 hover:text-blue-600">Главная</a>
      <a href="<?php echo esc_url($catalog_url); ?>" class="rounded-md px-3 py-2 text-sm font-semibold text-slate-900 hover:bg-blue-50 hover:text-blue-600">Каталог МФО</a>

      <div class="relative group">
        <button class="inline-flex items-center gap-1 rounded-md px-3 py-2 text-sm font-semibold text-slate-900 hover:bg-blue-50 hover:text-blue-600">Подборки <?php echo zaymi_icon('chevron-down','w-3.5 h-3.5'); ?></button>
        <div class="absolute left-1/2 top-full z-50 -translate-x-1/2 pt-2 hidden group-hover:block w-60">
          <div class="rounded-lg border border-slate-200 bg-white p-2 shadow-xl">
            <?php foreach ((array)$situations as $t): ?>
              <a href="<?php echo esc_url(get_term_link($t)); ?>" class="block rounded-md px-3 py-2 text-sm font-semibold text-slate-900 hover:bg-slate-50 hover:text-blue-600"><?php echo esc_html($t->name); ?></a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <div class="relative group">
        <button class="inline-flex items-center gap-1 rounded-md px-3 py-2 text-sm font-semibold text-slate-900 hover:bg-blue-50 hover:text-blue-600">По сумме <?php echo zaymi_icon('chevron-down','w-3.5 h-3.5'); ?></button>
        <div class="absolute left-1/2 top-full z-50 -translate-x-1/2 pt-2 hidden group-hover:block w-72">
          <div class="grid grid-cols-3 gap-1 rounded-lg border border-slate-200 bg-white p-2 shadow-xl">
            <?php foreach ((array)$amounts as $t): ?>
              <a href="<?php echo esc_url(get_term_link($t)); ?>" class="rounded-md px-2 py-2 text-center text-sm font-bold text-slate-900 hover:bg-emerald-50 hover:text-emerald-600"><?php echo esc_html($t->name); ?></a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <div class="relative group">
        <button class="inline-flex items-center gap-1 rounded-md px-3 py-2 text-sm font-semibold text-slate-900 hover:bg-blue-50 hover:text-blue-600">Города <?php echo zaymi_icon('chevron-down','w-3.5 h-3.5'); ?></button>
        <div class="absolute left-1/2 top-full z-50 -translate-x-1/2 pt-2 hidden group-hover:block w-64">
          <div class="rounded-lg border border-slate-200 bg-white p-2 shadow-xl max-h-96 overflow-y-auto">
            <?php foreach ((array)$cities as $t): ?>
              <a href="<?php echo esc_url(get_term_link($t)); ?>" class="block rounded-md px-3 py-2 text-sm font-semibold text-slate-900 hover:bg-slate-50 hover:text-blue-600"><?php echo esc_html($t->name); ?></a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <a href="<?php echo esc_url($blog_url); ?>" class="rounded-md px-3 py-2 text-sm font-semibold text-slate-900 hover:bg-blue-50 hover:text-blue-600">Блог</a>
    </nav>

    <!-- Поиск + CTA в шапке (десктоп) -->
    <div class="hidden md:flex items-center gap-2">
      <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="relative" data-zaymi-search>
        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"><?php echo zaymi_icon('search','w-4 h-4'); ?></span>
        <input
          type="search"
          name="s"
          placeholder="МФО или статья…"
          autocomplete="off"
          class="h-11 w-56 lg:w-64 rounded-full border border-slate-200 bg-white pl-9 pr-4 text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
          data-zaymi-search-input
        />
        <div class="absolute left-0 right-0 top-full mt-2 hidden rounded-xl border border-slate-200 bg-white p-2 shadow-2xl z-50" data-zaymi-search-results></div>
      </form>
      <a href="<?php echo esc_url(zaymi_opt('header_cta_url', '#mfo-catalog')); ?>" class="inline-flex h-11 items-center gap-2 rounded-full bg-emerald-500 px-4 lg:px-5 text-sm font-bold text-white shadow-md hover:bg-emerald-600">
        <?php echo esc_html(zaymi_opt('header_cta_text', 'Подобрать займ')); ?>
      </a>
    </div>
  </div>

  <!-- Мобильное меню -->
  <div class="md:hidden hidden border-t border-slate-200 bg-white px-4 py-4 max-h-[80vh] overflow-y-auto" data-zaymi-mobile-menu>
    <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="relative mb-4">
      <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"><?php echo zaymi_icon('search','w-4 h-4'); ?></span>
      <input type="search" name="s" placeholder="Поиск МФО или статей…" class="h-11 w-full rounded-full border border-slate-200 bg-white pl-9 pr-4 text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" />
    </form>

    <div class="space-y-1">
      <a href="<?php echo esc_url(home_url('/')); ?>" class="block rounded-md px-4 py-3 text-base font-bold text-slate-900 hover:bg-slate-50">Главная</a>
      <a href="<?php echo esc_url($catalog_url); ?>" class="block rounded-md px-4 py-3 text-base font-bold text-slate-900 hover:bg-slate-50">Каталог МФО</a>
      <a href="<?php echo esc_url($blog_url); ?>" class="block rounded-md px-4 py-3 text-base font-bold text-slate-900 hover:bg-slate-50">Блог</a>
    </div>

    <?php if (!empty($situations)): ?>
    <div class="mt-4">
      <div class="px-4 pb-2 text-xs font-extrabold uppercase tracking-wider text-emerald-600">Подборки</div>
      <div class="space-y-0.5">
        <?php foreach ((array)$situations as $t): ?>
          <a href="<?php echo esc_url(get_term_link($t)); ?>" class="block rounded-md px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50"><?php echo esc_html($t->name); ?></a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($amounts)): ?>
    <div class="mt-4">
      <div class="px-4 pb-2 text-xs font-extrabold uppercase tracking-wider text-blue-600">По сумме</div>
      <div class="grid grid-cols-3 gap-1 px-2">
        <?php foreach ((array)$amounts as $t): ?>
          <a href="<?php echo esc_url(get_term_link($t)); ?>" class="rounded-md border border-slate-200 px-2 py-2 text-center text-xs font-bold text-slate-900 hover:bg-emerald-50 hover:text-emerald-600"><?php echo esc_html($t->name); ?></a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($cities)): ?>
    <div class="mt-4">
      <div class="px-4 pb-2 text-xs font-extrabold uppercase tracking-wider text-slate-600">Города</div>
      <div class="grid grid-cols-2 gap-1 px-2">
        <?php foreach ((array)$cities as $t): ?>
          <a href="<?php echo esc_url(get_term_link($t)); ?>" class="rounded-md px-2 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"><?php echo esc_html($t->name); ?></a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <a href="<?php echo esc_url(zaymi_opt('header_cta_url', '#mfo-catalog')); ?>" class="mt-5 flex h-12 w-full items-center justify-center gap-2 rounded-full bg-emerald-500 text-base font-bold text-white shadow-md hover:bg-emerald-600">
      <?php echo esc_html(zaymi_opt('header_cta_text', 'Подобрать займ')); ?>
    </a>
  </div>
</header>

<main class="flex-1">
