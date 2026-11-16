# Syntra — site institucional

Site da Syntra, sistema de gestão para clínicas e hospitais. Todos os botões levam o visitante ao atendimento no WhatsApp.

- **Raiz** (`index.html`, `assets/`): versão estática publicada no GitHub Pages. Os CTAs apontam direto para o `wa.me`, e o status "online / fora do horário" é calculado no navegador, no fuso de Brasília.
- **`php/`**: versão PHP original, para hospedagens com PHP 8+. Os CTAs passam pelo `atendimento.php`, que monta a mensagem conforme a origem do clique. O número e o horário ficam em `php/config.php`.

Para mudar o número do WhatsApp na versão estática, troque `554896225170` em `index.html`.
