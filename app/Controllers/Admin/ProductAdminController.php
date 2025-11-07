<?php
function cadastrarProduto($conexao,$nome,$descricao,$valor,int $estoque,$imagem){
    if(empty($nome) || empty($valor)){
        return "Nome e Preço são obrigatórios.";
    }
    if(!is_numeric($valor)||$valor<=0){
        return "Preço deve ser um valor numérico positivo.";
    }
    if (!is_numeric($estoque)||$estoque<=0){
        return "Estoque deve ser um valor numérico não negativo.";
    }

    try{
        $sql = "INSERT INTO produtos(nome,descricao,valor,estoque,imagem_url) VALUES
                (:nome, :descricao, :valor, :estoque, :imagem_url)";
        $consulta = $conexao->prepare($sql);
        $consulta->bindValue(":nome",$nome);
        $consulta->bindValue(":descricao",$descricao);
        $consulta->bindValue(":valor",$valor);
        $consulta->bindValue(":estoque",(int)$estoque, PDO::PARAM_INT);
        $consulta->bindValue(":imagem_url",$imagem);

        $consulta->execute();
    }catch(PDOException $e){
        return "Erro ao cadastra produto.";
    }
}

function listarProdutos($conexao) {
    $sql = "SELECT * FROM produtos ORDER BY nome";

    /* Rxecutamos o comando e guardamos o resultado da consulta  */
    $consulta = $conexao -> query($sql);

    /* Retornamos o resultado em forma de array associativo */
    return $consulta->fetchAll();
}
?>
