# Zaymi Online — WordPress тема

Полноценная тема каталога МФО с дизайном zaymi-online.lovable.app.

## Что внутри
- **CPT** `mfo` + таксономии `situation`, `summa`, `city`
- **ACF Pro** группы полей (логотип, ставки, тарифы, FAQ, плюсы/минусы и т.д.)
- **15 шорткодов** для редактирования через админку без кода
- **Сидер на 50 МФО**, 10 городов, 6 ситуаций, 9 сумм, 3 статьи, 6 страниц
- **Авто-генерация PNG-плейсхолдеров** логотипов через GD (без копирайта)
- **Все шаблоны**: front-page, single-mfo, archive-mfo, taxonomy-*, page, single, index

## Установка
1. Установите плагин **ACF Pro** (https://www.advancedcustomfields.com/pro/)
2. Загрузите папку `zaymi-online/` в `/wp-content/themes/`
   ИЛИ запакуйте в ZIP и загрузите через «Внешний вид → Темы → Добавить → Загрузить»
3. Активируйте тему — сидер запустится автоматически
4. Готово: главная, каталог, страницы МФО, хабы по городам/суммам/ситуациям — всё работает

## Ручной перезапуск сидера
Админка → МФО → Инструменты → «Засеять демо-контент»

## Шорткоды (можно вставлять на любую страницу)
- `[zaymi_hero]` — главный экран с калькулятором
- `[zaymi_social_proof]` — полоска цифр (50+ МФО, 4.8/5...)
- `[zaymi_situations_grid]` — сетка подборок
- `[zaymi_mfo_catalog limit="8" situation="bez-otkaza"]` — каталог карточек
- `[zaymi_amounts_grid]` — кнопки сумм
- `[zaymi_cities_grid]` — сетка городов
- `[zaymi_comparison limit="8"]` — сравнительная таблица
- `[zaymi_seo_hub]` — тёмный SEO-блок ссылок
- `[zaymi_faq]` — общий FAQ-аккордеон
- `[zaymi_how_it_works]` — 3 шага «как это работает»
- `[zaymi_blog_section limit="3"]` — последние статьи
- `[zaymi_final_cta title="..." button="..." url="..."]` — финальный CTA
- `[zaymi_mfo_hero slug="zaymer"]`
- `[zaymi_mfo_conditions slug="zaymer"]`
- `[zaymi_mfo_pros_cons slug="zaymer"]`
- `[zaymi_mfo_rates slug="zaymer"]`
- `[zaymi_mfo_steps slug="zaymer"]`
- `[zaymi_mfo_reviews slug="zaymer"]`
- `[zaymi_mfo_faq slug="zaymer"]`
- `[zaymi_mfo_similar slug="zaymer" limit="3"]`

## Редактирование контента из админки
- **Карточка МФО**: МФО → выберите → справа поля ACF (логотип, рейтинг, ставки, тарифы, FAQ, плюсы/минусы)
- **Тексты хабов** (городов/сумм/ситуаций): «Записи → Метки» соответствующей таксономии → поля `hub_h1`, `hub_intro`
- **Глобальные настройки**: «Настройки темы Zaymi» (контакты, дисклеймер, CTA в шапке)
- **Меню**: «Внешний вид → Меню» (5 локаций: главное + 4 в футере)

## Стек
- Tailwind v3 utilities через CDN (моментальный рендер без сборки)
- Lucide-иконки inline SVG (MIT)
- Inter из Google Fonts
- Vanilla JS для калькулятора, аккордеонов и мобильного меню
