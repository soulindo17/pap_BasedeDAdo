<?php
// Configuração da conexão PDO
$host = 'localhost';
$dbname = '3m-t2';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Receber e limpar dados do formulário
        $codartigo = isset($_POST['tcodartigo']) ? trim($_POST['tcodartigo']) : '';
        $descricao = isset($_POST['tdescricao']) ? trim($_POST['tdescricao']) : '';
        $pvp = isset($_POST['tpvp']) ? floatval($_POST['tpvp']) : 0;
        $qstock = isset($_POST['tqstock']) ? intval($_POST['tqstock']) : 0;

        // Validação básica dos campos obrigatórios
        if (empty($codartigo) || empty($descricao) || $pvp <= 0 || $qstock < 0) {
            echo "⚠️ Por favor, preencha os campos obrigatórios corretamente.";
            exit;
        }

        // Processa imagem (se existir)
        if (isset($_FILES['timagem']) && $_FILES['timagem']['error'] === UPLOAD_ERR_OK) {
            $imagemData = file_get_contents($_FILES['timagem']['tmp_name']);
        } else {
            // Se não enviou imagem, define NULL (atenção: sua tabela não aceita NULL? Então pode usar string vazia '')
            $imagemData = null;
        }

        // Prepara a query de inserção
        $sql = "INSERT INTO artigos (codartigo, descricao, pvp, qstock, imagem)
                VALUES (:codartigo, :descricao, :pvp, :qstock, :imagem)";

        $stmt = $pdo->prepare($sql);

        // Bind dos parâmetros
        $stmt->bindParam(':codartigo', $codartigo);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindValue(':pvp', $pvp, PDO::PARAM_STR);  // pode usar STR para double
        $stmt->bindValue(':qstock', $qstock, PDO::PARAM_INT);
        $stmt->bindParam(':imagem', $imagemData, PDO::PARAM_LOB);  // indica que é LOB (BLOB)

        // Executa a inserção
        $stmt->execute();

        echo "✅ Artigo inserido com sucesso!";
        
    } else {
        echo "Método inválido.";
    }

} catch (PDOException $e) {
    echo "❌ Erro ao inserir artigo: " . $e->getMessage();
}
echo "<br><a href='insere_artigos.html'>Inserir outro artigo</a>";
echo "<br><a href='listagem_artigos.php'>Listagem de artigos</a>";
?>



  