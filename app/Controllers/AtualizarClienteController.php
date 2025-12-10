<?php

//app\Controllers\AtualizarClienteController.php

// NÃO chamar session_start() se o router já inicia
if (!isset($_SESSION['user_id'])) {
    die("Acesso não autorizado");
}

$userId = $_SESSION['user_id'];
$userModel = new UserModel($conn);

// Sanitização
function clean($v) {
    return htmlspecialchars(trim($v), ENT_QUOTES, 'UTF-8');
}

/* ================= ATUALIZAR DADOS ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nome'])) {

    $nome     = clean($_POST['nome']);
    $email    = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $telefone = preg_replace('/\D/', '', $_POST['telefone']);

    // Validações
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Email inválido.");
    }

    if (strlen($telefone) < 10) {
        die("Telefone inválido.");
    }

    $userModel->updateBasicData($userId, $nome, $email, $telefone);

    // ✅ ATUALIZA SESSÃO PADRONIZADA
    $_SESSION['user_nome']     = $nome;
    $_SESSION['user_email']    = $email;
    $_SESSION['user_telefone'] = $telefone;

    header("Location: " . BASE_URL . "/public/index.php?page=editar_perfil");
    exit;
}

/* ================= ALTERAR SENHA ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['senha_atual'])) {

    $senhaAtual   = $_POST['senha_atual'];
    $novaSenha    = $_POST['nova_senha'];
    $confirmSenha = $_POST['confirmar_senha'];

    if (!$userModel->verifyPassword($userId, $senhaAtual)) {
        die("Senha atual incorreta.");
    }

    if (strlen($novaSenha) < 6) {
        die("A senha deve ter no mínimo 6 caracteres.");
    }

    if ($novaSenha !== $confirmSenha) {
        die("As senhas não coincidem.");
    }

    $senhaHash = password_hash($novaSenha, PASSWORD_BCRYPT);

    $userModel->updatePassword($userId, $senhaHash);

    header("Location: " . BASE_URL . "/public/index.php?page=editar_perfil");
    exit;
}
