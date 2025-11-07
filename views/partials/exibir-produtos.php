<?php


/**
 * Busca produtos ativos no banco de dados e exibe os cards HTML.
 * @param PDO $conexao Objeto de conexão PDO.
 * @param string $templatePath Caminho para o arquivo do template do card.
 * @param string $baseUrl A URL base do site (vinda do main.php).
 */
function exibirProdutos($conexao, $templatePath, $baseUrl)
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
            
            // Define o preço
            $produto['preco'] = $produto['valor'] ?? 0;

            // --- LÓGICA DE IMAGEM CORRIGIDA E SIMPLIFICADA ---
            
            // 1. Verifica se a coluna 'imagem_url' (o nome correto) não está vazia
            if (!empty($produto['imagem_url'])) {
                
                // 2. O caminho já está correto no DB (ex: /public/images/products/marmita.jpg)
                //    Nós apenas adicionamos a $baseUrl no início.
                $produto['imagem_url'] = $baseUrl . $produto['imagem_url'];
                
            } else {
                // 3. Caminho padrão se não houver imagem
                $produto['imagem_url'] = $baseUrl . '/public/images/sem-imagem.png';
            }
            
            // 4. Inclui o card. O card vai usar a variável $produto['imagem_url']
            //    que agora tem o caminho completo (http://.../public/...)
            include $templatePath;
        }
    } catch (PDOException $e) {
        echo "<p style='color:red;'>Erro ao buscar produtos: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>