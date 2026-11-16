# Syntra — site institucional

Site da Syntra, sistema de gestão para clínicas e hospitais. Todos os botões levam o visitante ao atendimento no WhatsApp.

- **Raiz** (`index.html`, `assets/`): versão estática publicada no GitHub Pages. Os CTAs apontam direto para o `wa.me`, e o status "online / fora do horário" é calculado no navegador, no fuso de Brasília.
- **`php/`**: versão PHP original, para hospedagens com PHP 8+. Os CTAs passam pelo `atendimento.php`, que monta a mensagem conforme a origem do clique. O número e o horário ficam em `php/config.php`.

Para mudar o número do WhatsApp na versão estática, troque `554896225170` em `index.html`.

## Configurações rápidas (admin)
Abra `admin.html` no site publicado (`/syntra-site/admin.html`) e entre com uma chave do GitHub (fine-grained token só deste repositório, com permissão **Contents: Read and write**).
O admin edita o `config.js`: WhatsApp, e-mail, horário, mensagens de cada botão, textos do topo, recursos (balões, fundo animado, aviso de horário, botão flutuante) e tema inicial. Ao publicar, o site atualiza em cerca de 1 minuto.
