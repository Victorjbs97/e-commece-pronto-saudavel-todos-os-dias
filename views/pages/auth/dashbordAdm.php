<?php 

    require_once '../../../app/core/Session.php';
    require_once '../../../app/models/User.php';

    verificaAdmin();

    if($_SERVER["REQUEST_METHOD"]=="POST"){
        $nome = trim($_POST['nome']??'');
        $descricao = trim($_POST['descricao']??'');
        $valor = filter_input(INPUT_POST, 'valor',FILTER_VALIDATE_FLOAT);
        $estoque = filter_input(INPUT_POST, 'estoqur',FILTER_VALIDATE_INT);
        $imagem_url = trim($_POST['imagem_url']??'');

        cadastrarProduto($conexao,$nome,$descricao,$valor,$estoque,$imagem_url);
    }

?>