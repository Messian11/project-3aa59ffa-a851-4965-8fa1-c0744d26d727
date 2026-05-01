/* Zaymi Online — Trust & Compliance JS v2.2 */
(function(){
    'use strict';

    /* ---- Калькулятор ---- */
    document.querySelectorAll('.zaymi-calc').forEach(function(c){
        var rate = parseFloat(c.dataset.rate) || 0.8;
        var sumR = c.querySelector('.zaymi-calc__sum-range');
        var trmR = c.querySelector('.zaymi-calc__term-range');
        var sumO = c.querySelector('.zaymi-calc__sum');
        var trmO = c.querySelector('.zaymi-calc__term');
        var ovr  = c.querySelector('.zaymi-calc__overpay');
        var ttl  = c.querySelector('.zaymi-calc__total');
        function fmt(n){return Math.round(n).toLocaleString('ru-RU');}
        function recalc(){
            var s = +sumR.value, t = +trmR.value;
            var over = s * (rate/100) * t;
            sumO.textContent = fmt(s);
            trmO.textContent = t;
            ovr.textContent = fmt(over);
            ttl.textContent = fmt(s + over);
        }
        sumR.addEventListener('input', recalc);
        trmR.addEventListener('input', recalc);
        recalc();
    });

    /* ---- Форма отзыва ---- */
    document.querySelectorAll('.zaymi-review-form').forEach(function(f){
        f.addEventListener('submit', function(e){
            e.preventDefault();
            var msg = f.querySelector('.zaymi-review-form__msg');
            var fd = new FormData(f);
            fd.append('mfo_id', f.dataset.mfo);
            msg.className = 'zaymi-review-form__msg';
            msg.textContent = 'Отправляем...';
            fetch('/wp-json/zaymi/v1/review', {method:'POST', body:fd})
                .then(function(r){return r.json().then(function(d){return {ok:r.ok,d:d};});})
                .then(function(res){
                    if(res.ok && res.d.ok){
                        msg.className = 'zaymi-review-form__msg ok';
                        msg.textContent = res.d.message || 'Спасибо! Отзыв отправлен на модерацию.';
                        f.reset();
                    } else {
                        msg.className = 'zaymi-review-form__msg err';
                        msg.textContent = res.d.error === 'rate_limit'
                            ? 'Вы уже оставляли отзыв недавно.'
                            : 'Ошибка. Проверьте поля и попробуйте ещё раз.';
                    }
                })
                .catch(function(){
                    msg.className = 'zaymi-review-form__msg err';
                    msg.textContent = 'Ошибка сети.';
                });
        });
    });

    /* ---- Сравнение: чек-боксы → /sravnenie/?compare=1,2,3 ---- */
    var compareBox = document.getElementById('zaymi-compare-bar');
    document.querySelectorAll('.zaymi-mfo-compare-cb').forEach(function(cb){
        cb.addEventListener('change', function(){
            var checked = Array.from(document.querySelectorAll('.zaymi-mfo-compare-cb:checked')).map(function(x){return x.value;});
            if(compareBox){
                compareBox.hidden = checked.length < 2;
                var link = compareBox.querySelector('a');
                if(link) link.href = '/sravnenie/?compare=' + checked.join(',');
                var counter = compareBox.querySelector('.zaymi-compare-count');
                if(counter) counter.textContent = checked.length;
            }
        });
    });

    /* ---- Гео: подстановка города в [data-zaymi-city] ---- */
    if (window.ZAYMI_CITY) {
        document.querySelectorAll('[data-zaymi-city]').forEach(function(el){
            el.textContent = window.ZAYMI_CITY;
        });
    }
})();
