<?php
// public/api/PagSeguroController.php

require_once __DIR__ . '/pagseguro_helpers.php';

class PagSeguroController
{
    private string $token;
    private string $environment;
    private string $baseUrl;

    public function __construct(array $config)
    {
        $this->token       = $config["token"] ?? "";
        $this->environment = $config["environment"] ?? "sandbox";

        if (!$this->token) {
            throw new Exception("Token do PagSeguro não configurado");
        }

        // Base URL oficial PagSeguro
        $this->baseUrl = ($this->environment === "production")
            ? "https://api.pagseguro.com"
            : "https://sandbox.api.pagseguro.com";
    }

    /**
     * Cria um checkout no PagSeguro
     */
    public function criarCheckout(array $pedido): array
    {
        if (empty($pedido["reference_id"]) || empty($pedido["items"])) {
            return [
                "status" => 400,
                "body"   => ["error" => "Payload inválido"]
            ];
        }

        $url = $this->baseUrl . "/checkouts";

        // Idempotência
        $pedido["idempotency_key"] = $pedido["reference_id"];

        ps_log("📤 Enviando checkout para PagSeguro: " . json_encode($pedido));

        // CHAMADA CORRETA (apenas 4 parâmetros)
        $result = ps_api_request(
            "POST",
            $url,
            $this->token,
            $pedido
        );

        // Log de erro
        if (!in_array($result["status"] ?? 500, [200, 201])) {
            ps_log("❌ ERRO API PagSeguro: " . json_encode($result));
        }

        return $result;
    }
}
