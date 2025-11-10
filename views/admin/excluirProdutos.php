<?php
require_once '../../app/core/Session.php'; 
require_once '../../app/core/DataBaseConecta.php'; 
require_once '../../app/Controllers/Admin/ProductAdminController.php'; 

$id = $_GET['id'] ?? null;

if ($id) {
    $mensagem = excluirProduto($conexao, $id);
} else {
    $mensagem = "ID do produto não informado.";
}

// Após excluir, redireciona
header("Location: listarProdutos.php");
exit();
?>
