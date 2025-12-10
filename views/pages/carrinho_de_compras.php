<?php
// public/views/carrinho.php

require_once __DIR__ . '/../../app/core/Session.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../app/core/DataBaseConecta.php';
global $baseUrl;

$produtos_do_banco = [];
$total_carrinho = 0.00;
$carrinho_vazio = true;

try {
    if (isset($_SESSION['carrinho']) && !empty($_SESSION['carrinho'])) {

        $carrinho_vazio = false;

        $ids_produtos = array_keys($_SESSION['carrinho']);
        $placeholders = implode(',', array_fill(0, count($ids_produtos), '?'));

        $sql = "SELECT * FROM produtos WHERE id IN ($placeholders) AND ativo = 1";
        $stmt = $conexao->prepare($sql);
        $stmt->execute($ids_produtos);

        $produtos_do_banco = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($produtos_do_banco)) {
            $carrinho_vazio = true;
        } else {

            foreach ($produtos_do_banco as $p) {

                $qtd_temp   = $_SESSION['carrinho'][$p['id']];
                $preco_temp = (float)($p['valor'] ?? 0);

                if ($qtd_temp >= 10) {
                    $preco_temp -= 2.00;
                }

                if ($preco_temp < 0) $preco_temp = 0;

                $total_carrinho += ($preco_temp * $qtd_temp);
            }
        }
    }
} catch (PDOException $e) {
    echo "<p style='color:red;'>Erro SQL: " . htmlspecialchars($e->getMessage()) . "</p>";
    die();
}

/* ===============================
   ITENS PARA O CHECKOUT PAGSEGURO
=============================== */

$items_checkout = [];

foreach ($produtos_do_banco as $p) {

    $quantidade = $_SESSION['carrinho'][$p['id']];
    $preco = (float)($p['valor'] ?? 0);

    if ($quantidade >= 10) {
        $preco -= 2.00;
    }

    if ($preco < 0) $preco = 0;

    $items_checkout[] = [
        "id"          => $p['id'],   
        "name"        => $p['nome'],
        "quantity"    => intval($quantidade),
        "unit_amount" => intval(round($preco * 100))
    ];
    
}

/* ==================================================
   USUÁRIO LOGADO
================================================== */

$usuario = null;

if (
    isset($_SESSION['user_id']) &&
    isset($_SESSION['user_nome']) &&
    isset($_SESSION['user_email'])
) {
    $usuario = [
        'id'    => $_SESSION['user_id'],
        'nome'  => $_SESSION['user_nome'],
        'email' => $_SESSION['user_email'],
        'tipo'  => $_SESSION['user_tipo'] ?? 'cliente'
    ];
}
?>

<main>

    <section class="sessaoCarrinho">

        <?php if ($carrinho_vazio): ?>

            <div id="vazio">
                <p>Você não possui compras no carrinho</p>

                <svg xmlns="http://www.w3.org/2000/svg" width="250" height="250"
                    viewBox="0 0 24 24" fill="none"
                    stroke="#979797ff" stroke-width="1.6"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 6h15l-1.5 9h-13z" />
                    <circle cx="9" cy="20" r="1.5" />
                    <circle cx="18" cy="20" r="1.5" />
                    <path d="M6 6L4 2H1" />
                    <path d="M15 4l4 4M19 4l-4 4" />
                </svg>
            </div>

        <?php else: ?>

            <div id="cheio">
                <h1>Carrinho de Compras</h1>
            </div>

            <table id="tabelaDesktop" class="tab">
                <thead id="tab_he">
                <tr>
                    <th>Excluir</th>
                    <th>Produto</th>
                    <th>Nome</th>
                    <th>Preço</th>
                    <th>Quantidade</th>
                    <th>Subtotal</th>
                </tr>
                </thead>

                <tbody id="tab_bo">

                <?php foreach ($produtos_do_banco as $produto):

                    $quantidade = $_SESSION['carrinho'][$produto['id']];
                    $preco = (float)($produto['valor'] ?? 0);

                    if ($quantidade >= 10) $preco -= 2.00;
                    if ($preco < 0) $preco = 0;

                    $subtotal = $preco * $quantidade;

                    $imagem_url = (!empty($produto['imagem_url']))
                        ? $baseUrl . $produto['imagem_url']
                        : $baseUrl . '/public/images/sem-imagem.png';
                ?>

                    <tr>
                        <td id="lixeira">
                            <form action="<?= htmlspecialchars($baseUrl) ?>/public/index.php" method="POST">
                                <input type="hidden" name="action" value="gerenciar_carrinho">
                                <input type="hidden" name="acao_carrinho" value="deletar">
                                <input type="hidden" name="id_produto" value="<?= $produto['id'] ?>">
                                <button type="submit" class="botao-deletar"></button>
                            </form>
                        </td>

                        <td id="imagem">
                            <img src="<?= htmlspecialchars($imagem_url) ?>" width="50">
                        </td>

                        <td id="produto"><?= htmlspecialchars($produto['nome']) ?></td>

                        <td id="preco">
                            R$ <?= number_format($preco, 2, ',', '.') ?>
                        </td>

                        <td id="contadorProduto">

                            <form action="<?= htmlspecialchars($baseUrl) ?>/public/index.php"
                                  method="POST"
                                  class="form-carrinho">

                                <input type="hidden" name="action" value="gerenciar_carrinho">
                                <input type="hidden" name="acao_carrinho" value="atualizar">
                                <input type="hidden" name="id_produto" value="<?= $produto['id'] ?>">

                                <div class="quantidade-container">

                                    <button type="button"
                                            class="btn-quantidade"
                                            onclick="alterarQuantidade(this,-1)">−</button>

                                    <input id="qtd"
                                           type="number"
                                           name="quantidade"
                                           value="<?= $quantidade ?>"
                                           min="1"
                                           class="input-quantidade"
                                           readonly>

                                    <button type="button"
                                            class="btn-quantidade"
                                            onclick="alterarQuantidade(this,1)">+</button>

                                </div>

                            </form>

                        </td>

                        <td id="valorQtd">
                            R$ <?= number_format($subtotal, 2, ',', '.') ?>
                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>
            </table>

            <div id="total-carrinho">

                <div id="butaos">

                    <div id="botao-pagamento">
                        <a href="<?= htmlspecialchars($baseUrl) ?>/public/index.php?page=produtos">
                            Adicionar +
                        </a>
                    </div>

                    <?php if (!$carrinho_vazio && $usuario): ?>

                        <!-- CORRIGIDO AQUI -->
                        <form id="botao-pagamento"
                              method="post"
                              action="<?= htmlspecialchars($baseUrl) ?>/public/api/checkout.php">

                            <input type="hidden" name="nome"  value="<?= htmlspecialchars($usuario['nome']) ?>">
                            <input type="hidden" name="email" value="<?= htmlspecialchars($usuario['email']) ?>">
                            <input type="hidden" name="valor" value="<?= number_format($total_carrinho, 2, '.', '') ?>">
                            <input type="hidden" name="items" value="<?= htmlspecialchars(json_encode($items_checkout, JSON_UNESCAPED_UNICODE)) ?>">

                            <button type="submit" class="botao-pagamento">
                                Finalizar compra
                            </button>

                        </form>

                    <?php elseif (!$usuario): ?>

                        <p style="color:red;">Faça login para finalizar sua compra.</p>

                    <?php endif; ?>

                </div>

                <h3>
                    Total: R$ <?= number_format($total_carrinho, 2, ',', '.') ?>
                </h3>

            </div>

        <?php endif; ?>

    </section>

    <script>
        function alterarQuantidade(botao, delta) {

            const form = botao.closest('form');
            const input = form.querySelector('.input-quantidade');

            let valor = parseInt(input.value) || 1;
            valor += delta;

            if (valor < 1) valor = 1;

            input.value = valor;
            form.submit();
        }
    </script>

</main>
 