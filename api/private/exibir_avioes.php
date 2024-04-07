<?php
include "../private/conexao.php";

// Preparando a consulta SQL para buscar todos os aviões
$sql = "SELECT * FROM avioes";
$result = $conexao->query($sql);

$avioes = [];

if ($result->num_rows > 0) {
    // Armazenando os resultados em um array
    while($row = $result->fetch_assoc()) {
        $avioes[] = $row;
    }
    // Enviando os resultados em formato JSON
    echo json_encode($avioes);
} else {
    echo "0 results";
}

$conexao->close();
?>
