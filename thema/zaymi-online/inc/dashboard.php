<?php
/**
 * Zaymi Online — Админ-дашборд с метриками.
 *
 * Меню "Заявки → Дашборд":
 *  - KPI-карточки: всего заявок, заявок сегодня, кликов сегодня, общий CR, расчётный доход
 *  - График: заявки по дням (за 30 дней)
 *  - График: клики vs заявки по дням
 *  - Bar: CR по МФО (топ-15)
 *  - Pie: топ-10 источников трафика (utm_source)
 *  - Bar: доход по МФО (клики × payout_per_lead из ACF)
 *
 * Chart.js подгружается с CDN (admin only), без npm.
 *
 * @package Zaymi
 */

if (!defined('ABSPATH')) exit;

/* ---------- Поле "Выплата за лид (₽)" в МФО ---------- */
add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) return;
    acf_add_local_field_group([
        'key'    => 'group_mfo_payout',
        'title'  => 'Доход',
        'fields' => [
            ['key'=>'field_mfo_payout','label'=>'Выплата за подтверждённый лид, ₽','name'=>'payout_per_lead','type'=>'number','default_value'=>500,'instructions'=>'Используется для расчёта дохода в дашборде'],
            ['key'=>'field_mfo_approve_rate','label'=>'% апрува лидов','name'=>'approve_rate','type'=>'number','default_value'=>30,'min'=>0,'max'=>100,'instructions'=>'Сколько процентов лидов в среднем подтверждается партнёркой'],
        ],
        'location' => [[['param'=>'post_type','operator'=>'==','value'=>'mfo']]],
        'position' => 'side',
    ]);
});

/* ---------- Меню (отдельный top-level пункт) ---------- */
add_action('admin_menu', function () {
    add_menu_page('Zaymi Дашборд', '📊 Zaymi', 'manage_options', 'zaymi-dashboard', 'zaymi_dashboard_render', 'dashicons-chart-area', 3);
    // дублируем как подпункт в «Заявки» для совместимости
    add_submenu_page('zaymi-leads', 'Дашборд', '📊 Дашборд', 'manage_options', 'zaymi-dashboard', 'zaymi_dashboard_render');
}, 20);

/* ---------- Подключение Chart.js на нашей странице ---------- */
add_action('admin_enqueue_scripts', function ($hook) {
    if (strpos($hook, 'zaymi-dashboard') === false) return;
    wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js', [], '4.4.1', true);
});

/* ---------- Сбор данных ---------- */
function zaymi_dashboard_data($days = 30) {
    global $wpdb;
    $leads_t  = $wpdb->prefix . 'zaymi_leads';
    $clicks_t = $wpdb->prefix . 'zaymi_clicks';
    $since = date('Y-m-d', strtotime("-{$days} days"));

    // KPI
    $kpi = [
        'leads_total'   => (int)$wpdb->get_var("SELECT COUNT(*) FROM $leads_t"),
        'leads_today'   => (int)$wpdb->get_var("SELECT COUNT(*) FROM $leads_t WHERE DATE(created_at)=CURDATE()"),
        'leads_period'  => (int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $leads_t WHERE DATE(created_at)>=%s", $since)),
        'clicks_total'  => (int)$wpdb->get_var("SELECT COUNT(*) FROM $clicks_t"),
        'clicks_today'  => (int)$wpdb->get_var("SELECT COUNT(*) FROM $clicks_t WHERE DATE(created_at)=CURDATE()"),
        'clicks_period' => (int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $clicks_t WHERE DATE(created_at)>=%s", $since)),
    ];
    $kpi['cr'] = $kpi['clicks_period'] ? round($kpi['leads_period'] / $kpi['clicks_period'] * 100, 2) : 0;

    // Доход (расчётный): по каждому МФО — leads × approve_rate × payout
    $revenue = 0;
    $mfo_rev = [];
    if (function_exists('get_field')) {
        $by_mfo = $wpdb->get_results($wpdb->prepare(
            "SELECT mfo_slug, COUNT(*) AS c FROM $leads_t WHERE DATE(created_at)>=%s AND mfo_slug<>'' GROUP BY mfo_slug",
            $since
        ));
        foreach ($by_mfo as $r) {
            $post = get_page_by_path($r->mfo_slug, OBJECT, 'mfo');
            if (!$post) continue;
            $payout = (float) get_field('payout_per_lead', $post->ID);
            $appr   = (float) (get_field('approve_rate', $post->ID) ?: 30);
            $rev = $r->c * ($appr/100) * $payout;
            $revenue += $rev;
            $mfo_rev[get_the_title($post)] = round($rev);
        }
    }
    $kpi['revenue'] = round($revenue);
    arsort($mfo_rev);

    // Заявки по дням
    $by_day = $wpdb->get_results($wpdb->prepare(
        "SELECT DATE(created_at) d, COUNT(*) c FROM $leads_t WHERE DATE(created_at)>=%s GROUP BY d ORDER BY d", $since
    ));
    // Клики по дням
    $clicks_day = $wpdb->get_results($wpdb->prepare(
        "SELECT DATE(created_at) d, COUNT(*) c FROM $clicks_t WHERE DATE(created_at)>=%s GROUP BY d ORDER BY d", $since
    ));

    // Заполняем все дни (включая нули)
    $labels = []; $leads_series = []; $clicks_series = [];
    $leads_map = []; foreach ($by_day as $r) $leads_map[$r->d] = (int)$r->c;
    $clicks_map = []; foreach ($clicks_day as $r) $clicks_map[$r->d] = (int)$r->c;
    for ($i=$days-1; $i>=0; $i--) {
        $d = date('Y-m-d', strtotime("-{$i} days"));
        $labels[] = date('d.m', strtotime($d));
        $leads_series[]  = $leads_map[$d]  ?? 0;
        $clicks_series[] = $clicks_map[$d] ?? 0;
    }

    // CR по МФО
    $cr_rows = $wpdb->get_results("
        SELECT c.mfo_slug,
               COUNT(*) AS clicks,
               (SELECT COUNT(*) FROM $leads_t l WHERE l.mfo_slug=c.mfo_slug) AS leads
        FROM $clicks_t c GROUP BY c.mfo_slug HAVING clicks>0 ORDER BY clicks DESC LIMIT 15
    ");
    $cr_labels=[]; $cr_values=[];
    foreach ($cr_rows as $r) {
        $cr_labels[] = $r->mfo_slug;
        $cr_values[] = round($r->leads / max(1,$r->clicks) * 100, 2);
    }

    // Топ источников
    $src_rows = $wpdb->get_results("SELECT IFNULL(NULLIF(utm_source,''),'(direct)') s, COUNT(*) c FROM $leads_t GROUP BY s ORDER BY c DESC LIMIT 10");
    $src_labels=[]; $src_values=[];
    foreach ($src_rows as $r) { $src_labels[]=$r->s; $src_values[]=(int)$r->c; }

    return [
        'kpi'          => $kpi,
        'days_labels'  => $labels,
        'leads_series' => $leads_series,
        'clicks_series'=> $clicks_series,
        'cr_labels'    => $cr_labels,
        'cr_values'    => $cr_values,
        'src_labels'   => $src_labels,
        'src_values'   => $src_values,
        'revenue_labels'=> array_keys($mfo_rev),
        'revenue_values'=> array_values($mfo_rev),
    ];
}

/* ---------- Рендер страницы ---------- */
function zaymi_dashboard_render() {
    $period = isset($_GET['period']) ? max(7, min(90, (int)$_GET['period'])) : 30;
    $d = zaymi_dashboard_data($period);
    $k = $d['kpi'];
    ?>
    <div class="wrap zaymi-dash">
      <h1>📊 Дашборд Zaymi
        <span class="zd-period">
          <?php foreach ([7,14,30,60,90] as $p): ?>
            <a href="<?php echo esc_url(add_query_arg('period',$p)); ?>" class="<?php echo $p===$period?'is-active':''; ?>"><?php echo $p; ?> дн.</a>
          <?php endforeach; ?>
        </span>
      </h1>

      <div class="zd-kpi">
        <div class="zd-card"><div class="zd-num"><?php echo number_format($k['leads_total'],0,'',' '); ?></div><div class="zd-lab">Всего заявок</div></div>
        <div class="zd-card zd-accent"><div class="zd-num"><?php echo $k['leads_today']; ?></div><div class="zd-lab">Заявок сегодня</div></div>
        <div class="zd-card"><div class="zd-num"><?php echo number_format($k['clicks_total'],0,'',' '); ?></div><div class="zd-lab">Кликов всего</div></div>
        <div class="zd-card"><div class="zd-num"><?php echo $k['clicks_today']; ?></div><div class="zd-lab">Кликов сегодня</div></div>
        <div class="zd-card zd-good"><div class="zd-num"><?php echo $k['cr']; ?>%</div><div class="zd-lab">CR за <?php echo $period; ?> дн.</div></div>
        <div class="zd-card zd-money"><div class="zd-num"><?php echo number_format($k['revenue'],0,'',' '); ?> ₽</div><div class="zd-lab">Расч. доход за <?php echo $period; ?> дн.</div></div>
      </div>

      <div class="zd-grid">
        <div class="zd-chart-box zd-wide">
          <h3>Заявки и клики по дням</h3>
          <div class="zd-canvas-wrap" style="height:320px"><canvas id="zdChartDaily"></canvas></div>
        </div>
        <div class="zd-chart-box">
          <h3>CR по МФО, %</h3>
          <div class="zd-canvas-wrap" style="height:380px"><canvas id="zdChartCR"></canvas></div>
        </div>
        <div class="zd-chart-box">
          <h3>Топ-10 источников (заявки)</h3>
          <div class="zd-canvas-wrap" style="height:380px"><canvas id="zdChartSrc"></canvas></div>
        </div>
        <div class="zd-chart-box zd-wide">
          <h3>Расчётный доход по МФО, ₽</h3>
          <div class="zd-canvas-wrap" style="height:320px"><canvas id="zdChartRev"></canvas></div>
        </div>
      </div>

      <p class="description" style="margin-top:20px">
        💡 Расчётный доход = (заявки × % апрува × выплата за лид). Настрой эти параметры в карточке каждого МФО.<br>
        💡 Реальный доход смотри в личных кабинетах партнёрок (Leads.su, Salid и т.д.) — здесь только прогноз.
      </p>
    </div>

    <style>
      .zaymi-dash h1{display:flex;align-items:center;gap:20px;flex-wrap:wrap}
      .zd-period{font-size:13px;font-weight:400;display:flex;gap:6px}
      .zd-period a{padding:4px 10px;border-radius:6px;background:#fff;border:1px solid #ccd0d4;text-decoration:none;color:#2271b1}
      .zd-period a.is-active{background:#2271b1;color:#fff;border-color:#2271b1}
      .zd-kpi{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin:20px 0}
      .zd-card{background:#fff;padding:20px;border-radius:10px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,.04)}
      .zd-card .zd-num{font-size:28px;font-weight:700;color:#0f172a;line-height:1}
      .zd-card .zd-lab{margin-top:8px;font-size:12px;color:#64748b;text-transform:uppercase;letter-spacing:.5px}
      .zd-accent{border-left:4px solid #f59e0b}
      .zd-good{border-left:4px solid #16a34a}
      .zd-money{background:linear-gradient(135deg,#16a34a,#15803d);color:#fff}
      .zd-money .zd-num,.zd-money .zd-lab{color:#fff!important}
      .zd-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px}
      .zd-chart-box{background:#fff;padding:20px;border-radius:10px;border:1px solid #e5e7eb}
      .zd-chart-box h3{margin:0 0 14px;font-size:15px;color:#0f172a}
      .zd-wide{grid-column:1/-1}
      @media(max-width:900px){.zd-grid{grid-template-columns:1fr}}
    </style>

    <script>
      (function(){
        var data = <?php echo wp_json_encode($d); ?>;
        function ready(fn){ if (window.Chart) fn(); else setTimeout(function(){ ready(fn); }, 100); }
        ready(function(){
          var green='#16a34a', blue='#2563eb', orange='#f59e0b';
          var palette=['#16a34a','#2563eb','#f59e0b','#dc2626','#7c3aed','#0891b2','#db2777','#65a30d','#ea580c','#4f46e5'];

          new Chart(document.getElementById('zdChartDaily'), {
            type:'line',
            data:{labels:data.days_labels, datasets:[
              {label:'Клики', data:data.clicks_series, borderColor:blue, backgroundColor:'rgba(37,99,235,.1)', fill:true, tension:.3},
              {label:'Заявки', data:data.leads_series, borderColor:green, backgroundColor:'rgba(22,163,74,.15)', fill:true, tension:.3},
            ]},
            options:{responsive:true, maintainAspectRatio:false, plugins:{legend:{position:'top'}}, scales:{y:{beginAtZero:true}}}
          });

          new Chart(document.getElementById('zdChartCR'), {
            type:'bar',
            data:{labels:data.cr_labels, datasets:[{label:'CR, %', data:data.cr_values, backgroundColor:green}]},
            options:{indexAxis:'y', responsive:true, plugins:{legend:{display:false}}, scales:{x:{beginAtZero:true}}}
          });

          new Chart(document.getElementById('zdChartSrc'), {
            type:'doughnut',
            data:{labels:data.src_labels, datasets:[{data:data.src_values, backgroundColor:palette}]},
            options:{responsive:true, plugins:{legend:{position:'right'}}}
          });

          new Chart(document.getElementById('zdChartRev'), {
            type:'bar',
            data:{labels:data.revenue_labels, datasets:[{label:'Доход, ₽', data:data.revenue_values, backgroundColor:'#15803d'}]},
            options:{responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}}}
          });
        });
      })();
    </script>
    <?php
}
