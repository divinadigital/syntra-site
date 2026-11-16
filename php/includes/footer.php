<footer class="footer">
  <div class="wrap">
    <div class="footer__grid">
      <div>
        <span class="logo"><?= logo_full() ?></span>
        <p style="color:var(--muted);font-size:14.5px;max-width:34ch;margin:14px 0 18px">Gestão para clínicas e hospitais — agenda, atendimento, laudos, faturamento e BI no mesmo lugar.</p>
        <a class="btn btn--wa btn--sm" href="<?= cta('default') ?>" data-cta="footer"><?= icon('whatsapp', 18) ?> Chamar no WhatsApp</a>
      </div>
      <div>
        <h4>Produto</h4>
        <ul>
          <li><a href="#modulos">Módulos</a></li>
          <li><a href="#laudos">Laudos</a></li>
          <li><a href="#implantacao">Implantação</a></li>
          <li><a href="#seguranca">Segurança</a></li>
        </ul>
      </div>
      <div>
        <h4>Atendimento</h4>
        <ul>
          <li><a href="<?= cta('demo') ?>">Agendar demonstração</a></li>
          <li><a href="<?= cta('implantacao') ?>">Implantação e migração</a></li>
          <li><a href="mailto:<?= CONTACT_EMAIL ?>" data-cfg-email><?= CONTACT_EMAIL ?></a></li>
        </ul>
      </div>
      <div>
        <h4>Clientes</h4>
        <ul>
          <li><a href="<?= APP_URL ?>/login" rel="noopener">Entrar no sistema</a></li>
          <li><a href="<?= APP_URL ?>/login/esqueci" rel="noopener">Esqueci minha senha</a></li>
        </ul>
      </div>
    </div>
    <div class="footer__bottom">
      <span>© <?= date('Y') ?> Syntra · <?= SITE_TAGLINE ?></span>
      <span data-hours-label>Atendimento humano: seg. a sex., <?= BUSINESS_START ?>–<?= BUSINESS_END ?></span>
    </div>
  </div>
</footer>

<div class="fab" id="fab">
  <div class="fab__bubble" role="status">
    <b><span class="dot <?= $online ? '' : 'dot--off' ?>"></span><?= $online ? 'Atendente online' : 'Deixe sua mensagem' ?></b>
    <?= $online ? 'Tire sua dúvida agora pelo WhatsApp.' : 'Respondemos no próximo horário comercial.' ?>
  </div>
  <a class="fab__btn" href="<?= cta('flutuante') ?>" data-cta="flutuante" aria-label="Falar com um atendente no WhatsApp"><?= icon('whatsapp', 28) ?></a>
</div>

<script src="assets/js/main.js?v=2" defer></script>
</body>
</html>
