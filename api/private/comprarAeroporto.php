<?php
include "../private/conexao.php";

session_start();

// Verificar se o usuário está autenticado
if (!isset($_SESSION["username"])) {
    header("location: ../index.php");
    exit();
}

// Recuperar o ID do usuário
$username = $_SESSION["username"];

// Recuperar o nome do aeroporto e o valor
$local = $_POST['local'];
$valor = $_POST['valor'];

// Verificar se o usuário tem dinheiro suficiente para comprar o aeroporto
$query = "SELECT money FROM usuarios WHERE email = ? OR usuario = ?";
if ($stmt = $conexao->prepare($query)) {
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $stmt->bind_result($money);
    $stmt->fetch();
    $stmt->close();

    if ($money < $valor) {
        echo "Saldo insuficiente";
        exit;
    }
} else {
    echo "Erro ao preparar a declaração: " . $conexao->error;
    exit;
}

// Atualizar o saldo do usuário
$newMoney = $money - $valor;

if ($money < $valor) {
    http_response_code(400); // Requisição Inválida
    echo "Saldo insuficiente para comprar esta rota.";
    exit;
}

$sql = "UPDATE usuarios SET money = ? WHERE email = ? OR usuario = ?";
if ($stmt = $conexao->prepare($sql)) {
    $stmt->bind_param("dss", $newMoney, $username, $username);
    $stmt->execute();
    $stmt->close();
} else {
    echo "Erro ao atualizar o saldo do usuário: " . $conexao->error;
    exit;
}

// Registrar a compra na tabela de logs
// Registrar a compra na tabela de logs
$sql = "INSERT INTO logs (usuario, comprado, valor) VALUES (?, ?, ?)";
if ($stmt = $conexao->prepare($sql)) {
    $comprado = $local;
    $stmt->bind_param("ssd", $username, $comprado, $valor);
    if ($stmt->execute()) {
        echo "Aeroporto comprado com sucesso";
    } else {
        echo "Erro ao registrar a compra no log";
    }
    $stmt->close();
} else {
    echo "Erro ao preparar a declaração: " . $conexao->error;
}


$conexao->close();
?>
