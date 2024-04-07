<?php
// Incluir arquivo de conexão com o banco de dados
include "../private/conexao.php";
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION["username"])) {
    // Redirecionar para a página de login caso não esteja logado
    header("location: ../game/login.php");
    exit();
}

// Recebe os dados enviados via POST
$local = $_POST['local'];
$usuario = $_SESSION["username"];

// Loga a tentativa de verificação no servidor
error_log("Verificação de posse de local iniciada para o usuário: " . $usuario . " e local: " . $local);

// Prepara a consulta SQL para verificar se o usuário possui o local
$sql = "SELECT * FROM logs WHERE usuario = ? AND comprado = ?";

// Preparar a declaração SQL para execução
$stmt = $conexao->prepare($sql);

// Vincula os parâmetros à declaração (s = string)
$stmt->bind_param("ss", $usuario, $local);

// Executa a consulta
$stmt->execute();

// Obtém o resultado da consulta
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // O usuário possui o local
    echo json_encode(array('possui' => true));
    // Loga no servidor
    error_log("O usuário: " . $usuario . " possui o local: " . $local);
} else {
    // O usuário não possui o local
    echo json_encode(array('possui' => false));
    // Loga no servidor
    error_log("O usuário: " . $usuario . " NÃO possui o local: " . $local);
}

// Fechar a declaração e a conexão
$stmt->close();
$conexao->close();
?>
