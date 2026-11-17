/* Motion da página de links (bio). Tudo calmo de propósito: o fundo é ambiente, o destaque é o botão.
   Com "reduzir movimento" ligado no aparelho, nada disso roda e a página fica estática. */
(function () {
  'use strict';
  var d = document.documentElement;
  var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
  var fine = window.matchMedia && matchMedia('(hover: hover) and (pointer: fine)').matches;
  var links = Array.prototype.slice.call(document.querySelectorAll('.link'));

  /* ---------- toque: onda que nasce no ponto pressionado ---------- */
  if (!reduce) links.forEach(function (a) {
    a.addEventListener('pointerdown', function (e) {
      var r = a.getBoundingClientRect();
      var s = document.createElement('span');
      s.className = 'ripple';
      var size = Math.max(r.width, r.height) * 2;
      s.style.width = s.style.height = size + 'px';
      s.style.left = (e.clientX - r.left - size / 2) + 'px';
      s.style.top = (e.clientY - r.top - size / 2) + 'px';
      a.appendChild(s);
      s.addEventListener('animationend', function () { s.remove(); });
    });
  });

  /* ---------- mouse (computador): luz que segue o cursor + leve inclinação ---------- */
  if (fine && !reduce) links.forEach(function (a) {
    a.addEventListener('pointermove', function (e) {
      var r = a.getBoundingClientRect();
      var x = (e.clientX - r.left) / r.width, y = (e.clientY - r.top) / r.height;
      a.style.setProperty('--mx', (x * 100) + '%');
      a.style.setProperty('--my', (y * 100) + '%');
      a.style.transform = 'perspective(700px) rotateX(' + ((0.5 - y) * 3).toFixed(2) + 'deg) rotateY(' + ((x - 0.5) * 4).toFixed(2) + 'deg) translateY(-2px)';
    });
    a.addEventListener('pointerleave', function () { a.style.transform = ''; });
  });

  if (reduce) return;
  d.classList.add('motion');

  /* ---------- fundo: sinapses em deriva lenta ---------- */
  var cv = document.querySelector('canvas.synapse');
  var ctx = cv && cv.getContext && cv.getContext('2d');
  if (!ctx) return;
  var W = 0, H = 0, nodes = [], pulses = [], raf = 0;
  var DPR = Math.min(window.devicePixelRatio || 1, 2);
  var LINK = 130;

  function resize() {
    W = window.innerWidth; H = window.innerHeight;
    if (!W || !H) return;
    cv.width = W * DPR; cv.height = H * DPR;
    ctx.setTransform(DPR, 0, 0, DPR, 0, 0);
    var n = Math.round(Math.min(46, (W * H) / 20000));
    nodes = [];
    for (var i = 0; i < n; i++) nodes.push({
      x: Math.random() * W, y: Math.random() * H,
      vx: (Math.random() - 0.5) * 0.07, vy: (Math.random() - 0.5) * 0.07,
      r: Math.random() * 1.4 + 0.7
    });
  }

  function draw() {
    ctx.clearRect(0, 0, W, H);
    var i, j, a, b, dist;
    for (i = 0; i < nodes.length; i++) {
      a = nodes[i]; a.x += a.vx; a.y += a.vy;
      if (a.x < 0 || a.x > W) a.vx *= -1;
      if (a.y < 0 || a.y > H) a.vy *= -1;
    }
    ctx.lineWidth = 1;
    for (i = 0; i < nodes.length; i++) for (j = i + 1; j < nodes.length; j++) {
      a = nodes[i]; b = nodes[j]; dist = Math.hypot(a.x - b.x, a.y - b.y);
      if (dist < LINK) {
        ctx.strokeStyle = 'rgba(159,208,255,' + ((1 - dist / LINK) * 0.2).toFixed(3) + ')';
        ctx.beginPath(); ctx.moveTo(a.x, a.y); ctx.lineTo(b.x, b.y); ctx.stroke();
        if (Math.random() < 0.00015 && pulses.length < 2) pulses.push({ a: a, b: b, t: 0 });
      }
    }
    ctx.fillStyle = 'rgba(200,228,255,.5)';
    for (i = 0; i < nodes.length; i++) { ctx.beginPath(); ctx.arc(nodes[i].x, nodes[i].y, nodes[i].r, 0, 6.283); ctx.fill(); }
    // impulso que corre de um neurônio a outro, raro e devagar
    pulses = pulses.filter(function (p) { return p.t <= 1; });
    pulses.forEach(function (p) {
      p.t += 0.006;
      var x = p.a.x + (p.b.x - p.a.x) * p.t, y = p.a.y + (p.b.y - p.a.y) * p.t;
      var g = ctx.createRadialGradient(x, y, 0, x, y, 9);
      g.addColorStop(0, 'rgba(255,255,255,.6)'); g.addColorStop(1, 'rgba(120,190,255,0)');
      ctx.fillStyle = g; ctx.beginPath(); ctx.arc(x, y, 9, 0, 6.283); ctx.fill();
    });
    raf = requestAnimationFrame(draw);
  }

  resize();
  var rt;
  window.addEventListener('resize', function () { clearTimeout(rt); rt = setTimeout(resize, 150); });
  // não gasta bateria com a aba em segundo plano
  document.addEventListener('visibilitychange', function () {
    cancelAnimationFrame(raf);
    if (!document.hidden) raf = requestAnimationFrame(draw);
  });
  raf = requestAnimationFrame(draw);
})();
