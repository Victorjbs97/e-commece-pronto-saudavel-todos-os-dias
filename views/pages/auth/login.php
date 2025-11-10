<?php
require_once '../../../app/core/DataBaseConecta.php';
require_once '../../../app/models/User.php';
require_once '../../../app/core/Session.php';
verificaLoginPaginaLogin();
$erro = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? '');
    $senha = trim($_POST["senha"] ?? '');
    if (empty($email) || empty($senha)) {
        $erro = "Por favor, preencha todos os campos.";
    } else {
        $podeLogar = realizarLogin($conexao, $email, $senha);
        if ($podeLogar === true) {
            header("Location: logado.php");
            exit;
        } else {
            $erro = $podeLogar;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Login</title>
</head>

<body>
    <div class="container">

        <div class="topo">
            <a href="../../../public/index.php" class="bt-a">↩</a>
        </div>

        <h2>Login</h2>
        <?php if (!empty($erro)): ?>
            <div class="erro"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <form action="" method="post" class="form-login">
            <div>

                <label for="">E-mail:</label>
                <input type="text" name="email">
            </div>
            <div>
                <label for="">Senha:</label>
                <input type="password" name="senha">
            </div>
            <div class="dosBotoes">
                <button type="submit">Entrar</button>


                <a href="register.php" class="bt-b">Cadastrar</a>



            </div>

        </form>
    </div>

</body>

</html>