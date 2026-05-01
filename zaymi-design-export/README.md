# Zaymi Online — Design Export

Этот каталог содержит экспорт дизайна сайта по одной странице каждого типа.

## Структура

Каждая папка содержит:

- `screenshot.png` — скриншот страницы (первый экран, viewport 1280px)
- `markup.html` — отрендеренный HTML, скачанный по реальному URL
  (включает SSR-разметку, мета-теги, head, body)
- `styles.css` — собранный CSS-бандл сайта (одинаков для всех страниц,
  т.к. Tailwind собирает один общий файл)
- `scripts.js` — собранный JS-бандл клиентской гидратации (одинаков
  для всех страниц)

## Список страниц

| Папка        | URL                                | Описание                       |
|--------------|------------------------------------|--------------------------------|
| homepage     | /                                  | Главная страница               |
| single-mfo   | /mfo/zaymer                        | Карточка отдельного МФО        |
| catalog      | /mfo                               | Каталог МФО                    |
| summa        | /summa/zaim-10000                  | Подборка по сумме займа        |
| goroda       | /goroda/moskva                     | Подборка по городу             |
| situations   | /situations/bez-otkaza             | Тематическая подборка          |
| article      | /blog/kak-vybrat-mfo               | Страница статьи блога          |
| blog-index   | /blog                              | Индекс блога                   |
| components   | /components                        | Библиотека UI-компонентов      |

## Технические заметки

- Источник: https://credit-bridge-design.lovable.app
- Стек: TanStack Start (React 19, SSR) + Tailwind CSS v4
- HTML — это полноценный SSR-вывод, его можно открыть в браузере
  локально (нужно поправить пути к /assets/* на относительные или
  оставить абсолютные ссылки на хостинг)
- CSS-бандл `styles.css` содержит ВСЕ Tailwind-классы, использованные
  по всему сайту — это нормально и одинаково для каждой страницы
- JS-бандл `scripts.js` отвечает за гидратацию React. Без него
  страница останется статичной (это OK для дизайн-референса)

## Источники компонентов

Если нужно посмотреть исходный React-код страницы:

- homepage    → `src/routes/index.tsx`
- single-mfo  → `src/routes/mfo.$slug.tsx`
- catalog     → `src/routes/mfo.index.tsx`
- summa       → `src/routes/summa.$slug.tsx`
- goroda      → `src/routes/goroda.$slug.tsx`
- situations  → `src/routes/situations.$slug.tsx`
- article     → `src/routes/blog.$slug.tsx`
- blog-index  → `src/routes/blog.index.tsx`
- components  → `src/routes/components.tsx`
