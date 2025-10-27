<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS do header -->
    <link rel="stylesheet" href="../views/partials/css/header.css">

    <!-- CSS do Dialog de login -->
    <link rel="stylesheet" href="../views/partials/css/dialog_login.css">

    <!-- CSS do carrossel -->
    <link rel="stylesheet" href="../views/partials/css/carrossel.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">


    <title>Pronto & Saudavel</title>
</head>
<body>

    <!-- Header -->
    <?php require_once "../views/partials/header.php"; ?>

    

    <!-- Carrossel -->
    <?php require_once "../views/partials/carrossel.php"; ?>

    
    <?php

        define('VIEWS_PATH', __DIR__ . '/../views');

        // 2. Carregamos o nosso "molde" principal.
        // O 'main.php' agora é o responsável por montar o resto da página.
        require_once VIEWS_PATH . '/layouts/main.php';

    ?>
    <!-- Footer -->
    <?php require_once "../views/partials/footer.php"; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
        crossorigin="anonymous"></script>
</body>
</html>
