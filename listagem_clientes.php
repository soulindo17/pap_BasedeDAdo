<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Clientes</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
</head>
<body>

<?php
$success = filter_input(INPUT_GET, 'success', FILTER_VALIDATE_INT);
if ($success === 1) {
    echo '<div class="alert alert-success text-center" role="alert">
            Cliente alterado / eliminado com sucesso!
          </div>';
} else if ($success === 0) {
    echo '<div class="alert alert-danger text-center" role="alert">
            Falha ao alterar / eliminar cliente.
          </div>';
}
?>

<div class="container mt-4">
    <h1 class="text-center mb-4">Listagem de Clientes</h1>

    <table id="clientesTable" class="table table-striped table-bordered text-center">
        <thead class="table-dark">
            <tr>
                <th scope="col">Código</th>
                <th scope="col">Nome</th>
                <th scope="col">Morada</th>
                <th scope="col">Nº Fiscal</th>
                <th scope="col">Total Compras</th>
                <th scope="col">Operações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Configurações da base de dados
            $host = "localhost";
            $user = "root";
            $password = "";
            $database = "3m-t2";

            $conn = new mysqli($host, $user, $password, $database);
            if ($conn->connect_error) {
                die("Falha na conexão: " . $conn->connect_error);
            }

            $sql = "SELECT codcliente, nome, morada, numfiscal, total_compras FROM clientes";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $codcliente = urlencode($row['codcliente']);
                    $nome = urlencode($row['nome']);
                    echo "<tr>
                        <td>{$row['codcliente']}</td>
                        <td>{$row['nome']}</td>
                        <td>{$row['morada']}</td>
                        <td>{$row['numfiscal']}</td>
                        <td>{$row['total_compras']}</td>
                        <td>
                            <a href='alterar_cliente3.php?codcliente=$codcliente' class='btn btn-warning btn-sm'>Alterar</a>
                            <a href='elimina_listagem3.php?codcliente=$codcliente&nome=$nome' class='btn btn-danger btn-sm' onclick=\"return confirm('Tem a certeza que deseja eliminar o cliente {$row['nome']}?');\">Eliminar</a>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Nenhum cliente encontrado</td></tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>

    <a href="index.html" class="btn btn-secondary mt-2">← Voltar</a>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#clientesTable').DataTable({
            "pageLength": 10,
            "responsive": true,
            "language": {
                "search": "Pesquisar:",
                "lengthMenu": "Mostrar _MENU_ entradas",
                "info": "Mostrar _START_ a _END_ de _TOTAL_ entradas",
                "paginate": {
                    "previous": "Anterior",
                    "next": "Próximo"
                },
                "emptyTable": "Nenhum dado disponível na tabela",
                "infoEmpty": "Mostrando 0 a 0 de 0 entradas",
                "infoFiltered": "(filtrado de _MAX_ entradas totais)"
            }
        });
    });
</script>

</body>
</html>
