<?php
require 'config.php'; // Arquivo de conexão com o banco de dados

session_start();

$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Buscar o cliente pelo email
    $stmt = $pdo->prepare("SELECT * FROM clientes WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cliente) {
        $erro = "Cliente não encontrado. Por favor, verifique seu email.";
    } else {
        // Verifica se a senha fornecida é correta
        if (password_verify($senha, $cliente['senha'])) {
            // Armazena o ID do cliente e o nome na sessão
            			
			$_SESSION['codcliente'] = $cliente['codcliente'];
            $_SESSION['nome'] = $cliente['nome'];
            $_SESSION['email'] = $email;
			
			$_SESSION['morada'] = $cliente['morada'];
			$_SESSION['codpostal'] = $cliente['codpostal'];
			$_SESSION['localidade'] = $cliente['localidade'];
			$_SESSION['numfiscal'] = $cliente['numfiscal'];
			$_SESSION['contacto'] = $cliente['contacto'];

            // Ao fazer login
            $codcliente = $_SESSION['codcliente'];
            $sessionId = session_id();

            // Verificar se há itens no carrinho que ainda não têm `codcliente` associado
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM carrinho WHERE session_id = :session_id AND codcliente IS NULL");
            $stmt->bindParam(':session_id', $sessionId, PDO::PARAM_STR);
            $stmt->execute();

            $countItems = $stmt->fetchColumn();

            if ($countItems > 0) {
                // Só atualizar os itens do carrinho associados ao `session_id` atual
                $stmt = $pdo->prepare("UPDATE carrinho SET codcliente = :codcliente WHERE session_id = :session_id AND codcliente IS NULL");
                $stmt->bindParam(':codcliente', $codcliente, PDO::PARAM_INT);
                $stmt->bindParam(':session_id', $sessionId, PDO::PARAM_STR);
                $stmt->execute();
            }

            // Redireciona para a página de menu
            header("Location: menu_artigos_vendas.php");
            exit;
        } else {
            $erro = "Senha incorreta. Tente novamente.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Login</h2>
    
    <?php if (isset($erro)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>
        
        <div class="mb-3">
            <label for="senha" class="form-label">Senha:</label>
            <input type="password" name="senha" id="senha" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Login</button>
    </form>

    <p class="mt-3">Ainda não tem conta? <a href="registo.php">Registe-se aqui</a></p>

</body>
</html>