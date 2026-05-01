# Список Gutenberg-блоков (опционально)

Если планируется давать редактору гибкость собирать страницы из блоков —
вот список кастомных блоков для разработки через ACF Blocks или `@wordpress/create-block`.

## Приоритет 1 (must-have)

| Блок | slug | Привязка | Описание |
|---|---|---|---|
| MFO Card | `zaymi/mfo-card` | контекст: ID МФО или relationship | Карточка одной МФО |
| MFO Grid | `zaymi/mfo-grid` | фильтр + кол-во | Сетка карточек МФО |
| Hero | `zaymi/hero` | поля: H1, текст, CTA, фон | Главный hero-блок |
| FAQ | `zaymi/faq` | repeater | Аккордеон вопросов |
| Pros / Cons | `zaymi/pros-cons` | 2 списка | Плюсы и минусы |
| Steps | `zaymi/steps` | repeater 3-5 | Пронумерованные шаги |
| Rates Table | `zaymi/rates-table` | repeater | Таблица тарифов |
| CTA | `zaymi/cta` | заголовок + кнопка | Призыв к действию |
| SEO Content | `zaymi/seo-content` | wysiwyg | Длинный SEO-блок |

## Приоритет 2 (nice-to-have)

| Блок | slug |
|---|---|
| Калькулятор займа | `zaymi/loan-calculator` |
| Топ-3 МФО | `zaymi/top-mfo` |
| Подборки (теги) | `zaymi/situations-grid` |
| Города (плитка) | `zaymi/cities-grid` |
| Newsletter форма | `zaymi/newsletter` |
| Сравнение | `zaymi/comparison-table` |

## Технология

Рекомендую **ACF Blocks** (часть ACF Pro): пишутся как обычные PHP-шаблоны,
но появляются в Gutenberg как блоки. Минимум JS, максимум совместимости с PHP.

Альтернатива — нативные блоки через `@wordpress/create-block`. Сложнее,
но даёт нативный edit-experience с React.

## Шаблоны страниц через `theme.json` patterns

Создать в `patterns/`:
- `front-page-default.php` — дефолтная компоновка главной
- `mfo-default.php` — дефолтная страница МФО
- `taxonomy-hub-default.php` — дефолтная страница термина

Это даст редактору 1-кликом восстановить «эталонную» компоновку.
