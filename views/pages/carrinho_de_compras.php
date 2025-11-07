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
            echo "<p style='color:orange;'>Aviso: A sessão tem IDs, mas nenhum produto foi encontrado no banco com esses IDs (verifique se estão 'ativos').</p>";
        }

    } else {
         echo "<p>Aviso: O PHP não encontrou dados na sessão 'carrinho'.</p>";
    }

} catch (PDOException $e) {
    echo "<p style='color:red; font-size: 1.2rem; border: 2px solid red; padding: 10px;'>";
    echo "ERRO FATAL NA QUERY SQL: " . $e->getMessage();
    echo "</p>";
    die();
}
?>


<main>
    <section class="sessao_carrinho_de_compras">
        <h1>Carrinho de Compras</h1>

        <?php if ($carrinho_vazio): ?>
            <p>Seu carrinho de compras está vazio.</p>
        
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Nome</th>
                        <th>Preço</th>
                        <th>Quantidade</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>

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
                            <td>
                                <form action="<?= $baseUrl ?>/public/index.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="gerenciar_carrinho">
                                    <input type="hidden" name="acao_carrinho" value="deletar">
                                    <input type="hidden" name="id_produto" value="<?= $produto['id'] ?>">
                                    <button type="submit" class="botao-deletar">X</button>
                                </form>
                                <img src="<?= htmlspecialchars($imagem_url) ?>" alt="<?= htmlspecialchars($produto['nome']) ?>" width="50">
                            </td>
                            <td><?= htmlspecialchars($produto['nome']) ?></td>
                            <td>R$ <?= number_format($preco, 2, ',', '.') ?></td>
                            <td>
                                <form action="<?= $baseUrl ?>/public/index.php" method="POST">
                                    <input type="hidden" name="action" value="gerenciar_carrinho">
                                    <input type="hidden" name="acao_carrinho" value="atualizar">
                                    <input type="hidden" name="id_produto" value="<?= $produto['id'] ?>">
                                    <input type="number" name="quantidade" value="<?= $quantidade ?>" min="0" class="input-quantidade">
                                    <button type="submit">Atualizar</button>
                                </form>
                            </td>
                            <td>R$ <?= number_format($subtotal, 2, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="total-carrinho">
                <h3>Total: R$ <?= number_format($total_carrinho, 2, ',', '.') ?></h3>
                <form action="/caminho-para-gateway.php" method="POST">
                    <button type="submit" class="botao-pagamento">Pagamento</button>
                </form>
            </div>

        <?php endif; ?>
    </section>
</main>