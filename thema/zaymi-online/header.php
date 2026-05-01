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

<header class="sticky top-0 z-40 border-b border-slate-200 bg-white/85 backdrop-blur-lg shadow-sm">
  <div class="mx-auto max-w-7xl flex items-center gap-6 h-16 px-4 md:h-[72px] md:px-6">
    <button class="md:hidden -ml-1 flex h-10 w-10 items-center justify-center rounded-md text-slate-900 hover:bg-slate-100" data-zaymi-toggle="mobile-menu" aria-label="Меню">
      <?php echo zaymi_icon('menu','w-6 h-6'); ?>
    </button>

    <a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center gap-1 select-none flex-1 md:flex-none md:w-[180px]">
      <span class="text-2xl font-extrabold tracking-tight text-blue-600">Zaymi</span>
      <span class="text-2xl font-extrabold tracking-tight text-emerald-600">Online</span>
    </a>

    <nav class="hidden md:flex flex-1 items-center justify-center gap-1">
      <a href="<?php echo esc_url(home_url('/')); ?>" class="rounded-md px-3.5 py-2 text-sm font-semibold text-slate-900 hover:bg-blue-50 hover:text-blue-600">Главная</a>
      <a href="<?php echo esc_url(get_post_type_archive_link('mfo')); ?>" class="rounded-md px-3.5 py-2 text-sm font-semibold text-slate-900 hover:bg-blue-50 hover:text-blue-600">Каталог МФО</a>
      <?php
      $situations = get_terms(['taxonomy'=>'situation','hide_empty'=>false,'number'=>6]);
      $amounts    = get_terms(['taxonomy'=>'summa','hide_empty'=>false,'number'=>9]);
      $cities     = get_terms(['taxonomy'=>'city','hide_empty'=>false,'number'=>6]);
      ?>
      <div class="relative group">
        <button class="inline-flex items-center gap-1 rounded-md px-3.5 py-2 text-sm font-semibold text-slate-900 hover:bg-blue-50 hover:text-blue-600">Подборки <?php echo zaymi_icon('chevron-down','w-3.5 h-3.5'); ?></button>
        <div class="absolute left-1/2 top-full z-50 -translate-x-1/2 pt-2 hidden group-hover:block w-60">
          <div class="rounded-lg border border-slate-200 bg-white p-2 shadow-xl">
            <?php foreach ((array)$situations as $t): ?>
              <a href="<?php echo esc_url(get_term_link($t)); ?>" class="block rounded-md px-3 py-2 text-sm font-semibold text-slate-900 hover:bg-slate-50 hover:text-blue-600"><?php echo esc_html($t->name); ?></a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <div class="relative group">
        <button class="inline-flex items-center gap-1 rounded-md px-3.5 py-2 text-sm font-semibold text-slate-900 hover:bg-blue-50 hover:text-blue-600">По сумме <?php echo zaymi_icon('chevron-down','w-3.5 h-3.5'); ?></button>
        <div class="absolute left-1/2 top-full z-50 -translate-x-1/2 pt-2 hidden group-hover:block w-72">
          <div class="grid grid-cols-3 gap-1 rounded-lg border border-slate-200 bg-white p-2 shadow-xl">
            <?php foreach ((array)$amounts as $t): ?>
              <a href="<?php echo esc_url(get_term_link($t)); ?>" class="rounded-md px-2 py-2 text-center text-sm font-bold text-slate-900 hover:bg-emerald-50 hover:text-emerald-600"><?php echo esc_html($t->name); ?></a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <div class="relative group">
        <button class="inline-flex items-center gap-1 rounded-md px-3.5 py-2 text-sm font-semibold text-slate-900 hover:bg-blue-50 hover:text-blue-600">По городам <?php echo zaymi_icon('chevron-down','w-3.5 h-3.5'); ?></button>
        <div class="absolute left-1/2 top-full z-50 -translate-x-1/2 pt-2 hidden group-hover:block w-64">
          <div class="rounded-lg border border-slate-200 bg-white p-2 shadow-xl">
            <?php foreach ((array)$cities as $t): ?>
              <a href="<?php echo esc_url(get_term_link($t)); ?>" class="block rounded-md px-3 py-2 text-sm font-semibold text-slate-900 hover:bg-slate-50 hover:text-blue-600"><?php echo esc_html($t->name); ?></a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))) ?: esc_url(home_url('/blog')); ?>" class="rounded-md px-3.5 py-2 text-sm font-semibold text-slate-900 hover:bg-blue-50 hover:text-blue-600">Блог</a>
    </nav>

    <a href="<?php echo esc_url(zaymi_opt('header_cta_url', '#mfo-catalog')); ?>" class="hidden md:inline-flex h-11 items-center gap-2 rounded-full bg-emerald-500 px-5 text-sm font-bold text-white shadow-md hover:bg-emerald-600">
      <?php echo esc_html(zaymi_opt('header_cta_text', 'Подобрать займ')); ?>
    </a>
  </div>

  <!-- Мобильное меню -->
  <div class="md:hidden hidden border-t border-slate-200 bg-white px-4 py-4 space-y-1" data-zaymi-mobile-menu>
    <a href="<?php echo esc_url(home_url('/')); ?>" class="block rounded-md px-4 py-3 text-base font-bold text-slate-900 hover:bg-slate-50">Главная</a>
    <a href="<?php echo esc_url(get_post_type_archive_link('mfo')); ?>" class="block rounded-md px-4 py-3 text-base font-bold text-slate-900 hover:bg-slate-50">Каталог МФО</a>
    <?php foreach ((array)$situations as $t): ?>
      <a href="<?php echo esc_url(get_term_link($t)); ?>" class="block rounded-md px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">↳ <?php echo esc_html($t->name); ?></a>
    <?php endforeach; ?>
  </div>
</header>

<main class="flex-1">
