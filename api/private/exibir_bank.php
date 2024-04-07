<?php
include "../private/conexao.php";

// Preparando a consulta SQL para buscar todos os aviões
$sql = "SELECT * FROM bank";
$result = $conexao->query($sql);

$bank = [];

if ($result->num_rows > 0) {
    // Armazenando os resultados em um array
    while($row = $result->fetch_assoc()) {
        $bank[] = $row;
    }
    // Enviando os resultados em formato JSON
    echo json_encode($bank);
} else {
    echo "0 results";
}

$conexao->close();
?>
