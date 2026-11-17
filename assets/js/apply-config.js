/* Aplica as configurações de config.js (editadas em admin.html) na página.
   Roda antes do main.js; se config.js faltar, o site usa os textos padrão do HTML. */
(function () {
  'use strict';
  var C = window.SYNTRA_CONFIG;
  if (!C) return;
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };
  var num = String(C.whatsapp || '').replace(/\D/g, '');
  var M = C.messages || {};
  var perfis = { clinica: 'clínica / consultório', diagnostico: 'centro de diagnóstico', hospital: 'hospital', rede: 'rede com várias unidades' };
  var wa = function (text) { return 'https://wa.me/' + num + '?text=' + encodeURIComponent(text); };

  /* WhatsApp: cada botão usa a mensagem da sua origem */
  if (num) $$('a[data-origem]').forEach(function (a) {
    // sem mensagem configurada para esta origem: mantém a que já está no link da página
    var own = '';
    try { own = new URL(a.href).searchParams.get('text') || ''; } catch (e) {}
    var msg = M[a.dataset.origem] || (a.dataset.perfil ? '' : own) || M['default'] || 'Olá! Vim pelo site da Syntra.';
    var p = a.dataset.perfil;
    if (p && perfis[p]) msg += ' Perfil: ' + perfis[p] + '.';
    a.href = wa(msg);
  });

  /* E-mail */
  if (C.email) $$('[data-cfg-email]').forEach(function (a) { a.href = 'mailto:' + C.email; a.textContent = C.email; });

  /* Textos do topo */
  var H = C.hero || {};
  $$('[data-cfg]').forEach(function (el) {
    var path = el.getAttribute('data-cfg').split('.'), v = C;
    path.forEach(function (k) { v = v && v[k]; });
    if (typeof v === 'string' && v.trim()) el.textContent = v;
  });
  var h1 = document.querySelector('[data-cfg-title]');
  if (h1 && (H.title || H.highlight)) {
    h1.textContent = (H.title || '') + ' ';
    if (H.highlight) { var em = document.createElement('em'); em.textContent = H.highlight; h1.appendChild(em); }
  }
  var chips = document.querySelector('[data-cfg-chips]');
  if (chips && Array.isArray(H.chips)) {
    var icon = chips.querySelector('svg');
    var iconHtml = icon ? icon.outerHTML : '';
    chips.innerHTML = '';
    H.chips.filter(Boolean).forEach(function (t) {
      var s = document.createElement('span'); s.className = 'chip'; s.innerHTML = iconHtml + ' ';
      s.appendChild(document.createTextNode(t)); chips.appendChild(s);
    });
  }

  /* Horário de atendimento (fuso de Brasília) */
  var hrs = C.hours || {}, days = (hrs.days || []).map(Number).sort(), st = hrs.start || '08:00', en = hrs.end || '18:00';
  var names = ['', 'seg.', 'ter.', 'qua.', 'qui.', 'sex.', 'sáb.', 'dom.'];
  var consecutive = days.length > 2 && days.every(function (d, i) { return i === 0 || d === days[i - 1] + 1; });
  var dayLabel = days.length === 7 ? 'todos os dias' : consecutive ? names[days[0]] + ' a ' + names[days[days.length - 1]] : days.map(function (d) { return names[d]; }).join(', ');
  var label = dayLabel + ', ' + st + '–' + en;
  var on = false;
  try {
    var parts = new Intl.DateTimeFormat('en-GB', { timeZone: 'America/Sao_Paulo', weekday: 'short', hour: '2-digit', minute: '2-digit', hour12: false }).formatToParts(new Date());
    var g = function (t) { return (parts.filter(function (x) { return x.type === t; })[0] || {}).value; };
    var wd = { Mon: 1, Tue: 2, Wed: 3, Thu: 4, Fri: 5, Sat: 6, Sun: 7 }[g('weekday')];
    var hm = g('hour') + ':' + g('minute');
    on = days.indexOf(wd) > -1 && hm >= st && hm < en;
  } catch (e) {}
  $$('[data-hours-note]').forEach(function (el) { el.textContent = on ? 'Atendente online agora — resposta em minutos, sem compromisso.' : 'Fora do horário? Deixe sua mensagem — respondemos no próximo horário comercial.'; });
  $$('[data-hours-short]').forEach(function (el) { el.textContent = on ? 'Atendentes online agora' : 'Atendimento humano ' + label; });
  $$('[data-hours-label]').forEach(function (el) { el.textContent = 'Atendimento humano: ' + label; });
  $$('.dot').forEach(function (d) { d.classList.toggle('dot--off', !on); });
  var bubble = document.querySelector('.fab__bubble');
  if (bubble) bubble.innerHTML = '<b><span class="dot' + (on ? '' : ' dot--off') + '"></span>' + (on ? 'Atendente online' : 'Deixe sua mensagem') + '</b>' + (on ? 'Tire sua dúvida agora pelo WhatsApp.' : 'Respondemos no próximo horário comercial.');

  /* Liga/desliga recursos */
  var F = C.features || {};
  if (F.balloons === false) $$('.float-note').forEach(function (n) { n.remove(); });
  if (F.background === false) $$('canvas[data-synapse]').forEach(function (n) { n.remove(); });
  if (F.presence === false) {
    $$('[data-hours-note]').forEach(function (el) { var p = el.closest('.cta-note'); if (p) p.remove(); });
    $$('.hero .presence, .fab__bubble').forEach(function (n) { n.remove(); });
  }
  if (F.fab === false) { var fab = document.getElementById('fab'); if (fab) fab.remove(); }

  /* Formulário -> WhatsApp com a mensagem montada */
  var f = document.querySelector('[data-form]');
  if (f && num) f.addEventListener('submit', function (e) {
    e.preventDefault();
    var v = function (n) { var el = f.elements[n]; if (!el) return ''; if (el.length && !el.tagName) { for (var i = 0; i < el.length; i++) if (el[i].checked) return el[i].value; return ''; } return (el.value || '').trim(); };
    if (!v('nome') || v('website')) return;
    var L = ['Olá! Vim pelo site da Syntra.'];
    L.push('Sou ' + v('nome') + (v('clinica') ? ', da ' + v('clinica') + '.' : '.'));
    if (v('perfil')) L.push('Tipo de operação: ' + v('perfil') + '.');
    if (v('equipe')) L.push('Profissionais de saúde: ' + v('equipe') + '.');
    if (v('assunto')) L.push('Quero conversar sobre: ' + v('assunto') + '.');
    var url = wa(L.join('\n'));
    window.dataLayer && window.dataLayer.push({ event: 'cta_whatsapp', origem: 'form' });
    // link de reserva caso o navegador bloqueie a nova aba
    var link = f.querySelector('[data-form-link]');
    if (link) { link.href = url; link.hidden = false; }
    window.open(url, '_blank', 'noopener');
  });
})();
