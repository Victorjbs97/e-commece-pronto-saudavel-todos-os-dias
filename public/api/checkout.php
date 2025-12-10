<?php
// public/api/checkout.php

header("Content-Type: application/json; charset=utf-8");

// Segurança: evita cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

require_once __DIR__ . '/FinalizarCompraService.php';

// ---------------------------------------------------------------------------
// 1. Verificar método HTTP
// ---------------------------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode([
        "ok"    => false,
        "error" => "Método não permitido. Use POST."
    ]);
    exit;
}

// ---------------------------------------------------------------------------
// 2. Ler dados do FORMULÁRIO (POST tradicional)
// ---------------------------------------------------------------------------
if (empty($_POST)) {
    http_response_code(400);
    echo json_encode([
        "ok"    => false,
        "error" => "Nenhum dado recebido"
    ]);
    exit;
}

$data = $_POST;

// ---------------------------------------------------------------------------
// 3. Decodificar campo "items" (JSON vindo do input hidden)
// ---------------------------------------------------------------------------
$items = json_decode($data["items"] ?? "", true);

if (!$items) {
    http_response_code(400);
    echo json_encode([
        "ok"    => false,
        "error" => "JSON inválido em items: " . json_last_error_msg()
    ]);
    exit;
}

// Substitui a string JSON pelo array válido
$data["items"] = $items;

// ---------------------------------------------------------------------------
// 4. Chamar serviço de checkout
// ---------------------------------------------------------------------------
$result = FinalizarCompraService::criarCheckout($data);

// ---------------------------------------------------------------------------
// 5. Resposta final
// ---------------------------------------------------------------------------
$statusCode = isset($result["status"]) ? intval($result["status"]) : 500;

$responseData = $result["data"] ?? [
    "ok"    => false,
    "error" => "Erro desconhecido ao finalizar checkout"
];

http_response_code($statusCode);
echo json_encode($responseData);
exit;
