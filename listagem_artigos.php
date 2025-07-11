<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Listagem de Artigos</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />
    <!-- Bootstrap Icons (moved to head) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        .table img {
            width: 80px;
            height: auto;
            border-radius: 5px;
            cursor: pointer;
            transition: transform 0.3s ease;
        }
        .table img:hover {
            transform: scale(1.2);
        }
        .dt-center { text-align: center; }
    </style>
</head>
<body>

<div class="container mt-4">

<?php
// Define charset UTF-8 para evitar problemas com acentuação
header('Content-Type: text/html; charset=utf-8');

// --- BLOCO PARA ELIMINAR ARTIGO ---
$host = "localhost";
$user = "root";
$password = "";
$database = "3m-t2";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    echo '<div class="alert alert-danger">Falha na conexão: ' . htmlspecialchars($conn->connect_error) . '</div>';
    exit;
}

if (isset($_GET['eliminar'])) {
    $codartigo = $_GET['eliminar'];  // tratar como string

    if (!empty($codartigo)) {
        $stmt = $conn->prepare("DELETE FROM artigos WHERE codartigo = ?");
        $stmt->bind_param("s", $codartigo); // "s" para string

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: " . strtok($_SERVER["REQUEST_URI"], '?') . "?success=1");
            exit;
        } else {
            $stmt->close();
            $conn->close();
            header("Location: " . strtok($_SERVER["REQUEST_URI"], '?') . "?success=0");
            exit;
        }
    } else {
        $conn->close();
        header("Location: " . strtok($_SERVER["REQUEST_URI"], '?') . "?success=0");
        exit;
    }
}

// --- MENSAGENS DE SUCESSO/ERRO ---
if (isset($_GET['success']) && $_GET['success'] == 1) {
    echo '<div class="alert alert-success alert-dismissible fade show text-center" role="alert">
            Artigo alterado / eliminado com sucesso!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
} else if (isset($_GET['success']) && $_GET['success'] == 0) {
    echo '<div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
            Falha ao alterar / eliminar artigo.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
}

// --- CONSULTA ARTIGOS ---
$sql = "SELECT codartigo, descricao, pvp, qstock, imagem FROM artigos";
$result = $conn->query($sql);

// Função para detectar MIME de imagem (exemplo simples)
function getImageMimeType($imageData) {
    if (substr($imageData, 0, 3) === "\xFF\xD8\xFF") {
        return 'jpeg';
    } elseif (substr($imageData, 0, 8) === "\x89PNG\x0D\x0A\x1A\x0A") {
        return 'png';
    } elseif (substr($imageData, 0, 6) === "GIF87a" || substr($imageData, 0, 6) === "GIF89a") {
        return 'gif';
    }
    return 'jpeg'; // padrão
}
?>

    <h1 class="text-center mb-4">Listagem de Artigos</h1>
    <table id="artigosTable" class="table table-striped table-bordered text-center">
        <thead class="table-dark">
            <tr>
                <th>Código</th>
                <th>Descrição</th>
                <th>Preço (€)</th>
                <th>Quant. Stock</th>
                <th>Imagem</th>
                <th>Operações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['codartigo']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['descricao']) . "</td>";
                    echo "<td>" . number_format($row['pvp'], 2, ',', '.') . "</td>";
                    echo "<td>" . (int)$row['qstock'] . "</td>";

                    if ($row['imagem']) {
                        $imgDataRaw = $row['imagem'];
                        $mimeType = getImageMimeType($imgDataRaw);
                        $imgData = base64_encode($imgDataRaw);
                        echo "<td><img src='data:image/{$mimeType};base64,{$imgData}' alt='Imagem' title='Clique para ampliar' onclick='showImageModal(this.src)'></td>";
                    } else {
                        echo "<td>Sem Imagem</td>";
                    }

                    // ALTERAR mantém igual, ELIMINAR usa ?eliminar=codartigo
                    echo "<td>
                            <a href='alterar_artigo3.php?codartigo=" . urlencode($row['codartigo']) . "' class='btn btn-warning btn-sm me-1' data-bs-toggle='tooltip' data-bs-placement='top' title='Alterar artigo'>
                                <i class='bi bi-pencil-square'></i> Alterar
                            </a>
                            <a href='?eliminar=" . urlencode($row['codartigo']) . "' class='btn btn-danger btn-sm' data-bs-toggle='tooltip' data-bs-placement='top' title='Eliminar artigo' onclick='return confirm(\"Tem certeza que deseja eliminar este artigo?\");'>
                                <i class='bi bi-trash'></i> Eliminar
                            </a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Nenhum artigo encontrado</td></tr>";
            }
            $conn->close();
            ?>
        </tbody>
    </table>
    <br>
    <a href="index.html" class="btn btn-secondary">Voltar</a>
</div>

<!-- Modal para ampliar imagem -->
<div class="modal fade" id="imagemModal" tabindex="-1" aria-labelledby="imagemModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-body p-0">
        <img id="imagemModalSrc" src="" alt="Imagem ampliada" class="img-fluid w-100" />
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS + Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
// Inicializa DataTables com customizações e idioma PT-BR
$(document).ready(function() {
    $('#artigosTable').DataTable({
        "pageLength": 10,
        "language": {
            "search": "Pesquisar:",
            "lengthMenu": "Mostrar _MENU_ entradas",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ entradas",
            "paginate": {
                "previous": "Anterior",
                "next": "Próximo"
            },
            "emptyTable": "Nenhum dado disponível na tabela",
            "infoEmpty": "Mostrando 0 a 0 de 0 entradas",
            "infoFiltered": "(filtrado de _MAX_ entradas totais)"
        },
        "columnDefs": [
            { "orderable": false, "targets": [4, 5] }, // Imagem e operações não ordenáveis
            { "className": "dt-center", "targets": "_all" }
        ]
    });

    // Ativa tooltips Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});

// Função para mostrar modal com imagem ampliada
function showImageModal(src) {
    const modalImg = document.getElementById('imagemModalSrc');
    modalImg.src = src;
    const imagemModal = new bootstrap.Modal(document.getElementById('imagemModal'));
    imagemModal.show();
}
</script>

</body>
</html>
