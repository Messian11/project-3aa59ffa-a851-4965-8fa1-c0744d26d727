<?php
/**
 * ACF Pro: программная регистрация полей и страницы опций.
 * Если ACF Pro не установлен — функции просто не выполнятся.
 */
if (!defined('ABSPATH')) exit;

/* ------------------------------------------------------------------
 * Страница опций
 * ------------------------------------------------------------------ */
add_action('acf/init', function () {
    if (!function_exists('acf_add_options_page')) return;
    acf_add_options_page([
        'page_title' => 'Настройки темы Zaymi',
        'menu_title' => 'Настройки Zaymi',
        'menu_slug'  => 'zaymi-theme-options',
        'capability' => 'edit_posts',
        'icon_url'   => 'dashicons-admin-customizer',
        'position'   => 60,
    ]);
});

/* ------------------------------------------------------------------
 * Регистрация групп полей
 * ------------------------------------------------------------------ */
add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) return;

    /* === MFO: основная карточка === */
    acf_add_local_field_group([
        'key'    => 'group_mfo_card',
        'title'  => 'МФО — Карточка',
        'fields' => [
            ['key' => 'field_mfo_logo',          'label' => 'Логотип',              'name' => 'mfo_logo',          'type' => 'image',  'return_format' => 'array', 'preview_size' => 'thumbnail'],
            ['key' => 'field_mfo_tagline',       'label' => 'Слоган',               'name' => 'mfo_tagline',       'type' => 'text'],
            ['key' => 'field_mfo_rating',        'label' => 'Рейтинг (0-5)',        'name' => 'mfo_rating',        'type' => 'number', 'min' => 0, 'max' => 5, 'step' => 0.1],
            ['key' => 'field_mfo_reviews_count', 'label' => 'Кол-во отзывов',       'name' => 'mfo_reviews_count', 'type' => 'number'],
            ['key' => 'field_mfo_amount_min',    'label' => 'Сумма от (₽)',         'name' => 'mfo_amount_min',    'type' => 'number'],
            ['key' => 'field_mfo_amount_max',    'label' => 'Сумма до (₽)',         'name' => 'mfo_amount_max',    'type' => 'number'],
            ['key' => 'field_mfo_term_min',      'label' => 'Срок от (дней)',       'name' => 'mfo_term_min',      'type' => 'number'],
            ['key' => 'field_mfo_term_max',      'label' => 'Срок до (дней)',       'name' => 'mfo_term_max',      'type' => 'number'],
            ['key' => 'field_mfo_rate_min',      'label' => 'Ставка от (% в день)', 'name' => 'mfo_rate_min',      'type' => 'number', 'step' => 0.01],
            ['key' => 'field_mfo_rate_max',      'label' => 'Ставка до (% в день)', 'name' => 'mfo_rate_max',      'type' => 'number', 'step' => 0.01],
            ['key' => 'field_mfo_approval_rate', 'label' => '% одобрения',          'name' => 'mfo_approval_rate', 'type' => 'number'],
            ['key' => 'field_mfo_age_min',       'label' => 'Возраст от',           'name' => 'mfo_age_min',       'type' => 'number'],
            ['key' => 'field_mfo_age_max',       'label' => 'Возраст до',           'name' => 'mfo_age_max',       'type' => 'number'],
            ['key' => 'field_mfo_partner_url',   'label' => 'Партнёрская ссылка',   'name' => 'mfo_partner_url',   'type' => 'url'],
            ['key' => 'field_mfo_cta_text',      'label' => 'Текст кнопки',         'name' => 'mfo_cta_text',      'type' => 'text', 'default_value' => 'Получить займ'],
            ['key' => 'field_mfo_license',       'label' => 'Лицензия ЦБ РФ',       'name' => 'mfo_license',       'type' => 'text'],
        ],
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'mfo']]],
    ]);

    /* === MFO: плюсы и минусы === */
    acf_add_local_field_group([
        'key'    => 'group_mfo_pros_cons',
        'title'  => 'МФО — Плюсы и минусы',
        'fields' => [
            [
                'key' => 'field_mfo_pros', 'label' => 'Плюсы', 'name' => 'mfo_pros', 'type' => 'repeater',
                'layout' => 'table', 'button_label' => 'Добавить плюс',
                'sub_fields' => [['key' => 'field_pros_text', 'label' => 'Текст', 'name' => 'text', 'type' => 'text']],
            ],
            [
                'key' => 'field_mfo_cons', 'label' => 'Минусы', 'name' => 'mfo_cons', 'type' => 'repeater',
                'layout' => 'table', 'button_label' => 'Добавить минус',
                'sub_fields' => [['key' => 'field_cons_text', 'label' => 'Текст', 'name' => 'text', 'type' => 'text']],
            ],
        ],
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'mfo']]],
    ]);

    /* === MFO: подходит / шаги / документы === */
    acf_add_local_field_group([
        'key'    => 'group_mfo_conditions',
        'title'  => 'МФО — Кому подходит, шаги, документы',
        'fields' => [
            [
                'key' => 'field_mfo_fit_for', 'label' => 'Кому подходит', 'name' => 'mfo_fit_for', 'type' => 'repeater',
                'layout' => 'block', 'button_label' => 'Добавить блок',
                'sub_fields' => [
                    ['key' => 'field_fit_icon',  'label' => 'Иконка (lucide)', 'name' => 'icon',        'type' => 'text', 'instructions' => 'user, briefcase, graduation-cap, wallet, calendar, zap, shield-check'],
                    ['key' => 'field_fit_title', 'label' => 'Заголовок',       'name' => 'title',       'type' => 'text'],
                    ['key' => 'field_fit_desc',  'label' => 'Описание',        'name' => 'description', 'type' => 'textarea', 'rows' => 2],
                ],
            ],
            [
                'key' => 'field_mfo_steps', 'label' => 'Шаги получения', 'name' => 'mfo_steps', 'type' => 'repeater',
                'layout' => 'block', 'button_label' => 'Добавить шаг', 'min' => 3, 'max' => 5,
                'sub_fields' => [
                    ['key' => 'field_step_title', 'label' => 'Заголовок шага', 'name' => 'title',       'type' => 'text'],
                    ['key' => 'field_step_desc',  'label' => 'Описание',       'name' => 'description', 'type' => 'textarea', 'rows' => 2],
                ],
            ],
            [
                'key' => 'field_mfo_documents', 'label' => 'Документы', 'name' => 'mfo_documents', 'type' => 'checkbox',
                'choices' => ['passport' => 'Паспорт РФ', 'snils' => 'СНИЛС', 'inn' => 'ИНН', 'card' => 'Банковская карта'],
                'layout' => 'horizontal',
            ],
        ],
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'mfo']]],
    ]);

    /* === MFO: тарифы === */
    acf_add_local_field_group([
        'key'    => 'group_mfo_rates',
        'title'  => 'МФО — Тарифная сетка',
        'fields' => [
            [
                'key' => 'field_mfo_rates', 'label' => 'Тарифы', 'name' => 'mfo_rates', 'type' => 'repeater', 'layout' => 'table',
                'sub_fields' => [
                    ['key' => 'field_rate_amount',      'label' => 'Сумма (₽)',         'name' => 'amount',      'type' => 'number'],
                    ['key' => 'field_rate_term',        'label' => 'Срок (дней)',       'name' => 'term',        'type' => 'number'],
                    ['key' => 'field_rate_rate',        'label' => 'Ставка (%/день)',   'name' => 'rate',        'type' => 'number', 'step' => 0.01],
                    ['key' => 'field_rate_overpayment', 'label' => 'Переплата (₽)',     'name' => 'overpayment', 'type' => 'number'],
                    ['key' => 'field_rate_total',       'label' => 'К возврату (₽)',    'name' => 'total',       'type' => 'number'],
                ],
            ],
        ],
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'mfo']]],
    ]);

    /* === MFO: FAQ === */
    acf_add_local_field_group([
        'key'    => 'group_mfo_faq',
        'title'  => 'МФО — FAQ',
        'fields' => [
            [
                'key' => 'field_mfo_faq', 'label' => 'Вопросы и ответы', 'name' => 'mfo_faq', 'type' => 'repeater', 'layout' => 'block',
                'sub_fields' => [
                    ['key' => 'field_faq_q', 'label' => 'Вопрос', 'name' => 'question', 'type' => 'text'],
                    ['key' => 'field_faq_a', 'label' => 'Ответ',  'name' => 'answer',   'type' => 'wysiwyg', 'tabs' => 'visual', 'media_upload' => 0],
                ],
            ],
        ],
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'mfo']]],
    ]);

    /* === SEO для всех типов === */
    acf_add_local_field_group([
        'key'    => 'group_seo_content',
        'title'  => 'SEO',
        'fields' => [
            ['key' => 'field_seo_h1',       'label' => 'H1 (если отличается)', 'name' => 'seo_h1',              'type' => 'text'],
            ['key' => 'field_seo_intro',    'label' => 'Вступительный текст',  'name' => 'seo_intro',           'type' => 'wysiwyg', 'tabs' => 'visual'],
            ['key' => 'field_seo_outro',    'label' => 'Нижний SEO-текст',     'name' => 'seo_outro',           'type' => 'wysiwyg', 'tabs' => 'visual'],
            ['key' => 'field_seo_meta_t',   'label' => 'Meta title',           'name' => 'seo_meta_title',      'type' => 'text'],
            ['key' => 'field_seo_meta_d',   'label' => 'Meta description',     'name' => 'seo_meta_description','type' => 'textarea', 'rows' => 3, 'maxlength' => 160],
            ['key' => 'field_seo_og_image', 'label' => 'OG-изображение',       'name' => 'seo_og_image',        'type' => 'image', 'return_format' => 'array'],
        ],
        'location' => [
            [['param' => 'post_type', 'operator' => '==', 'value' => 'mfo']],
            [['param' => 'post_type', 'operator' => '==', 'value' => 'post']],
            [['param' => 'post_type', 'operator' => '==', 'value' => 'page']],
        ],
    ]);

    /* === Хабы таксономий (страница термина) === */
    acf_add_local_field_group([
        'key'    => 'group_taxonomy_hub',
        'title'  => 'Страница-хаб (термин)',
        'fields' => [
            ['key' => 'field_hub_h1',       'label' => 'H1',                  'name' => 'hub_h1',       'type' => 'text'],
            ['key' => 'field_hub_intro',    'label' => 'Вступление (под H1)', 'name' => 'hub_intro',    'type' => 'wysiwyg'],
            ['key' => 'field_hub_seo_text', 'label' => 'Длинный SEO-текст',   'name' => 'hub_seo_text', 'type' => 'wysiwyg'],
            [
                'key' => 'field_hub_faq', 'label' => 'FAQ', 'name' => 'hub_faq', 'type' => 'repeater', 'layout' => 'block',
                'sub_fields' => [
                    ['key' => 'field_hub_faq_q', 'label' => 'Вопрос', 'name' => 'question', 'type' => 'text'],
                    ['key' => 'field_hub_faq_a', 'label' => 'Ответ',  'name' => 'answer',   'type' => 'wysiwyg'],
                ],
            ],
            [
                'key' => 'field_hub_featured', 'label' => 'Рекомендуемые МФО', 'name' => 'hub_featured_mfos', 'type' => 'relationship',
                'post_type' => ['mfo'], 'filters' => ['search', 'taxonomy'], 'max' => 10, 'return_format' => 'id',
            ],
        ],
        'location' => [
            [['param' => 'taxonomy', 'operator' => '==', 'value' => 'situation']],
            [['param' => 'taxonomy', 'operator' => '==', 'value' => 'summa']],
            [['param' => 'taxonomy', 'operator' => '==', 'value' => 'city']],
        ],
    ]);

    /* === Опции темы === */
    acf_add_local_field_group([
        'key'    => 'group_theme_options',
        'title'  => 'Настройки сайта',
        'fields' => [
            ['key' => 'field_to_phone',        'label' => 'Телефон',              'name' => 'phone',           'type' => 'text'],
            ['key' => 'field_to_email',        'label' => 'Email',                'name' => 'email',           'type' => 'email'],
            ['key' => 'field_to_header_cta_t', 'label' => 'CTA в шапке (текст)',  'name' => 'header_cta_text', 'type' => 'text', 'default_value' => 'Получить займ'],
            ['key' => 'field_to_header_cta_u', 'label' => 'CTA в шапке (ссылка)', 'name' => 'header_cta_url',  'type' => 'url'],
            ['key' => 'field_to_disclaimer',   'label' => 'Дисклеймер (футер)',   'name' => 'disclaimer_text', 'type' => 'wysiwyg'],
            [
                'key' => 'field_to_socials', 'label' => 'Соцсети', 'name' => 'socials', 'type' => 'repeater', 'layout' => 'table',
                'sub_fields' => [
                    ['key' => 'field_to_soc_net', 'label' => 'Сеть', 'name' => 'network', 'type' => 'select', 'choices' => ['vk' => 'VK', 'telegram' => 'Telegram', 'youtube' => 'YouTube']],
                    ['key' => 'field_to_soc_url', 'label' => 'URL',  'name' => 'url',     'type' => 'url'],
                ],
            ],
        ],
        'location' => [[['param' => 'options_page', 'operator' => '==', 'value' => 'zaymi-theme-options']]],
    ]);
});
