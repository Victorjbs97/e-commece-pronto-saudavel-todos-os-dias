<?php

/**
 * Busca produtos ativos no banco de dados e exibe os cards HTML.
 * @param PDO $conexao Objeto de conexão PDO.
 * @param string $templatePath Caminho para o arquivo do template do card.
 */
function exibirProdutos($conexao, $templatePath)
{
    try {
        $sql = "SELECT * FROM produtos WHERE ativo = 1 ORDER BY nome ASC";
        $stmt = $conexao->prepare($sql);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            echo "<p>Nenhum produto encontrado.</p>";
            return;
        }

        while ($produto = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $produto['preco'] = $produto['valor'] ?? 0;

            if (!empty($produto['imagem'])) {
                // Corrige o caminho para formato web
                $imgPath = str_replace('\\', '/', $produto['imagem']);

                // Remove caminhos locais do Windows, se existirem
                $imgPath = str_replace(['C:/xampp/htdocs/', 'c:/xampp/htdocs/'], '', $imgPath);

                // Se não começar com "/", adiciona
                if ($imgPath[0] !== '/') {
                    $imgPath = '/' . $imgPath;
                }

                // Garante que o caminho contenha o nome do projeto
                if (strpos($imgPath, '/e-commece-pronto-saudavel-todos-os-dias/') === false) {
                    $imgPath = '/e-commece-pronto-saudavel-todos-os-dias/' . ltrim($imgPath, '/');
                }

                $produto['imagem'] = $imgPath;
            } else {
                // Caminho padrão se não houver imagem
                $produto['imagem'] = '/e-commece-pronto-saudavel-todos-os-dias/public/images/sem-imagem.png';
            }


            include $templatePath;
        }
    } catch (PDOException $e) {
        echo "<p style='color:red;'>Erro ao buscar produtos: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
