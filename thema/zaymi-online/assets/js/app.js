/* Zaymi Online — клиентский JS: мобильное меню, аккордеоны, калькулятор */
(function () {
  'use strict';

  // ===== Мобильное меню =====
  document.querySelectorAll('[data-zaymi-toggle="mobile-menu"]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var menu = document.querySelector('[data-zaymi-mobile-menu]');
      if (!menu) return;
      menu.classList.toggle('hidden');
      menu.classList.toggle('is-open');
    });
  });

  // ===== Аккордеоны =====
  document.querySelectorAll('[data-zaymi-accordion]').forEach(function (acc) {
    acc.querySelectorAll('[data-acc-item]').forEach(function (item) {
      var trigger = item.querySelector('[data-acc-trigger]');
      var panel = item.querySelector('[data-acc-panel]');
      if (!trigger || !panel) return;
      trigger.addEventListener('click', function () {
        var open = item.classList.toggle('is-open');
        if (open) {
          panel.classList.remove('hidden');
        } else {
          panel.classList.add('hidden');
        }
      });
    });
  });

  // ===== Калькулятор =====
  document.querySelectorAll('[data-zaymi-calc]').forEach(function (calc) {
    var amount = calc.querySelector('[data-input="amount"]');
    var term   = calc.querySelector('[data-input="term"]');
    var aOut   = calc.querySelector('[data-out="amount"]');
    var tOut   = calc.querySelector('[data-out="term"]');
    var cOut   = calc.parentElement && calc.parentElement.querySelector('[data-out="count"]');

    function pluralDays(n) {
      var m10 = n % 10, m100 = n % 100;
      if (m10 === 1 && m100 !== 11) return 'день';
      if (m10 >= 2 && m10 <= 4 && (m100 < 10 || m100 >= 20)) return 'дня';
      return 'дней';
    }
    function fmt(n) { return Number(n).toLocaleString('ru-RU'); }
    function update() {
      var av = +amount.value, tv = +term.value;
      if (aOut) aOut.textContent = fmt(av) + ' ₽';
      if (tOut) tOut.textContent = tv + ' ' + pluralDays(tv);
      if (cOut) {
        var count = Math.max(3, Math.min(48, Math.floor((av/2000) + (tv/15))));
        cOut.textContent = count;
      }
      amount.style.setProperty('--val', ((av-amount.min)/(amount.max-amount.min)*100) + '%');
      term.style.setProperty('--val', ((tv-term.min)/(term.max-term.min)*100) + '%');
    }
    if (amount && term) {
      amount.addEventListener('input', update);
      term.addEventListener('input', update);
      update();
    }
  });
})();

/* ===== Поиск в шапке (REST: МФО + посты) ===== */
(function(){
  'use strict';
  var forms = document.querySelectorAll('[data-zaymi-search]');
  if (!forms.length) return;
  var apiBase = (window.zaymiData && window.zaymiData.restUrl) || '/wp-json/';

  forms.forEach(function(form){
    var input = form.querySelector('[data-zaymi-search-input]');
    var box   = form.querySelector('[data-zaymi-search-results]');
    if (!input || !box) return;
    var t = null, lastQ = '';
    function hide(){ box.classList.add('hidden'); }
    function show(){ box.classList.remove('hidden'); }

    function render(items){
      if (!items.length){
        box.innerHTML = '<div class="px-3 py-3 text-sm text-slate-500">Ничего не найдено</div>';
        return;
      }
      box.innerHTML = items.map(function(it){
        var icon = it.subtype === 'mfo' ? '🏦' : '📝';
        var label = it.subtype === 'mfo' ? 'МФО' : 'Статья';
        return '<a href="'+it.url+'" class="flex items-start gap-3 rounded-md px-3 py-2 hover:bg-slate-50">'
          + '<span class="text-xl shrink-0 leading-none mt-0.5">'+icon+'</span>'
          + '<div class="min-w-0">'
          +   '<div class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">'+label+'</div>'
          +   '<div class="text-sm font-semibold text-slate-900 truncate">'+(it.title||'(без названия)')+'</div>'
          + '</div></a>';
      }).join('');
    }

    function doSearch(q){
      if (q === lastQ) return; lastQ = q;
      if (q.length < 2){ hide(); return; }
      box.innerHTML = '<div class="px-3 py-3 text-sm text-slate-500">Ищем…</div>';
      show();
      var url = apiBase + 'wp/v2/search?per_page=8&search=' + encodeURIComponent(q) + '&subtype[]=mfo&subtype[]=post';
      fetch(url, { credentials: 'same-origin' })
        .then(function(r){ return r.ok ? r.json() : []; })
        .then(function(data){
          if (lastQ !== q) return;
          render(Array.isArray(data) ? data : []);
        })
        .catch(function(){ box.innerHTML = '<div class="px-3 py-3 text-sm text-rose-500">Ошибка поиска</div>'; });
    }
    input.addEventListener('input', function(){
      if (t) clearTimeout(t);
      var q = input.value.trim();
      t = setTimeout(function(){ doSearch(q); }, 220);
    });
    input.addEventListener('focus', function(){ if (input.value.trim().length >= 2) show(); });
    document.addEventListener('click', function(e){
      if (!form.contains(e.target)) hide();
    });
  });
})();

/* ===== MFO Filter — фильтр/сортировка/grid-list ===== */
(function(){
  'use strict';
  var roots = document.querySelectorAll('[data-zaymi-filter]');
  if (!roots.length) return;

  roots.forEach(function(root){
    var grid    = root.querySelector('[data-zf-grid]');
    var items   = Array.from(root.querySelectorAll('[data-zf-item]'));
    var countEl = root.querySelector('[data-zf-count]');
    var emptyEl = root.querySelector('[data-zf-empty]');
    var sortSel = root.querySelector('[data-zf-sort]');
    var resetBtn= root.querySelector('[data-zf-reset]');
    var viewBtns= Array.from(root.querySelectorAll('[data-zf-view]'));
    var feats   = Array.from(root.querySelectorAll('[data-zf-feature]'));
    var inputs  = {
      sumMin:  root.querySelector('[data-zf-input="sum-min"]'),
      sumMax:  root.querySelector('[data-zf-input="sum-max"]'),
      termMin: root.querySelector('[data-zf-input="term-min"]'),
      termMax: root.querySelector('[data-zf-input="term-max"]'),
    };
    var outs = {
      sumMin:  root.querySelector('[data-zf-out="sum-min"]'),
      sumMax:  root.querySelector('[data-zf-out="sum-max"]'),
      termMin: root.querySelector('[data-zf-out="term-min"]'),
      termMax: root.querySelector('[data-zf-out="term-max"]'),
    };

    function num(v, def){ var n = parseFloat(v); return isNaN(n) ? def : n; }
    function fmt(n){ return Number(n).toLocaleString('ru-RU'); }

    function apply(){
      var sumMin  = num(inputs.sumMin && inputs.sumMin.value, 0);
      var sumMax  = num(inputs.sumMax && inputs.sumMax.value, Infinity);
      var termMin = num(inputs.termMin && inputs.termMin.value, 0);
      var termMax = num(inputs.termMax && inputs.termMax.value, Infinity);
      var activeFeats = feats.filter(function(f){return f.checked;}).map(function(f){return f.value;});

      if (outs.sumMin)  outs.sumMin.textContent  = fmt(sumMin);
      if (outs.sumMax)  outs.sumMax.textContent  = isFinite(sumMax) ? fmt(sumMax) : '∞';
      if (outs.termMin) outs.termMin.textContent = termMin;
      if (outs.termMax) outs.termMax.textContent = isFinite(termMax) ? termMax : '∞';

      var visible = 0;
      items.forEach(function(it){
        var aMax = +it.dataset.amountMax || 0;
        var tMax = +it.dataset.termMax   || 0;
        // Логика: МФО подходит, если его диапазон сумм пересекается с запросом
        var okSum  = aMax >= sumMin && (sumMax === Infinity || (+it.dataset.amountMin || 0) <= sumMax);
        var okTerm = tMax >= termMin && (termMax === Infinity || (+it.dataset.termMin || 0) <= termMax);
        var itemFeats = (it.dataset.features || '').split(',').filter(Boolean);
        var okFeats = activeFeats.every(function(f){ return itemFeats.indexOf(f) !== -1; });
        var show = okSum && okTerm && okFeats;
        it.style.display = show ? '' : 'none';
        if (show) visible++;
      });
      if (countEl) countEl.textContent = visible;
      if (emptyEl) emptyEl.classList.toggle('hidden', visible !== 0);
    }

    function sort(){
      if (!sortSel) return;
      var key = sortSel.value;
      var arr = items.slice();
      arr.sort(function(a, b){
        switch(key){
          case 'rate':     return (+a.dataset.rate || 999) - (+b.dataset.rate || 999);
          case 'amount':   return (+b.dataset.amountMax || 0) - (+a.dataset.amountMax || 0);
          case 'approval': return (+b.dataset.approval || 0) - (+a.dataset.approval || 0);
          default:         return (+b.dataset.rating || 0) - (+a.dataset.rating || 0);
        }
      });
      arr.forEach(function(it){ grid.appendChild(it); });
    }

    function setView(v){
      root.dataset.view = v;
      viewBtns.forEach(function(b){
        var active = b.dataset.zfView === v;
        b.dataset.active = active ? 'true' : 'false';
        b.classList.toggle('text-blue-600', active);
        b.classList.toggle('text-slate-400', !active);
      });
    }

    Object.values(inputs).forEach(function(el){ if (el) el.addEventListener('input', apply); });
    feats.forEach(function(f){ f.addEventListener('change', apply); });
    if (sortSel) sortSel.addEventListener('change', sort);
    if (resetBtn) resetBtn.addEventListener('click', function(){
      Object.values(inputs).forEach(function(el){ if (el) el.value = ''; });
      feats.forEach(function(f){ f.checked = false; });
      apply();
    });
    viewBtns.forEach(function(b){ b.addEventListener('click', function(){ setView(b.dataset.zfView); }); });

    setView('grid');
    sort();
    apply();
  });
})();
