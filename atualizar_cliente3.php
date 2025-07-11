<?php
// Configurações da base de dados
$host = "localhost";
$user = "root";
$password = "";
$database = "3m-t2";

// Conexão com a base de dados
$conn = new mysqli($host, $user, $password, $database);

// Verifica a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Recebe os dados do formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $codcliente = $_POST['codcliente'];
    $nome = $_POST['nome'];
    $morada = $_POST['morada'];
    $numfiscal = $_POST['numfiscal'];
    $total_compras = $_POST['total_compras'];

    // Query para atualizar os dados do cliente
    $sql = "UPDATE clientes 
            SET nome = ?, morada = ?, numfiscal = ?, total_compras = ? 
            WHERE codcliente = ?";

    // Prepara a consulta
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssdi", $nome, $morada, $numfiscal, $total_compras, $codcliente);

    // Executa a consulta
    if ($stmt->execute()) {
        // Redireciona para a página de listagem com uma mensagem de sucesso
        header("Location: listagem3.php?success=1");
    } else {
        // Redireciona com uma mensagem de erro
        header("Location: listagem3.php?success=0");
    }

    // Fecha a conexão
    $stmt->close();
    $conn->close();
}
?>

