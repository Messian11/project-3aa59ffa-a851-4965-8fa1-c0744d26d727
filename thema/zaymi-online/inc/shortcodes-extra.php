<?php
/**
 * Дополнительные шорткоды для синхронизации с превью credit-bridge-design:
 *  - [zaymi_newsletter]      — финальный CTA на email-рассылку (главная)
 *  - [zaymi_mfo_fit]         — «Кому подойдёт / Не подойдёт» (single-mfo)
 *  - [zaymi_mfo_seo_content] — длинный SEO-абзац внизу карточки МФО
 *  - [zaymi_mfo_breadcrumbs] — крошки для страницы МФО
 *
 * @package Zaymi
 */

if (!defined('ABSPATH')) exit;

/* ------------------------------------------------------------------
 * 1. Newsletter (главная)
 * ------------------------------------------------------------------ */
add_shortcode('zaymi_newsletter', function () {
    $action = esc_url(rest_url('zaymi/v1/subscribe'));
    ob_start(); ?>
    <section class="zo-newsletter-cta">
      <div class="zo-container-wide">
        <div class="zo-newsletter-card">
          <div class="zo-newsletter-text">
            <span class="kicker">✉ Рассылка</span>
            <h2>Узнавайте о лучших предложениях первыми</h2>
            <p>Раз в неделю — топ МФО недели и эксклюзивные предложения 0%</p>
          </div>
          <form class="zo-newsletter-form" method="post" action="<?php echo $action; ?>">
            <input type="email" name="email" required placeholder="ваш@email.ru" aria-label="Email">
            <button type="submit">Подписаться →</button>
          </form>
        </div>
      </div>
    </section>
    <?php
    return ob_get_clean();
});

/* ------------------------------------------------------------------
 * 2. MFO: «Кому подойдёт / Не подойдёт»
 *    Берёт данные из ACF mfo_fit_for и mfo_fit_against (если последнего
 *    нет — генерит дефолтный список «не подойдёт»).
 * ------------------------------------------------------------------ */
add_shortcode('zaymi_mfo_fit', function ($atts) {
    $a    = shortcode_atts(['slug' => ''], $atts);
    $mfo  = $a['slug'] ? zaymi_get_mfo_by_slug($a['slug']) : get_post();
    if (!$mfo) return '';

    $fit_raw = function_exists('get_field') ? get_field('mfo_fit_for', $mfo->ID) : [];
    $against_raw = function_exists('get_field') ? get_field('mfo_fit_against', $mfo->ID) : [];

    $to_list = function ($r) {
        if (!$r) return [];
        if (is_string($r)) return array_filter(array_map('trim', preg_split('/\r?\n/', $r)));
        if (is_array($r)) {
            return array_values(array_filter(array_map(function ($i) {
                if (is_array($i)) return $i['text'] ?? ($i['item'] ?? reset($i));
                return (string)$i;
            }, $r)));
        }
        return [];
    };
    $fit     = $to_list($fit_raw);
    $against = $to_list($against_raw);

    if (!$fit) {
        $fit = ['Срочно нужны деньги', 'Плохая кредитная история', 'Нет справок о доходах', 'Возраст 18-70 лет'];
    }
    if (!$against) {
        $age_max = (int) (function_exists('get_field') ? get_field('mfo_age_max', $mfo->ID) : 70);
        $amount_max = (int) (function_exists('get_field') ? get_field('mfo_amount_max', $mfo->ID) : 30000);
        $against = [
            'Нужна сумма больше ' . number_format($amount_max, 0, '', ' ') . ' ₽',
            'Гражданство не РФ',
            'Возраст старше ' . ($age_max ?: 70) . ' лет',
            'Открытое банкротство',
        ];
    }

    ob_start(); ?>
    <section class="zo-fit">
      <div class="zo-container-wide">
        <header class="zo-section-head">
          <h2>Кому подойдёт <?php echo esc_html(get_the_title($mfo)); ?></h2>
          <p>Честно говорим: подходим не всем — проверьте свой случай</p>
        </header>
        <div class="zo-fit-grid">
          <div class="zo-fit-card zo-fit-yes">
            <div class="zo-fit-head"><span class="ico">✓</span><h3>Подойдёт если:</h3></div>
            <ul>
              <?php foreach ($fit as $it): ?><li><span class="b">✓</span><?php echo esc_html($it); ?></li><?php endforeach; ?>
            </ul>
          </div>
          <div class="zo-fit-card zo-fit-no">
            <div class="zo-fit-head"><span class="ico">✕</span><h3>Не подойдёт если:</h3></div>
            <ul>
              <?php foreach ($against as $it): ?><li><span class="b">✕</span><?php echo esc_html($it); ?></li><?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div>
    </section>
    <?php
    return ob_get_clean();
});

/* ------------------------------------------------------------------
 * 3. MFO: длинный SEO-блок (post_content + опц. ACF mfo_seo_content)
 * ------------------------------------------------------------------ */
add_shortcode('zaymi_mfo_seo_content', function ($atts) {
    $a   = shortcode_atts(['slug' => ''], $atts);
    $mfo = $a['slug'] ? zaymi_get_mfo_by_slug($a['slug']) : get_post();
    if (!$mfo) return '';

    $extra = function_exists('get_field') ? (string) get_field('mfo_seo_content', $mfo->ID) : '';
    $body  = trim($mfo->post_content) ?: $extra;
    if (!$body) return '';

    ob_start(); ?>
    <section class="zo-mfo-seo">
      <article class="zo-container-narrow prose-article">
        <h2>Подробнее о <?php echo esc_html(get_the_title($mfo)); ?></h2>
        <?php echo apply_filters('the_content', $body); ?>
      </article>
    </section>
    <?php
    return ob_get_clean();
});

/* ------------------------------------------------------------------
 * 4. MFO: breadcrumbs
 * ------------------------------------------------------------------ */
add_shortcode('zaymi_mfo_breadcrumbs', function ($atts) {
    $a   = shortcode_atts(['slug' => ''], $atts);
    $mfo = $a['slug'] ? zaymi_get_mfo_by_slug($a['slug']) : get_post();
    if (!$mfo) return '';
    $home = esc_url(home_url('/'));
    $cat  = esc_url(get_post_type_archive_link('mfo') ?: home_url('/mfo/'));
    ob_start(); ?>
    <nav class="zo-breadcrumbs zo-mfo-breadcrumbs" aria-label="Хлебные крошки">
      <div class="zo-container-wide">
        <ol>
          <li><a href="<?php echo $home; ?>">Главная</a></li>
          <li><span class="sep">›</span></li>
          <li><a href="<?php echo $cat; ?>">Каталог МФО</a></li>
          <li><span class="sep">›</span></li>
          <li class="current"><?php echo esc_html(get_the_title($mfo)); ?></li>
        </ol>
      </div>
    </nav>
    <?php
    return ob_get_clean();
});

/* ------------------------------------------------------------------
 * Стили (минимальные, остальное в общем CSS темы)
 * ------------------------------------------------------------------ */
add_action('wp_enqueue_scripts', function () {
    if (!is_singular('mfo') && !is_front_page() && !is_home()) return;
    $css = "
    /* Newsletter */
    .zo-newsletter-cta{padding:80px 24px;}
    .zo-newsletter-card{position:relative;overflow:hidden;border-radius:28px;padding:48px;color:#fff;
      background:radial-gradient(circle at 85% 20%,rgba(255,255,255,.18),transparent 40%),radial-gradient(circle at 10% 90%,rgba(37,99,235,.35),transparent 45%),linear-gradient(135deg,#10b981 0%,#059669 50%,#0f766e 100%);
      box-shadow:0 30px 60px -20px rgba(15,118,110,.45);display:grid;gap:32px;}
    @media(min-width:900px){.zo-newsletter-card{grid-template-columns:1.4fr 1fr;align-items:center;}}
    .zo-newsletter-card .kicker{display:inline-flex;gap:8px;align-items:center;background:rgba(255,255,255,.18);padding:6px 14px;border-radius:999px;font-size:12px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;}
    .zo-newsletter-card h2{margin:14px 0 8px;font-size:36px;font-weight:800;line-height:1.15;}
    .zo-newsletter-card p{font-size:17px;opacity:.92;}
    .zo-newsletter-form{display:flex;gap:6px;background:#fff;padding:7px;border-radius:999px;box-shadow:0 20px 45px -15px rgba(0,0,0,.25);}
    .zo-newsletter-form input{flex:1;border:0;background:transparent;padding:0 18px;font-size:14px;font-weight:600;color:#0f172a;outline:none;min-width:0;}
    .zo-newsletter-form button{height:44px;padding:0 22px;border:0;border-radius:999px;background:#0f172a;color:#fff;font-size:14px;font-weight:700;cursor:pointer;transition:.15s;}
    .zo-newsletter-form button:hover{background:#000;}

    /* Fit */
    .zo-fit{padding:64px 24px;background:#f4f7fb;}
    .zo-fit .zo-section-head{text-align:center;max-width:640px;margin:0 auto 40px;}
    .zo-fit .zo-section-head h2{font-size:36px;font-weight:800;color:#0f172a;}
    .zo-fit .zo-section-head p{margin-top:10px;color:#64748b;font-size:17px;}
    .zo-fit-grid{display:grid;gap:20px;}
    @media(min-width:768px){.zo-fit-grid{grid-template-columns:1fr 1fr;}}
    .zo-fit-card{border-radius:18px;padding:28px;border:1px solid;box-shadow:0 8px 20px -10px rgba(0,0,0,.08);}
    .zo-fit-yes{background:rgba(16,185,129,.08);border-color:rgba(16,185,129,.25);}
    .zo-fit-no{background:rgba(239,68,68,.06);border-color:rgba(239,68,68,.25);}
    .zo-fit-head{display:flex;gap:12px;align-items:center;margin-bottom:18px;}
    .zo-fit-head .ico{width:40px;height:40px;border-radius:50%;color:#fff;display:inline-flex;align-items:center;justify-content:center;font-weight:800;font-size:18px;}
    .zo-fit-yes .zo-fit-head .ico{background:#10b981;}
    .zo-fit-no  .zo-fit-head .ico{background:#ef4444;}
    .zo-fit-card h3{font-size:20px;font-weight:800;color:#0f172a;margin:0;}
    .zo-fit-card ul{list-style:none;padding:0;margin:0;display:grid;gap:12px;}
    .zo-fit-card li{display:flex;gap:10px;align-items:flex-start;font-size:15px;font-weight:600;color:#0f172a;}
    .zo-fit-card .b{flex-shrink:0;width:20px;height:20px;border-radius:50%;color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;margin-top:2px;}
    .zo-fit-yes .b{background:#10b981;}
    .zo-fit-no  .b{background:#ef4444;}

    /* MFO breadcrumbs */
    .zo-mfo-breadcrumbs{padding:18px 24px;background:#fff;border-bottom:1px solid #eef2f7;}

    /* MFO seo content */
    .zo-mfo-seo{padding:64px 24px;}
    .zo-mfo-seo h2{font-size:34px;font-weight:800;color:#0f172a;}
    .zo-mfo-seo p{margin:14px 0;font-size:16px;line-height:1.75;color:rgba(15,23,42,.85);}
    .zo-mfo-seo h3{margin-top:36px;font-size:22px;font-weight:800;color:#0f172a;}
    .zo-mfo-seo ul{padding-left:22px;}
    .zo-mfo-seo ul li{margin:8px 0;}
    .zo-mfo-seo ul li::marker{color:#10b981;}
    ";
    wp_add_inline_style('zaymi-trust', $css);
}, 30);
