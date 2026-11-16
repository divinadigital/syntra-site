<?php
/**
 * Ponte entre os CTAs do site e o atendente no WhatsApp.
 * - GET  ?origem=hero         → mensagem pré-definida para aquela origem
 * - POST (formulário de contato) → mensagem montada com os dados do lead
 */

declare(strict_types=1);
require __DIR__ . '/config.php';

function clean(string $key, int $max = 80): string
{
    $v = trim((string) ($_POST[$key] ?? ''));
    if (!mb_check_encoding($v, 'UTF-8')) {
        $v = mb_convert_encoding($v, 'UTF-8', 'Windows-1252');
    }
    $v = preg_replace('/[\x00-\x1F\x7F]/u', ' ', $v) ?? '';
    return mb_substr($v, 0, $max);
}

$origem = preg_replace('/[^a-z_]/', '', strtolower((string) ($_GET['origem'] ?? 'default'))) ?: 'default';
$message = CTA_MESSAGES[$origem] ?? CTA_MESSAGES['default'];

$perfis = [
    'clinica'     => 'clínica / consultório',
    'diagnostico' => 'centro de diagnóstico',
    'hospital'    => 'hospital',
    'rede'        => 'rede com várias unidades',
];
$perfilGet = (string) ($_GET['perfil'] ?? '');
if (isset($perfis[$perfilGet])) {
    $message .= ' Perfil: ' . $perfis[$perfilGet] . '.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Honeypot anti-bot: humanos não preenchem este campo.
    if (clean('website') !== '') {
        header('Location: index.php', true, 303);
        exit;
    }

    $nome    = clean('nome', 60);
    $clinica = clean('clinica', 80);
    $perfil  = clean('perfil', 40);
    $equipe  = clean('equipe', 20);
    $assunto = clean('assunto', 40);

    $lines = ['Olá! Vim pelo site da Syntra.'];
    if ($nome !== '')    $lines[] = "Sou {$nome}" . ($clinica !== '' ? ", da {$clinica}." : '.');
    elseif ($clinica !== '') $lines[] = "Falo pela {$clinica}.";
    if ($perfil !== '')  $lines[] = "Tipo de operação: {$perfil}.";
    if ($equipe !== '')  $lines[] = "Profissionais de saúde: {$equipe}.";
    if ($assunto !== '') $lines[] = "Quero conversar sobre: {$assunto}.";
    $message = implode("\n", $lines);
}

$url = 'https://wa.me/' . WHATSAPP_NUMBER . '?text=' . rawurlencode($message);

header('Cache-Control: no-store');
header('Location: ' . $url, true, 302);
exit;
