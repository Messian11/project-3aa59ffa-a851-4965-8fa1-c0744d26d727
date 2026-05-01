# Спецификация компонентов / блоков

Каждая секция текущего React-сайта → один template-part в WP-теме.
Источник HTML/CSS — в `zaymi-design-export/{page}/markup.html` и `styles.css`.

## Главная (`/`)

| Секция | React-компонент | template-part WP | Контент-источник |
|---|---|---|---|
| Шапка | `SiteHeader` | `template-parts/header/site-header.php` | Меню `primary`, опции темы |
| Hero (поиск + калькулятор) | `HeroSection` | `template-parts/home/hero.php` | ACF (опции темы) или хардкод |
| Социальное доказательство | `SocialProofStrip` | `template-parts/home/social-proof.php` | ACF Repeater (логотипы) |
| Каталог МФО (топ-10) | `MfoCatalogSection` | `template-parts/home/mfo-catalog.php` | `WP_Query mfo orderby=rating LIMIT 10` |
| Быстрые суммы | `AmountQuickLinks` | `template-parts/home/amounts.php` | Термины таксономии `summa` |
| Подборки | `SituationsSection` | `template-parts/home/situations.php` | Термины таксономии `situation` |
| Как это работает | `HowItWorksSection` | `template-parts/home/how-it-works.php` | ACF (опции) или хардкод |
| Сравнение | `ComparisonSection` | `template-parts/home/comparison.php` | ACF Repeater |
| Блог-секция | `BlogSection` | `template-parts/home/blog.php` | `WP_Query post_type=post LIMIT 3` |
| FAQ | `FaqSection` | `template-parts/home/faq.php` | ACF Repeater на странице «Главная» |
| SEO-хаб | `SeoHubSection` | `template-parts/home/seo-hub.php` | Термины всех таксономий |
| Newsletter CTA | `NewsletterCta` | `template-parts/home/newsletter.php` | Форма (Contact Form 7 / Fluent Forms) |
| Футер | `SiteFooter` | `template-parts/footer/site-footer.php` | Меню футера + опции |

## Страница МФО (`/mfo/{slug}`)

| Секция | React-компонент | template-part WP | Источник данных |
|---|---|---|---|
| Хлебные крошки | `Breadcrumbs` | `template-parts/breadcrumbs.php` | автоматически (Yoast / свой код) |
| Hero-карточка МФО | `MfoHeroCard` | `template-parts/mfo/hero-card.php` | ACF группа A |
| Условия | `ConditionsSection` | `template-parts/mfo/conditions.php` | ACF группа A (суммы, ставки, сроки) |
| Кому подходит | `FitSection` | `template-parts/mfo/fit.php` | ACF `mfo_fit_for` |
| Шаги получения | `StepsSection` | `template-parts/mfo/steps.php` | ACF `mfo_steps` |
| Плюсы и минусы | `ProsConsSection` | `template-parts/mfo/pros-cons.php` | ACF B |
| Тарифная таблица | `RatesTable` | `template-parts/mfo/rates.php` | ACF `mfo_rates` |
| Отзывы | `ReviewsSection` | `template-parts/mfo/reviews.php` | WP-комменты + meta `comment_rating` |
| FAQ | `MfoFaqSection` | `template-parts/mfo/faq.php` | ACF `mfo_faq` |
| Похожие МФО | `SimilarMfos` | `template-parts/mfo/similar.php` | `WP_Query` + рандом/таксономия |
| Финальный CTA | `FinalCta` | `template-parts/mfo/final-cta.php` | ACF поле `mfo_partner_url` |
| SEO-контент | `SeoContent` | `template-parts/seo-content.php` | ACF `seo_intro` + `seo_outro` |

## Каталог (`/mfo`), подборки, сумма, города

Все 4 типа используют одну общую template-part `template-parts/mfo-list.php`:

```php
<?php /* template-parts/mfo-list.php */ ?>
<div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
  <?php while ($query->have_posts()): $query->the_post(); ?>
    <?php get_template_part('template-parts/mfo-card'); ?>
  <?php endwhile; wp_reset_postdata(); ?>
</div>
```

## JS-поведения (минимально необходимые)

| Поведение | Где | Реализация |
|---|---|---|
| Дропдауны меню | Шапка | Vanilla JS hover/click toggle |
| Mobile menu (Sheet) | Шапка mobile | CSS transform + JS toggle класса |
| Аккордеон FAQ | FAQ-секции | `<details>/<summary>` (нативно) или JS |
| Калькулятор займа | Hero | Range input + расчёт переплаты |
| Sticky header | Шапка | `position: sticky` (CSS-only) |
| Tabs (если есть) | Тарифы/обзоры | JS toggle data-attr |

Готовый JS лежит в `zaymi-design-export/*/scripts.js` — можно выдрать нужные обработчики.

## Иконки

Используется библиотека `lucide-react`. На WP — заменить на инлайн-SVG.
Каждый импорт типа `import { Star } from 'lucide-react'` → SVG из
[lucide.dev/icons/star](https://lucide.dev/icons/star), сохранить в `assets/icons/`.

Список используемых иконок (грепом по проекту):
`Star, Check, X, ChevronDown, Search, Menu, MapPin, Wallet, Sparkles,
Send, Youtube, Share2, ArrowRight, Phone, Clock, ShieldCheck, Zap,
Calculator, CreditCard, FileText, User`.

## Адаптивность

Брейкпоинты: `sm 640px / md 768px / lg 1024px / xl 1280px`. Совпадают с Tailwind.
Mobile-first. Mobile меню разворачивается с правой стороны (Sheet).
