<?php

// 1. Inclusão de Arquivos Essenciais
// Inclui o script que estabelece a conexão com o banco de dados ($pdo).
require_once '../../../app/core/DataBaseConecta.php'; 
// Inclui o arquivo que contém a função 'cadastrar_usuario'.
require_once '../../../app/models/User.php'; 

require_once '../../../app/core/Session.php';

verificaLoginPaginaLogin();

// 2. Inicialização de Variáveis (Inclui variáveis para pré-preenchimento do formulário)
// Variável para armazenar a mensagem de feedback (sucesso ou erro) ao usuário.
$mensagem = "";
// Variáveis para manter o estado do formulário em caso de erro
$nome = '';
$email = '';
$telefone = '';


// 3. Verificação do Método de Requisição
// Checa se a página foi acessada via submissão de um formulário (método POST).
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 4. Coleta e Limpeza dos Dados do Formulário
    // Coleta os dados enviados pelo formulário.
    // A função 'trim' remove espaços em branco no início e no fim dos campos.
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    
    // Aplicar trim() na senha também, apenas para remover espaços laterais.
    $senha = trim($_POST['senha']); 
    
    $telefone = trim($_POST['telefone']);

    // 5. Chamada da Função de Cadastro
    // Chama a função de cadastro, passando o objeto PDO e os dados coletados.
    $resultado = cadastrar_usuario($conexao, $nome, $email, $senha, $telefone);

    // 6. Tratamento do Resultado
    // A função retorna 'true' em caso de sucesso ou uma string com a mensagem de erro.
    if ($resultado === true) {
        // Redirecionamento em caso de sucesso.
        // Redireciona para login e adiciona um parâmetro para exibir a mensagem lá.
        header("Location: register.php?register=sucesso");
        exit;
    } else {
        // Define a mensagem de erro, usando a string retornada pela função
        $mensagem = "<p class='mensagem erro'>$resultado</p>";
        
        // Se houver erro, os valores coletados ($nome, $email, $telefone) 
        // são mantidos para pré-preencher o formulário abaixo.
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Cadastro de Usuário</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="container">
    <h2>Cadastro de Usuário</h2>

    <?= $mensagem ?>

    <form method="post" class="form-cadastro">
      <label>Nome:</label>
      <input type="text" name="nome" required value="<?= htmlspecialchars($nome) ?>">

      <label>Email:</label>
      <input type="email" name="email" required value="<?= htmlspecialchars($email) ?>">

      <label>Senha:</label>
      <input type="password" name="senha" required>

      <label>Telefone:</label>
      <input type="text" name="telefone" value="<?= htmlspecialchars($telefone) ?>">

      <button type="submit">Cadastrar</button>
      <a href="login.php">login</a>
    </form>
  </div>
</body>
</html>