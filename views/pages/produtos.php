<?php
// Inclui o arquivo que define a função
require_once __DIR__ . "/../partials/exibir-produtos.php";
// Inclui o arquivo que conecta ao DB e define o template
require_once __DIR__ . "/../partials/todos-produtos.php"; 

?>

<h2 class="titulo">Todos os Produtos</h2>

<main class="product-grid">

    <?php exibirProdutos($conexao, $template_card, $baseUrl); ?>
    
</main>