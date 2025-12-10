<?php
    //Usado para proteger a página, caso o usuário tente entrar direto, ele vai ser redirecionado para a página de login.
    //require_once '../../../app/core/Session.php';

    // Verifica se o usuário está logado
    //verificaLogin();


?>

<div class="container-conteudo-padrao">
    
    <div class="card-login">
        <h1>Seja bem-vindo, <?= htmlspecialchars($_SESSION["user_nome"], ENT_QUOTES, 'UTF-8'); ?>!</h1>
        <p>E ótimas compras</p>

        <div class="opcoes-botoes">
            <a href="<?= BASE_URL ?>/public/index.php?page=home" class="btn-verde">Inicio</a>
            <a href="<?= BASE_URL ?>/public/index.php?page=painel_adm" class="btn-verde">Marmitas</a>
        </div>
    </div>

</div>