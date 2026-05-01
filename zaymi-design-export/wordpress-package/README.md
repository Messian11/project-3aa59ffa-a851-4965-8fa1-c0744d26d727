# Zaymi Online → WordPress: Пакет для переноса

Полный набор файлов, токенов, моделей контента и инструкций, чтобы пересобрать
сайт `zaymi-online` на WordPress с рабочей админкой и сохранить дизайн 1:1.

> Передавай эту папку целиком в Codex / разработчика. Все файлы самодостаточны.

## 📁 Содержимое

```
wordpress-package/
├── README.md                    ← этот файл (обзор + порядок работ)
├── INSTALL.md                   ← пошаговая инструкция установки на сервер
├── tokens/
│   ├── tokens.json              ← дизайн-токены (цвета, тени, радиусы, шрифты)
│   ├── theme.json               ← готовый theme.json для FSE / Gutenberg
│   └── tailwind.preset.js       ← пресет Tailwind (если будет использоваться)
├── acf/
│   ├── content-model.md         ← полная схема CPT, таксономий, ACF-полей
│   ├── acf-export.json          ← готовый импорт групп полей в ACF
│   └── cpt-register.php         ← PHP-код регистрации CPT и таксономий
├── templates-spec/
│   ├── pages-map.md             ← карта маршрут → WP-шаблон → запрос данных
│   ├── components-spec.md       ← спецификация всех блоков/секций
│   └── blocks-list.md           ← список Gutenberg-блоков для разработки
├── demo-data/
│   ├── mfo.json                 ← демо МФО (Займер, Webbankir, eKapusta, ...)
│   ├── cities.json              ← города
│   ├── situations.json          ← подборки (без отказа, пенсионерам, ...)
│   └── amounts.json             ← суммы (1000₽ — 100000₽)
└── seo/
    └── meta-map.md              ← title/description/OG для каждого типа страниц
```

## 🚀 Порядок работ для Codex

1. **Установка темы-основы** — взять `_s` (underscores) или Sage 10
2. **Применить токены** — скопировать `tokens/theme.json` в корень темы
3. **Зарегистрировать CPT и таксономии** — подключить `acf/cpt-register.php`
4. **Импортировать ACF-поля** — через ACF → Tools → Import (файл `acf/acf-export.json`)
5. **Создать шаблоны** — по таблице из `templates-spec/pages-map.md`
6. **Сверстать блоки** — по `templates-spec/components-spec.md` (HTML/CSS уже в `../`)
7. **Импортировать демо-контент** — WP All Import + JSON из `demo-data/`
8. **Настроить меню и SEO** — по `seo/meta-map.md`

## 🎨 Источники дизайна

- HTML-разметка каждой страницы: `zaymi-design-export/{homepage,single-mfo,...}/markup.html`
- Скомпилированный CSS: `zaymi-design-export/*/styles.css`
- Скриншоты: `zaymi-design-export/*/screenshot.png`
- Карта файлов проекта → страниц: `zaymi-design-export/README.md`

## ⚠️ Важно

- **Tailwind-бандл (~125 КБ)** содержит тысячи неиспользуемых утилит. В WP-теме используй
  токены из `tokens/tokens.json` и пиши собственный CSS на их основе, либо подключи
  Tailwind через `tailwind.preset.js` и пересобирай в build-процессе темы.
- **Шрифты**: используется системный стек на базе Inter. Подключить через `wp_enqueue_style`.
- **Иконки**: проект использует `lucide-react`. На WP — заменить на инлайн-SVG из
  [lucide.dev](https://lucide.dev) (они MIT и есть в виде SVG-файлов).

## 📝 Контакт

Все вопросы по реализации — к разработчику, который будет ставить тему на сервер.
Этот пакет содержит ВСЁ, что нужно для воспроизведения дизайна и админки.
