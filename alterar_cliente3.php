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

// Obtém o código do cliente a ser alterado
if (isset($_GET['codcliente'])) {
    $codcliente = $_GET['codcliente'];

    // Query para buscar os dados do cliente
    $sql = "SELECT * FROM clientes WHERE codcliente = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $codcliente);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $cliente = $result->fetch_assoc();
    } else {
        die("Cliente não encontrado.");
    }
} else {
    die("Código do cliente não fornecido.");
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Cliente</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h1 class="text-center mb-4">Alterar Cliente</h1>

    <form action="atualizar_cliente3.php" method="POST">
        <input type="hidden" name="codcliente" value="<?php echo $cliente['codcliente']; ?>">

        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" class="form-control" id="nome" name="nome" value="<?php echo $cliente['nome']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="morada" class="form-label">Morada</label>
            <input type="text" class="form-control" id="morada" name="morada" value="<?php echo $cliente['morada']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="numfiscal" class="form-label">Nº Fiscal</label>
            <input type="text" class="form-control" id="numfiscal" name="numfiscal" value="<?php echo $cliente['numfiscal']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="total_compras" class="form-label">Total Compras</label>
            <input type="number" class="form-control" id="total_compras" name="total_compras" value="<?php echo $cliente['total_compras']; ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
    </form>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>