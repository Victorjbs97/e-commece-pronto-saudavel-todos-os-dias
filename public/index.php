<?php

define('VIEWS_PATH', __DIR__ . '/../views');


// Decidir qual página carregar:
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// 3. Mapeamento Seguro (Whitelist):
$allowedPages = [
    'home' => VIEWS_PATH . '/pages/home.php',
    'produtos' => VIEWS_PATH . '/pages/produtos.php',
    // Adicione as outras páginas aqui
   
];

// Definir o arquivo da View:
// Verificamos se a página pedida está na nossa lista.
if (array_key_exists($page, $allowedPages)) {
    // Se estiver, definimos $viewFile como o caminho do arquivo.
    $viewFile = $allowedPages[$page];
} else {
    
    $viewFile = $allowedPages['home']; 
}

// Carregar o "Molde" Principal
require_once VIEWS_PATH . '/layouts/main.php';

?>