<?php
     
    $ligacao=mysqli_connect("localhost","root","","3m-t2");
   
    $codcliente = $_POST["tcodcliente"]; 
    $nome = $_POST["tnome"];
    $morada = $_POST["tmorada"];
    $numfiscal = $_POST["tnumfiscal"];
    $totcompras = $_POST["ttotcompras"];


    $string_sql = "INSERT INTO clientes (codcliente, nome, morada, numfiscal, total_compras) VALUES ('$codcliente','$nome','$morada','$numfiscal','$totcompras')"; 
     
    $result = mysqli_query($ligacao,$string_sql); 
     
    if(mysqli_affected_rows($ligacao) == 1){ 
        echo 'Dados gravados com sucesso';
        echo '<br>';
        echo '<a href="insere_clientes.html">Voltar</a>'; 
    } else {
        echo "Erro, não foi possível inserir na base de dados - código repetido ou nulo";
        echo "<br>";
        echo '<a href="insere_clientes.html">Voltar</a>'; 
        
    }
    
    mysqli_close($ligacao); 
?>       