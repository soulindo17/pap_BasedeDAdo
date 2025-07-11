<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Conexão com a base de dados
    $host = "localhost";
    $user = "root";
    $password = "";
    $database = "3m-t2";

    $conn = new mysqli($host, $user, $password, $database);

    if ($conn->connect_error) {
        die("Erro na conexão com a base de dados: " . $conn->connect_error);
    }

    // Obter o ID do cliente enviado pelo formulário
    $codcliente = intval($_POST['codcliente']);

    // Query para apagar o cliente
    $sql = "DELETE FROM clientes WHERE codcliente = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Erro ao preparar a query: " . $conn->error);
    }

    $stmt->bind_param("i", $codcliente);

    if ($stmt->execute()) {
        header("Location: listagem3.php?success=1");
        exit;
    } else {
        header("Location: listagem3.php?success=0");
        exit;
    }

    $stmt->close();
    $conn->close();
} elseif (isset($_GET['codcliente']) && isset($_GET['nome'])) {
    // Lógica para exibir o formulário
    $codcliente = $_GET['codcliente'];
    $nome = $_GET['nome'];
} else {
    // Redirecionar se as informações não forem fornecidas
    header("Location: listagem3.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Cliente</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="elimina_listagem3.php">
                <div class="modal-header">
                    <h5 class="modal-title">Eliminar Cliente</h5>
                </div>
                <div class="modal-body">
                    <p>Deseja mesmo eliminar o cliente <strong><?php echo htmlspecialchars($nome); ?></strong>?</p>
                    <input type="hidden" name="codcliente" value="<?php echo htmlspecialchars($codcliente); ?>">
                </div>
                <div class="modal-footer">
                    <a href="listagem3.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-danger">Eliminar Cliente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>