<?php

require_once "../partials/exibir-produtos.php";

require_once "../partials/todos-produtos.php"
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Nossos Produtos - Pronto Saudável</title>
    <link rel="stylesheet" href="../../public/css/produto_card.css"> 
    <link rel="stylesheet" href="../partials/css/header.css">
    <link rel="stylesheet" href="css/marmitas.css">
    <link rel="stylesheet" href="../../views/pages/css/marmitas.css">

</head>
<body>

    <?php
        require_once "../partials/header.php"
    ?>

    <h1 class="titulo">Todos os Produtos</h1>

    <main class="product-grid">
        <?php

        exibirProdutos($conexao, $template_card);

        ?>
    </main>

    <?php
        require_once "../partials/footer.php"
    ?>

</body>
</html>