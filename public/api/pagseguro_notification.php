<?php
// public/api/pagseguro_notification.php

require_once __DIR__ . '/pagseguro_helpers.php';
require_once __DIR__ . '/../../app/core/DataBaseConecta.php';

header("Content-Type: text/plain; charset=utf-8");

$raw = file_get_contents("php://input");

// 🔍 Verifica se veio algo
if (empty($raw)) {
    ps_log("❌ Notificação vazia");
    http_response_code(400);
    exit("EMPTY");
}

ps_log("🔔 NOTIFICAÇÃO RECEBIDA RAW → " . $raw);

// ========================
// 🔄 TENTA DECODIFICAR JSON
// ========================
$data = json_decode($raw, true);

// Se não for JSON → tenta tratar como query string
if (json_last_error() !== JSON_ERROR_NONE) {
    $qsData = [];
    parse_str($raw, $qsData);

    if (!empty($qsData)) {
        $data = $qsData;
        ps_log("ℹ️ Notificação convertida de query-string");
    } else {
        ps_log("❌ Não foi possível interpretar notificação");
        http_response_code(400);
        exit("INVALID");
    }
}

// ========================
// 🔍 CAPTA O REFERENCE_ID
// ========================
$reference =
       $data["reference_id"]
    ?? $data["reference"]
    ?? ($data["charge"]["reference_id"] ?? null);

// ========================
// 🔍 CAPTA STATUS
// ========================
$status_pagamento =
       $data["status"]
    ?? ($data["payment_statuses"][0]["status"] ?? null)
    ?? ($data["charge"]["status"] ?? null);

if (!$reference || !$status_pagamento) {
    ps_log("❌ Notificação incompleta → " . json_encode($data));
    http_response_code(400);
    exit("MISSING_DATA");
}

$status_pagamento = strtolower($status_pagamento);

// ========================
// 🔁 MAPEAR STATUS
// ========================
$status_final = match ($status_pagamento) {
    "paid", "approved", "authorized", "available" => "pago",
    "declined", "canceled"                       => "cancelado",
    "analysis", "in_analysis", "under_review"    => "analise",
    "refunded", "chargeback"                     => "estornado",
    default                                      => "pendente",
};

// ========================
// 🔌 CONEXÃO DB (corrigido)
// ========================
if (!isset($conexao) || !$conexao instanceof PDO) {
    global $conexaoDB;
    $conexao = $conexaoDB ?? null;
}

if (!$conexao) {
    ps_log("❌ ERRO: sem conexão PDO");
    http_response_code(500);
    exit("NO_DB");
}

// ========================
// 💾 ATUALIZA PEDIDO
// ========================
try {
    $stmt = $conexao->prepare("
        UPDATE pedido
           SET status = ?
         WHERE reference_id = ?
           AND status != 'pago'
    ");

    $stmt->execute([$status_final, $reference]);

    if ($stmt->rowCount() === 0) {
        ps_log("⚠️ Pedido NÃO atualizado (já pago ou inexistente): {$reference}");
    } else {
        ps_log("✅ Pedido atualizado: {$reference} → {$status_final}");
    }

    http_response_code(200);
    exit("OK");

} catch (PDOException $e) {
    ps_log("❌ PDO ERROR: " . $e->getMessage());
    http_response_code(500);
    exit("DB_ERROR");
}
