<?php
require_once __DIR__ . '/../../../app/Controllers/AtualizarClienteController.php';

// views/pages/auth/salvar_senha.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../app/core/DataBaseConecta.php';
require_once __DIR__ . '/../../../app/models/UserModel.php';


if (!isset($_SESSION['usuario']['id'])) {
    header("Location: /e-commece-pronto-saudavel-todos-os-dias/public/index.php?page=login");
    exit;
}

$id = $_SESSION['usuario']['id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /e-commece-pronto-saudavel-todos-os-dias/public/index.php?page=editar_perfil");
    exit;
}

$senha_atual     = trim($_POST['senha_atual'] ?? '');
$nova_senha      = trim($_POST['nova_senha'] ?? '');
$confirmar_senha = trim($_POST['confirmar_senha'] ?? '');

if ($senha_atual === '' || $nova_senha === '' || $confirmar_senha === '') {
    header("Location: /e-commece-pronto-saudavel-todos-os-dias/public/index.php?page=editar_perfil&erro=campos");
    exit;
}

if ($nova_senha !== $confirmar_senha) {
    header("Location: /e-commece-pronto-saudavel-todos-os-dias/public/index.php?page=editar_perfil&erro=senhas");
    exit;
}

$db = $conn ?? $conexao ?? null;

if (!$db instanceof PDO) {
    die("Falha conexão DB");
}

$userModel = new UserModel($db);

if (!$userModel->verifyPassword($id, $senha_atual)) {
    header("Location: /e-commece-pronto-saudavel-todos-os-dias/public/index.php?page=editar_perfil&erro=senha_incorreta");
    exit;
}

$senhaHash = password_hash($nova_senha, PASSWORD_DEFAULT);

if ($userModel->updatePassword($id, $senhaHash)) {
    header("Location: /e-commece-pronto-saudavel-todos-os-dias/public/index.php?page=editar_perfil&senha_alterada=1");
    exit;
}

header("Location: /e-commece-pronto-saudavel-todos-os-dias/public/index.php?page=editar_perfil&erro=update");
exit;
