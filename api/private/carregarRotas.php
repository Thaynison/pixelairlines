<?php
include "../private/conexao.php";
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION["username"])) {
    echo json_encode(["error" => "Usuário não autenticado"]);
    exit;
}

$username = $_SESSION["username"];

// Busca as rotas do usuário
$queryRotas = "SELECT pontoA, pontoB FROM rotas WHERE usuario = '$username'";
$resultRotas = mysqli_query($conexao, $queryRotas);

if (!$resultRotas) {
    echo json_encode(["error" => mysqli_error($conexao)]);
    exit;
}

$rotas = [];
while ($rota = mysqli_fetch_assoc($resultRotas)) {
    // Para cada pontoA e pontoB, buscar suas coordenadas na tabela locais
    $pontoA = buscarCoordenadas($conexao, $rota['pontoA']);
    $pontoB = buscarCoordenadas($conexao, $rota['pontoB']);

    if ($pontoA && $pontoB) {
        $rotas[] = [
            "pontoA" => $rota['pontoA'],
            "latitudeA" => $pontoA['latitude'],
            "longitudeA" => $pontoA['longitude'],
            "pontoB" => $rota['pontoB'],
            "latitudeB" => $pontoB['latitude'],
            "longitudeB" => $pontoB['longitude'],
        ];
    }
}

echo json_encode($rotas);

function buscarCoordenadas($conexao, $local) {
    $queryLocal = "SELECT latitude, longitude FROM locais WHERE local = '$local'";
    $resultLocal = mysqli_query($conexao, $queryLocal);
    if ($resultLocal && $linha = mysqli_fetch_assoc($resultLocal)) {
        return $linha;
    }
    return null;
}
?>