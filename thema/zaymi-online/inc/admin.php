<?php
/**
 * Админка: страница инструментов темы (кнопка ручного запуска сидера).
 */
if (!defined('ABSPATH')) exit;

add_action('admin_menu', function () {
    add_submenu_page(
        'edit.php?post_type=mfo',
        'Инструменты Zaymi',
        'Инструменты',
        'manage_options',
        'zaymi-tools',
        'zaymi_tools_page'
    );
});

function zaymi_tools_page() {
    $seeded = get_option(ZAYMI_SEEDER_FLAG);
    ?>
    <div class="wrap">
        <h1>Инструменты темы Zaymi</h1>

        <?php if (!empty($_GET['seeded'])): ?>
            <div class="notice notice-success"><p>Демо-контент успешно засеян!</p></div>
        <?php endif; ?>

        <div class="card" style="max-width:720px;padding:20px;margin-top:20px;">
            <h2>Демо-контент</h2>
            <p>
                <?php if ($seeded): ?>
                    Демо-контент уже был создан <?php echo esc_html(date_i18n('d.m.Y H:i', $seeded)); ?>.
                    При повторном запуске существующие записи <strong>не будут продублированы</strong> (поиск по слагу).
                <?php else: ?>
                    Демо-контент ещё не создавался.
                <?php endif; ?>
            </p>
            <p>Создаёт: 8 МФО (с логотипами), 10 городов, 6 подборок, 9 сумм, 3 статьи, 4 страницы (Главная, О нас, Контакты, Политика), главное меню.</p>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('zaymi_run_seeder'); ?>
                <input type="hidden" name="action" value="zaymi_run_seeder">
                <p>
                    <button type="submit" class="button button-primary">
                        <?php echo $seeded ? 'Засеять заново (без дублей)' : 'Засеять демо-контент'; ?>
                    </button>
                </p>
            </form>
        </div>

        <div class="card" style="max-width:720px;padding:20px;margin-top:20px;">
            <h2>Шорткоды для вставки в страницы и записи</h2>
            <p>Вставляйте через блок «Шорткод» в Гутенберге или прямо в HTML:</p>
            <table class="widefat striped">
                <thead><tr><th style="width:35%;">Шорткод</th><th>Что вставит</th></tr></thead>
                <tbody>
                    <tr><td><code>[zaymi_hero]</code></td><td>Главная hero-секция с заголовком и калькулятором</td></tr>
                    <tr><td><code>[zaymi_mfo_catalog limit="10"]</code></td><td>Каталог МФО (рейтинг по убыванию)</td></tr>
                    <tr><td><code>[zaymi_mfo_card slug="zaymer"]</code></td><td>Одна карточка МФО</td></tr>
                    <tr><td><code>[zaymi_situations_grid]</code></td><td>Сетка подборок (без отказа, пенсионерам и т.д.)</td></tr>
                    <tr><td><code>[zaymi_amounts_grid]</code></td><td>Сетка по суммам (1000-100000 ₽)</td></tr>
                    <tr><td><code>[zaymi_cities_grid]</code></td><td>Сетка городов</td></tr>
                    <tr><td><code>[zaymi_how_it_works]</code></td><td>Блок «Как это работает» (3-4 шага)</td></tr>
                    <tr><td><code>[zaymi_blog_section limit="3"]</code></td><td>Последние статьи блога</td></tr>
                    <tr><td><code>[zaymi_faq]</code></td><td>Общий FAQ-блок</td></tr>
                    <tr><td><code>[zaymi_rates_table mfo="zaymer"]</code></td><td>Тарифная таблица одной МФО</td></tr>
                    <tr><td><code>[zaymi_seo_hub]</code></td><td>Блок ссылок на популярные категории</td></tr>
                    <tr><td><code>[zaymi_cta]</code></td><td>Финальный CTA-блок</td></tr>
                    <tr><td><code>[zaymi_newsletter]</code></td><td>Подписка на рассылку</td></tr>
                    <tr><td><code>[zaymi_breadcrumbs]</code></td><td>Хлебные крошки</td></tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}

/* Удобные ссылки в списке тем */
add_filter('plugin_action_links', function ($links) { return $links; });

/* Метабокс «Как использовать» в редакторе МФО */
add_action('add_meta_boxes', function () {
    add_meta_box('zaymi_mfo_help', 'Как настроить МФО', function () {
        echo '<p><strong>1.</strong> Заполните основную карточку: логотип, слоган, рейтинг, суммы, сроки, ставки.</p>';
        echo '<p><strong>2.</strong> Добавьте плюсы/минусы, шаги получения и тарифную таблицу.</p>';
        echo '<p><strong>3.</strong> Справа выберите подборки/суммы/города.</p>';
        echo '<p><strong>4.</strong> Опубликуйте — карточка автоматически появится в каталоге и на привязанных страницах.</p>';
    }, 'mfo', 'side', 'low');
});
