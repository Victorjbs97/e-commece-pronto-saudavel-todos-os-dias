<?php 

    require_once '../../../app/core/Session.php';
    require_once '../../app/Controllers/Admin/ProductAdminController.php';

    verificaAdmin();
    $erro = null;

    if($_SERVER["REQUEST_METHOD"]=="POST"){
        try{
            $nome = trim($_POST['nome']??'');
            $descricao = trim($_POST['descricao']??'');
            $valor = filter_input(INPUT_POST, 'valor',FILTER_VALIDATE_FLOAT);
            $estoque = filter_input(INPUT_POST, 'estoque',FILTER_VALIDATE_INT);
            $imagem_url = trim($_POST['imagem_url']??'');
    
            cadastrarProduto($conexao,$nome,$descricao,$valor,$estoque,$imagem_url);
    
        }catch(Throwable $e){
            $erro = "Erro ao registar produto!";
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
    <form action="" method="post"></form>
    <div>
        <label class="form-label" for="nome">Nome:</label>
        <input required value="<?= $_POST['nome'] ?? '' ?> " class="form-control" type="text" id="nome" name="nome">
    </div>
 
</body>
</html>