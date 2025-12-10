<?php
require_once __DIR__ . '/../../../app/core/DataBaseConecta.php';
require_once __DIR__ . '/../../../app/models/UserModel.php';

/**
 * Recupera o ID do usuário da sessão
 * (compatível com todos os formatos usados no projeto)
 */
$userId = $_SESSION['usuario']['id'] ?? $_SESSION['user_id'] ?? null;

// Proteção
if (!$userId) {
    header("Location: " . BASE_URL . "/public/index.php?page=login");
    exit;
}

// Usa conexão aberta pelo sistema
$db = $conn;

$userModel = new UserModel($db);

// Busca dados reais do banco
$dadosBanco = $userModel->getById($userId);

$usuario = [
    'nome'     => $dadosBanco['nome']      ?? $_SESSION['user_nome']  ?? '',
    'email'    => $dadosBanco['email']     ?? $_SESSION['user_email'] ?? '',
    'telefone' => $dadosBanco['telefone']  ?? ''
];
?>

<main class="login-wrapper">
<div class="perfil-layout">

    <!-- ===== EDITAR PERFIL ===== -->
    <div class="login-container">
        <h2>Editar Perfil</h2>

        <form method="POST"
              action="<?= BASE_URL ?>/public/index.php?page=salvar_edicao"
              class="form-login">

            <div class="grupo-input">
                <label>Nome</label>
                <input class="input-login" type="text" name="nome"
                       value="<?= htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8') ?>"
                       required>
            </div>

            <div class="grupo-input">
                <label>Email</label>
                <input class="input-login" type="email" name="email"
                       value="<?= htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8') ?>"
                       required>
            </div>

            <div class="grupo-input">
                <label>Telefone</label>
                <input class="input-login" type="text" name="telefone"
                       value="<?= htmlspecialchars($usuario['telefone'], ENT_QUOTES, 'UTF-8') ?>"
                       required>
            </div>

            <button type="submit" class="btn-entrar">Salvar Dados</button>
        </form>
    </div>

    <!-- ===== ALTERAR SENHA ===== -->
    <div class="login-container">
        <h2>Alterar Senha</h2>

        <form method="POST"
              action="<?= BASE_URL ?>/public/index.php?page=salvar_senha"
              class="form-login">

            <div class="grupo-input">
                <label>Senha atual</label>
                <input type="password" class="input-login" name="senha_atual" required>
            </div>

            <div class="grupo-input">
                <label>Nova senha</label>
                <input type="password" class="input-login" name="nova_senha" required>
            </div>

            <div class="grupo-input">
                <label>Confirmar nova senha</label>
                <input type="password" class="input-login" name="confirmar_senha" required>
            </div>

            <button type="submit" class="btn-entrar">Alterar Senha</button>
        </form>
    </div>

</div>
</main>
