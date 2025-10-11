<?php
    //Usado para proteger a página, caso o usuário tente entrar direto, ele vai ser redirecionado para a página de login.
    require_once '../../../app/core/Session.php';
    // Verifica se o usuário está logado
    verificaLogin();

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Área Logada</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .card {
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
        }
        .btn {
            background-color: #e63946;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 6px;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #c82e3a;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Bem-vindo, <?= htmlspecialchars($_SESSION["user_nome"], ENT_QUOTES, 'UTF-8'); ?>!</h1>
        <p>Você está logado com o e-mail: <strong><?= htmlspecialchars(usuarioNome()); ?></strong></p>
        <a href="logout.php" class="btn">Sair</a>
    </div>
</body>
</html>