<?php
// public/api/FinalizarCompraService.php

require_once __DIR__ . '/pagseguro.php';
require_once __DIR__ . '/PagSeguroController.php';
require_once __DIR__ . '/pagseguro_helpers.php';
require_once __DIR__ . '/../../app/core/DataBaseConecta.php';

class FinalizarCompraService
{
    public static function criarCheckout(array $post): array
    {
        session_start();

        // ------------------------------------------------------------------
        // 1. Dados básicos vindos do checkout
        // ------------------------------------------------------------------
        $nome  = trim($post['nome'] ?? '');
        $email = trim($post['email'] ?? '');
        $items = $post['items'] ?? null;
        $valor = $post['valor'] ?? null;

        if (is_string($items)) {
            $json = json_decode($items, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $items = $json;
            }
        }

        if (!$nome || !$email) {
            return self::erro(400, 'Nome e email obrigatórios');
        }

        if (empty($items) && (!$valor || floatval($valor) <= 0)) {
            return self::erro(400, 'Itens ou valor são obrigatórios');
        }

        // ------------------------------------------------------------------
        // 2. Captura AUTOMÁTICA do usuário logado
        // ------------------------------------------------------------------
        $usuario_id = $_SESSION['user_id'] ?? null;

        if (!$usuario_id) {
            return self::erro(401, 'Usuário não autenticado');
        }

        // ------------------------------------------------------------------
        // 3. Buscar endereço pela tabela endereco
        // ------------------------------------------------------------------
        try {
            global $conexao;

            $sql = "SELECT id FROM endereco WHERE usuario_id = ? LIMIT 1";
            $stmt = $conexao->prepare($sql);
            $stmt->execute([$usuario_id]);

            $endereco_id = $stmt->fetchColumn();

            if (!$endereco_id) {
                return self::erro(400, 'Usuário não possui endereço cadastrado');
            }

        } catch (PDOException $e) {
            ps_log("❌ ERRO BUSCA ENDEREÇO: " . $e->getMessage());
            return self::erro(500, 'Erro ao buscar endereço de entrega');
        }

        // ------------------------------------------------------------------
        // 4. Normalização de itens
        // ------------------------------------------------------------------
        $itensNormalizados = [];

        if (is_array($items)) {
            foreach ($items as $it) {

                $produto_id = intval($it['id'] ?? $it['produto_id'] ?? 0);
                $qtd = (int) ($it['quantity'] ?? $it['qtd'] ?? 1);

                $unit_amount = (int) ($it['unit_amount'] ?? $it['amount'] ?? 0);

                if ($unit_amount === 0 && isset($it['price'])) {
                    $unit_amount = (int) round(floatval($it['price']) * 100);
                }

                if ($produto_id <= 0 || $unit_amount <= 0 || $qtd <= 0) {
                    continue;
                }

                $itensNormalizados[] = [
                    'produto_id' => $produto_id,
                    'name'        => substr($it['name'] ?? 'Produto', 0, 128),
                    'quantity'    => $qtd,
                    'unit_amount' => $unit_amount
                ];
            }
        }

    

        if (empty($itensNormalizados)) {
            return self::erro(400, 'Nenhum item válido recebido');
        }

        // ------------------------------------------------------------------
        // 5. Geração da referência
        // ------------------------------------------------------------------
        $reference_id = 'pedido-' . time() . '-' . rand(100, 999);

        // ------------------------------------------------------------------
        // 6. BASE URL automática
        // ------------------------------------------------------------------
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';

        $projectPath = '/e-commece-pronto-saudavel-todos-os-dias';
        $BASE_URL = rtrim($protocol . '://' . $host . $projectPath, '/');

        // ------------------------------------------------------------------
        // 7. Payload PagSeguro
        // ------------------------------------------------------------------
        $payload = [
            'reference_id' => $reference_id,

            'customer' => [
                'name'  => $nome,
                'email' => $email
            ],

            'items' => $itensNormalizados,

            'notification_urls' => [
                $BASE_URL . '/pagseguro/notification'
            ],

            'redirect_url' => $BASE_URL . '/pagseguro/redirect'
        ];

        // ------------------------------------------------------------------
        // 8. Salvar pedido + itens
        // ------------------------------------------------------------------
        try {

            $totalCentavos = 0;

            foreach ($itensNormalizados as $it) {
                $totalCentavos += ($it['unit_amount'] * $it['quantity']);
            }

            // ------------------- INSERT PEDIDO -------------------
            $sql = "
                INSERT INTO pedido 
                (reference_id, status, total, usuario_id, endereco_id, observacoes, data_criacao)
                VALUES (?, 'PENDENTE', ?, ?, ?, 'Pedido criado automaticamente', NOW())
            ";

            $stmt = $conexao->prepare($sql);
            $stmt->execute([
                $reference_id,
                $totalCentavos / 100,
                $usuario_id,
                $endereco_id
            ]);
            

            // ------------------- ID PEDIDO -------------------
            $pedido_id = $conexao->lastInsertId();

            // ------------------- INSERT ITENS -------------------
            $sqlItem = "
                INSERT INTO item_pedido
                (pedido_id, produto_id, quantidade, valor_unitario)
                VALUES (?, ?, ?, ?)
            ";

            $stmtItem = $conexao->prepare($sqlItem);

            foreach ($itensNormalizados as $it) {

                $stmtItem->execute([
                    $pedido_id,
                    $it['produto_id'],
                    $it['quantity'],
                    $it['unit_amount'] / 100
                ]);
            }

        } catch (PDOException $e) {
            ps_log("❌ ERRO INSERT PEDIDO: " . $e->getMessage());
            return self::erro(500, 'Erro ao salvar pedido');
        }

        // ------------------------------------------------------------------
        // 9. Criar checkout PagSeguro
        // ------------------------------------------------------------------
        try {
            $pg = new PagSeguroController(require __DIR__ . '/pagseguro.php');
            $response = $pg->criarCheckout($payload);

        } catch (Throwable $t) {
            ps_log("❌ EXCEPTION PAGSEGURO: " . $t->getMessage());
            return self::erro(500, 'Erro ao criar checkout');
        }

        $status = $response['status'] ?? 500;
        $body   = $response['body'] ?? [];

        // ------------------------------------------------------------------
        // 10. Captura do link PAY
        // ------------------------------------------------------------------
        $paymentUrl = null;

        if (!empty($body['links'])) {
            foreach ($body['links'] as $l) {
                if (($l['rel'] ?? '') === 'PAY') {
                    $paymentUrl = $l['href'];
                    break;
                }
            }
        }

        if ($paymentUrl && in_array($status, [200, 201])) {

            ps_log("✅ CHECKOUT CRIADO", [
                "reference_id" => $reference_id,
                "usuario_id"   => $usuario_id,
                "endereco_id"  => $endereco_id,
                "total"        => $totalCentavos,
                "payment_url"  => $paymentUrl
            ]);

            return [
                'status' => 200,
                'data' => [
                    'ok'           => true,
                    'payment_url'  => $paymentUrl,
                    'reference_id' => $reference_id
                ]
            ];
        }

        ps_log("❌ ERRO PAGSEGURO:", json_encode($response));

        return self::erro(500, $body);
    }

    private static function erro(int $status, $msg): array
    {
        return [
            'status' => $status,
            'data'   => [
                'ok'    => false,
                'error' => $msg
            ]
        ];
    }
} 