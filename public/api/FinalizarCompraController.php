<?php
// public/api/FinalizarCompraController.php

require_once __DIR__ . '/../../app/core/Session.php';

// Garantir sessão ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Config do PagSeguro (⚠️ caminho estava errado)
$config = require __DIR__ . '/pagseguro.php';

// Classes auxiliares
require_once __DIR__ . '/PagSeguroController.php';
require_once __DIR__ . '/pagseguro_helpers.php';

// Resposta JSON
header("Content-Type: application/json; charset=utf-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

/*
|--------------------------------------------------------------------------
| 1. Validar dados recebidos
|--------------------------------------------------------------------------
*/
$nome  = $_POST["nome"]  ?? null;
$email = $_POST["email"] ?? null;
$items = $_POST["items"] ?? null;

if (!$nome || !$email) {
    http_response_code(400);
    echo json_encode(["ok" => false, "error" => "Nome e email são obrigatórios"]);
    exit;
}

$items = json_decode($items, true);

if (!is_array($items) || empty($items)) {
    http_response_code(400);
    echo json_encode(["ok" => false, "error" => "Itens inválidos"]);
    exit;
}

/*
|--------------------------------------------------------------------------
| 2. Normalizar itens
|--------------------------------------------------------------------------
*/
foreach ($items as &$item) {
    $item["name"]        = $item["name"]        ?? "Produto";
    $item["quantity"]    = (int) ($item["quantity"]    ?? 1);
    $item["unit_amount"] = (int) ($item["unit_amount"] ?? 0);

    if ($item["quantity"] <= 0) {
        $item["quantity"] = 1;
    }
    if ($item["unit_amount"] < 0) {
        $item["unit_amount"] = 0;
    }
}

/*
|--------------------------------------------------------------------------
| 3. Criar referência do pedido
|--------------------------------------------------------------------------
*/
$reference_id = "pedido-" . time();

/*
|--------------------------------------------------------------------------
| 4. Montar payload
|--------------------------------------------------------------------------
*/
$payload = [
    "reference_id" => $reference_id,
    "customer" => [
        "name"  => $nome,
        "email" => $email
    ],
    "items" => $items,
    "notification_urls" => [
        // URL CORRETA DO WEBHOOK
        "https://prontoesaudavel.infinityfree.me/pagseguro/notification"
    ],
    // Você pode ativar: "redirect_url" e "cancel_url"
];

/*
|--------------------------------------------------------------------------
| 5. Enviar para o PagSeguro
|--------------------------------------------------------------------------
*/
$pg = new PagSeguroController($config);
$response = $pg->criarCheckout($payload);

$status = $response["status"] ?? 500;
$body   = $response["body"] ?? [];

/*
|--------------------------------------------------------------------------
| 6. Capturar o link PAY
|--------------------------------------------------------------------------
*/
$link = null;

if (!empty($body["links"]) && is_array($body["links"])) {
    foreach ($body["links"] as $l) {
        if (($l["rel"] ?? "") === "PAY") {
            $link = $l["href"];
            break;
        }
    }
}

/*
|--------------------------------------------------------------------------
| 7. Resposta ao cliente
|--------------------------------------------------------------------------
*/
if ($link && in_array($status, [200, 201])) {

    // Salva referência para finalizar compra depois
    $_SESSION["pedido_temp"]["reference_id"] = $reference_id;

    ps_log("✅ CHECKOUT CRIADO", [
        "reference_id" => $reference_id,
        "payment_url"  => $link,
        "payload"      => $payload,
        "response"     => $body
    ]);

    echo json_encode([
        "ok"           => true,
        "payment_url"  => $link,
        "reference_id" => $reference_id
    ]);
    exit;
}

/*
|--------------------------------------------------------------------------
| 8. ERRO — Retornar resposta detalhada
|--------------------------------------------------------------------------
*/
ps_log("❌ ERRO AO CRIAR CHECKOUT", [
    "status"   => $status,
    "payload"  => $payload,
    "response" => $response
]);

http_response_code($status);
echo json_encode([
    "ok"    => false,
    "error" => "Falha ao criar checkout",
    "body"  => $body
]);
exit;
