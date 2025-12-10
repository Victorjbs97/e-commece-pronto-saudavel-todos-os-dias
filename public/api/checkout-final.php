<?php
// public/api/checkout-final.php

require_once __DIR__ . '/../../app/core/Session.php';
require_once __DIR__ . '/pagseguro_helpers.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$config = require __DIR__ . '/pagseguro.php';

$checkoutId = $_GET['checkout_id'] ?? null;
$reference  = $_GET['reference_id'] ?? null;
$status     = 'pending';

/*
|--------------------------------------------------------------------------
| 1. Consultar status do checkout no PagSeguro
|--------------------------------------------------------------------------
*/
if (!empty($checkoutId)) {

    $baseUrl = $config['environment'] === "production"
        ? "https://api.pagseguro.com"
        : "https://sandbox.api.pagseguro.com";

    $url = $baseUrl . "/checkouts/" . urlencode($checkoutId);

    $response = ps_api_request(
        "GET",
        $url,
        $config['token']
    );

    // Verificar se a API respondeu corretamente
    if (!empty($response['status']) && in_array($response['status'], [200, 201])) {

        $data = $response["body"] ?? [];

        // PagSeguro pode retornar "status" ou dentro de "payment"
        $status = $data["status"]
            ?? ($data["payment"]["status"] ?? 'pending');

        // Garantir referência
        if (empty($reference)) {
            $reference = $data["reference_id"] ?? null;
        }

        ps_log("↩️ CHECKOUT CONSULTADO: " . json_encode([
            "checkout_id" => $checkoutId,
            "reference"   => $reference,
            "status"      => $status,
            "raw"         => $data
        ]));

    } else {
        // Log de erro
        ps_log("❌ ERRO AO CONSULTAR CHECKOUT: " . json_encode($response));
    }
}

/*
|--------------------------------------------------------------------------
| 2. Validação antes de redirecionar
|--------------------------------------------------------------------------
*/
if (empty($reference)) {
    ps_log("❌ Redirect cancelado: reference_id ausente");
    header("Location: /");
    exit;
}

/*
|--------------------------------------------------------------------------
| 3. Redirecionar para checkout-final (página do cliente)
|--------------------------------------------------------------------------
*/
$redirectUrl = "/checkout-final.php?reference=" . urlencode($reference);

ps_log("➡️ REDIRECIONANDO PARA: {$redirectUrl}");

header("Location: {$redirectUrl}");
exit;
