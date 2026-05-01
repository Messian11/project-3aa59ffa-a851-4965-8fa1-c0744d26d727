<?php if (!defined('ABSPATH')) exit; ?>
</main>

<footer class="bg-slate-900 px-6 pt-16 pb-10 text-white/80">
  <div class="mx-auto max-w-7xl">
    <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-5">
      <div class="lg:col-span-1">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center gap-1 select-none">
          <span class="text-2xl font-extrabold text-blue-500">Zaymi</span>
          <span class="text-2xl font-extrabold text-emerald-500">Online</span>
        </a>
        <p class="mt-4 text-sm font-medium leading-relaxed text-white/65">
          <?php echo esc_html(zaymi_opt('footer_about', get_bloginfo('description') ?: 'Zaymi Online — независимый агрегатор МФО России. Сравниваем 50+ компаний и помогаем выбрать лучшие условия.')); ?>
        </p>
      </div>
      <?php
      $cols = [
          ['Каталог', [
              ['Все МФО', get_post_type_archive_link('mfo')],
              ['Сравнение МФО', home_url('/sravnenie-mfo')],
          ]],
          ['Подборки', array_map(fn($t)=>[$t->name, get_term_link($t)], (array)get_terms(['taxonomy'=>'situation','hide_empty'=>false,'number'=>5]))],
          ['Полезное', [
              ['Блог', get_permalink(get_option('page_for_posts')) ?: home_url('/blog')],
              ['Контакты', home_url('/kontakty')],
              ['О нас', home_url('/o-nas')],
          ]],
          ['Документы', [
              ['Политика конфиденциальности', home_url('/privacy')],
              ['Дисклеймер', '#'],
          ]],
      ];
      foreach ($cols as $c): ?>
        <div>
          <h3 class="text-xs font-extrabold uppercase tracking-[0.16em] text-white"><?php echo esc_html($c[0]); ?></h3>
          <ul class="mt-5 space-y-3">
            <?php foreach ($c[1] as $l): ?>
              <li><a href="<?php echo esc_url($l[1]); ?>" class="text-sm font-semibold text-white/65 hover:text-white"><?php echo esc_html($l[0]); ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="mt-14 border-t border-white/10 pt-8 text-center">
      <div class="text-sm font-semibold text-white/70">© <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. Все права защищены.</div>
      <p class="mx-auto mt-4 max-w-4xl text-xs leading-relaxed text-white/50">
        <?php echo wp_kses_post(zaymi_opt('disclaimer_text', 'Сайт является информационным агрегатором. Мы НЕ выдаём займы. Все МФО — наши партнёры. Решения о выдаче принимают исключительно МФО на основании внутренних правил. Возрастное ограничение: 18+.')); ?>
      </p>
    </div>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
