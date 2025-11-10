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

function atualizarProduto($conexao, $id, $nome, $descricao, $valor, int $estoque, $imagem) {
    if (empty($id)) {
        return "ID do produto é obrigatório para atualização.";
    }
    if (empty($nome) || empty($valor)) {
        return "Nome e Preço são obrigatórios.";
    }
    if (!is_numeric($valor) || $valor <= 0) {
        return "Preço deve ser um valor numérico positivo.";
    }
    if (!is_numeric($estoque) || $estoque < 0) {
        return "Estoque deve ser um valor numérico não negativo.";
    }

    try {
        $sql = "UPDATE produtos 
                SET nome = :nome, descricao = :descricao, valor = :valor, 
                    estoque = :estoque, imagem_url = :imagem_url
                WHERE id = :id";
        $consulta = $conexao->prepare($sql);
        $consulta->bindValue(":id", (int)$id, PDO::PARAM_INT);
        $consulta->bindValue(":nome", $nome);
        $consulta->bindValue(":descricao", $descricao);
        $consulta->bindValue(":valor", $valor);
        $consulta->bindValue(":estoque", (int)$estoque, PDO::PARAM_INT);
        $consulta->bindValue(":imagem_url", $imagem);

        $consulta->execute();

        return "Produto atualizado com sucesso!";
    } catch (PDOException $e) {
        return "Erro ao atualizar produto.";
    }
}


function excluirProduto($conexao, $id) {
    if (empty($id)) {
        return "ID do produto é obrigatório para exclusão.";
    }

    try {
        $sql = "DELETE FROM produtos WHERE id = :id";
        $consulta = $conexao->prepare($sql);
        $consulta->bindValue(":id", (int)$id, PDO::PARAM_INT);
        $consulta->execute();

        if ($consulta->rowCount() > 0) {
            return "Produto excluído com sucesso!";
        } else {
            return "Produto não encontrado.";
        }
    } catch (PDOException $e) {
        return "Erro ao excluir produto.";
    }
}

function buscarProdutoPorId($conexao, $id) {
    if (empty($id) || !is_numeric($id)) {
        return null; // ID inválido
    }

    try {
        $sql = "SELECT * FROM produtos WHERE id = :id";
        $stmt = $conexao->prepare($sql);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $stmt->execute();

        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        return $produto ?: null; // retorna null se não encontrar
    } catch (PDOException $e) {
        return null;
    }
}

?>
