<?php


global $baseUrl; 

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Acesso negado.");
}
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

$id_produto = $_POST['id_produto'] ?? null;
$quantidade = (int)($_POST['quantidade'] ?? 0);
$acao_carrinho = $_POST['acao_carrinho'] ?? 'adicionar'; 

if (!$id_produto || $quantidade < 0) {
    header("Location: $baseUrl/public/index.php?page=carrinho_de_compras&erro=dados");
    exit;
}

// adicionar o produto
switch ($acao_carrinho) {
    case 'adicionar':
        if (isset($_SESSION['carrinho'][$id_produto])) {
            $_SESSION['carrinho'][$id_produto] += $quantidade;
        } else {
            $_SESSION['carrinho'][$id_produto] = $quantidade;
        }
        break;
    
    //atualizar e deletar
    case 'atualizar':
        if ($quantidade <= 0) {
            unset($_SESSION['carrinho'][$id_produto]);
        } else {
            $_SESSION['carrinho'][$id_produto] = $quantidade;
        }
        break;
    case 'deletar':
        unset($_SESSION['carrinho'][$id_produto]);
        break;
}


// Força o PHP a "Salvar o armário" AGORA, antes de redirecionar.
session_write_close();

header("Location: $baseUrl/public/index.php?page=carrinho_de_compras");
exit;
?>