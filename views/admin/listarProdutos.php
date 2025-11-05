<?php

require_once '../../app/core/Session.php'; 
require_once '../../app/core/DataBaseConecta.php'; 
require_once '../../app/Controllers/Admin/ProductAdminController.php'; 

$produtos = listarProdutos($conexao);


?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Produtos - Admin</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>📋 Produtos</h1>
    
    <a href="inserir.php">➕ Novo Produto</a> 
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Preço</th>
                    <th>Estoque</th>
                    <th>Imagem (URL)</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $produto): ?>
                <tr>
                    <td><?= htmlspecialchars($produto['id'] ?? 'N/A'); ?></td>
                    <td><?= htmlspecialchars($produto['nome'] ?? 'N/A'); ?></td>
                    <td>R$ <?= number_format($produto['valor'] ?? 0, 2, ',', '.'); ?></td>
                    <td><?= htmlspecialchars($produto['estoque'] ?? 0); ?></td>
                    <td>
                        <?php 
                        $url = htmlspecialchars($produto['imagem_url'] ?? '');
                        // Se a URL existir, exibe um link, senão 'Nenhuma'
                        echo $url ? "<a href='{$url}' target='_blank'>Ver Imagem</a>" : "Nenhuma";
                        ?>
                    </td>
                    <td>
                        <a href="editar.php?id=<?= $produto['id']; ?>">Editar</a> | 
                        <a href="excluir.php?id=<?= $produto['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir este produto?');">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    
</body>
</html>