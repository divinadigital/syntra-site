# Site Syntra (PHP)

Site institucional com CTAs que levam o visitante a um atendente no WhatsApp.

## Antes de publicar
1. `WHATSAPP_NUMBER` em `config.php` já está com o atendimento (48) 9622-5170. Para trocar, use só dígitos: 55 + DDD + número.
2. Ajuste `CONTACT_EMAIL` e o horário de atendimento (`BUSINESS_*`). O site mostra "Atendente online" ou "Deixe sua mensagem" conforme esse horário.
3. Se quiser, edite as mensagens pré-preenchidas em `CTA_MESSAGES`. Cada botão envia uma mensagem diferente, para o atendente saber de onde o lead veio.

## Como funciona
- Todo CTA aponta para `atendimento.php?origem=...`, que redireciona para `wa.me` com a mensagem certa.
- O formulário (nome, clínica, perfil, equipe, assunto) monta a mensagem e abre o WhatsApp. Não grava nada no servidor e tem honeypot anti-bot.
- Há um gancho para analytics: evento `cta_whatsapp` no `dataLayer` (GA4/GTM), se existir.

## Rodar localmente
```
C:/xampp/php/php.exe -S 127.0.0.1:5520 -t syntra-site
```
Requisitos: PHP 8.0+ (com mbstring). Em Apache, o `.htaccess` bloqueia `config.php`, `includes/` e os `.md`.

## Estrutura
- `index.php`: página (seções e dados dos módulos)
- `includes/`: cabeçalho, rodapé e ícones SVG
- `assets/css/style.css`: tema claro/escuro, que segue o novo visual v0.43
- `assets/js/main.js`: motion (canvas "sinapse", split text, tilt 3D, abas e filtros deslizantes, contadores, acordeão), com respeito a "reduzir movimento"
