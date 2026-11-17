# Syntra — site institucional

Site da Syntra, sistema de gestão para clínicas e hospitais. Todos os botões levam o visitante ao atendimento no WhatsApp.

- **Raiz** (`index.html`, `assets/`): versão estática publicada no GitHub Pages. Os CTAs apontam direto para o `wa.me`, e o status "online / fora do horário" é calculado no navegador, no fuso de Brasília.
- **`php/`**: versão PHP original, para hospedagens com PHP 8+. Os CTAs passam pelo `atendimento.php`, que monta a mensagem conforme a origem do clique. O número e o horário ficam em `php/config.php`.

Para mudar o número do WhatsApp, o horário ou os textos do topo na versão estática, use o admin (abaixo) ou edite `config.js`.

## Configurações rápidas (admin)
Abra `admin.html` no site publicado (`/syntra-site/admin.html`) e entre com uma chave do GitHub (fine-grained token só deste repositório, com permissão **Contents: Read and write**).
O admin edita o `config.js`: WhatsApp, e-mail, horário, mensagens de cada botão, textos do topo, recursos (balões, fundo animado, aviso de horário, botão flutuante) e tema inicial. Ao publicar, o site atualiza em cerca de 1 minuto.

## Como o site é gerado
A fonte é a versão PHP (`php/`). O `index.html` da raiz é gerado a partir dela com `php tools/build-pages.php` (o script fica na pasta de trabalho local, fora deste repositório) e não deve ser editado à mão.

## Segurança e SEO
- **CSP**: as páginas só executam scripts do próprio site (não há script inline). Para adicionar Google Analytics ou Tag Manager, inclua os domínios deles em `script-src` e `connect-src` na meta `Content-Security-Policy` de `php/includes/header.php` e gere o site de novo.
- **Admin**: `admin.html` não é indexado, não roda dentro de outras páginas e só se conecta ao próprio site e a `api.github.com`.
- **SEO**: `sitemap.xml`, `robots.txt`, canonical, Open Graph (`assets/img/og.png`) e dados estruturados (Organization, WebSite, SoftwareApplication, FAQPage). O endereço público fica em `SITE_URL` (`php/config.php`); ao usar domínio próprio, troque lá e gere de novo.
