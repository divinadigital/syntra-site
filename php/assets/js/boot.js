/* Roda antes da primeira pintura: marca que há JS e escolhe o tema (sem "piscar").
   Fica em arquivo próprio para o site não precisar de nenhum script inline (CSP). */
(function () {
  var d = document.documentElement;
  d.classList.add('js');

  var C = window.SYNTRA_CONFIG || {};
  var t = null;
  try { t = localStorage.getItem('syntra-theme'); } catch (e) {}
  if (t !== 'dark' && t !== 'light') {
    // sem escolha do visitante: usa o padrão do admin (claro, escuro ou o do aparelho)
    t = null;
    if (C.theme === 'dark') t = 'dark';
    else if (C.theme === 'auto' && window.matchMedia && matchMedia('(prefers-color-scheme: dark)').matches) t = 'dark';
  }
  if (t) d.setAttribute('data-theme', t);

  // rede de segurança: se o main.js não carregar, o conteúdo aparece mesmo assim
  setTimeout(function () { if (!window.__syntraReady) d.classList.remove('js'); }, 3500);
})();
