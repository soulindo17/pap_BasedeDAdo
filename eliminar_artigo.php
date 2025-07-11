<?php
// Configurações da base de dados
$host = "localhost";
$user = "root";
$password = "";
$database = "3m-t2";

// Conexão com a base de dados
$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

if (isset($_GET['codartigo'])) {
    $codartigo = (int)$_GET['codartigo'];

    if ($codartigo > 0) {
        // Preparar e executar a query DELETE
        $sql = "DELETE FROM artigos WHERE codartigo = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $codartigo);

        if ($stmt->execute()) {
            // Redirecionar com sucesso
            header("Location: listagem_artigos.php?success=1");
            exit;
        } else {
            // Redirecionar com erro ao eliminar
            header("Location: listagem_artigos.php?success=0");
            exit;
        }
    } else {
        // Código inválido
        header("Location: listagem_artigos.php?success=0");
        exit;
    }
} else {
    // Código não fornecido
    header("Location: listagem_artigos.php?success=0");
    exit;
}

$conn->close();
?>
