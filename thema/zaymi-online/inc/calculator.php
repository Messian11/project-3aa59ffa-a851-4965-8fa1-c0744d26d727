<?php
/**
 * Zaymi Online — Calculator + Comparison + FAQ (v2.2)
 *
 * Шорткоды:
 *   [zaymi_calculator]               — слайдеры суммы и срока, расчёт переплаты
 *   [zaymi_compare ids="1,2,3"]      — таблица сравнения МФО (или из ?compare=)
 *   [zaymi_faq mfo_id="1"]           — аккордеон FAQ из ACF + FAQ Schema
 *
 * @package Zaymi
 */
if (!defined('ABSPATH')) exit;

/* ---------- Калькулятор ---------- */
add_shortcode('zaymi_calculator', function ($atts) {
    $a = shortcode_atts([
        'min_sum' => 1000, 'max_sum' => 100000, 'def_sum' => 15000,
        'min_term' => 1, 'max_term' => 30, 'def_term' => 14,
        'rate' => 0.8, // % в день по умолчанию
    ], $atts);
    ob_start(); ?>
    <div class="zaymi-calc" data-rate="<?php echo esc_attr($a['rate']); ?>">
        <h3 class="zaymi-calc__title">Калькулятор займа</h3>
        <div class="zaymi-calc__row">
            <label>Сумма: <output class="zaymi-calc__sum"><?php echo (int)$a['def_sum']; ?></output> ₽</label>
            <input type="range" class="zaymi-calc__sum-range"
                   min="<?php echo (int)$a['min_sum']; ?>" max="<?php echo (int)$a['max_sum']; ?>"
                   step="500" value="<?php echo (int)$a['def_sum']; ?>">
        </div>
        <div class="zaymi-calc__row">
            <label>Срок: <output class="zaymi-calc__term"><?php echo (int)$a['def_term']; ?></output> дней</label>
            <input type="range" class="zaymi-calc__term-range"
                   min="<?php echo (int)$a['min_term']; ?>" max="<?php echo (int)$a['max_term']; ?>"
                   step="1" value="<?php echo (int)$a['def_term']; ?>">
        </div>
        <div class="zaymi-calc__result">
            <div><span>Переплата:</span> <strong class="zaymi-calc__overpay">0</strong> ₽</div>
            <div><span>К возврату:</span> <strong class="zaymi-calc__total">0</strong> ₽</div>
        </div>
        <a href="#zaymi-form" class="zaymi-calc__cta">Подобрать займ →</a>
    </div>
    <?php return ob_get_clean();
});

/* ---------- Сравнение МФО ---------- */
add_shortcode('zaymi_compare', function ($atts) {
    $a = shortcode_atts(['ids' => ''], $atts);
    $ids = array_filter(array_map('intval', explode(',', $a['ids'] ?: ($_GET['compare'] ?? ''))));
    if (count($ids) < 2) {
        return '<div class="zaymi-compare__hint">Выберите 2-4 МФО в каталоге для сравнения.</div>';
    }
    $ids = array_slice($ids, 0, 4);
    ob_start(); ?>
    <div class="zaymi-compare">
        <table>
            <thead><tr>
                <th>Параметр</th>
                <?php foreach ($ids as $id): ?>
                    <th>
                        <?php if (has_post_thumbnail($id)) echo get_the_post_thumbnail($id, [80, 40]); ?>
                        <a href="<?php echo get_permalink($id); ?>"><?php echo get_the_title($id); ?></a>
                    </th>
                <?php endforeach; ?>
            </tr></thead>
            <tbody>
                <?php
                $rows = [
                    'min_sum' => ['Мин. сумма', '₽'],
                    'max_sum' => ['Макс. сумма', '₽'],
                    'min_term' => ['Мин. срок', ' дн.'],
                    'max_term' => ['Макс. срок', ' дн.'],
                    'rate'    => ['Ставка', '% в день'],
                    'min_age' => ['Возраст от', ' лет'],
                    'decision_time' => ['Решение', ''],
                ];
                foreach ($rows as $key => $meta) {
                    echo '<tr><td>'.$meta[0].'</td>';
                    $vals = []; foreach ($ids as $id) $vals[$id] = (float) get_field($key, $id);
                    $best = ($key==='rate'||$key==='min_age'||$key==='min_sum'||$key==='min_term') ? min($vals) : max($vals);
                    foreach ($ids as $id) {
                        $v = get_field($key, $id);
                        $cls = ((float)$v === $best && $v) ? 'zaymi-compare__best' : '';
                        echo '<td class="'.$cls.'">'.($v ? esc_html($v).$meta[1] : '—').'</td>';
                    }
                    echo '</tr>';
                }
                ?>
                <tr><td>Получить</td>
                    <?php foreach ($ids as $id): ?>
                        <td><a class="zaymi-btn zaymi-btn--primary" href="<?php echo esc_url(home_url('/go/'.$id.'/')); ?>" rel="nofollow sponsored">Оформить</a></td>
                    <?php endforeach; ?>
                </tr>
            </tbody>
        </table>
    </div>
    <?php return ob_get_clean();
});

/* ---------- FAQ-аккордеон ---------- */
add_shortcode('zaymi_faq', function ($atts) {
    $a = shortcode_atts(['mfo_id' => get_the_ID()], $atts);
    $faq = get_field('faq', $a['mfo_id']);
    if (!$faq || !is_array($faq)) return '';
    ob_start(); ?>
    <div class="zaymi-faq">
        <h3>Частые вопросы</h3>
        <?php foreach ($faq as $i => $item): ?>
            <details class="zaymi-faq__item" <?php echo $i===0?'open':''; ?>>
                <summary><?php echo esc_html($item['question'] ?? ''); ?></summary>
                <div class="zaymi-faq__a"><?php echo wp_kses_post($item['answer'] ?? ''); ?></div>
            </details>
        <?php endforeach; ?>
    </div>
    <?php return ob_get_clean();
});
