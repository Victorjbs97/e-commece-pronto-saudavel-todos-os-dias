<?php
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
            echo "<p style='color:orange;'>Aviso: IDs na sessão, mas nenhum produto ativo encontrado.</p>";
        }

    } 
} catch (PDOException $e) {
    echo "<p style='color:red;'>Erro SQL: " . $e->getMessage() . "</p>";
    die();
}
?>

<main>

    <section class="sessaoCarrinho">

        <?php if ($carrinho_vazio): ?>

            <div id="vazio">
                <p>Você não possui compras no carrinho</p>

                <svg xmlns="http://www.w3.org/2000/svg" width="250" height="250" viewBox="0 0 24 24" fill="none" stroke="#979797ff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
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

            <table id="tab">

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
                    <?php foreach ($produtos_do_banco as $produto): ?>
                        <?php
                        $quantidade = $_SESSION['carrinho'][$produto['id']];
                        $preco = (float)($produto['valor'] ?? 0);
                        $subtotal = $preco * $quantidade;
                        $total_carrinho += $subtotal;
                        $imagem_url = (!empty($produto['imagem_url']))
                            ? $baseUrl . $produto['imagem_url']
                            : $baseUrl . '/public/images/sem-imagem.png';
                        ?>
                        <tr>

                            <td id="lixeira">
                                <form action="<?= $baseUrl ?>/public/index.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="gerenciar_carrinho">
                                    <input type="hidden" name="acao_carrinho" value="deletar">
                                    <input type="hidden" name="id_produto" value="<?= $produto['id'] ?>">
                                    <button type="submit" class="botao-deletar"></button>
                                </form>
                            </td>

                            <td id="imagem">
                                <img src="<?= htmlspecialchars($imagem_url) ?>" alt="<?= htmlspecialchars($produto['nome']) ?>" width="50">
                            </td>

                            <td id="produto"><?= htmlspecialchars($produto['nome']) ?></td>

                            <td id="preco">R$ <?= number_format($preco, 2, ',', '.') ?></td>

                            <td id="contadorProduto">
                                <form action="<?= $baseUrl ?>/public/index.php" method="POST" class="form-carrinho">
                                    <input type="hidden" name="action" value="gerenciar_carrinho">
                                    <input type="hidden" name="acao_carrinho" value="atualizar">
                                    <input type="hidden" name="id_produto" value="<?= $produto['id'] ?>">

                                    <div class="quantidade-container">
                                        <button type="button" class="btn-quantidade" onclick="alterarQuantidade(this, -1)">−</button>

                                        <input
                                            id="qtd"
                                            type="number"
                                            name="quantidade"
                                            value="<?= $quantidade ?>"
                                            min="1"
                                            class="input-quantidade"
                                            readonly>

                                        <button type="button" class="btn-quantidade" onclick="alterarQuantidade(this, 1)">+</button>
                                    </div>
                                </form>
                            </td>

                            <td id="valorQtd">R$ <?= number_format($subtotal, 2, ',', '.') ?></td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>

            <div id="total-carrinho">

                <form id="butaos" action="/caminho-para-gateway.php" method="POST">

                    <div id="botao-pagamento"><a href="http://localhost/e-commece-pronto-saudavel-todos-os-dias/public/index.php?page=produtos">+</a></div>

                    <button type="submit" class="botao-pagamento">Finalizar Compra</button>

                </form>

                <h3>Total: R$ <?= number_format($total_carrinho, 2, ',', '.') ?></h3>

            </div>

        <?php endif; ?>

    </section>

    <script>
        // Atualiza automaticamente ao clicar em + ou −
        function alterarQuantidade(botao, delta) {
            const form = botao.closest('form');
            const input = form.querySelector('.input-quantidade');
            let valor = parseInt(input.value) || 0;
            valor += delta;
            if (valor < 1) valor = 1;
            input.value = valor;

            // envia o formulário automaticamente
            form.submit();
        }
    </script>

</main>