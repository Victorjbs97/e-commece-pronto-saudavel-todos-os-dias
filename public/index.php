<?php 
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

define('VIEWS_PATH', __DIR__ . '/../views');
$baseUrl = 'http://localhost/e-commece-pronto-saudavel-todos-os-dias';


$action = $_POST['action'] ?? null;

if ($action === 'gerenciar_carrinho') {
    
    require_once VIEWS_PATH . '/partials/gerenciar-carrinho.php';
    exit; 

} else {
    // Decidir qual página carregar
    $page = isset($_GET['page']) ? $_GET['page'] : 'home';

    $allowedPages = [
        'home' => VIEWS_PATH . '/pages/home.php',
        'produtos' => VIEWS_PATH . '/pages/produtos.php',
        'carrinho_de_compras' => VIEWS_PATH . '/pages/carrinho_de_compras.php'
        // Adicione as outras páginas aqui
    
    ];

   
    // Verificamos se a página pedida está na nossa lista.
    if (array_key_exists($page, $allowedPages)) {
        // Se estiver, definimos $viewFile como o caminho do arquivo.
        $viewFile = $allowedPages[$page];
    } else {
        
        $viewFile = $allowedPages['home']; 
    }

    // Carregar o "Molde" Principal
    require_once VIEWS_PATH . '/layouts/main.php';
}
?>