<?php
// todos-produtos.php

// Caminho base absoluto para evitar erros de include
$baseDir = __DIR__;

// Inclui o arquivo de conexão (ajuste correto de nível)
require_once $baseDir . '/../../app/core/DataBaseConecta.php';

// Inclui a função de exibição de produtos
require_once $baseDir . '/exibir-produtos.php';

// Caminho do template do card
$template_card = $baseDir . '/produto-card.php';

// Verifica se a conexão foi estabelecida
if (!isset($conexao)) {
    die("<p style='color:red;'>Erro: conexão com o banco de dados não foi inicializada.</p>");
}
?>

