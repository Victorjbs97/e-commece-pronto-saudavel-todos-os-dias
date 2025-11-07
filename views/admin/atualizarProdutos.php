<?php
require_once '../../app/core/Session.php'; 
require_once '../../app/core/DataBaseConecta.php'; 
require_once '../../app/Controllers/Admin/ProductAdminController.php'; 

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: listarProdutos.php");
    exit();
}

$produto = buscarProdutoPorId($conexao, $id);

if (!$produto) {
    echo "Produto não encontrado!";
    exit();
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $valor = filter_input(INPUT_POST, 'valor', FILTER_VALIDATE_FLOAT);
    $estoque = filter_input(INPUT_POST, 'estoque', FILTER_VALIDATE_INT);
    $imagem_url = trim($_POST['imagem_url'] ?? '');

    $msg = atualizarProduto($conexao, $id, $nome, $descricao, $valor, $estoque, $imagem_url);

    // Após atualizar, redireciona
    header("Location: listarProdutos.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Produto</title>
</head>
<body>
    <h1>✏️ Editar Produto</h1>

    <form action="" method="post">
        <div>
            <label for="nome">Nome:</label>
            <input type="text" name="nome" id="nome" required value="<?= htmlspecialchars($produto['nome']); ?>">
        </div>

        <div>
            <label for="descricao">Descrição:</label>
            <textarea name="descricao" id="descricao" rows="5"><?= htmlspecialchars($produto['descricao']); ?></textarea>
        </div>

        <div>
            <label for="valor">Preço:</label>
            <input type="number" name="valor" id="valor" required min="0" step="0.01" value="<?= htmlspecialchars($produto['valor']); ?>">
        </div>

        <div>
            <label for="estoque">Estoque:</label>
            <input type="number" name="estoque" id="estoque" required min="0" value="<?= htmlspecialchars($produto['estoque']); ?>">
        </div>

        <div>
            <label for="imagem_url">Imagem (URL):</label>
            <input type="text" name="imagem_url" id="imagem_url" value="<?= htmlspecialchars($produto['imagem_url']); ?>">
        </div>

        <button type="submit">Salvar Alterações</button>
        <a href="listarProdutos.php">Cancelar</a>
    </form>
</body>
</html>
