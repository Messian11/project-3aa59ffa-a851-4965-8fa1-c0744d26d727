# Карта маршрутов: TanStack Start → WordPress

| URL | TanStack-файл | WP-шаблон | WP-сущность / запрос | ACF-поля |
|---|---|---|---|---|
| `/` | `src/routes/index.tsx` | `front-page.php` | Статическая страница «Главная» | `home_*` (через ACF Flexible) |
| `/mfo` | `src/routes/mfo.index.tsx` | `archive-mfo.php` | `WP_Query post_type=mfo` + сортировка по рейтингу | — |
| `/mfo/{slug}` | `src/routes/mfo.$slug.tsx` | `single-mfo.php` | `get_post_by_slug` + все ACF группы A–G | A, B, C, D, E, F, G |
| `/situations/{slug}` | `src/routes/situations.$slug.tsx` | `taxonomy-situation.php` | Термин таксономии + связанные МФО | H (Taxonomy Hub) |
| `/summa/{slug}` | `src/routes/summa.$slug.tsx` | `taxonomy-summa.php` | Термин таксономии `summa` | H |
| `/goroda/{slug}` | `src/routes/goroda.$slug.tsx` | `taxonomy-city.php` | Термин таксономии `city` | H |
| `/blog` | `src/routes/blog.index.tsx` | `home.php` (или `archive.php`) | Стандартный архив постов | — |
| `/blog/{slug}` | `src/routes/blog.$slug.tsx` | `single.php` | Стандартный пост | G (SEO) |
| `/components` | `src/routes/components.tsx` | НЕ ПЕРЕНОСИТЬ | Внутренний styleguide | — |
| `/styleguide` | `src/routes/styleguide.tsx` | НЕ ПЕРЕНОСИТЬ | Внутренний styleguide | — |
| 404 | — | `404.php` | — | — |
| Поиск | — | `search.php` | `WP_Query s=...` | — |

## Структура файлов темы

```
zaymi-theme/
├── style.css                       # Заголовок темы + базовый CSS
├── theme.json                      # Из tokens/theme.json
├── functions.php                   # Точка входа
├── inc/
│   ├── cpt-register.php            # Из acf/cpt-register.php
│   ├── enqueue.php                 # Подключение стилей и скриптов
│   ├── acf-helpers.php             # Хелперы для ACF
│   └── seo.php                     # Meta-теги, Schema.org
├── template-parts/
│   ├── header/                     # Логотип, меню, поиск, CTA
│   ├── footer/
│   ├── mfo-card.php                # Карточка МФО (используется в архиве и подборках)
│   ├── mfo-hero.php                # Hero-блок страницы МФО
│   ├── mfo-pros-cons.php
│   ├── mfo-rates.php
│   ├── mfo-faq.php
│   ├── breadcrumbs.php
│   └── reviews.php
├── front-page.php
├── single-mfo.php
├── archive-mfo.php
├── taxonomy-situation.php
├── taxonomy-summa.php
├── taxonomy-city.php
├── home.php
├── single.php
├── search.php
├── 404.php
├── header.php
├── footer.php
├── assets/
│   ├── css/main.css                # Скомпилированный из tokens
│   ├── js/main.js                  # Дропдауны, аккордеоны, мобильное меню
│   ├── fonts/Inter-VariableFont.woff2
│   └── icons/                      # SVG-иконки lucide
└── screenshot.png                  # Превью темы для админки (1200×900)
```

## Запросы данных по шаблонам

### `archive-mfo.php`
```php
$mfos = new WP_Query([
    'post_type'      => 'mfo',
    'posts_per_page' => 20,
    'meta_key'       => 'mfo_rating',
    'orderby'        => 'meta_value_num',
    'order'          => 'DESC',
]);
```

### `taxonomy-situation.php` (и summa/city — аналогично)
```php
$term = get_queried_object();
$mfos = new WP_Query([
    'post_type' => 'mfo',
    'tax_query' => [[
        'taxonomy' => 'situation',
        'field'    => 'slug',
        'terms'    => $term->slug,
    ]],
    'posts_per_page' => 30,
]);

// SEO-контент термина
$hub_h1       = get_field('hub_h1', $term);
$hub_intro    = get_field('hub_intro', $term);
$hub_seo_text = get_field('hub_seo_text', $term);
$hub_faq      = get_field('hub_faq', $term);
```

### `single-mfo.php`
```php
$mfo = [
    'logo'         => get_field('mfo_logo'),
    'tagline'      => get_field('mfo_tagline'),
    'rating'       => (float) get_field('mfo_rating'),
    'amount_min'   => (int) get_field('mfo_amount_min'),
    'amount_max'   => (int) get_field('mfo_amount_max'),
    'rate_min'     => (float) get_field('mfo_rate_min'),
    'rate_max'     => (float) get_field('mfo_rate_max'),
    'pros'         => get_field('mfo_pros'),
    'cons'         => get_field('mfo_cons'),
    'fit_for'      => get_field('mfo_fit_for'),
    'steps'        => get_field('mfo_steps'),
    'rates'        => get_field('mfo_rates'),
    'faq'          => get_field('mfo_faq'),
    'partner_url'  => get_field('mfo_partner_url'),
];

// Похожие МФО
$similar = new WP_Query([
    'post_type'      => 'mfo',
    'posts_per_page' => 4,
    'post__not_in'   => [get_the_ID()],
    'orderby'        => 'rand',
]);
```
