<?php
/**
 * Zaymi AI Writer — крон автопубликации
 *
 * Каждые 5 минут проверяет: можно ли публиковать (окно/лимит/интервал/джиттер).
 * Если да — берёт случайную тему со статусом pending и запускает пайплайн.
 *
 * @package Zaymi
 */

if (!defined('ABSPATH')) exit;

/* Регистрируем кастомный интервал «каждые 5 минут» */
add_filter('cron_schedules', function ($s) {
    if (!isset($s['zaymi_5min'])) {
        $s['zaymi_5min'] = ['interval' => 300, 'display' => 'Zaymi: каждые 5 минут'];
    }
    return $s;
});

/* Планируем хук при загрузке темы */
add_action('init', function () {
    if (!wp_next_scheduled('zaymi_ai_tick')) {
        wp_schedule_event(time() + 60, 'zaymi_5min', 'zaymi_ai_tick');
    }
});

/* Снимаем при деактивации темы */
add_action('switch_theme', function () {
    wp_clear_scheduled_hook('zaymi_ai_tick');
});

/* Хук — главный тик */
add_action('zaymi_ai_tick', function () {
    if (!class_exists('Zaymi_AI_Writer')) return;

    list($can, $why) = Zaymi_AI_Writer::can_publish_now();
    if (!$can) {
        // Логируем редко, чтобы не засорять
        if (rand(0, 9) === 0) Zaymi_AI_Writer::log('info', '⏸ Тик: пропуск — ' . $why);
        return;
    }

    $topic = Zaymi_AI_Writer::pick_random_pending();
    if (!$topic) return;

    Zaymi_AI_Writer::log('info', '⏰ Крон-тик: запускаю генерацию «' . $topic['title'] . '»');

    try {
        Zaymi_AI_Writer::generate_post($topic['id']);
    } catch (Exception $e) {
        Zaymi_AI_Writer::update_topic($topic['id'], ['status' => 'failed', 'error' => $e->getMessage()]);
        Zaymi_AI_Writer::log('error', '✗ Крон: ' . $e->getMessage(), ['topic' => $topic['id']]);
    }
});
