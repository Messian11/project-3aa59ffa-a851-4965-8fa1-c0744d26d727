# Модель контента WordPress

## Custom Post Types (CPT)

### 1. `mfo` — Микрофинансовая организация

| Параметр | Значение |
|---|---|
| Slug | `mfo` |
| URL | `/mfo/{slug}` |
| Архив | `/mfo` |
| supports | title, editor, thumbnail, excerpt, custom-fields |
| has_archive | true |
| menu_icon | `dashicons-money-alt` |
| Иерархия | нет |

**Таксономии для MFO:** `situation`, `summa`, `city`

---

### 2. `article` (или стандартные `post`) — Статьи блога

URL: `/blog/{slug}`. Использовать стандартные посты с категорией «Блог», либо создать
отдельный CPT `article` если нужно отделить от других типов записей.

---

## Таксономии

### `situation` — Подборки/ситуации
- URL: `/situations/{slug}`
- Hierarchical: нет (плоские теги)
- Привязана к: `mfo`
- **Термины (создать заранее):**
  - `bez-otkaza` — Без отказа
  - `s-plohoy-ki` — С плохой КИ
  - `pensioneram` — Пенсионерам
  - `studentam` — Студентам
  - `bez-spravok` — Без справок
  - `srochno` — Срочно

### `summa` — По сумме займа
- URL: `/summa/{slug}`
- Hierarchical: нет
- Привязана к: `mfo`
- **Термины:**
  - `zaim-1000`, `zaim-3000`, `zaim-5000`, `zaim-10000`,
  - `zaim-15000`, `zaim-20000`, `zaim-30000`, `zaim-50000`, `zaim-100000`

### `city` — Города
- URL: `/goroda/{slug}`
- Hierarchical: нет
- Привязана к: `mfo`
- **Термины:** `moskva`, `spb`, `kazan`, `novosibirsk`, `ekaterinburg`, …

---

## ACF: Группы полей

### Группа A — «MFO Card» (привязка: post_type == mfo)

| Поле | Имя (key) | Тип | Обязат. | Описание |
|---|---|---|---|---|
| Логотип | `mfo_logo` | Image | да | 200×200, PNG |
| Слоган | `mfo_tagline` | Text | да | Например: «Первый займ под 0%» |
| Рейтинг | `mfo_rating` | Number (0–5, шаг 0.1) | да | 4.6 |
| Кол-во отзывов | `mfo_reviews_count` | Number | нет | 12 845 |
| Сумма от | `mfo_amount_min` | Number | да | в рублях |
| Сумма до | `mfo_amount_max` | Number | да | в рублях |
| Срок от (дней) | `mfo_term_min` | Number | да | 7 |
| Срок до (дней) | `mfo_term_max` | Number | да | 30 |
| Ставка от (% в день) | `mfo_rate_min` | Number | да | 0 |
| Ставка до (% в день) | `mfo_rate_max` | Number | да | 0.99 |
| % одобрения | `mfo_approval_rate` | Number | нет | 95 |
| Возраст от | `mfo_age_min` | Number | да | 18 |
| Возраст до | `mfo_age_max` | Number | да | 70 |
| Партнёрская ссылка | `mfo_partner_url` | URL | да | https://... |
| Текст CTA | `mfo_cta_text` | Text | нет | «Получить займ» |
| Лицензия ЦБ РФ | `mfo_license` | Text | нет | № 651303045003 |

### Группа B — «MFO Pros & Cons» (repeater)

| Поле | Имя | Тип |
|---|---|---|
| Плюсы (repeater) | `mfo_pros` | Repeater |
| ↳ Текст | `mfo_pros.text` | Text |
| Минусы (repeater) | `mfo_cons` | Repeater |
| ↳ Текст | `mfo_cons.text` | Text |

### Группа C — «MFO Conditions» (Подходит / Условия)

| Поле | Имя | Тип |
|---|---|---|
| Кому подходит (repeater) | `mfo_fit_for` | Repeater |
| ↳ Иконка | `mfo_fit_for.icon` | Select (lucide) |
| ↳ Заголовок | `mfo_fit_for.title` | Text |
| ↳ Описание | `mfo_fit_for.description` | Textarea |
| Шаги получения (repeater, 3-5) | `mfo_steps` | Repeater |
| ↳ Заголовок шага | `mfo_steps.title` | Text |
| ↳ Описание | `mfo_steps.description` | Textarea |
| Документы | `mfo_documents` | Checkbox (паспорт, СНИЛС, ИНН, банк. карта) |

### Группа D — «MFO Rates Table» (тарифы)

Repeater `mfo_rates`:
- `amount` — сумма
- `term` — срок (дней)
- `rate` — ставка % в день
- `overpayment` — переплата
- `total` — к возврату

### Группа E — «MFO Reviews» (опц., если не использовать комменты)

Repeater `mfo_reviews`:
- `author_name`, `author_avatar`, `rating`, `date`, `text`, `is_verified` (bool)

### Группа F — «MFO FAQ»

Repeater `mfo_faq`:
- `question` (Text)
- `answer` (Wysiwyg)

### Группа G — «SEO Content» (для всех типов)

- `seo_h1` — H1, если отличается от заголовка
- `seo_intro` — Wysiwyg, вступительный текст
- `seo_outro` — Wysiwyg, нижний SEO-текст
- `seo_meta_title` — Text (используется в `<title>` если задано)
- `seo_meta_description` — Textarea
- `seo_og_image` — Image

### Группа H — «Taxonomy Hub» (привязка: taxonomy in [situation, summa, city])

Поля для страниц-хабов (страница термина таксономии):
- `hub_h1` — Text
- `hub_intro` — Wysiwyg
- `hub_seo_text` — Wysiwyg (длинный SEO-блок внизу)
- `hub_faq` — Repeater (question, answer)
- `hub_featured_mfos` — Relationship → mfo (рекомендуемые МФО для этой подборки)

### Группа I — «Theme Options» (страница опций)

Создать через `acf_add_options_page()`:
- `phone` — Text
- `email` — Email
- `socials` — Repeater (network, url)
- `footer_columns` — Flexible Content (4 колонки)
- `disclaimer_text` — Wysiwyg (нижний текст футера)
- `header_cta_text` — Text
- `header_cta_url` — URL

---

## Меню

Создать 1 меню «Главное», подключить к локации `primary`:

1. Главная → `/`
2. Каталог МФО → `/mfo`
3. Подборки → mega-menu, термины таксономии `situation`
4. По сумме → mega-menu, термины таксономии `summa`
5. По городам → mega-menu, термины таксономии `city`
6. Блог → `/blog`

В шапке: логотип + меню + поиск + CTA «Получить займ».

---

## Комментарии и отзывы

Два варианта:
1. **Стандартные WP-комментарии** на CPT `mfo` — простой путь, но без рейтинга.
2. **ACF Repeater `mfo_reviews`** — гибче, но редактируется только админом. Рекомендую совмещать:
   стандартные комменты + поле «рейтинг» через плагин WP Comment Rating, либо
   собственный мета-бокс `comment_rating`.
