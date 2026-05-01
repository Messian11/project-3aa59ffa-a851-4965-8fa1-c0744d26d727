# SEO: Карта meta-данных по типам страниц

Шаблоны title/description для каждого типа. Переменные в `{фигурных скобках}` —
подставляются динамически из ACF / WP API.

## Главная (`/`)

```
title:        Zaymi Online — каталог МФО России 2026, займы онлайн от 50+ компаний
description:  Сравните 50+ микрофинансовых организаций в одном месте. Займы под 0% для новых клиентов, одобрение 95%, деньги за 5 минут. Топ-рейтинг 2026.
og:title:     Zaymi Online — каталог МФО России 2026
og:image:     /assets/og-default.jpg (1200×630)
```

## Страница МФО (`/mfo/{slug}`)

```
title:        {mfo.title} — обзор, условия и отзывы 2026 | Zaymi Online
description:  {mfo.title}: займы онлайн до {amount_max} ₽ под {rate_min}% для новых клиентов. Одобрение {approval_rate}%, решение за 5 минут, деньги на карту мгновенно.
og:title:     {mfo.title} — обзор, условия и отзывы 2026
og:description: Первый займ под {rate_min}%, одобрение {approval_rate}%, деньги за 5 минут на карту любого банка.
og:image:     {mfo.logo} (использовать большой вариант)

Schema.org:   FinancialProduct + AggregateRating + FAQPage (из mfo_faq)
```

## Каталог МФО (`/mfo`)

```
title:        Каталог МФО России 2026 — 50+ микрофинансовых организаций | Zaymi Online
description:  Полный каталог МФО России: ставки, условия, рейтинги, отзывы клиентов. Сравните и выберите лучший займ за минуту.
```

## Подборки (`/situations/{slug}`)

```
title:        {term.h1} — лучшие предложения 2026 | Zaymi Online
description:  {term.intro} Список из {count} МФО с описанием условий и реальными отзывами.
Schema.org:   ItemList (список МФО) + FAQPage (если задан hub_faq)
```

## По сумме (`/summa/{slug}`)

```
title:        {term.h1} — список МФО, ставки, отзывы | Zaymi Online
description:  Где взять {term.amount} ₽ онлайн на карту. {count} проверенных МФО с актуальными ставками 2026.
```

## По городам (`/goroda/{slug}`)

```
title:        Займы онлайн в {city.name} 2026 — {count} МФО | Zaymi Online
description:  Займы онлайн в городе {city.name}: 50+ МФО с моментальным переводом на карту. Возраст от 18 лет, без справок.
Schema.org:   LocalBusiness (опционально)
```

## Блог (`/blog`)

```
title:        Блог о займах и МФО 2026 — гайды, советы, новости | Zaymi Online
description:  Полезные статьи о микрозаймах: как выбрать МФО, что делать при отказе, как улучшить КИ. Регулярные обновления.
```

## Статья (`/blog/{slug}`)

```
title:        {post.title} | Блог Zaymi Online
description:  {post.excerpt} (до 160 символов)
og:title:     {post.title}
og:image:     {post.featured_image} (предпочтительно 1200×630)
Schema.org:   Article + BreadcrumbList
```

## 404

```
title:        Страница не найдена | Zaymi Online
description:  (не индексировать, добавить <meta name="robots" content="noindex">)
```

## Глобальные мета (на всех страницах)

```html
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="canonical" href="{current_url}">
<meta name="theme-color" content="#10b981">
<link rel="icon" href="/favicon.ico">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">
```

## Schema.org разметка (минимум)

### Organization (на главной)
```json
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "Zaymi Online",
  "url": "https://your-domain.ru",
  "logo": "https://your-domain.ru/logo.png",
  "sameAs": ["{vk}", "{telegram}", "{youtube}"]
}
```

### FinancialProduct (на странице МФО)
```json
{
  "@context": "https://schema.org",
  "@type": "FinancialProduct",
  "name": "{mfo.title}",
  "provider": { "@type": "Organization", "name": "{mfo.title}" },
  "interestRate": "{mfo.rate_min}-{mfo.rate_max}% в день",
  "amount": { "@type": "MonetaryAmount", "minValue": "{amount_min}", "maxValue": "{amount_max}", "currency": "RUB" },
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "{rating}",
    "reviewCount": "{reviews_count}",
    "bestRating": "5"
  }
}
```

### FAQPage (если есть FAQ)
Генерируется автоматически из `mfo_faq` или `hub_faq`.

## Плагины

Рекомендую:
- **Yoast SEO** или **Rank Math** — для title/description и sitemap
- **Schema & Structured Data for WP** — для Schema.org разметки
- **Redirection** — для редиректов
