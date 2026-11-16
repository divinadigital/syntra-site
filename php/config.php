<?php
/**
 * Configuração central do site Syntra.
 * Altere aqui o número do WhatsApp do atendimento e o horário comercial.
 */

declare(strict_types=1);

date_default_timezone_set('America/Sao_Paulo');

const SITE_NAME   = 'Syntra';
const SITE_TAGLINE = 'A sinapse da sua clínica';
const SITE_URL    = 'https://syntrabrasil.tech';
const APP_URL     = 'https://app.syntrabrasil.tech';

// Número do atendente no formato internacional, só dígitos (55 + DDD + número).
// Atendimento comercial: (48) 9622-5170
const WHATSAPP_NUMBER = '554896225170';

const CONTACT_EMAIL = 'contato@syntrabrasil.tech';

// Horário de atendimento humano (seg = 1 ... dom = 7)
const BUSINESS_DAYS  = [1, 2, 3, 4, 5];
const BUSINESS_START = '08:00';
const BUSINESS_END   = '18:00';

/** Mensagens pré-preenchidas por origem do clique — o atendente sabe de onde veio o lead. */
const CTA_MESSAGES = [
    'hero'        => 'Olá! Vim pelo site da Syntra e quero conhecer o sistema para a minha clínica.',
    'nav'         => 'Olá! Quero falar com um especialista da Syntra.',
    'modulos'     => 'Olá! Quero entender melhor os módulos da Syntra.',
    'implantacao' => 'Olá! Quero saber como funciona a implantação da Syntra.',
    'faturamento' => 'Olá! Quero saber como a Syntra cuida do faturamento e convênios (TISS).',
    'demo'        => 'Olá! Gostaria de agendar uma demonstração da Syntra.',
    'flutuante'   => 'Olá! Estou no site da Syntra e tenho uma dúvida.',
    'faq'         => 'Olá! Não achei minha dúvida no FAQ, pode me ajudar?',
    'default'     => 'Olá! Vim pelo site da Syntra.',
];

function is_business_hours(?DateTimeImmutable $now = null): bool
{
    $now ??= new DateTimeImmutable('now');
    if (!in_array((int) $now->format('N'), BUSINESS_DAYS, true)) {
        return false;
    }
    $t = $now->format('H:i');
    return $t >= BUSINESS_START && $t < BUSINESS_END;
}

/** Link interno de CTA (passa pelo atendimento.php para montar a mensagem certa). */
function cta(string $origem = 'default'): string
{
    return 'atendimento.php?origem=' . rawurlencode($origem);
}

function e(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
