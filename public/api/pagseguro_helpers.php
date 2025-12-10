<?php
// public/api/pagseguro_helpers.php

function ps_log($msg) {
    $file = __DIR__ . "/pagseguro.log";
    @file_put_contents(
        $file,
        "[" . date("Y-m-d H:i:s") . "] " . $msg . PHP_EOL,
        FILE_APPEND
    );
}

function ps_api_request($method, $url, $token, $data = null) {

    if (empty($token)) {
        return [
            "status" => 401,
            "body"   => ["error" => "Token PagSeguro não informado"]
        ];
    }

    $headers = [
        "Authorization: Bearer {$token}",
        "Content-Type: application/json",
        "Accept: application/json",
        "User-Agent: PagSeguro-Checkout/1.0" // recomendado
    ];

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST  => strtoupper($method),
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 15,
        CURLOPT_TIMEOUT        => 40,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
    ]);

    // Envia o payload apenas se precisar
    if ($data !== null) {
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        ps_log("📤 ENVIADO → {$json}");
    }

    $response = curl_exec($ch);
    $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Erro CURL
    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);

        ps_log("❌ CURL ERROR: {$error}");

        return [
            "status" => 500,
            "body"   => ["error" => $error]
        ];
    }

    curl_close($ch);

    // Loga a resposta crua
    ps_log("📥 RECEBIDO ({$status}) → {$response}");

    // Caso venha HTML (erro do PagSeguro)
    if (stripos($response, "<html") !== false) {
        return [
            "status" => 500,
            "body" => [
                "error" => "Resposta HTML inesperada (token inválido ou requisição incorreta)",
                "raw"   => $response
            ]
        ];
    }

    // Decodifica JSON
    $decoded = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        ps_log("❌ JSON INVALIDO: {$response}");

        return [
            "status" => 500,
            "body" => [
                "error" => "Retorno não é JSON válido",
                "raw"   => $response
            ]
        ];
    }

    return [
        "status" => $status,
        "body"   => $decoded
    ];
}
