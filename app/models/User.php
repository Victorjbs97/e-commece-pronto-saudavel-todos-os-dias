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