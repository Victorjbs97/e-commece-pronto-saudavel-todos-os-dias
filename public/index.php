<?php
// public/index.php

session_start();

// 1. Carregamento de Configurações e Banco
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/core/DataBaseConecta.php';
require_once __DIR__ . '/../app/core/Session.php';

// 2. Carregamento de Models e Controllers
require_once __DIR__ . '/../app/models/User.php';
// require_once __DIR__ . '/../app/models/UserModel.php'; // ATENÇÃO: Só descomente se esse arquivo existir!

// Controller do Admin (ESSENCIAL PARA O SEU TRABALHO)
require_once __DIR__ . '/../app/Controllers/Admin/ProductAdminController.php';

// Controllers do Colega (Só descomente se você tiver esses arquivos, senão vai travar o site)
if (file_exists(__DIR__ . '/../app/Controllers/EnderecoController.php')) {
    require_once __DIR__ . '/../app/Controllers/EnderecoController.php';
}
if (file_exists(__DIR__ . '/../app/Controllers/PedidoController.php')) {
    require_once __DIR__ . '/../app/Controllers/PedidoController.php';
}

ob_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Caminho padrão das views
define('VIEWS_PATH', __DIR__ . '/../views');

// Garante que BASE_URL existe (caso não venha do config)
if (!defined('BASE_URL')) {
    // Fallback manual se o config falhar
    define('BASE_URL', 'http://localhost/e-commece-pronto-saudavel-todos-os-dias');
}

$baseUrl = BASE_URL;

// Dados do usuário logado
$usuario_logado = isset($_SESSION['user_nome']);
$nome_usuario = $usuario_logado ? $_SESSION["user_nome"] : '';
// Ajuste seguro para verificar admin
$ehAdmin = $usuario_logado && isset($_SESSION["user_tipo"]) && $_SESSION["user_tipo"] === 'admin';

// Tratamento para ações via POST (Carrinho)
$action = $_POST['action'] ?? null;
if ($action === 'gerenciar_carrinho') {
    require_once VIEWS_PATH . '/partials/gerenciar-carrinho.php';
    exit;
}

// Página sendo acessada
$page = $_GET['page'] ?? 'home';

// Lista de páginas
$allowedPages = [
    // --- PÁGINAS COMUNS ---
    'home'                => VIEWS_PATH . '/pages/home.php',
    'marmitas'            => VIEWS_PATH . '/pages/produtos-marmitas.php',
    'caldo'               => VIEWS_PATH . '/pages/produtos-caldo.php',
    'fitness'             => VIEWS_PATH . '/pages/produtos-fitness.php',
    'lowcarb'             => VIEWS_PATH . '/pages/produtos-lowcarb.php',
    'outros'              => VIEWS_PATH . '/pages/produtos-outros.php',
    'sobremesa'           => VIEWS_PATH . '/pages/produtos-sobremesa.php',
    'sopa'                => VIEWS_PATH . '/pages/produtos-sopa.php',
    'suco'                => VIEWS_PATH . '/pages/produtos-suco.php',
    'tempero'             => VIEWS_PATH . '/pages/produtos-tempero.php',
    'torta'               => VIEWS_PATH . '/pages/produtos-torta.php',
    'vegana'              => VIEWS_PATH . '/pages/produtos-vegana.php',
    'produtos_buscados'   => VIEWS_PATH . '/pages/produtos_buscados.php',

    'carrinho_de_compras' => VIEWS_PATH . '/pages/carrinho_de_compras.php',
    'productDetails'      => VIEWS_PATH . '/pages/productDetails.php',
    'personalChefe'       => VIEWS_PATH . '/pages/personal_chefe.php',
    'about'               => VIEWS_PATH . '/pages/about.php',
    
    // --- NOVAS PÁGINAS DO COLEGA ---
    'pedidos'             => VIEWS_PATH . '/pages/pedidos.php',
    'enderecos'           => VIEWS_PATH . '/pages/listarendereco.php',
    'novo_endereco'       => VIEWS_PATH . '/pages/novoendereco.php',
    'editar_endereco'     => VIEWS_PATH . '/pages/editarendereco.php',
    'editar_perfil'       => VIEWS_PATH . '/pages/auth/editar_perfil.php',

    // --- AUTENTICAÇÃO ---
    'dashboard_cliente'   => VIEWS_PATH . '/pages/auth/logado.php',
    'login'               => VIEWS_PATH . '/pages/auth/login.php',
    'registrar'           => VIEWS_PATH . '/pages/auth/register.php',
    'logout'              => VIEWS_PATH . '/pages/auth/logout.php',
    'recuperar_senha'     => VIEWS_PATH . '/pages/auth/recuperar_senha.php',
    'nova_senha'          => VIEWS_PATH . '/pages/auth/nova_senha.php',

    // --- ADMIN ---
    'painel_adm'          => VIEWS_PATH . '/admin/administracaoPainel.php',
    'listar_produtos'     => VIEWS_PATH . '/admin/listarProdutos.php',
    'inserir_produto'     => VIEWS_PATH . '/admin/inserir.php',
    'atualizar_produto'   => VIEWS_PATH . '/admin/atualizarProdutos.php',
    'excluir_produto'     => VIEWS_PATH . '/admin/excluirProdutos.php',
];

// Configuração de Segurança
$paginasProtegidas = ['dashboard_cliente', 'editar_perfil', 'enderecos', 'novo_endereco', 'editar_endereco', 'pedidos', 'logout'];
$paginasGuest = ['login', 'registrar', 'recuperar_senha', 'nova_senha'];
$paginasAdmin = ['painel_adm', 'listar_produtos', 'inserir_produto', 'atualizar_produto', 'excluir_produto'];

// =======================================================================
//  PROTEÇÃO DE ROTAS
// =======================================================================

// 1. Usuário precisa estar logado
if (in_array($page, $paginasProtegidas) && !$usuario_logado) {
    header("Location: " . BASE_URL . "/public/index.php?page=login");
    exit;
}

// 2. Se já está logado, não pode ir para login/registrar/recuperar
if (in_array($page, $paginasGuest) && $usuario_logado) {
    header("Location: " . BASE_URL . "/public/index.php?page=dashboard_cliente");
    exit;
}

// 3. Área do admin
if (in_array($page, $paginasAdmin) && !$ehAdmin) {
    header("Location: " . ($usuario_logado
        ? BASE_URL . "/public/index.php?page=dashboard_cliente"
        : BASE_URL . "/public/index.php?page=login"));
    exit;
}

// =======================================================================
//  AÇÕES DE CONTROLLERS (DO COLEGA)
// =======================================================================
// Coloquei verify de file_exists para não quebrar se você não tiver os arquivos
if (class_exists('EnderecoController')) {
    if ($page === 'salvar_endereco') {
        $controller = new EnderecoController();
        $controller->criarEndereco();
        exit;
    }
    if ($page === 'update_endereco') {
        $controller = new EnderecoController();
        $controller->atualizarEndereco();
        exit;
    }
    if ($page === 'deletar_endereco') {
        $controller = new EnderecoController();
        $controller->excluirEndereco();
        exit;
    }
}

if ($page === 'salvar_edicao' || $page === 'salvar_senha') {
    $caminhoController = __DIR__ . '/../app/Controllers/AtualizarClienteController.php';
    if (file_exists($caminhoController)) {
        require_once $caminhoController;
        // O colega provavelmente instancia a classe dentro desse arquivo ou roda direto
    }
    exit;
}

// =======================================================================
//  CARREGAR A VIEW FINAL
// =======================================================================

$viewFile = $allowedPages[$page] ?? $allowedPages['home'];

// Layout principal
require_once VIEWS_PATH . '/layouts/main.php';