<?php
declare(strict_types=1);
require __DIR__ . '/config.php';
require __DIR__ . '/includes/icons.php';

$pageTitle = 'Syntra — gestão para clínicas e hospitais';
$pageDesc  = 'Agenda, recepção, laudos com ditado, faturamento TISS, financeiro e BI em um só sistema. Implantação guiada e atendimento humano pelo WhatsApp.';

$modules = [
    'atendimento' => [
        'label' => 'Atendimento', 'icon' => 'stetho',
        'items' => [
            ['users',    'Pacientes',          'Cadastro com busca por nome, CPF ou CNS, convênio e histórico de exames.', 'CPF · CNS'],
            ['calendar', 'Agenda',             'Escala por profissional e sala, confirmação e encaixes sem planilha paralela.', 'escala por sala'],
            ['desk',     'Recepção',           'Chegada, check-in e fila do dia. O paciente aparece na sala na hora.', 'fila em tempo real'],
            ['stetho',   'Consultório',        'Atendimento, prescrição e conduta, com o painel do médico ao lado.', 'painel do médico'],
            ['scalpel',  'Centro cirúrgico',   'Mapa de salas, tempos do procedimento e materiais usados.', 'tempos do exame'],
            ['bed',      'Internação',         'Leitos, evolução e alta, ligados ao faturamento.', 'leitos'],
            ['leaf',     'Saúde integrativa',  'Fluxos próprios para terapias e acompanhamento contínuo.', 'sessões'],
            ['funnel',   'CRM / Funil',        'Do primeiro contato ao comparecimento: novo, em contato, agendado, compareceu, ganho.', 'funil de 5 etapas'],
            ['file',     'Laudos',             'Editor com ditado, frases prontas, CID-10, anatomopatológico e laudo por voz na sala.', 'ditado · CID-10'],
        ],
    ],
    'financeiro' => [
        'label' => 'Financeiro', 'icon' => 'wallet',
        'items' => [
            ['receipt', 'Faturamento',          'Guias e lotes para convênio com códigos TUSS e entrada de XML automatizada.', 'TISS · TUSS · XML'],
            ['wallet',  'Financeiro',           'Contas a pagar e a receber, caixa e conciliação do que foi realmente pago.', 'a receber'],
            ['pill',    'Farmácia e estoque',   'Entrada, saída e consumo por procedimento, ligados ao custo por paciente.', 'custo por paciente'],
            ['cart',    'Compras',              'Fornecedores, pedidos e entrada de estoque com reflexo nas contas a pagar.', 'fornecedores'],
        ],
    ],
    'gestao' => [
        'label' => 'Gestão', 'icon' => 'chart',
        'items' => [
            ['chart',  'BI & Painéis',          '12 meses de atendimentos, faltas, receita, despesa e exames, com painel para TV.', 'painel de gestão (TV)'],
            ['users',  'Origem dos pacientes',  'Mapa por estado, novos x retornos, carteira ativa e quem está sem voltar.', 'reativação'],
            ['shield', 'Qualidade & Segurança', 'Pendências clínicas por médico: malignidade sem conduta, biópsia atrasada, resultado não avisado.', 'nada se perde'],
            ['cog',    'Saúde do sistema',      'Verificação automática de importação, PACS, arquivos e backups, com alerta quando algo para.', 'monitoramento'],
            ['book',   'Manual do sistema',     'Ajuda dentro do próprio sistema, por tela, para a equipe aprender sozinha.', 'ajuda contextual'],
            ['phone',  'App instalável',        'Instale no computador ou no celular direto do navegador, sem loja de aplicativos.', 'PWA'],
        ],
    ],
];

// Dados FICTÍCIOS para a demonstração de laudos.
$laudos = [
    ['sem',  'Ana Ribeiro',        'Endoscopia digestiva alta',      'Dra. Lúcia Prado'],
    ['rasc', 'Bruno Tavares',      'Colonoscopia',                   'Dr. Marcos Lima'],
    ['pend', 'Carla Nogueira',     'EDA com biópsia e teste de urease', 'Dra. Lúcia Prado'],
    ['lib',  'Diego Fontes',       'Ultrassom de abdome total',      'Dr. Rafael Couto'],
    ['sem',  'Elisa Moura',        'Ecocardiograma',                 'Dr. Rafael Couto'],
    ['pend', 'Fábio Antunes',      'Colonoscopia com polipectomia',  'Dr. Marcos Lima'],
    ['lib',  'Gabriela Souto',     'Endoscopia digestiva alta',      'Dra. Lúcia Prado'],
    ['rasc', 'Heitor Campos',      'Raio-X de tórax',                'Dr. Rafael Couto'],
];
$statusLabel = ['sem' => 'Sem laudo', 'rasc' => 'Rascunho', 'pend' => 'Pendente', 'lib' => 'Liberado'];
$counts = array_count_values(array_column($laudos, 0));

$specialties = ['Endoscopia', 'Colonoscopia', 'Radiologia', 'Ultrassonografia', 'Cardiologia', 'Centro cirúrgico', 'Internação', 'Saúde integrativa', 'Consultórios', 'Clínicas multiespecialidade'];

require __DIR__ . '/includes/header.php';
?>

<main id="conteudo">

<!-- =================== HERO =================== -->
<section class="hero" id="topo">
  <canvas class="hero__canvas" data-synapse aria-hidden="true"></canvas>
  <div class="wrap hero__grid">
    <div>
      <span class="eyebrow" data-reveal><span class="presence"><span class="dot <?= $online ? '' : 'dot--off' ?>"></span></span>
        <span class="shiny" data-cfg="hero.eyebrow">Gestão para clínicas e hospitais</span></span>
      <h1 class="h1 split-text" data-split data-cfg-title>A sinapse da sua <em>clínica.</em></h1>
      <p class="lead" data-reveal style="--i:3" data-cfg="hero.lead">Agenda, recepção, laudo com ditado, faturamento de convênio e BI conversando entre si. Um cadastro, um login, uma fonte de verdade — do agendamento ao laudo liberado.</p>
      <div class="cta-row" data-reveal style="--i:4">
        <a class="btn btn--primary magnetic" href="<?= cta('hero') ?>" data-cta="hero"><?= icon('whatsapp') ?> Falar com um especialista <?= icon('arrow', 18, 'i-arrow') ?></a>
        <a class="btn btn--on-dark" href="#modulos">Ver os módulos</a>
      </div>
      <p class="cta-note" data-reveal style="--i:5"><?= icon('clock', 16) ?>
        <?= $online ? 'Atendente online agora — resposta em minutos, sem compromisso.' : 'Fora do horário? Deixe sua mensagem — respondemos no próximo horário comercial.' ?></p>
      <div class="hero__chips" data-reveal style="--i:6" data-cfg-chips>
        <span class="chip"><?= icon('check', 14) ?> Implantação guiada</span>
        <span class="chip"><?= icon('check', 14) ?> TISS / TUSS</span>
        <span class="chip"><?= icon('check', 14) ?> PACS / DICOM</span>
        <span class="chip"><?= icon('check', 14) ?> Modo claro e escuro</span>
      </div>
    </div>

    <!-- Prévia real do produto (tela "Início" do novo visual) -->
    <div class="tilt" data-reveal style="--i:2">
      <div class="app" data-tilt aria-label="Prévia da tela inicial do Syntra" role="img">
        <div class="app__glare"></div>
        <aside class="app__side" aria-hidden="true">
          <div class="app__brand"><?= logo_mark('') ?> Syntra</div>
          <span class="app__sel" data-sel></span>
          <div class="app__grp">Atendimento</div>
          <div class="app__item is-on" data-sel-item><?= icon('desk') ?> Início</div>
          <div class="app__item" data-sel-item><?= icon('users') ?> Pacientes</div>
          <div class="app__item" data-sel-item><?= icon('calendar') ?> Agenda</div>
          <div class="app__item" data-sel-item><?= icon('file') ?> Laudos</div>
          <div class="app__item" data-sel-item><?= icon('funnel') ?> CRM / Funil</div>
          <div class="app__grp">Gestão</div>
          <div class="app__item" data-sel-item><?= icon('receipt') ?> Faturamento</div>
          <div class="app__item" data-sel-item><?= icon('chart') ?> BI & Painéis</div>
        </aside>
        <div class="app__main" aria-hidden="true">
          <div class="app__top">
            <div class="app__search"><?= icon('search') ?> Buscar paciente, laudo…</div>
            <span class="app__pill">Escuro</span>
            <span class="app__avatar">A</span>
          </div>
          <h4>Bom dia, Dra. Lúcia</h4>
          <div class="app__sub">Agenda de hoje · dados ilustrativos</div>
          <div class="kpis">
            <div class="kpi"><small>Agendados</small><b class="tnum" data-count="24">0</b></div>
            <div class="kpi"><small>A confirmar</small><b class="tnum" data-count="5">0</b></div>
            <div class="kpi kpi--ok"><small>Realizados</small><b class="tnum" data-count="11">0</b></div>
            <div class="kpi"><small>Laudos p/ liberar</small><b class="tnum" data-count="3">0</b></div>
          </div>
          <div class="card-ui">
            <div class="ready"><span>Pronto para atender</span><span class="ready__pct tnum" data-ready-pct>0%</span><span class="ready__bar"><i data-ready-bar></i></span><span class="ready__link">Abrir o assistente →</span></div>
            <ul class="steps" data-steps>
              <li><span class="st"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5"><path d="M20 6 9 17l-5-5"/></svg></span><span>Dados da clínica (CNPJ e CNES)</span></li>
              <li><span class="st"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5"><path d="M20 6 9 17l-5-5"/></svg></span><span>Kit da especialidade (procedimentos e modelos de laudo)</span></li>
              <li><span class="st"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5"><path d="M20 6 9 17l-5-5"/></svg></span><span>Médicos com horário na agenda</span></li>
            </ul>
          </div>
          <div class="quick">
            <div><?= icon('calendar') ?> Agendar</div>
            <div><?= icon('users') ?> Novo paciente</div>
            <div><?= icon('file') ?> Laudos</div>
            <div><?= icon('chart') ?> BI</div>
          </div>
        </div>
      </div>
      <div class="float-note float-note--a" aria-hidden="true">
        <span class="float-note__ic" style="background:#dcfce7;color:#15803d"><?= icon('check', 18) ?></span>
        <div><b>Laudo liberado</b><span>EDA · Gabriela S. · agora</span></div>
      </div>
      <div class="float-note float-note--b" aria-hidden="true">
        <span class="float-note__ic" style="background:#fef3c7;color:#b45309"><?= icon('alert', 18) ?></span>
        <div><b>Alergia não perguntada</b><span>Antes da sedação</span></div>
      </div>
    </div>
  </div>
  <div class="hero__fade"></div>
</section>

<!-- =================== ESPECIALIDADES =================== -->
<section class="section--tight" aria-label="Especialidades atendidas">
  <p class="trust-title" data-reveal>Kits prontos por especialidade — procedimentos, modelos de laudo, frases e materiais</p>
  <div class="marquee" data-reveal>
    <div class="marquee__track">
      <?php for ($k = 0; $k < 2; $k++): foreach ($specialties as $s): ?>
        <span class="chip" <?= $k ? 'aria-hidden="true"' : '' ?>><?= icon('sparkle', 15) ?> <?= e($s) ?></span>
      <?php endforeach; endfor; ?>
    </div>
  </div>
</section>

<!-- =================== PROBLEMA x SOLUÇÃO =================== -->
<section class="section" id="porque">
  <div class="wrap split">
    <div>
      <span class="eyebrow" data-reveal>Por que Syntra</span>
      <h2 class="h2 split-text" data-split>Sua clínica não precisa de seis sistemas.</h2>
      <p class="lead" data-reveal>Quando agenda, laudo e faturamento não se falam, alguém redigita o paciente, a guia volta glosada e o laudo fica esquecido. A Syntra liga essas pontas: o que a recepção registra chega à sala, ao laudo e ao convênio.</p>
      <div class="seg" role="tablist" aria-label="Comparar" data-seg>
        <span class="seg__thumb" aria-hidden="true"></span>
        <button role="tab" aria-selected="true" aria-controls="cmp-hoje" id="tab-hoje">Como é hoje</button>
        <button role="tab" aria-selected="false" aria-controls="cmp-syntra" id="tab-syntra" tabindex="-1">Com a Syntra</button>
      </div>
    </div>
    <div class="compare" data-reveal>
      <div class="compare__pane" id="cmp-hoje" role="tabpanel" aria-labelledby="tab-hoje">
        <div class="chaos"><?= icon('x', 18) ?> Agenda numa planilha, confirmação no celular pessoal</div>
        <div class="chaos"><?= icon('x', 18) ?> Paciente cadastrado três vezes, com dados diferentes</div>
        <div class="chaos"><?= icon('x', 18) ?> Laudo digitado no Word e copiado para o e-mail</div>
        <div class="chaos"><?= icon('x', 18) ?> Guia do convênio montada à mão, glosa descoberta no mês seguinte</div>
        <div class="chaos"><?= icon('x', 18) ?> Ninguém sabe quantos laudos estão atrasados</div>
        <div class="chaos"><?= icon('x', 18) ?> Relatório de gestão só no fim do mês — se sair</div>
      </div>
      <div class="compare__pane" id="cmp-syntra" role="tabpanel" aria-labelledby="tab-syntra" hidden>
        <div class="unified">
          <div class="unified__row"><?= icon('check', 18, 'ok') ?> Agenda com confirmação e fila da recepção <small>Agenda</small></div>
          <div class="unified__row"><?= icon('check', 18, 'ok') ?> Um cadastro por paciente (CPF/CNS) <small>Pacientes</small></div>
          <div class="unified__row"><?= icon('check', 18, 'ok') ?> Laudo com ditado, frases prontas e CID-10 <small>Laudos</small></div>
          <div class="unified__row"><?= icon('check', 18, 'ok') ?> Guias TISS com códigos TUSS e XML automático <small>Faturamento</small></div>
          <div class="unified__row"><?= icon('check', 18, 'ok') ?> Pendências por médico, com alerta <small>Qualidade</small></div>
          <div class="unified__row"><?= icon('check', 18, 'ok') ?> BI de 12 meses em tempo real e painel para TV <small>BI</small></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- =================== MÓDULOS =================== -->
<section class="section" id="modulos" style="background:var(--surface-2);border-block:1px solid var(--line)">
  <div class="wrap">
    <div class="section-head">
      <span class="eyebrow" data-reveal>Módulos</span>
      <h2 class="h2 split-text" data-split>Tudo o que a clínica faz, num lugar só.</h2>
      <p class="lead" data-reveal>Comece com o que você precisa hoje e ligue o resto quando fizer sentido. Os módulos compartilham o mesmo paciente, a mesma agenda e o mesmo caixa.</p>
    </div>

    <div class="tabs" role="tablist" aria-label="Áreas do sistema" data-tabs data-reveal>
      <span class="tabs__thumb" aria-hidden="true"></span>
      <?php $first = true; foreach ($modules as $key => $grp): ?>
        <button role="tab" id="t-<?= $key ?>" aria-controls="p-<?= $key ?>" aria-selected="<?= $first ? 'true' : 'false' ?>" <?= $first ? '' : 'tabindex="-1"' ?>>
          <?= icon($grp['icon'], 17) ?> <?= e($grp['label']) ?> <span class="tnum" style="opacity:.6;font-weight:500"><?= count($grp['items']) ?></span>
        </button>
      <?php $first = false; endforeach; ?>
    </div>

    <?php $first = true; foreach ($modules as $key => $grp): ?>
      <div class="tab-panel" id="p-<?= $key ?>" role="tabpanel" aria-labelledby="t-<?= $key ?>" <?= $first ? '' : 'hidden' ?>>
        <?php foreach ($grp['items'] as $i => [$ic, $name, $desc, $tag]): ?>
          <article class="mod" style="--i:<?= $i ?>" <?= $first ? 'data-reveal' : '' ?>>
            <div class="mod__ic"><?= icon($ic, 22) ?></div>
            <h3><?= e($name) ?></h3>
            <p><?= e($desc) ?></p>
            <span class="mod__tag"><?= e($tag) ?></span>
          </article>
        <?php endforeach; ?>
      </div>
    <?php $first = false; endforeach; ?>

    <div class="mods-cta" data-reveal>
      <p>Não sabe por onde começar? Um especialista monta o pacote certo para a sua operação.</p>
      <a class="btn btn--primary magnetic" href="<?= cta('modulos') ?>" data-cta="modulos"><?= icon('whatsapp', 18) ?> Montar meu pacote <?= icon('arrow', 18, 'i-arrow') ?></a>
    </div>
  </div>
</section>

<!-- =================== LAUDOS (demo interativa) =================== -->
<section class="section" id="laudos">
  <div class="wrap demo">
    <div>
      <span class="eyebrow" data-reveal>Laudos</span>
      <h2 class="h2 split-text" data-split>Nenhum laudo fica para trás.</h2>
      <p class="lead" data-reveal>Cada linha mostra a cor da sua situação. Filtre com um toque e veja o que está sem laudo, em rascunho, pendente ou liberado.</p>
      <ul class="feature-list">
        <li data-reveal style="--i:1"><?= icon('chat', 22) ?><span><b>Ditado e laudo falado na sala.</b> Fale o exame, monte o laudo e passe para o próximo paciente.</span></li>
        <li data-reveal style="--i:2"><?= icon('file', 22) ?><span><b>Laudos padrão por procedimento,</b> frases prontas, CID-10 e anatomopatológico no mesmo editor.</span></li>
        <li data-reveal style="--i:3"><?= icon('alert', 22) ?><span><b>Travas de segurança clínica:</b> "alergia não perguntada", achado crítico para o solicitante e biópsia atrasada.</span></li>
      </ul>
      <div class="cta-row" data-reveal style="--i:4">
        <a class="btn btn--primary magnetic" href="<?= cta('demo') ?>" data-cta="laudos"><?= icon('whatsapp', 18) ?> Ver uma demonstração ao vivo</a>
      </div>
    </div>

    <div class="reports" data-reveal style="--i:1">
      <div class="reports__head">
        <b><span class="tnum" data-total><?= count($laudos) ?></span> laudos</b>
        <div class="pills" role="group" aria-label="Filtrar por situação" data-pills>
          <span class="pills__thumb" aria-hidden="true"></span>
          <button type="button" aria-pressed="true" data-f="all">Todos</button>
          <?php foreach ($statusLabel as $k => $label): ?>
            <button type="button" aria-pressed="false" data-f="<?= $k ?>"><?= $label ?> <i class="tnum"><?= $counts[$k] ?? 0 ?></i></button>
          <?php endforeach; ?>
        </div>
      </div>
      <ul class="rows" aria-live="polite">
        <?php foreach ($laudos as $n => [$s, $pac, $proc, $doc]): ?>
          <li class="row" data-s="<?= $s ?>">
            <span class="bullet" aria-hidden="true"></span>
            <span><b><?= e($pac) ?></b><small><?= e($proc) ?></small></span>
            <span class="doc"><small><?= e($doc) ?></small></span>
            <span class="badge"><?= $statusLabel[$s] ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
      <div class="reports__foot">Demonstração com dados fictícios.</div>
    </div>
  </div>
</section>

<!-- =================== JORNADA =================== -->
<section class="section" id="jornada" style="background:var(--surface-2);border-block:1px solid var(--line)">
  <div class="wrap">
    <div class="section-head">
      <span class="eyebrow" data-reveal>Fluxo do paciente</span>
      <h2 class="h2 split-text" data-split>Do agendamento ao relatório, sem redigitar nada.</h2>
    </div>
    <div class="journey" data-journey>
      <div class="journey__line" aria-hidden="true"><i></i></div>
      <?php
      $journey = [
          ['calendar', 'Agenda',      'Horário marcado e confirmado; o CRM acompanha quem ainda não veio.'],
          ['desk',     'Recepção',    'Check-in com convênio; o paciente entra na fila da sala.'],
          ['stetho',   'Sala',        'O médico vê o próximo paciente e registra o exame e as fotos.'],
          ['file',     'Laudo',       'Ditado, modelo padrão e liberação com trava de segurança.'],
          ['receipt',  'Faturamento', 'Guia TISS gerada a partir do que foi feito, sem nova digitação.'],
          ['chart',    'BI',          'Receita, faltas e produção por médico atualizadas na hora.'],
      ];
      foreach ($journey as $i => [$ic, $t, $d]): ?>
        <div class="step" data-reveal style="--i:<?= $i ?>">
          <div class="step__n"><?= icon($ic, 24) ?></div>
          <h3><?= $t ?></h3>
          <p><?= $d ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- =================== IMPLANTAÇÃO =================== -->
<section class="section" id="implantacao">
  <div class="wrap onb">
    <div>
      <span class="eyebrow" data-reveal>Implantação</span>
      <h2 class="h2 split-text" data-split>Pronta para atender, etapa por etapa.</h2>
      <p class="lead" data-reveal>Um assistente mostra o que falta para a sua clínica começar e leva direto à tela certa. Traga pacientes, procedimentos e profissionais de uma planilha, com prévia, validação linha a linha e opção de desfazer.</p>
      <div class="stats" data-reveal>
        <div class="stat"><b class="tnum" data-count="<?= array_sum(array_map(fn($g) => count($g['items']), $modules)) ?>">0</b><span>módulos integrados</span></div>
        <div class="stat"><b>CSV</b><span>importação com desfazer</span></div>
        <div class="stat"><b>PWA</b><span>instala no PC e no celular</span></div>
      </div>
      <div class="cta-row" data-reveal>
        <a class="btn btn--primary magnetic" href="<?= cta('implantacao') ?>" data-cta="implantacao"><?= icon('whatsapp', 18) ?> Planejar minha implantação</a>
      </div>
      <p class="cta-note" data-reveal><?= icon('shield', 16) ?> Vindo de outro sistema? Ajudamos a migrar seus dados.</p>
    </div>

    <div class="onb__card" data-reveal data-onboarding>
      <div class="ready"><span>Pronto para atender</span><span class="ready__pct tnum" data-ready-pct>0%</span></div>
      <div class="ready__bar" style="margin-top:12px"><i data-ready-bar></i></div>
      <div class="onb__meta"><span><span class="tnum" data-ready-n>0</span> de 6 etapas prontas</span><span>Assistente de implantação</span></div>
      <ul class="steps" data-steps>
        <?php foreach (['Dados da clínica (CNPJ, CNES e registro ANS)', 'Convênios e planos', 'Procedimentos com código TUSS', 'Profissionais, acessos e CRM', 'Kit da especialidade', 'Médicos com horário na agenda'] as $st): ?>
          <li><span class="st"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5"><path d="M20 6 9 17l-5-5"/></svg></span><span><?= $st ?></span></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</section>

<!-- =================== INTEGRAÇÕES + SEGURANÇA =================== -->
<section class="section" id="seguranca" style="background:var(--surface-2);border-block:1px solid var(--line)">
  <div class="wrap">
    <div class="section-head">
      <span class="eyebrow" data-reveal>Integrações e segurança</span>
      <h2 class="h2 split-text" data-split>Fala a língua da saúde. Protege o que é sensível.</h2>
      <p class="lead" data-reveal>Padrões do setor para convênios e equipamentos, e controles pensados para dados de saúde.</p>
    </div>
    <div class="integr">
      <?php foreach (['TISS', 'TUSS', 'XML de convênio', 'CID-10', 'CNES', 'ANS', 'PACS / DICOM', 'Pixeon', 'Fujinon', 'WhatsApp', 'Importação CSV', 'Painel de chamada'] as $i => $t): ?>
        <span class="chip" data-reveal style="--i:<?= $i % 6 ?>"><?= e($t) ?></span>
      <?php endforeach; ?>
    </div>
    <div class="sec-grid">
      <?php foreach ([
          ['lock',   'Acesso por perfil',      'Médico, recepção, enfermagem e gestão veem só o que precisam.'],
          ['shield', 'Auditoria',              'Ações sensíveis e uso de IA ficam registrados, com data e responsável.'],
          ['cog',    'Saúde do sistema',       'Importação, PACS, arquivos e backups conferidos de forma automática.'],
          ['sparkle','IA dentro da sua rede',  'O texto automático pode rodar num modelo local, sem enviar dados para fora.'],
      ] as $i => [$ic, $t, $d]): ?>
        <div class="sec" data-reveal style="--i:<?= $i ?>"><?= icon($ic, 24) ?><h3><?= $t ?></h3><p><?= $d ?></p></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- =================== PARA QUEM =================== -->
<section class="section" id="paraquem">
  <div class="wrap">
    <div class="section-head">
      <span class="eyebrow" data-reveal>Para quem</span>
      <h2 class="h2 split-text" data-split>Do consultório ao hospital.</h2>
    </div>
    <div class="who">
      <?php foreach ([
          ['stetho',   'Clínicas e consultórios',   'Agenda, prontuário, financeiro e CRM para crescer sem planilha.', 'clinica'],
          ['file',     'Centros de diagnóstico',    'Endoscopia, imagem e cardiologia com laudo por voz e PACS.', 'diagnostico'],
          ['hospital', 'Hospitais',                 'Internação, centro cirúrgico, farmácia e faturamento de convênio.', 'hospital'],
          ['building', 'Redes e unidades remotas',  'Várias unidades num só painel, com fotos e exames chegando de outros endereços.', 'rede'],
      ] as $i => [$ic, $t, $d, $o]): ?>
        <a href="<?= cta('demo') ?>&perfil=<?= $o ?>" data-cta="perfil-<?= $o ?>" data-reveal style="--i:<?= $i ?>">
          <?= icon($ic, 26) ?><h3><?= $t ?></h3><p><?= $d ?></p>
          <span class="more">Falar sobre o meu caso <?= icon('arrow', 16) ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- =================== FAQ =================== -->
<section class="section" id="faq" style="padding-top:0">
  <div class="wrap faq">
    <div class="faq__aside">
      <span class="eyebrow" data-reveal>Dúvidas</span>
      <h2 class="h2 split-text" data-split>Perguntas frequentes.</h2>
      <p class="lead" data-reveal>Não achou a sua? Um atendente responde pelo WhatsApp.</p>
      <div class="cta-row" data-reveal><a class="btn btn--ghost" href="<?= cta('faq') ?>" data-cta="faq"><?= icon('whatsapp', 18) ?> Perguntar a um atendente</a></div>
    </div>
    <div class="acc" data-acc>
      <?php foreach ([
          ['Preciso instalar alguma coisa?', 'Não. A Syntra roda no navegador e pode ser instalada como aplicativo no computador ou no celular, sem loja de apps. Para PACS e importação de XML, há um agente leve que nossa equipe configura com você.'],
          ['Quanto tempo leva a implantação?', 'Depende do tamanho da operação. O assistente "Pronto para atender" mostra cada etapa e o que falta. Clínicas que já têm cadastros em planilha importam pacientes, procedimentos e profissionais em minutos, com prévia e opção de desfazer.'],
          ['Funciona com convênios?', 'Sim. O faturamento usa os padrões TISS e TUSS, lê XML de convênio e mantém os dados da clínica (CNES, registro ANS) num só lugar.'],
          ['Consigo migrar do meu sistema atual?', 'Sim. Exportando os dados em planilha (CSV), a importação valida linha a linha antes de gravar. Para bases maiores, nossa equipe ajuda na migração.'],
          ['Como ficam a segurança e a LGPD?', 'Cada usuário tem um perfil de acesso, as ações sensíveis ficam na auditoria e a saúde do sistema é monitorada (backups, importação, arquivos). O recurso de texto automático pode rodar num modelo dentro da rede da clínica.'],
          ['Quanto custa?', 'O valor depende dos módulos e do número de profissionais. Fale com um atendente: em poucos minutos ele monta uma proposta para a sua operação.'],
      ] as $i => [$q, $a]): ?>
        <div class="acc__item" data-reveal style="--i:<?= $i ?>">
          <button class="acc__btn" type="button" aria-expanded="<?= $i === 0 ? 'true' : 'false' ?>" aria-controls="faq-<?= $i ?>" id="faq-b<?= $i ?>"><?= e($q) ?> <?= icon('plus', 22) ?></button>
          <div class="acc__body" id="faq-<?= $i ?>" role="region" aria-labelledby="faq-b<?= $i ?>"><div><p><?= e($a) ?></p></div></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- =================== CTA FINAL + FORM =================== -->
<section id="contato" style="padding-bottom:0">
  <div class="wrap">
    <div class="final" data-reveal>
      <canvas data-synapse data-density="0.6" aria-hidden="true"></canvas>
      <div>
        <span class="eyebrow">Fale com a gente</span>
        <h2 class="h2" style="margin-top:14px">Veja a Syntra funcionando com a rotina da sua clínica.</h2>
        <p class="lead">Conte em 20 segundos como é a sua operação. O atendente recebe tudo no WhatsApp e já começa a conversa no ponto certo.</p>
        <p class="cta-note" style="color:rgb(226 238 252 / .8)"><span class="dot <?= $online ? '' : 'dot--off' ?>"></span>
          <span data-hours-short><?= $online ? 'Atendentes online agora' : 'Atendimento humano seg. a sex., ' . BUSINESS_START . '–' . BUSINESS_END ?></span></p>
      </div>

      <form class="form" method="post" action="atendimento.php?origem=form" data-form novalidate>
        <h3>Falar com um especialista</h3>
        <p>Só o nome é obrigatório.</p>
        <div class="field">
          <label for="f-nome">Seu nome</label>
          <input id="f-nome" name="nome" autocomplete="name" required maxlength="60" placeholder="Ex.: Ana Souza">
        </div>
        <div class="field">
          <label for="f-clinica">Clínica ou hospital</label>
          <input id="f-clinica" name="clinica" autocomplete="organization" maxlength="80" placeholder="Ex.: Clínica Bem Viver">
        </div>
        <fieldset class="field">
          <legend>Tipo de operação</legend>
          <div class="opts">
            <?php foreach (['Clínica', 'Centro de diagnóstico', 'Hospital', 'Consultório'] as $o): ?>
              <label><input type="radio" name="perfil" value="<?= $o ?>"><span><?= $o ?></span></label>
            <?php endforeach; ?>
          </div>
        </fieldset>
        <div class="field-2">
          <div class="field">
            <label for="f-equipe">Profissionais de saúde</label>
            <select id="f-equipe" name="equipe">
              <option value="">Selecione</option>
              <option>1 a 3</option><option>4 a 10</option><option>11 a 30</option><option>Mais de 30</option>
            </select>
          </div>
          <div class="field">
            <label for="f-assunto">Quero falar sobre</label>
            <select id="f-assunto" name="assunto">
              <option>Demonstração</option><option>Preços e planos</option><option>Implantação</option><option>Migração de dados</option><option>Faturamento / convênios</option>
            </select>
          </div>
        </div>
        <input class="hp" type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">
        <button class="btn btn--wa magnetic" type="submit"><?= icon('whatsapp') ?> Continuar no WhatsApp <?= icon('arrow', 18, 'i-arrow') ?></button>
        <small>Abrimos o WhatsApp com a mensagem pronta. Nada é enviado sem você confirmar.</small>
      </form>
    </div>
  </div>
</section>

</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
