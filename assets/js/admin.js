/* Admin de configurações rápidas do site Syntra.
   Lê e grava o config.js do repositório pela API do GitHub, com a chave que a pessoa informa. */
(function () {
  'use strict';
  // não funciona dentro de outra página (evita cliques induzidos / clickjacking)
  if (window.top !== window.self) { document.documentElement.hidden = true; return; }

  var $ = function (id) { return document.getElementById(id); };
  var PATH = 'config.js', BRANCH = 'main';
  var MSG_LABELS = {
    hero: 'Botão principal do topo', nav: 'Botão do menu (topo da página)', modulos: 'Seção Módulos',
    implantacao: 'Seção Implantação', demo: 'Demonstração, Laudos e "Para quem"', faq: 'Dúvidas (FAQ)',
    flutuante: 'Botão flutuante do WhatsApp', 'default': 'Rodapé e demais botões'
  };
  var FEATURES = {
    balloons: ['Balões na prévia do sistema', '"Laudo liberado" e "Alergia não perguntada" sobre a tela do topo.'],
    background: ['Fundo animado (sinapses)', 'Os pontos e linhas que se movem no azul do topo e do contato.'],
    presence: ['Aviso de horário', '"Atendente online" ou "Deixe sua mensagem" conforme o horário.'],
    fab: ['Botão flutuante do WhatsApp', 'O botão verde no canto da tela.']
  };
  var DAYS = ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'];
  var token = '', repo = '', sha = '', original = null, dirty = false;

  /* ---------- armazenamento (falha sem quebrar) ---------- */
  var store = {
    get: function (k) { try { return sessionStorage.getItem(k) || localStorage.getItem(k); } catch (e) { return null; } },
    set: function (k, v, keep) { try { (keep ? localStorage : sessionStorage).setItem(k, v); } catch (e) {} },
    clear: function (k) { try { sessionStorage.removeItem(k); localStorage.removeItem(k); } catch (e) {} }
  };

  /* ---------- GitHub API ---------- */
  function gh(path, opts) {
    opts = opts || {};
    return fetch('https://api.github.com/repos/' + repo + path, {
      method: opts.method || 'GET',
      headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/vnd.github+json', 'X-GitHub-Api-Version': '2022-11-28' },
      body: opts.body ? JSON.stringify(opts.body) : undefined,
      cache: 'no-store'
    }).then(function (r) {
      return r.json().catch(function () { return {}; }).then(function (j) { if (!r.ok) { var e = new Error(j.message || ('HTTP ' + r.status)); e.status = r.status; throw e; } return j; });
    });
  }
  function b64decode(s) { var bin = atob(s.replace(/\s/g, '')); var bytes = new Uint8Array(bin.length); for (var i = 0; i < bin.length; i++) bytes[i] = bin.charCodeAt(i); return new TextDecoder().decode(bytes); }
  function b64encode(str) { var bytes = new TextEncoder().encode(str), bin = ''; for (var i = 0; i < bytes.length; i += 0x8000) bin += String.fromCharCode.apply(null, bytes.subarray(i, i + 0x8000)); return btoa(bin); }
  function parseConfig(src) { var json = src.replace(/^[\s\S]*?=\s*/, '').replace(/;\s*$/, ''); return JSON.parse(json); }
  function serialize(c) { return 'window.SYNTRA_CONFIG = ' + JSON.stringify(c, null, 2) + ';\n'; }

  function setStatus(el, text, s) { el.textContent = text; if (s) el.dataset.s = s; else delete el.dataset.s; }
  function errText(e) {
    if (e.status === 401) return 'Chave inválida ou expirada.';
    if (e.status === 403) return 'A chave não tem permissão de escrita neste repositório.';
    if (e.status === 404) return 'Repositório ou arquivo não encontrado (confira o nome e a permissão da chave).';
    if (e.status === 409) return 'O arquivo mudou enquanto você editava. Recarregue e tente de novo.';
    return 'Erro: ' + e.message;
  }

  /* ---------- montar campos ---------- */
  DAYS.forEach(function (d, i) {
    var l = document.createElement('label');
    l.innerHTML = '<input type="checkbox" value="' + (i + 1) + '"><span>' + d + '</span>';
    $('days').appendChild(l);
  });
  Object.keys(MSG_LABELS).forEach(function (k) {
    var f = document.createElement('div'); f.className = 'field';
    f.innerHTML = '<label for="m-' + k + '"></label><textarea id="m-' + k + '" data-msg="' + k + '" maxlength="300"></textarea>';
    f.querySelector('label').textContent = MSG_LABELS[k];
    $('messages').appendChild(f);
  });
  Object.keys(FEATURES).forEach(function (k) {
    var t = document.createElement('div'); t.className = 'tg';
    t.innerHTML = '<div><b></b><small></small></div><span class="switch"><input type="checkbox" id="f-' + k + '" data-feat="' + k + '"><i></i></span>';
    t.querySelector('b').textContent = FEATURES[k][0]; t.querySelector('small').textContent = FEATURES[k][1];
    t.querySelector('input').setAttribute('aria-label', FEATURES[k][0]);
    $('features').appendChild(t);
  });

  function fill(c) {
    $('whatsapp').value = c.whatsapp || '';
    $('email').value = c.email || '';
    var days = (c.hours && c.hours.days) || [];
    document.querySelectorAll('#days input').forEach(function (i) { i.checked = days.indexOf(+i.value) > -1; });
    $('start').value = (c.hours && c.hours.start) || '08:00';
    $('end').value = (c.hours && c.hours.end) || '18:00';
    document.querySelectorAll('[data-msg]').forEach(function (t) { t.value = (c.messages || {})[t.dataset.msg] || ''; });
    var h = c.hero || {};
    $('eyebrow').value = h.eyebrow || ''; $('title').value = h.title || ''; $('highlight').value = h.highlight || '';
    $('lead').value = h.lead || ''; $('chips').value = (h.chips || []).join(', ');
    document.querySelectorAll('[data-feat]').forEach(function (i) { i.checked = (c.features || {})[i.dataset.feat] !== false; });
    document.querySelectorAll('input[name=theme]').forEach(function (r) { r.checked = r.value === (c.theme || 'light'); });
    updateWa(); setDirty(false);
  }
  function collect() {
    var c = JSON.parse(JSON.stringify(original || {}));
    c.whatsapp = $('whatsapp').value.replace(/\D/g, '');
    c.email = $('email').value.trim();
    c.hours = {
      days: Array.prototype.filter.call(document.querySelectorAll('#days input'), function (i) { return i.checked; }).map(function (i) { return +i.value; }),
      start: $('start').value || '08:00', end: $('end').value || '18:00'
    };
    c.messages = c.messages || {};
    document.querySelectorAll('[data-msg]').forEach(function (t) { c.messages[t.dataset.msg] = t.value.trim(); });
    c.hero = { eyebrow: $('eyebrow').value.trim(), title: $('title').value.trim(), highlight: $('highlight').value.trim(), lead: $('lead').value.trim(),
      chips: $('chips').value.split(',').map(function (s) { return s.trim(); }).filter(Boolean) };
    c.features = {};
    document.querySelectorAll('[data-feat]').forEach(function (i) { c.features[i.dataset.feat] = i.checked; });
    var th = document.querySelector('input[name=theme]:checked'); c.theme = th && /^(light|dark|auto)$/.test(th.value) ? th.value : 'light';
    return c;
  }
  function validate(c) {
    var ok = true;
    var waOk = /^55\d{10,11}$/.test(c.whatsapp);
    $('whatsapp').setAttribute('aria-invalid', String(!waOk));
    if (!waOk) { setStatus($('saveStatus'), 'Confira o WhatsApp: 55 + DDD + número (12 ou 13 dígitos).', 'bad'); $('whatsapp').focus(); ok = false; }
    else if (!c.hours.days.length) { setStatus($('saveStatus'), 'Escolha pelo menos um dia de atendimento.', 'bad'); ok = false; }
    else if (c.hours.start >= c.hours.end) { setStatus($('saveStatus'), 'O horário de fechar precisa ser depois do de abrir.', 'bad'); $('end').focus(); ok = false; }
    return ok;
  }
  function fmtPhone(d) {
    var m = d.match(/^55(\d{2})(\d{4,5})(\d{4})$/);
    return m ? '(' + m[1] + ') ' + m[2] + '-' + m[3] : '';
  }
  function updateWa() {
    var d = $('whatsapp').value.replace(/\D/g, '');
    var f = fmtPhone(d);
    $('waHelp').textContent = f ? 'Número: ' + f : '55 + DDD + número, só dígitos.';
    var msg = (document.querySelector('[data-msg=hero]') || {}).value || '';
    $('testWa').href = 'https://wa.me/' + d + '?text=' + encodeURIComponent(msg);
  }
  function setDirty(v) {
    dirty = v; $('bar').classList.toggle('show', v);
    if (v) setStatus($('saveStatus'), 'Alterações não publicadas', 'warn');
  }

  /* ---------- fluxo ---------- */
  function load() {
    setStatus($('loadStatus'), 'Carregando…', 'busy');
    return gh('/contents/' + PATH + '?ref=' + BRANCH).then(function (j) {
      sha = j.sha; original = parseConfig(b64decode(j.content));
      fill(original);
      setStatus($('loadStatus'), 'Configuração atual carregada.', 'ok');
    });
  }
  function enter() {
    setStatus($('loginStatus'), 'Verificando…', 'busy');
    return gh('').then(function (r) {
      if (!r.permissions || !r.permissions.push) { var e = new Error('sem escrita'); e.status = 403; throw e; }
      $('login').hidden = true; $('panel').hidden = false; $('logout').hidden = false;
      return load();
    }).catch(function (e) {
      $('login').hidden = false; $('panel').hidden = true;
      setStatus($('loginStatus'), errText(e), 'bad');
      store.clear('syntra-admin-token');
    });
  }
  $('loginForm').addEventListener('submit', function (e) {
    e.preventDefault();
    token = $('token').value.trim(); repo = $('repo').value.trim();
    if (!/^[A-Za-z0-9_.-]+\/[A-Za-z0-9_.-]+$/.test(repo)) { setStatus($('loginStatus'), 'Repositório no formato dono/nome.', 'bad'); $('repo').focus(); return; }
    if (token && !/^[A-Za-z0-9_]+$/.test(token)) { setStatus($('loginStatus'), 'A chave tem caracteres inválidos. Copie de novo.', 'bad'); $('token').focus(); return; }
    if (!token) { setStatus($('loginStatus'), 'Cole a chave de acesso.', 'bad'); $('token').focus(); return; }
    store.set('syntra-admin-token', token, $('remember').checked);
    store.set('syntra-admin-repo', repo, true);
    $('token').value = '';
    enter();
  });
  $('logout').addEventListener('click', function () {
    store.clear('syntra-admin-token'); token = ''; location.reload();
  });

  $('cfg').addEventListener('input', function (e) { if (e.target.id === 'whatsapp' || e.target.dataset.msg === 'hero') updateWa(); setDirty(true); });
  $('cfg').addEventListener('change', function () { setDirty(true); });
  $('discard').addEventListener('click', function () { if (original) fill(original); });

  $('publish').addEventListener('click', function () {
    var c = collect();
    if (!validate(c)) return;
    var btn = $('publish'); btn.disabled = true;
    setStatus($('saveStatus'), 'Publicando…', 'busy');
    var text = serialize(c);
    gh('/contents/' + PATH, { method: 'PUT', body: { message: 'Admin: atualiza configurações do site', content: b64encode(text), sha: sha, branch: BRANCH } })
      .then(function (r) {
        sha = r.content.sha; original = c; dirty = false;
        setStatus($('saveStatus'), 'Salvo. Atualizando o site (cerca de 1 minuto)…', 'busy');
        return waitLive(text);
      })
      .then(function (live) {
        setStatus($('saveStatus'), live ? 'No ar! Recarregue o site para ver.' : 'Salvo. O site deve atualizar em instantes.', 'ok');
        setTimeout(function () { if (!dirty) $('bar').classList.remove('show'); }, 6000);
      })
      .catch(function (e) { setStatus($('saveStatus'), errText(e), 'bad'); })
      .then(function () { btn.disabled = false; });
  });
  function waitLive(text, tries) {
    tries = tries || 0;
    // cache: 'reload' também renova a cópia guardada neste navegador, então o site já abre atualizado aqui
    return fetch('config.js', { cache: 'reload' }).then(function (r) { return r.ok ? r.text() : ''; }).catch(function () { return ''; })
      .then(function (t) {
        if (t.trim() === text.trim()) return true;
        if (tries >= 18) return false;
        return new Promise(function (res) { setTimeout(res, 10000); }).then(function () { return waitLive(text, tries + 1); });
      });
  }
  addEventListener('beforeunload', function (e) { if (dirty) { e.preventDefault(); e.returnValue = ''; } });

  /* entra direto se já houver chave salva */
  token = store.get('syntra-admin-token') || '';
  repo = store.get('syntra-admin-repo') || $('repo').value;
  if (!/^[A-Za-z0-9_.-]+\/[A-Za-z0-9_.-]+$/.test(repo)) repo = 'divinadigital/syntra-site';
  $('repo').value = repo;
  if (token) enter();
})();
