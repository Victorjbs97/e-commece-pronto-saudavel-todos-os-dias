<?php
// public/api/pagseguro_redirect.php

require_once __DIR__ . '/../../app/core/Session.php';
require_once __DIR__ . '/pagseguro_helpers.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 🎯 Carrega config
$config = require __DIR__ . '/../../config/pagseguro.php';

// ⚠️ Dados vindos da URL
$checkoutId = $_GET['checkout_id'] ?? null;
$reference  = $_GET['reference_id'] ?? null;

$status = 'pending';

/**
 * 🔍 Opcionalmente consultar o status do checkout no PagSeguro
 * Isso é apenas para UX — o status real deve vir pela NOTIFICAÇÃO.
 */
if ($checkoutId) {

    $baseUrl =
        ($config['environment'] === "production")
            ? "https://api.pagseguro.com"
            : "https://sandbox.api.pagseguro.com";

    $url = $baseUrl . "/checkouts/" . urlencode($checkoutId);

    $response = ps_api_request("GET", $url, $config['token']);

    if (in_array($response["status"] ?? 0, [200, 201])) {

        $body = $response["body"] ?? [];

        // Captura status
        $status = strtolower(
            $body["status"]
                ?? ($body["payment"]["status"] ?? 'pending')
        );

        // Garante referência
        $reference = $reference
            ?? ($body["reference_id"] ?? null);

        ps_log("↩️ REDIRECT CHECKOUT: " . json_encode([
            "checkout_id" => $checkoutId,
            "reference_id" => $reference,
            "status"       => $status
        ]));
    } else {
        ps_log("⚠️ FALHA AO CONSULTAR CHECKOUT: HTTP " . ($response["status"] ?? "??"));
    }
}

// 🛡️ Segurança — sem reference_id → manda para home
if (!$reference) {
    header("Location: https://prontoesaudavel.infinityfree.me/");
    exit;
}

/**
 * ✔ URL FINAL DO FRONT
 * O front-end SEMPRE consulta o banco para evitar erro de status.
 */
$redirect = "https://prontoesaudavel.infinityfree.me/checkout-final.php"
          . "?reference=" . urlencode($reference);

header("Location: {$redirect}");
exit;
