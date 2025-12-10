<?php
require_once __DIR__ . '/../../../app/Controllers/AtualizarClienteController.php';

// views/pages/auth/salvar_edicao.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../app/core/DataBaseConecta.php';
require_once __DIR__ . '/../../../app/models/UserModel.php';

// Proteção
if (!isset($_SESSION['usuario']['id'])) {
    header("Location: /e-commece-pronto-saudavel-todos-os-dias/public/index.php?page=login");
    exit;
}

// Apenas POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /e-commece-pronto-saudavel-todos-os-dias/public/index.php?page=editar_perfil");
    exit;
}

$id       = (int) $_SESSION['usuario']['id'];
$nome     = trim($_POST['nome']     ?? '');
$email    = trim($_POST['email']    ?? '');
$telefone = trim($_POST['telefone'] ?? '');

if ($nome === '' || $email === '' || $telefone === '') {
    header("Location: /e-commece-pronto-saudavel-todos-os-dias/public/index.php?page=editar_perfil&erro=campos");
    exit;
}

// Conexão PDO única do projeto
$db = $conn ?? $conexao ?? null;

if (!$db instanceof PDO) {
    error_log("salvar_edicao.php: conexão PDO inválida");
    header("Location: /e-commece-pronto-saudavel-todos-os-dias/public/index.php?page=editar_perfil&erro=db");
    exit;
}

$userModel = new UserModel($db);

// Atualiza no banco
$sucesso = $userModel->updateBasicData($id, $nome, $email, $telefone);

if ($sucesso) {

    // Atualiza sessão para refletir imediatamente na tela
    $_SESSION['user_nome']  = $nome;
    $_SESSION['user_email'] = $email;

    header("Location: /e-commece-pronto-saudavel-todos-os-dias/public/index.php?page=editar_perfil&salvo=1");
    exit;
}

// Falha inesperada
header("Location: /e-commece-pronto-saudavel-todos-os-dias/public/index.php?page=editar_perfil&erro=update");
exit;
