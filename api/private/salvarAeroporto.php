<?php
include "../private/conexao.php";

$local = $_POST['local'];
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];
$valor = 20000000; // Este valor está sendo definido estáticamente aqui

$sql = "INSERT INTO locais (local, latitude, longitude, valor) VALUES (?, ?, ?, ?)";

if ($stmt = $conexao->prepare($sql)) {
    // Corrigido para incluir 'i' para o tipo inteiro de $valor
    $stmt->bind_param("sddi", $local, $latitude, $longitude, $valor);

    if ($stmt->execute()) {
        echo "Sucesso";
    } else {
        echo "Erro: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Erro ao preparar a declaração: " . $conexao->error;
}
$conexao->close();
?>
