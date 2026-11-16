<?php
/** @var string $pageTitle */
/** @var string $pageDesc */
$online = is_business_hours();
function logo_mark(string $class = 'logo-mark'): string
{
    return '<svg class="' . $class . '" viewBox="0 0 32 32" aria-hidden="true"><defs><linearGradient id="lg" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#1a7ad6"/><stop offset="1" stop-color="#0e4c8a"/></linearGradient></defs><rect width="32" height="32" rx="8" fill="url(#lg)"/><g stroke="#fff" stroke-width="2.2" stroke-linecap="round"><path d="M9 9l14 14M23 9 9 23"/></g><g fill="#fff"><circle cx="9" cy="9" r="2.6"/><circle cx="23" cy="9" r="2.6"/><circle cx="9" cy="23" r="2.6"/><circle cx="23" cy="23" r="2.6"/><circle cx="16" cy="16" r="3.2"/></g></svg>';
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title><?= e($pageTitle) ?></title>
  <meta name="description" content="<?= e($pageDesc) ?>">
  <meta name="theme-color" content="#0b2f57">
  <meta property="og:type" content="website">
  <meta property="og:title" content="<?= e($pageTitle) ?>">
  <meta property="og:description" content="<?= e($pageDesc) ?>">
  <meta property="og:locale" content="pt_BR">
  <link rel="icon" href="assets/img/favicon.svg" type="image/svg+xml">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css?v=1">
  <script>
    // tema antes da pintura (sem "piscar")
    (function () {
      var d = document.documentElement; d.classList.add('js');
      try { var t = localStorage.getItem('syntra-theme'); if (t) d.dataset.theme = t; } catch (e) {}
    })();
  </script>
  <script type="application/ld+json">
  {"@context":"https://schema.org","@type":"SoftwareApplication","name":"Syntra","applicationCategory":"BusinessApplication","operatingSystem":"Web","description":"<?= e($pageDesc) ?>","url":"<?= SITE_URL ?>"}
  </script>
</head>
<body>
<a class="skip" href="#conteudo">Pular para o conteúdo</a>

<header class="nav" id="nav">
  <div class="wrap nav__in">
    <a href="#topo" class="logo" aria-label="Syntra — início"><?= logo_mark() ?><span>Syntra</span></a>

    <nav aria-label="Principal">
      <ul class="nav__links" id="navLinks">
        <li><a href="#modulos">Módulos</a></li>
        <li><a href="#laudos">Laudos</a></li>
        <li><a href="#implantacao">Implantação</a></li>
        <li><a href="#seguranca">Segurança</a></li>
        <li><a href="#faq">Dúvidas</a></li>
        <li class="nav__pill" aria-hidden="true"></li>
      </ul>
    </nav>

    <div class="nav__actions">
      <a class="btn btn--on-dark btn--sm" href="<?= APP_URL ?>/login" rel="noopener">Entrar</a>
      <button class="icon-btn theme-toggle" type="button" aria-label="Alternar tema claro/escuro">
        <svg class="i-sun" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
        <svg class="i-moon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
      </button>
      <a class="btn btn--primary btn--sm magnetic" href="<?= cta('nav') ?>" data-cta="nav">Falar com especialista</a>
      <button class="icon-btn nav__burger" type="button" aria-label="Abrir menu" aria-expanded="false" aria-controls="drawer"><?= icon('menu') ?></button>
    </div>
  </div>
</header>

<div class="drawer" id="drawer" aria-hidden="true">
  <div class="drawer__bg" data-close></div>
  <div class="drawer__panel" role="dialog" aria-modal="true" aria-label="Menu">
    <div class="drawer__top">
      <span class="logo"><?= logo_mark() ?>Syntra</span>
      <button class="icon-btn" type="button" data-close aria-label="Fechar menu"><?= icon('x') ?></button>
    </div>
    <ul>
      <li><a href="#modulos" data-close>Módulos <?= icon('arrow', 18) ?></a></li>
      <li><a href="#laudos" data-close>Laudos <?= icon('arrow', 18) ?></a></li>
      <li><a href="#implantacao" data-close>Implantação <?= icon('arrow', 18) ?></a></li>
      <li><a href="#seguranca" data-close>Segurança <?= icon('arrow', 18) ?></a></li>
      <li><a href="#faq" data-close>Dúvidas <?= icon('arrow', 18) ?></a></li>
      <li><a href="<?= APP_URL ?>/login" rel="noopener">Entrar no sistema <?= icon('arrow', 18) ?></a></li>
    </ul>
    <a class="btn btn--wa" href="<?= cta('nav') ?>" data-cta="menu"><?= icon('whatsapp') ?> Falar com um atendente</a>
  </div>
</div>
