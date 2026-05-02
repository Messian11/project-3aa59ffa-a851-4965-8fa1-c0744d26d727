<?php
/**
 * Zaymi Analytics — встроенный модуль счётчиков и целей.
 *
 * Не зависит от ACF и сторонних плагинов. Хранит настройки в одной wp_option.
 * Автоматически:
 *  - вставляет Я.Метрику, Google Analytics 4 и GTM (head + body noscript);
 *  - регистрирует JS-цели: cpa_click, lead_submit, calculator_use, scroll_75,
 *    time_on_site_2min, phone_click, compare_mfo;
 *  - даёт админскую страницу "📈 Аналитика" под пунктом «📊 Zaymi».
 *
 * @package Zaymi
 */

if (!defined('ABSPATH')) exit;

const ZAYMI_ANALYTICS_OPT = 'zaymi_analytics_settings';

function zaymi_analytics_get(): array {
    $defaults = [
        'ym_id'      => '',
        'ga4_id'     => '',
        'gtm_id'     => '',
        'webvisor'   => 1,
        'clickmap'   => 1,
        'ecommerce'  => 1,
        'fb_pixel'   => '',
        'vk_pixel'   => '',
        'head_extra' => '',
        'body_extra' => '',
    ];
    return wp_parse_args(get_option(ZAYMI_ANALYTICS_OPT, []), $defaults);
}

/* ---------- Меню ---------- */
add_action('admin_menu', function () {
    add_submenu_page(
        'zaymi-dashboard',
        'Аналитика и цели',
        '📈 Аналитика',
        'manage_options',
        'zaymi-analytics',
        'zaymi_analytics_page'
    );
}, 25);

function zaymi_analytics_page() {
    if (!current_user_can('manage_options')) return;

    if (!empty($_POST['zaymi_analytics_nonce']) && wp_verify_nonce($_POST['zaymi_analytics_nonce'], 'zaymi_analytics_save')) {
        $data = [
            'ym_id'      => preg_replace('/\D+/', '', $_POST['ym_id'] ?? ''),
            'ga4_id'     => sanitize_text_field($_POST['ga4_id'] ?? ''),
            'gtm_id'     => sanitize_text_field($_POST['gtm_id'] ?? ''),
            'webvisor'   => empty($_POST['webvisor'])  ? 0 : 1,
            'clickmap'   => empty($_POST['clickmap'])  ? 0 : 1,
            'ecommerce'  => empty($_POST['ecommerce']) ? 0 : 1,
            'fb_pixel'   => sanitize_text_field($_POST['fb_pixel'] ?? ''),
            'vk_pixel'   => sanitize_text_field($_POST['vk_pixel'] ?? ''),
            'head_extra' => trim((string)($_POST['head_extra'] ?? '')),
            'body_extra' => trim((string)($_POST['body_extra'] ?? '')),
        ];
        update_option(ZAYMI_ANALYTICS_OPT, $data);
        echo '<div class="notice notice-success is-dismissible"><p>✅ Настройки сохранены. Счётчики уже работают на всех страницах сайта.</p></div>';
    }

    $s = zaymi_analytics_get();
    ?>
    <div class="wrap">
      <h1>📈 Аналитика и цели</h1>
      <p>Заполни ID счётчиков — они появятся <b>сразу на всех страницах</b> сайта. Цели для Я.Метрики и GA4 уже встроены (см. список ниже).</p>

      <form method="post" style="max-width:900px">
        <?php wp_nonce_field('zaymi_analytics_save', 'zaymi_analytics_nonce'); ?>

        <h2 style="margin-top:30px">🟡 Яндекс.Метрика</h2>
        <table class="form-table">
          <tr>
            <th><label>Номер счётчика</label></th>
            <td>
              <input type="text" name="ym_id" value="<?php echo esc_attr($s['ym_id']); ?>" placeholder="98765432" class="regular-text" />
              <p class="description">Только цифры. Создать счётчик: <a href="https://metrika.yandex.ru" target="_blank">metrika.yandex.ru</a></p>
            </td>
          </tr>
          <tr>
            <th>Опции</th>
            <td>
              <label><input type="checkbox" name="webvisor"  <?php checked($s['webvisor']); ?>> Webvisor (запись сессий)</label><br>
              <label><input type="checkbox" name="clickmap"  <?php checked($s['clickmap']); ?>> Карта кликов</label><br>
              <label><input type="checkbox" name="ecommerce" <?php checked($s['ecommerce']); ?>> Ecommerce (dataLayer)</label>
            </td>
          </tr>
        </table>

        <h2 style="margin-top:30px">🔵 Google Analytics 4</h2>
        <table class="form-table">
          <tr>
            <th><label>Measurement ID</label></th>
            <td>
              <input type="text" name="ga4_id" value="<?php echo esc_attr($s['ga4_id']); ?>" placeholder="G-XXXXXXXXXX" class="regular-text" />
              <p class="description">Формат <code>G-XXXXXXXXXX</code>. Получить: <a href="https://analytics.google.com" target="_blank">analytics.google.com</a> → Admin → Data Streams.</p>
            </td>
          </tr>
        </table>

        <h2 style="margin-top:30px">🟢 Google Tag Manager</h2>
        <table class="form-table">
          <tr>
            <th><label>GTM ID</label></th>
            <td>
              <input type="text" name="gtm_id" value="<?php echo esc_attr($s['gtm_id']); ?>" placeholder="GTM-XXXXXXX" class="regular-text" />
              <p class="description">Если ставишь GTM — все остальные пиксели лучше добавлять <b>внутри GTM</b>, а не сюда.</p>
            </td>
          </tr>
        </table>

        <h2 style="margin-top:30px">🟣 Пиксели рекламы</h2>
        <table class="form-table">
          <tr>
            <th><label>VK Ads пиксель</label></th>
            <td><input type="text" name="vk_pixel" value="<?php echo esc_attr($s['vk_pixel']); ?>" placeholder="VK-RTRG-XXXXX-XXXXX" class="regular-text" /></td>
          </tr>
          <tr>
            <th><label>Facebook / Meta пиксель</label></th>
            <td><input type="text" name="fb_pixel" value="<?php echo esc_attr($s['fb_pixel']); ?>" placeholder="123456789012345" class="regular-text" /></td>
          </tr>
        </table>

        <h2 style="margin-top:30px">🛠 Свой код</h2>
        <table class="form-table">
          <tr>
            <th><label>Дополнительный код в &lt;head&gt;</label></th>
            <td><textarea name="head_extra" rows="6" class="large-text code" placeholder="<!-- любые скрипты, мета-теги верификации -->"><?php echo esc_textarea($s['head_extra']); ?></textarea></td>
          </tr>
          <tr>
            <th><label>Код перед &lt;/body&gt;</label></th>
            <td><textarea name="body_extra" rows="6" class="large-text code"><?php echo esc_textarea($s['body_extra']); ?></textarea></td>
          </tr>
        </table>

        <p><button type="submit" class="button button-primary button-large">💾 Сохранить</button></p>
      </form>

      <hr style="margin:40px 0">

      <h2>🎯 Встроенные цели (работают автоматически)</h2>
      <p>Тема сама отслеживает эти события и шлёт их в Я.Метрику (<code>ym(ID, 'reachGoal', '...')</code>) и в GA4 (<code>gtag('event', '...')</code>). Просто создай одноимённые цели в кабинетах.</p>
      <table class="widefat striped" style="max-width:900px">
        <thead><tr><th>Цель</th><th>Триггер</th><th>Зачем</th></tr></thead>
        <tbody>
          <tr><td><code>cpa_click</code></td><td>клик по кнопке "Получить займ" / редирект через /go/</td><td>🔥 главная цель для рекламы</td></tr>
          <tr><td><code>lead_submit</code></td><td>отправка формы заявки</td><td>заявка с сайта</td></tr>
          <tr><td><code>mfo_view</code></td><td>посещение страницы /mfo/*</td><td>интерес к продукту</td></tr>
          <tr><td><code>compare_mfo</code></td><td>сравнение МФО</td><td>горячий клиент</td></tr>
          <tr><td><code>calculator_use</code></td><td>взаимодействие с калькулятором</td><td>тёплая аудитория</td></tr>
          <tr><td><code>phone_click</code></td><td>клик по телефону / WhatsApp / Telegram</td><td>микро-конверсия</td></tr>
          <tr><td><code>scroll_75</code></td><td>проскроллил 75% страницы</td><td>вовлечение</td></tr>
          <tr><td><code>time_on_site_2min</code></td><td>120 сек на сайте</td><td>вовлечение</td></tr>
          <tr><td><code>newsletter_subscribe</code></td><td>подписка на рассылку</td><td>email-база</td></tr>
        </tbody>
      </table>

      <h3 style="margin-top:30px">📋 Как настроить цели в Я.Метрике</h3>
      <ol>
        <li>metrika.yandex.ru → твой счётчик → <b>Настройки → Цели → Добавить цель</b></li>
        <li>Тип: <b>JavaScript-событие</b></li>
        <li>Идентификатор цели: вставь точное имя из таблицы выше (например <code>cpa_click</code>)</li>
        <li>Сохранить. Повторить для каждой цели.</li>
      </ol>

      <h3>📋 Как настроить в Google Analytics 4</h3>
      <ol>
        <li>analytics.google.com → Admin → <b>Events</b> — события прилетят сами через 24 ч</li>
        <li><b>Mark as conversion</b> у нужных (cpa_click, lead_submit)</li>
      </ol>
    </div>
    <?php
}

/* ---------- Вставка кода в &lt;head&gt; ---------- */
add_action('wp_head', function () {
    if (is_admin()) return;
    $s = zaymi_analytics_get();

    // Я.Метрика
    if (!empty($s['ym_id'])) {
        $ym  = (int)$s['ym_id'];
        $opts = ['clickmap' => (bool)$s['clickmap'], 'trackLinks' => true, 'accurateTrackBounce' => true, 'webvisor' => (bool)$s['webvisor']];
        if (!empty($s['ecommerce'])) $opts['ecommerce'] = 'dataLayer';
        $opts_json = wp_json_encode($opts, JSON_UNESCAPED_SLASHES);
        echo "\n<!-- Yandex.Metrika -->\n<script>(function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})(window,document,'script','https://mc.yandex.ru/metrika/tag.js','ym');ym($ym,'init',$opts_json);window._zaymi_ym=$ym;</script>\n<noscript><div><img src=\"https://mc.yandex.ru/watch/$ym\" style=\"position:absolute;left:-9999px\" alt=\"\"/></div></noscript>\n";
    }

    // GA4
    if (!empty($s['ga4_id'])) {
        $ga = esc_attr($s['ga4_id']);
        echo "\n<!-- GA4 -->\n<script async src=\"https://www.googletagmanager.com/gtag/js?id={$ga}\"></script>\n<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{$ga}');</script>\n";
    }

    // GTM
    if (!empty($s['gtm_id'])) {
        $gtm = esc_attr($s['gtm_id']);
        echo "\n<!-- Google Tag Manager -->\n<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','{$gtm}');</script>\n";
    }

    // VK Pixel
    if (!empty($s['vk_pixel'])) {
        $vk = esc_attr($s['vk_pixel']);
        echo "\n<!-- VK Ads Pixel -->\n<script>!function(){var t=document.createElement(\"script\");t.type=\"text/javascript\",t.async=!0,t.src='https://vk.com/js/api/openapi.js?169',t.onload=function(){VK.Retargeting.Init(\"{$vk}\"),VK.Retargeting.Hit()},document.head.appendChild(t)}();</script>\n";
    }

    // FB Pixel
    if (!empty($s['fb_pixel'])) {
        $fb = esc_attr($s['fb_pixel']);
        echo "\n<!-- Meta Pixel -->\n<script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','{$fb}');fbq('track','PageView');</script>\n";
    }

    if (!empty($s['head_extra'])) {
        echo "\n<!-- Custom head -->\n" . $s['head_extra'] . "\n";
    }
}, 1);

/* ---------- GTM &lt;noscript&gt; и кастомный body-код ---------- */
add_action('wp_body_open', function () {
    if (is_admin()) return;
    $s = zaymi_analytics_get();
    if (!empty($s['gtm_id'])) {
        $gtm = esc_attr($s['gtm_id']);
        echo "<noscript><iframe src=\"https://www.googletagmanager.com/ns.html?id={$gtm}\" height=\"0\" width=\"0\" style=\"display:none;visibility:hidden\"></iframe></noscript>\n";
    }
});

add_action('wp_footer', function () {
    if (is_admin()) return;
    $s = zaymi_analytics_get();
    if (!empty($s['body_extra'])) {
        echo "\n<!-- Custom body -->\n" . $s['body_extra'] . "\n";
    }

    // Универсальные JS-цели — работают если есть Я.Метрика или GA4/GTM
    if (empty($s['ym_id']) && empty($s['ga4_id']) && empty($s['gtm_id'])) return;
    ?>
<script>
(function(){
  function track(goal, params){
    try { if (window._zaymi_ym && typeof ym !== 'undefined') ym(window._zaymi_ym, 'reachGoal', goal, params || {}); } catch(e){}
    try { if (typeof gtag !== 'undefined') gtag('event', goal, params || {}); } catch(e){}
    try { if (window.dataLayer) window.dataLayer.push({event: goal, ...(params||{})}); } catch(e){}
  }
  window.zaymiTrack = track;

  // CPA-клик
  document.addEventListener('click', function(e){
    var a = e.target.closest('a[href*="/go/"], .zaymi-cpa-btn, [data-cpa]');
    if (a) track('cpa_click', { mfo: a.getAttribute('data-mfo') || a.textContent.trim().slice(0,40) });

    var p = e.target.closest('a[href^="tel:"], a[href^="https://wa.me"], a[href*="t.me/"], a[href*="api.whatsapp"]');
    if (p) track('phone_click');

    var c = e.target.closest('.zaymi-compare-btn, [data-compare]');
    if (c) track('compare_mfo');

    var n = e.target.closest('.zaymi-newsletter [type=submit], [data-newsletter] [type=submit]');
    if (n) track('newsletter_subscribe');
  }, true);

  // Отправка форм
  document.addEventListener('submit', function(e){
    var f = e.target;
    if (f.matches && (f.matches('.zaymi-lead-form, form[data-zaymi-lead], #zaymi-lead-form'))) track('lead_submit');
    if (f.matches && (f.matches('.zaymi-newsletter form, form[data-newsletter]'))) track('newsletter_subscribe');
  }, true);

  // Калькулятор
  document.addEventListener('input', function(e){
    if (e.target.closest('.zaymi-calculator, [data-calculator]')) {
      if (!window._zaymi_calc_done) { window._zaymi_calc_done = true; track('calculator_use'); }
    }
  }, true);

  // Просмотр МФО
  if (location.pathname.indexOf('/mfo/') === 0) track('mfo_view');

  // 75% скролла
  var s75 = false;
  window.addEventListener('scroll', function(){
    if (s75) return;
    var p = (window.scrollY + window.innerHeight) / document.documentElement.scrollHeight * 100;
    if (p >= 75) { s75 = true; track('scroll_75'); }
  }, { passive: true });

  // 2 минуты на сайте
  setTimeout(function(){ track('time_on_site_2min'); }, 120000);
})();
</script>
    <?php
});
