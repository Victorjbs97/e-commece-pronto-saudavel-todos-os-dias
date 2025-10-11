<?php

// Função de Cadastro de Novos Usuarios
function cadastrar_usuario($conexao, $nome, $email, $senha, $telefone) { 

    // 1. Validação básica de campos obrigatórios
    if (empty($nome) || empty($email) || empty($senha)) {
        return "Preencha todos os campos obrigatórios.";
    }

    // 2. Validação do formato do email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Email inválido.";
    }

    // 3. MELHORIA: Validação do Telefone (se preenchido)
    if (!empty($telefone) && !ctype_digit(str_replace([' ', '(', ')', '-'], '', $telefone))) {
        return "Formato de telefone inválido. Use apenas números, parênteses e traços.";
    }
    
    // 4. MELHORIA: Criptografia da senha (Hashing) com trim()
    // Remove espaços em branco antes/depois da senha e usa o algoritmo padrão recomendado.
    $senha_hash = password_hash(trim($senha), PASSWORD_DEFAULT);

    // 5. Bloco Try-Catch para execução da query SQL e tratamento de erros
    try {
        // Define a query SQL de inserção (usa placeholders nomeados para segurança)
        $sql = "INSERT INTO usuario (nome, email, senha, telefone)
                VALUES (:nome, :email, :senha_hash, :telefone)"; //placeholder renomeado para clareza
        
        // Prepara a instrução SQL para execução (evita SQL Injection)
        $Statement = $conexao->prepare($sql); // Statement -> significa Instrução
        
        // Executa a instrução, passando os valores como um array associativo
        $Statement->execute([
            ':nome' => $nome,
            ':email' => $email,
            ':senha_hash' => $senha_hash, // Salva a senha hasheada
            ':telefone' => $telefone
        ]);

        // Se a execução for bem-sucedida, retorna true
        return true;
        
    } catch (PDOException $e) {
        // Captura exceções geradas pelo PDO

        // Verifica se o erro é de violação de restrição única (código 23000)
        // Isso geralmente ocorre quando o email (que deve ser UNIQUE) já existe.
        if ($e->getCode() == 23000) {
            return "Erro: este email já está cadastrado.";
        } else {
            // error_log("Erro no cadastro de usuário: " . $e->getMessage(), 0);
            return "Erro ao realizar o cadastro. Tente novamente mais tarde.";
        }
    }
}

function realizarLogin($conexao,$email,$senha){
    try {
        //Consulta ao banco de dados, o prepare com :mail é importante para evitar SQL inject
        $stmt = $conexao->prepare("SELECT * FROM usuario WHERE email = :email LIMIT 1");
        //Executa o SQL acima
        $stmt->execute([":email" => $email]);
        //Retorna o resultado da consulta, o fetch especifica um array associativo com as informações do BD relacionados com o email. se não tiver o retorno é false.
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifica se o usuário existe e a senha está correta
        if ($user && password_verify($senha, $user["senha"])) {
//Importante!!!  Isso faz gerar um novo id a cada sessão, isso impede o cliente de ter seus dados sequestrados por meio de roubo de cookies.
            session_regenerate_id(true); 
            //Uma variavel global como um array, onde armazena os dados, quando tiver o adm tem que mudar aqui.
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_email"] = $user["email"];
            $_SESSION["user_nome"] = $user["nome"];
            $_SESSION["user_tipo"] = $user["tipo_usuario"];
            //Se tudo for bem sucedido, retorna um true para a função, vai ser usado para liberar o login.
            return true;
        } else {
            //Retorna uma mensagem ao usuário.
            return "Email e/ou senha inválidos!";
        }
        // caso falhe o try
    } catch (Exception $e) {
        //retorne para o adm.
        return "Erro ao tentar fazer login: " . $e->getMessage();
    }

}
?>