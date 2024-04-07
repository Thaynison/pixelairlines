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
$usuario = $_SESSION["username"];

// Prepara a consulta SQL para verificar os rotas do usuário
$sql = "SELECT * FROM rotas WHERE usuario = ?";

// Preparar a declaração SQL para execução
$stmt = $conexao->prepare($sql);

// Vincula o parâmetro à declaração (s = string)
$stmt->bind_param("s", $usuario);

// Executa a consulta
$stmt->execute();

// Obtém o resultado da consulta
$result = $stmt->get_result();

$rotas_do_usuario = array(); // Array para armazenar os rotas do usuário

// Verifica se há resultados
if ($result->num_rows > 0) {
    // Itera sobre os resultados e adiciona os rotas do usuário ao array
    while ($row = $result->fetch_assoc()) {
        $rotas_do_usuario[] = $row; // Adiciona o avião ao array
    }
}

// Fecha a declaração e a conexão
$stmt->close();
$conexao->close();

// Retorna os rotas do usuário em formato JSON
echo json_encode($rotas_do_usuario);
?>
