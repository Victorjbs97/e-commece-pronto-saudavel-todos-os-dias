<?php 

    require_once '../../app/core/Session.php';
    require_once '../../app/Controllers/Admin/ProductAdminController.php';
    require_once '../../app/core/DataBaseConecta.php';

   //verificaAdmin();
    $erro = null;

    $mensagem = '';
    $sucesso = false;

    if($_SERVER["REQUEST_METHOD"]=="POST"){
            $nome = trim($_POST['nome']??'');
            $descricao = trim($_POST['descricao']??'');
            $valor = filter_input(INPUT_POST, 'valor',FILTER_VALIDATE_FLOAT);
            $estoque = filter_input(INPUT_POST, 'estoque',FILTER_VALIDATE_INT);
            $imagem_url = trim($_POST['imagem_url']??'');
    
            $resultado = cadastrarProduto($conexao,$nome,$descricao,$valor,$estoque,$imagem_url);

            if($resultado ===true){
                $mensagem= "Produto **{$nome}** cadatrado com sucesso!";
                $sucesso =  true;
            }


    }


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php if ($mensagem): ?>
        <p style="color: <?= $sucesso ? 'green' : 'red'; ?>; font-weight: bold;"><?= $mensagem; ?></p>
    <?php endif; ?>
    <form action="" method="post">
        <div>
            <label for="nome">Nome:</label>
            <input type="text" name="nome" id="nome" required>
        </div>
        <div>
            <label for="descricao">Descrição</label>
            <textarea name="descricao" id="descricao" rows="5"></textarea>
        </div>
        <div>
            <label for="valor">Preço:</label>
            <input type="number" name="valor" id="valor" required min="0" step="0.01">
        </div>
        <div>
            <label for="estoque">Quantidade:</label>
            <input type="number" name="estoque" id="estoque" required min="0">
        </div>

        <div>
            <label for="imagem_url">Imagem caminho:</label>
            <input type="text" name="imagem_url" id="imagem_url">
        </div>


        <button type="submit">Salvar</button>
    </form>
</body>
</html>