<?php
// Cria uma variável com a URL base do seu projeto.
// Isso funciona mesmo que seu projeto não esteja na raiz do localhost.
$baseUrl = sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['HTTP_HOST'],
    dirname($_SERVER['SCRIPT_NAME']) === DIRECTORY_SEPARATOR ? '' : dirname($_SERVER['SCRIPT_NAME'])
);

// Remove a pasta /public da URL base se ela existir, para apontar para a raiz correta
$baseUrl = rtrim(str_replace('/public', '', $baseUrl), '/');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pronto Saudável</title>

    <link rel="stylesheet" href="<?= $baseUrl ?>/public/css/produto_card.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>/public/css/carrosel-produtos.css">

    <link rel="stylesheet" href="<?= $baseUrl ?>/public/css/galeria-de-fotos.css">
</head>
<body>
    
    <main>
        <?php require_once VIEWS_PATH . '/pages/home.php'; ?>
    </main>
    

    <script src="<?= $baseUrl ?>/public/js/carrossel-produtos.js"></script>
</body>
</html>