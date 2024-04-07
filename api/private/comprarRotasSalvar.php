<?php
include "../private/conexao.php";

session_start();

// Verificar se o usuário está autenticado
if (!isset($_SESSION["username"])) {
    http_response_code(403); // Acesso Proibido
    echo "Acesso Proibido";
    exit;
}

// Recuperar o ID do usuário
$username = $_SESSION["username"];

// Recuperar o nome do  oporto e o valor
$pontoA = $_POST['select1'];
$pontoB = $_POST['select2'];
$valor = 2000000;
$pagar = 5000;

// Verificar se o usuário tem dinheiro suficiente
$query = "SELECT money FROM usuarios WHERE email = ? OR usuario = ?";
if ($stmt = $conexao->prepare($query)) {
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $stmt->bind_result($money);
    $stmt->fetch();
    $stmt->close();

    if ($money < $valor) {
        http_response_code(400); // Requisição Inválida
        echo "Saldo insuficiente";
        exit;
    }
} else {
    http_response_code(500); // Erro Interno do Servidor
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
    if (!$stmt->execute()) {
        http_response_code(500); // Erro Interno do Servidor
        echo "Erro ao atualizar o saldo do usuário: " . $stmt->error;
        exit;
    }
    $stmt->close();
} else {
    http_response_code(500); // Erro Interno do Servidor
    echo "Erro ao preparar a declaração: " . $conexao->error;
    exit;
}

// Gerar um ID único para a rota
$rota_id = generateUniqueID();

// Registrar a compra na tabela de logs
$sql = "INSERT INTO rotas (usuario, rota_id, pontoA, pontoB, pagar) VALUES (?, ?, ?, ?, ?)";
if ($stmt = $conexao->prepare($sql)) {
    $stmt->bind_param("ssssd", $username, $rota_id, $pontoA, $pontoB, $pagar); // Corrigido "ssssdi" para "ssssd"
    if ($stmt->execute()) {
        http_response_code(200); // OK
        echo "Rota adquirida com sucesso";
    } else {
        http_response_code(500); // Erro Interno do Servidor
        echo "Erro ao registrar a compra no log: " . $stmt->error;
    }
    $stmt->close();
} else {
    http_response_code(500); // Erro Interno do Servidor
    echo "Erro ao preparar a declaração: " . $conexao->error;
}

$conexao->close();

// Função para gerar um ID único para a rota
function generateUniqueID() {
    return '#' . uniqid() . '-LA';
}
?>
