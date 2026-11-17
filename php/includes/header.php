<?php
/** @var string $pageTitle */
/** @var string $pageDesc */
$online = is_business_hours();
/** Ícone oficial da Syntra (sinapse: nó central + 4 nós assimétricos). */
function logo_mark(string $class = 'logo-mark'): string
{
    static $n = 0;
    $id = 'syg' . (++$n);
    return '<svg class="' . $class . '" viewBox="0 0 1024 1024" aria-hidden="true"><defs><linearGradient id="' . $id . '" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#0f568e"/><stop offset="1" stop-color="#0e9de2"/></linearGradient></defs>'
        . '<rect width="1024" height="1024" rx="232" fill="url(#' . $id . ')"/>'
        . '<g stroke="#eaf3fa" stroke-width="54" stroke-linecap="round"><path d="M288 318 512 512M736 288 512 512M384 768 512 512M768 704 512 512"/></g>'
        . '<g fill="#fff"><circle cx="512" cy="512" r="98"/><circle cx="288" cy="318" r="67"/><circle cx="736" cy="288" r="67"/><circle cx="384" cy="768" r="67"/><circle cx="768" cy="704" r="67"/></g></svg>';
}

/** Logo completo: ícone + palavra "Syntra" em Lato com o degradê da marca. */
function logo_full(): string
{
    return logo_mark() . '<span class="logo__word">Syntra</span>';
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <!-- Segurança: só carrega scripts do próprio site (nenhum script inline) e fontes do Google Fonts.
       Ao adicionar Analytics/Tag Manager, inclua os domínios deles em script-src e connect-src. -->
  <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data:; connect-src 'self'; object-src 'none'; base-uri 'self'; form-action 'self' https://wa.me https://api.whatsapp.com">
  <meta name="referrer" content="strict-origin-when-cross-origin">
  <title><?= e($pageTitle) ?></title>
  <meta name="description" content="<?= e($pageDesc) ?>">
  <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1">
  <link rel="canonical" href="<?= SITE_URL ?>/">
  <meta name="theme-color" content="#0b2f57">
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="Syntra">
  <meta property="og:url" content="<?= SITE_URL ?>/">
  <meta property="og:title" content="<?= e($pageTitle) ?>">
  <meta property="og:description" content="<?= e($pageDesc) ?>">
  <meta property="og:locale" content="pt_BR">
  <meta property="og:image" content="<?= SITE_URL ?>/assets/img/og.png">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">
  <meta property="og:image:alt" content="Syntra — A sinapse da sua clínica. Sistema de gestão para clínicas e hospitais.">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= e($pageTitle) ?>">
  <meta name="twitter:description" content="<?= e($pageDesc) ?>">
  <meta name="twitter:image" content="<?= SITE_URL ?>/assets/img/og.png">
  <link rel="icon" href="assets/img/favicon.svg" type="image/svg+xml">
  <link rel="apple-touch-icon" href="assets/img/apple-touch-icon.png">
  <link rel="manifest" href="manifest.webmanifest">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Lato:wght@700;900&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css?v=6">
  <script src="assets/js/boot.js?v=1"></script>
  <script type="application/ld+json"><?= json_encode([
      '@context' => 'https://schema.org',
      '@graph' => [
          [
              '@type' => 'Organization',
              '@id' => SITE_URL . '/#org',
              'name' => 'Syntra',
              'url' => SITE_URL . '/',
              'logo' => SITE_URL . '/assets/img/icon-512.png',
              'slogan' => SITE_TAGLINE,
              'email' => CONTACT_EMAIL,
              'contactPoint' => [
                  '@type' => 'ContactPoint',
                  'contactType' => 'sales',
                  'telephone' => '+' . WHATSAPP_NUMBER,
                  'availableLanguage' => 'pt-BR',
                  'areaServed' => 'BR',
              ],
          ],
          [
              '@type' => 'WebSite',
              '@id' => SITE_URL . '/#site',
              'url' => SITE_URL . '/',
              'name' => 'Syntra',
              'inLanguage' => 'pt-BR',
              'publisher' => ['@id' => SITE_URL . '/#org'],
          ],
          [
              '@type' => 'SoftwareApplication',
              'name' => 'Syntra',
              'applicationCategory' => 'BusinessApplication',
              'applicationSubCategory' => 'Gestão de clínicas e hospitais',
              'operatingSystem' => 'Web',
              'inLanguage' => 'pt-BR',
              'description' => $pageDesc,
              'url' => SITE_URL . '/',
              'featureList' => array_merge(...array_map(fn($g) => array_column($g['items'], 1), array_values($modules ?? []))),
              'publisher' => ['@id' => SITE_URL . '/#org'],
          ],
          [
              '@type' => 'FAQPage',
              'mainEntity' => array_map(fn($f) => [
                  '@type' => 'Question',
                  'name' => $f[0],
                  'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f[1]],
              ], $faqs ?? []),
          ],
      ],
  ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
</head>
<body>
<a class="skip" href="#conteudo">Pular para o conteúdo</a>

<header class="nav" id="nav">
  <div class="wrap nav__in">
    <a href="#topo" class="logo" aria-label="Syntra — início"><?= logo_full() ?></a>

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
      <span class="logo"><?= logo_full() ?></span>
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
