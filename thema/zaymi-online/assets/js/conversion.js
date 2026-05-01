/* Zaymi Online — Conversion widgets
 * Exit-intent, "approved just now" notice, lead form, progress bar.
 * Все настройки приходят из window.ZaymiConv.
 */
(function(){
  'use strict';
  var C = window.ZaymiConv || {};

  /* ---------- helpers ---------- */
  function qs(s,p){return (p||document).querySelector(s);}
  function qsa(s,p){return Array.prototype.slice.call((p||document).querySelectorAll(s));}
  function getUTM(){
    var u={},p=new URLSearchParams(location.search);
    ['utm_source','utm_medium','utm_campaign','utm_content','utm_term','gclid','yclid'].forEach(function(k){
      var v=p.get(k); if(v){u[k]=v; try{sessionStorage.setItem('zaymi_'+k,v);}catch(e){}}
      else{ try{var s=sessionStorage.getItem('zaymi_'+k); if(s) u[k]=s;}catch(e){} }
    });
    return u;
  }

  /* ---------- 1. Lead form submission ---------- */
  qsa('form[data-zaymi-lead]').forEach(function(form){
    form.addEventListener('submit', function(ev){
      ev.preventDefault();
      var btn = form.querySelector('button[type=submit]');
      var res = form.querySelector('.zlf-result');
      btn.disabled = true; btn.textContent = 'Отправка...';

      var fd = new FormData(form);
      var utm = getUTM();
      var payload = {
        name: fd.get('name'),
        phone: fd.get('phone'),
        email: fd.get('email') || '',
        amount: parseInt(fd.get('amount')||'0',10),
        term: parseInt(fd.get('term')||'0',10),
        mfo_id: form.dataset.mfoId || '',
        mfo_slug: form.dataset.mfoSlug || '',
        page_url: location.href,
        referer: document.referrer,
        website: fd.get('website') || '',
        utm_source: utm.utm_source||'', utm_medium: utm.utm_medium||'',
        utm_campaign: utm.utm_campaign||'', utm_content: utm.utm_content||'',
        utm_term: utm.utm_term||'', gclid: utm.gclid||'', yclid: utm.yclid||''
      };

      function send() {
        return fetch(C.restUrl, {
          method:'POST', headers:{'Content-Type':'application/json'},
          body: JSON.stringify(payload)
        }).then(function(r){return r.json();}).then(function(d){
        if(d && d.ok){
          if (C.progressEnabled && form.dataset.mfoSlug) {
            showProgress(form.dataset.mfoSlug);
          } else {
            res.hidden=false; res.className='zlf-result ok';
            res.textContent='✓ Заявка #'+d.id+' принята. Перезвоним в течение 5 минут.';
            form.reset();
          }
          // GA4 / Метрика
          if(window.gtag) gtag('event','generate_lead',{value:payload.amount,mfo:payload.mfo_slug});
          if(window.ym) ym(window._yaCounter||0,'reachGoal','lead');
        } else {
          res.hidden=false; res.className='zlf-result err';
          res.textContent='Ошибка: '+(d.error||'попробуйте ещё раз');
        }
      }).catch(function(){
        res.hidden=false; res.className='zlf-result err';
        res.textContent='Ошибка сети, попробуйте позже';
      }).finally(function(){
        btn.disabled=false; btn.textContent='Отправить заявку';
      });
    });
  });

  /* ---------- 2. Progress bar (после успешной заявки или клика "Получить") ---------- */
  function showProgress(mfoSlug){
    var box=document.createElement('div');
    box.className='zaymi-progress';
    box.innerHTML='<div class="zaymi-progress-box"><h3>Проверяем вашу заявку</h3><div class="zaymi-progress-bar"><i></i></div><div class="zaymi-progress-step">Отправка данных...</div></div>';
    document.body.appendChild(box);
    requestAnimationFrame(function(){box.classList.add('show');});

    var bar=box.querySelector('.zaymi-progress-bar i');
    var step=box.querySelector('.zaymi-progress-step');
    var steps=[
      [15,'Проверяем заявку...'],
      [40,'Сверяем с базой МФО...'],
      [70,'Подбираем лучшие условия...'],
      [95,'Почти готово...']
    ];
    var i=0;
    var tick=setInterval(function(){
      if(i>=steps.length){clearInterval(tick); finish(); return;}
      bar.style.width=steps[i][0]+'%'; step.textContent=steps[i][1]; i++;
    }, (C.progressDelay||2500)/steps.length);

    function finish(){
      box.classList.add('done');
      box.querySelector('h3').textContent='Одобрено!';
      step.textContent='Перенаправляем на оформление...';
      setTimeout(function(){
        location.href='/go/'+mfoSlug+'/';
      }, 1200);
    }
  }
  // Перехват кликов по кнопкам "Получить" с data-mfo-slug
  document.addEventListener('click', function(ev){
    var a=ev.target.closest('[data-zaymi-progress]');
    if(!a || !C.progressEnabled) return;
    ev.preventDefault();
    showProgress(a.dataset.zaymiProgress);
  });

  /* ---------- 3. "Approved just now" notice ---------- */
  if (C.noticeEnabled && C.noticeNames && C.noticeNames.length) {
    var shown=false;
    function showNotice(){
      if(shown) return;
      var name=pick(C.noticeNames), city=pick(C.noticeCities), mfo=pick(C.noticeMfo);
      var sum=(Math.floor(Math.random()*15)+3)*1000;
      var n=document.createElement('div');
      n.className='zaymi-notice';
      n.innerHTML='<button class="zn-close" aria-label="Закрыть">×</button><div class="zn-ico">✓</div><div class="zn-text"><b>'+name+' из '+city+'</b>получил '+sum.toLocaleString('ru-RU')+' ₽ в '+mfo+'</div>';
      document.body.appendChild(n);
      requestAnimationFrame(function(){n.classList.add('show');});
      n.querySelector('.zn-close').onclick=function(){n.remove();};
      setTimeout(function(){n.classList.remove('show'); setTimeout(function(){n.remove();},500);}, 7000);
      setTimeout(showNotice, (C.noticeInterval||25)*1000);
    }
    setTimeout(showNotice, 8000);
    function pick(a){return a[Math.floor(Math.random()*a.length)].trim();}
  }

  /* ---------- 4. Exit-intent popup ---------- */
  if (C.exitEnabled && C.topMfo && C.topMfo.length) {
    var fired=false;
    document.addEventListener('mouseleave', function(ev){
      if(fired || ev.clientY>0) return;
      if(sessionStorage.getItem('zaymi_exit_shown')) return;
      fired=true; sessionStorage.setItem('zaymi_exit_shown','1');
      var html='<div class="zaymi-exit-modal"><button class="zem-close" aria-label="Закрыть">×</button>';
      html+='<h2>'+escapeHtml(C.exitTitle)+'</h2>';
      html+='<p>Одобрение за 5 минут, деньги на карту мгновенно</p>';
      html+='<div class="zem-list">';
      C.topMfo.forEach(function(m){
        html+='<a class="zem-item" href="'+m.url+'">';
        if(m.logo) html+='<img src="'+m.logo+'" alt="'+escapeHtml(m.name)+'">';
        html+='<div><div class="zem-name">'+escapeHtml(m.name)+'</div>';
        if(m.rate) html+='<div class="zem-rate">от '+m.rate+'% в день</div>';
        html+='</div><span class="zem-cta">Получить</span></a>';
      });
      html+='</div></div>';
      var ov=document.createElement('div');
      ov.className='zaymi-exit-overlay'; ov.innerHTML=html;
      document.body.appendChild(ov);
      requestAnimationFrame(function(){ov.classList.add('show');});
      ov.addEventListener('click', function(e){
        if(e.target===ov || e.target.classList.contains('zem-close')) ov.remove();
      });
    });
  }
  function escapeHtml(s){return String(s||'').replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}
})();
