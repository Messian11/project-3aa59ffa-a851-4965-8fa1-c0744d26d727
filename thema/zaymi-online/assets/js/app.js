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
