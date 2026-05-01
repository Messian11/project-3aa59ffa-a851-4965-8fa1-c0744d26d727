# Установка темы Zaymi Online на WordPress — пошагово

## 0. Требования

- WordPress 6.4+
- PHP 8.1+
- MySQL 5.7+ / MariaDB 10.3+
- Плагины:
  - **Advanced Custom Fields PRO** (обязательно)
  - **Yoast SEO** или **Rank Math**
  - **WP All Import** (для импорта демо-данных)
  - Опционально: **Contact Form 7** или **Fluent Forms**, **Redirection**

## 1. Создание скелета темы

```bash
cd wp-content/themes
mkdir zaymi-online
cd zaymi-online
```

Создать `style.css`:

```css
/*
Theme Name: Zaymi Online
Theme URI: https://your-domain.ru
Author: Codex
Description: Каталог МФО — кастомная тема, портированная с TanStack Start.
Version: 1.0.0
Requires PHP: 8.1
Requires at least: 6.4
Text Domain: zaymi
*/
```

Создать `index.php` (заглушка, чтобы тема активировалась):
```php
<?php get_template_part('template-parts/fallback'); ?>
```

## 2. Перенос токенов

```bash
cp wordpress-package/tokens/theme.json ./theme.json
```

Это активирует палитру и типографику в Gutenberg.

## 3. Подключение CPT и таксономий

```bash
mkdir inc
cp wordpress-package/acf/cpt-register.php ./inc/cpt-register.php
```

В `functions.php`:
```php
<?php
require_once get_template_directory() . '/inc/cpt-register.php';
require_once get_template_directory() . '/inc/enqueue.php';
require_once get_template_directory() . '/inc/seo.php';
```

## 4. Подключение CSS и шрифтов

Создать `inc/enqueue.php`:
```php
<?php
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('zaymi-main',
        get_template_directory_uri() . '/assets/css/main.css',
        [], wp_get_theme()->get('Version')
    );
    wp_enqueue_script('zaymi-main',
        get_template_directory_uri() . '/assets/js/main.js',
        [], wp_get_theme()->get('Version'), true
    );
});
```

Скопировать шрифт `Inter-VariableFont.woff2` в `assets/fonts/` (скачать с
[fonts.google.com/specimen/Inter](https://fonts.google.com/specimen/Inter)).

## 5. Активация темы

WP Admin → Внешний вид → Темы → Активировать «Zaymi Online».

## 6. Импорт ACF-полей

WP Admin → ACF → Tools → Import Field Groups →
загрузить `wordpress-package/acf/acf-export.json` → Import.

## 7. Создание терминов таксономий

Можно вручную через админку или через WP-CLI:

```bash
# Подборки
wp term create situation "Без отказа" --slug=bez-otkaza
wp term create situation "С плохой КИ" --slug=s-plohoy-ki
wp term create situation "Пенсионерам" --slug=pensioneram
wp term create situation "Студентам" --slug=studentam
wp term create situation "Без справок" --slug=bez-spravok
wp term create situation "Срочно" --slug=srochno

# Суммы
wp term create summa "1 000 ₽" --slug=zaim-1000
wp term create summa "3 000 ₽" --slug=zaim-3000
wp term create summa "5 000 ₽" --slug=zaim-5000
wp term create summa "10 000 ₽" --slug=zaim-10000
wp term create summa "15 000 ₽" --slug=zaim-15000
wp term create summa "20 000 ₽" --slug=zaim-20000
wp term create summa "30 000 ₽" --slug=zaim-30000
wp term create summa "50 000 ₽" --slug=zaim-50000
wp term create summa "100 000 ₽" --slug=zaim-100000

# Города
wp term create city "Москва" --slug=moskva
wp term create city "Санкт-Петербург" --slug=spb
wp term create city "Казань" --slug=kazan
wp term create city "Новосибирск" --slug=novosibirsk
wp term create city "Екатеринбург" --slug=ekaterinburg
```

## 8. Импорт демо-МФО

Через **WP All Import**:
1. New Import → Upload file → `wordpress-package/demo-data/mfo.json`
2. Тип записи → MFO
3. Сопоставить поля → перетащить XPath в нужные ACF-поля
4. Импортировать

Или через WP-CLI скрипт `import-mfo.php` (написать отдельно — стандартный паттерн `wp_insert_post` + `update_field`).

## 9. Создание шаблонов

Скопировать структуру из `templates-spec/pages-map.md`, использовать
HTML-разметку из `zaymi-design-export/{page}/markup.html` как референс.

Минимальный набор файлов:
```
front-page.php
single-mfo.php
archive-mfo.php
taxonomy-situation.php
taxonomy-summa.php
taxonomy-city.php
home.php
single.php
header.php
footer.php
404.php
```

## 10. Настройка постоянных ссылок

WP Admin → Настройки → Постоянные ссылки → выбрать «Название записи»
(`/%postname%/`) → Сохранить.

Это перерегистрирует rewrite rules для всех CPT и таксономий.

## 11. Создание главного меню

WP Admin → Внешний вид → Меню:
1. Создать меню «Главное» → присвоить локацию `primary`
2. Добавить пункты: Главная, Каталог МФО, Подборки (с подменю терминов),
   По сумме (с подменю), По городам (с подменю), Блог
3. Сохранить

## 12. Заполнение опций темы

WP Admin → Настройки Zaymi → заполнить телефон, email, соцсети, дисклеймер.

## 13. SEO-настройка

- Yoast / Rank Math → подключить Google Search Console
- Сгенерировать sitemap.xml (автоматически)
- Применить шаблоны title из `seo/meta-map.md`

## 14. Финальная проверка

- [ ] Главная открывается, дизайн совпадает со скриншотом `homepage/screenshot.png`
- [ ] Страница МФО `/mfo/zaymer` работает, все секции на месте
- [ ] Подборка `/situations/bez-otkaza` показывает связанные МФО
- [ ] Сумма `/summa/zaim-10000` работает
- [ ] Город `/goroda/moskva` работает
- [ ] Блог открывается
- [ ] Мобильное меню работает
- [ ] Поиск работает
- [ ] 404-страница оформлена
- [ ] Скорость > 80 в PageSpeed Insights
- [ ] Schema.org валидируется на validator.schema.org

## Возможные проблемы

| Проблема | Решение |
|---|---|
| 404 на CPT/таксономиях | Перейти в Настройки → Постоянные ссылки → Сохранить |
| ACF-поля не отображаются | Проверить, активирован ACF Pro и группы импортированы |
| Шрифты не подгружаются | Проверить путь в `theme.json` → `fontFace.src` |
| Стили Gutenberg ломают вид | Добавить `add_theme_support('disable-custom-colors')` если нужно |
| Кириллические slug | Использовать транслит (`bez-otkaza`, не `без-отказа`) |
