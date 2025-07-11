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

$mensagem = ""; // Mensagem de feedback

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Processar a atualização
    $codartigo = isset($_POST['codartigo']) ? trim($_POST['codartigo']) : '';
    $descricao = isset($_POST['descricao']) ? trim($_POST['descricao']) : '';
    $pvp = isset($_POST['pvp']) ? floatval(str_replace(',', '.', $_POST['pvp'])) : 0;
    $qstock = isset($_POST['qstock']) ? (int)$_POST['qstock'] : 0;

    if (empty($codartigo) || empty($descricao) || $pvp <= 0 || $qstock < 0) {
        $mensagem = "<div class='alert alert-danger'>Dados inválidos. Por favor, preencha corretamente.</div>";
    } else {
        $imagem_blob = null;
        $updateImagem = false;

        // Verificar imagem (opcional)
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['imagem']['tmp_name'];
            $fileSize = $_FILES['imagem']['size'];
            $fileType = mime_content_type($fileTmpPath);
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

            if (in_array($fileType, $allowedTypes) && $fileSize <= 5 * 1024 * 1024) {
                $imagem_blob = file_get_contents($fileTmpPath);
                $updateImagem = true;
            } else {
                $mensagem = "<div class='alert alert-danger'>Imagem inválida ou muito grande (máx 5MB).</div>";
            }
        }

        if (empty($mensagem)) {
            if ($updateImagem) {
                $sql = "UPDATE artigos SET descricao = ?, pvp = ?, qstock = ?, imagem = ? WHERE codartigo = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sdisb", $descricao, $pvp, $qstock, $imagem_blob, $codartigo);
                $stmt->send_long_data(3, $imagem_blob);
            } else {
                $sql = "UPDATE artigos SET descricao = ?, pvp = ?, qstock = ? WHERE codartigo = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sdis", $descricao, $pvp, $qstock, $codartigo);
            }

            if ($stmt->execute()) {
                // Atualizado com sucesso
                header("Location: listagem_artigos.php?success=1");
                exit;
            } else {
                $mensagem = "<div class='alert alert-danger'>Erro ao atualizar o artigo.</div>";
            }

            $stmt->close();
        }
    }
}

// Carregar dados do artigo para o formulário
if (isset($_GET['codartigo'])) {
    $codartigo = trim($_GET['codartigo']);
} elseif (isset($codartigo)) {
    // Já definido no POST
} else {
    die("Código do artigo não fornecido.");
}

$sql = "SELECT codartigo, descricao, pvp, qstock FROM artigos WHERE codartigo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $codartigo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $artigo = $result->fetch_assoc();
} else {
    die("Artigo não encontrado.");
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Alterar Artigo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
    <h1 class="mb-4">Alterar Artigo</h1>

    <?= $mensagem ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="codartigo" value="<?= htmlspecialchars($artigo['codartigo']) ?>">

        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição:</label>
            <input type="text" name="descricao" id="descricao" class="form-control" required value="<?= htmlspecialchars($artigo['descricao']) ?>">
        </div>

        <div class="mb-3">
            <label for="pvp" class="form-label">Preço (€):</label>
            <input type="text" name="pvp" id="pvp" class="form-control" required value="<?= number_format($artigo['pvp'], 2, ',', '.') ?>">
        </div>

        <div class="mb-3">
            <label for="qstock" class="form-label">Quantidade em Stock:</label>
            <input type="number" name="qstock" id="qstock" class="form-control" required value="<?= (int)$artigo['qstock'] ?>">
        </div>

        <div class="mb-3">
            <label for="imagem" class="form-label">Imagem (opcional, máx. 5MB):</label>
            <input type="file" name="imagem" id="imagem" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        <a href="listagem_artigos.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
