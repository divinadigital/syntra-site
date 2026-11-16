/* Syntra — interações e motion (vanilla, sem dependências)
   Inspirado em padrões de reactbits.dev (SplitText, ShinyText, SpotlightCard, Magnet, CountUp)
   e transitions.dev (tabs sliding, accordion grid-rows, 3D tilt + glare, check draw, number pop-in). */
(() => {
  'use strict';
  const $ = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => [...r.querySelectorAll(s)];
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const fine = matchMedia('(hover: hover) and (pointer: fine)').matches;

  /* ---------- Tema claro / escuro ---------- */
  $$('.theme-toggle').forEach(b => b.addEventListener('click', () => {
    const d = document.documentElement;
    const next = d.dataset.theme === 'dark' ? 'light' : 'dark';
    const apply = () => { d.dataset.theme = next; try { localStorage.setItem('syntra-theme', next); } catch (e) {} };
    if (document.startViewTransition && !reduce) document.startViewTransition(apply); else apply();
  }));

  /* ---------- Split text (palavra a palavra) ---------- */
  $$('[data-split]').forEach(el => {
    const walk = (node, out) => {
      node.childNodes.forEach(n => {
        if (n.nodeType === 3) {
          n.textContent.split(/(\s+)/).forEach(part => {
            if (!part) return;
            if (/^\s+$/.test(part)) { out.appendChild(document.createTextNode(' ')); return; }
            const w = document.createElement('span'); w.className = 'w';
            const s = document.createElement('span'); s.textContent = part; w.appendChild(s); out.appendChild(w);
          });
        } else if (n.nodeType === 1) {
          const clone = n.cloneNode(false); walk(n, clone); out.appendChild(clone);
        }
      });
    };
    el.setAttribute('aria-label', el.textContent.trim());
    const frag = document.createElement('span'); frag.setAttribute('aria-hidden', 'true');
    walk(el, frag); el.textContent = ''; el.appendChild(frag);
    $$('.w > span', el).forEach((s, i) => s.style.setProperty('--i', i));
  });

  /* ---------- Revelação no scroll ---------- */
  const io = new IntersectionObserver(entries => {
    entries.forEach(en => {
      if (!en.isIntersecting) return;
      en.target.classList.add('in');
      en.target.dispatchEvent(new CustomEvent('reveal'));
      io.unobserve(en.target);
    });
  }, { rootMargin: '0px 0px -8% 0px', threshold: 0.12 });
  $$('[data-reveal], [data-split], [data-onboarding], .kpis').forEach(el => io.observe(el));
  // conteúdo acima da dobra entra imediatamente, sem esperar o observer
  requestAnimationFrame(() => $$('.hero [data-reveal], .hero [data-split]').forEach(el => el.classList.add('in')));

  /* ---------- Contadores (count up) ---------- */
  const countUp = el => {
    const end = +el.dataset.count; if (isNaN(end)) return;
    if (reduce) { el.textContent = end; return; }
    const dur = 1200, t0 = performance.now();
    const tick = t => {
      const p = Math.min(1, (t - t0) / dur), e = 1 - Math.pow(1 - p, 4);
      el.textContent = Math.round(end * e);
      if (p < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
  };
  const cio = new IntersectionObserver(es => es.forEach(en => { if (en.isIntersecting) { countUp(en.target); cio.unobserve(en.target); } }), { threshold: .6 });
  $$('[data-count]').forEach(el => cio.observe(el));

  /* ---------- Checklist "Pronto para atender" ---------- */
  const runReady = (root, keepLast) => {
    const items = $$('[data-steps] li', root);
    const pct = $('[data-ready-pct]', root), bar = $('[data-ready-bar]', root), n = $('[data-ready-n]', root);
    const total = items.length, target = keepLast ? total - 1 : total;
    let done = 0;
    const step = () => {
      if (done >= target) return;
      items[done].classList.add('is-done'); done++;
      const p = Math.round(done / total * 100);
      if (pct) pct.textContent = p + '%';
      if (bar) bar.style.width = p + '%';
      if (n) n.textContent = done;
      setTimeout(step, reduce ? 0 : 650);
    };
    setTimeout(step, reduce ? 0 : 500);
  };
  const heroApp = $('.hero .app');
  if (heroApp) {
    // mostra um estado real de implantação: 2 de 3 concluídas (67%)
    setTimeout(() => runReady(heroApp, true), reduce ? 0 : 900);
  }
  $$('[data-onboarding]').forEach(el => el.addEventListener('reveal', () => runReady(el, false), { once: true }));

  /* ---------- Seleção que desliza no menu do mock (mola) ---------- */
  const sel = $('[data-sel]');
  if (sel) {
    const items = $$('[data-sel-item]');
    const place = it => { sel.style.transform = `translateY(${it.offsetTop}px)`; items.forEach(i => i.classList.toggle('is-on', i === it)); };
    requestAnimationFrame(() => place(items[0]));
    if (!reduce) {
      let k = 0;
      setInterval(() => { if (document.hidden) return; k = (k + 1) % items.length; place(items[k]); }, 2600);
    }
  }

  /* ---------- Tilt 3D + reflexo ---------- */
  if (fine && !reduce) {
    $$('[data-tilt]').forEach(card => {
      const host = card.parentElement;
      host.addEventListener('pointermove', e => {
        const r = card.getBoundingClientRect();
        const x = (e.clientX - r.left) / r.width, y = (e.clientY - r.top) / r.height;
        card.style.transform = `rotateY(${(x - .5) * 8}deg) rotateX(${(.5 - y) * 6}deg)`;
        card.style.setProperty('--gx', x * 100 + '%'); card.style.setProperty('--gy', y * 100 + '%');
      });
      host.addEventListener('pointerleave', () => { card.style.transform = ''; });
    });
  }

  /* ---------- Botões magnéticos ---------- */
  if (fine && !reduce) {
    $$('.magnetic').forEach(b => {
      b.addEventListener('pointermove', e => {
        const r = b.getBoundingClientRect();
        b.style.transform = `translate(${(e.clientX - r.left - r.width / 2) * .18}px, ${(e.clientY - r.top - r.height / 2) * .3}px)`;
      });
      b.addEventListener('pointerleave', () => { b.style.transform = ''; });
    });
  }

  /* ---------- Spotlight nos cards de módulo ---------- */
  document.addEventListener('pointermove', e => {
    const m = e.target.closest && e.target.closest('.mod');
    if (!m) return;
    const r = m.getBoundingClientRect();
    m.style.setProperty('--mx', e.clientX - r.left + 'px'); m.style.setProperty('--my', e.clientY - r.top + 'px');
  }, { passive: true });

  /* ---------- Indicador deslizante genérico ---------- */
  const slideTo = (thumb, btn, vertical) => {
    thumb.style.width = btn.offsetWidth + 'px';
    thumb.style.transform = `translate(${btn.offsetLeft}px, ${vertical ? btn.offsetTop : 0}px)`;
    if (vertical) thumb.style.height = btn.offsetHeight + 'px';
  };

  /* Abas com teclado (setas) — padrão WAI-ARIA */
  const tabKeys = (list, onSelect) => list.addEventListener('keydown', e => {
    const tabs = $$('[role="tab"]', list); const i = tabs.indexOf(document.activeElement);
    if (i < 0) return;
    let j = null;
    if (e.key === 'ArrowRight') j = (i + 1) % tabs.length;
    if (e.key === 'ArrowLeft') j = (i - 1 + tabs.length) % tabs.length;
    if (e.key === 'Home') j = 0;
    if (e.key === 'End') j = tabs.length - 1;
    if (j === null) return;
    e.preventDefault(); tabs[j].focus(); onSelect(tabs[j]);
  });

  /* Módulos */
  const tabs = $('[data-tabs]');
  if (tabs) {
    const thumb = $('.tabs__thumb', tabs);
    const select = btn => {
      $$('[role="tab"]', tabs).forEach(t => {
        const on = t === btn; t.setAttribute('aria-selected', on); t.tabIndex = on ? 0 : -1;
        const p = document.getElementById(t.getAttribute('aria-controls'));
        if (on) { p.hidden = false; p.classList.remove('is-entering'); void p.offsetWidth; p.classList.add('is-entering'); $$('[data-reveal]', p).forEach(x => x.classList.add('in')); }
        else p.hidden = true;
      });
      slideTo(thumb, btn, true);
    };
    $$('[role="tab"]', tabs).forEach(t => t.addEventListener('click', () => select(t)));
    tabKeys(tabs, select);
    const init = () => slideTo(thumb, $('[aria-selected="true"]', tabs), true);
    init(); addEventListener('resize', init);
    document.fonts && document.fonts.ready.then(init);
  }

  /* Comparação hoje x Syntra */
  const seg = $('[data-seg]');
  if (seg) {
    const thumb = $('.seg__thumb', seg);
    const select = btn => {
      $$('[role="tab"]', seg).forEach(t => {
        const on = t === btn; t.setAttribute('aria-selected', on); t.tabIndex = on ? 0 : -1;
        document.getElementById(t.getAttribute('aria-controls')).hidden = !on;
      });
      slideTo(thumb, btn);
    };
    $$('[role="tab"]', seg).forEach(t => t.addEventListener('click', () => { select(t); seg.dataset.touched = 1; }));
    tabKeys(seg, select);
    const init = () => slideTo(thumb, $('[aria-selected="true"]', seg));
    init(); addEventListener('resize', init); document.fonts && document.fonts.ready.then(init);
    // troca sozinho uma vez para mostrar a solução, se o usuário não mexer
    const cmp = $('.compare');
    if (cmp && !reduce) cmp.addEventListener('reveal', () => setTimeout(() => { if (!seg.dataset.touched) select($('#tab-syntra')); }, 2600), { once: true });
  }

  /* Pílulas de filtro dos laudos */
  const pills = $('[data-pills]');
  if (pills) {
    const thumb = $('.pills__thumb', pills);
    const rows = $$('.rows .row'); const total = $('[data-total]');
    const apply = btn => {
      $$('button', pills).forEach(b => b.setAttribute('aria-pressed', b === btn));
      slideTo(thumb, btn);
      const f = btn.dataset.f; let n = 0;
      rows.forEach((r, i) => {
        const show = f === 'all' || r.dataset.s === f;
        if (show) {
          n++;
          r.classList.remove('is-out');
          if (!reduce) r.animate([{ opacity: 0, transform: 'translateY(8px)' }, { opacity: 1, transform: 'none' }], { duration: 380, delay: n * 40, easing: 'cubic-bezier(.22,1,.36,1)', fill: 'backwards' });
        } else r.classList.add('is-out');
      });
      if (total) {
        total.textContent = n;
        if (!reduce) total.animate([{ transform: 'scale(1.35)', color: 'var(--accent)' }, { transform: 'scale(1)' }], { duration: 420, easing: 'cubic-bezier(.34,1.56,.64,1)' });
      }
    };
    $$('button', pills).forEach(b => b.addEventListener('click', () => apply(b)));
    const init = () => slideTo(thumb, $('[aria-pressed="true"]', pills));
    init(); addEventListener('resize', init); document.fonts && document.fonts.ready.then(init);
  }

  /* ---------- Jornada: linha que se desenha no scroll ---------- */
  const journey = $('[data-journey]');
  if (journey) {
    const line = $('.journey__line', journey); const steps = $$('.step', journey);
    const onScroll = () => {
      const r = journey.getBoundingClientRect(), vh = innerHeight;
      const p = Math.max(0, Math.min(1, (vh * .8 - r.top) / (r.height + vh * .3)));
      line.style.setProperty('--p', p);
      steps.forEach((s, i) => s.classList.toggle('is-lit', p >= i / (steps.length - 1) - .02));
    };
    addEventListener('scroll', onScroll, { passive: true }); onScroll();
  }

  /* ---------- Acordeão (grid-rows) ---------- */
  $$('[data-acc] .acc__btn').forEach(b => b.addEventListener('click', () => {
    const open = b.getAttribute('aria-expanded') === 'true';
    b.setAttribute('aria-expanded', String(!open));
  }));

  /* ---------- Navegação: fundo, esconder ao descer, pílula ativa ---------- */
  const nav = $('#nav'); let lastY = scrollY;
  const navLinks = $('#navLinks'); const pill = $('.nav__pill');
  const onNav = () => {
    const y = scrollY;
    nav.classList.toggle('is-scrolled', y > 40);
    nav.classList.toggle('is-hidden', y > 500 && y > lastY + 4 && !document.body.classList.contains('menu-open'));
    if (y < lastY - 4) nav.classList.remove('is-hidden');
    lastY = y;
    fab && fab.classList.toggle('is-in', y > innerHeight * .6);
  };
  const fab = $('#fab');
  addEventListener('scroll', onNav, { passive: true }); onNav();
  if (fab) setTimeout(() => fab.classList.add('show-bubble'), 9000);

  if (navLinks && pill) {
    const links = $$('a', navLinks);
    const movePill = a => {
      if (!a) { pill.style.opacity = 0; return; }
      pill.style.opacity = 1; pill.style.width = a.offsetWidth + 'px'; pill.style.transform = `translateX(${a.offsetLeft}px)`;
    };
    const sections = links.map(a => document.querySelector(a.getAttribute('href'))).filter(Boolean);
    const sio = new IntersectionObserver(es => es.forEach(en => {
      if (!en.isIntersecting) return;
      links.forEach(a => a.classList.toggle('is-active', a.getAttribute('href') === '#' + en.target.id));
      movePill(links.find(a => a.classList.contains('is-active')));
    }), { rootMargin: '-45% 0px -50% 0px' });
    sections.forEach(s => sio.observe(s));
    links.forEach(a => a.addEventListener('mouseenter', () => movePill(a)));
    navLinks.addEventListener('mouseleave', () => movePill(links.find(a => a.classList.contains('is-active'))));
  }

  /* ---------- Menu mobile ---------- */
  const drawer = $('#drawer'); const burger = $('.nav__burger');
  const setDrawer = open => {
    drawer.classList.toggle('is-open', open); drawer.setAttribute('aria-hidden', String(!open));
    burger.setAttribute('aria-expanded', String(open)); document.body.classList.toggle('menu-open', open);
    document.body.style.overflow = open ? 'hidden' : '';
    if (open) $('a, button', $('.drawer__panel')).focus(); else burger.focus({ preventScroll: true });
  };
  if (drawer && burger) {
    burger.addEventListener('click', () => setDrawer(true));
    $$('[data-close]', drawer).forEach(x => x.addEventListener('click', () => setDrawer(false)));
    addEventListener('keydown', e => { if (e.key === 'Escape' && drawer.classList.contains('is-open')) setDrawer(false); });
  }

  /* ---------- Formulário: validação amigável ---------- */
  const form = $('[data-form]');
  if (form) form.addEventListener('submit', e => {
    const nome = form.elements.nome;
    if (!nome.value.trim()) {
      e.preventDefault();
      nome.focus();
      nome.setAttribute('aria-invalid', 'true');
      nome.placeholder = 'Como podemos te chamar?';
      if (!reduce) nome.animate([{ transform: 'translateX(0)' }, { transform: 'translateX(-6px)' }, { transform: 'translateX(5px)' }, { transform: 'translateX(-3px)' }, { transform: 'translateX(0)' }], { duration: 380, easing: 'cubic-bezier(.36,.07,.19,.97)' });
      return;
    }
    // abre o WhatsApp em nova aba no desktop; no celular segue no mesmo fluxo
    if (fine) form.target = '_blank';
  });

  /* CTAs para o WhatsApp: no desktop abrem em nova aba (o visitante não perde o site) */
  $$('a[data-cta]').forEach(a => {
    if (fine) { a.target = '_blank'; a.rel = 'noopener'; }
    a.addEventListener('click', () => {
      // gancho para analytics (GA4 / Meta Pixel), se existir
      window.dataLayer && window.dataLayer.push({ event: 'cta_whatsapp', origem: a.dataset.cta });
    });
  });

  /* ---------- Canvas "sinapse": neurônios que se conectam ---------- */
  $$('canvas[data-synapse]').forEach(cv => {
    const ctx = cv.getContext('2d'); if (!ctx) return;
    const density = +(cv.dataset.density || 1);
    let W, H, nodes = [], pulses = [], raf, visible = true;
    const mouse = { x: -999, y: -999 };
    const DPR = Math.min(devicePixelRatio || 1, 2);
    const resize = () => {
      const r = cv.getBoundingClientRect(); W = r.width; H = r.height;
      cv.width = W * DPR; cv.height = H * DPR; ctx.setTransform(DPR, 0, 0, DPR, 0, 0);
      const n = Math.round(Math.min(90, (W * H) / 16000) * density);
      nodes = Array.from({ length: n }, () => ({ x: Math.random() * W, y: Math.random() * H, vx: (Math.random() - .5) * .25, vy: (Math.random() - .5) * .25, r: Math.random() * 1.6 + .8 }));
    };
    const LINK = 140;
    const draw = () => {
      ctx.clearRect(0, 0, W, H);
      for (const a of nodes) {
        a.x += a.vx; a.y += a.vy;
        if (a.x < 0 || a.x > W) a.vx *= -1;
        if (a.y < 0 || a.y > H) a.vy *= -1;
        const dx = a.x - mouse.x, dy = a.y - mouse.y, d = Math.hypot(dx, dy);
        if (d < 120) { a.x += dx / d * .6; a.y += dy / d * .6; }
      }
      for (let i = 0; i < nodes.length; i++) for (let j = i + 1; j < nodes.length; j++) {
        const a = nodes[i], b = nodes[j], d = Math.hypot(a.x - b.x, a.y - b.y);
        if (d < LINK) {
          ctx.strokeStyle = `rgba(159,208,255,${(1 - d / LINK) * .28})`; ctx.lineWidth = 1;
          ctx.beginPath(); ctx.moveTo(a.x, a.y); ctx.lineTo(b.x, b.y); ctx.stroke();
          if (Math.random() < .0009 && pulses.length < 14) pulses.push({ a, b, t: 0 });
        }
      }
      for (const n of nodes) { ctx.fillStyle = 'rgba(200,228,255,.75)'; ctx.beginPath(); ctx.arc(n.x, n.y, n.r, 0, 6.283); ctx.fill(); }
      pulses = pulses.filter(p => p.t <= 1);
      for (const p of pulses) {
        p.t += .018; const x = p.a.x + (p.b.x - p.a.x) * p.t, y = p.a.y + (p.b.y - p.a.y) * p.t;
        const g = ctx.createRadialGradient(x, y, 0, x, y, 10); g.addColorStop(0, 'rgba(255,255,255,.95)'); g.addColorStop(1, 'rgba(120,190,255,0)');
        ctx.fillStyle = g; ctx.beginPath(); ctx.arc(x, y, 10, 0, 6.283); ctx.fill();
      }
      if (visible) raf = requestAnimationFrame(draw);
    };
    resize(); addEventListener('resize', () => { resize(); if (reduce) draw(); });
    cv.parentElement.addEventListener('pointermove', e => { const r = cv.getBoundingClientRect(); mouse.x = e.clientX - r.left; mouse.y = e.clientY - r.top; });
    cv.parentElement.addEventListener('pointerleave', () => { mouse.x = mouse.y = -999; });
    if (reduce) { visible = false; draw(); return; }
    new IntersectionObserver(([en]) => { visible = en.isIntersecting; cancelAnimationFrame(raf); if (visible) raf = requestAnimationFrame(draw); }).observe(cv);
  });
})();
