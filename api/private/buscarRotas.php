<?php
include "../private/conexao.php";
session_start();

if (!isset($_SESSION["username"])) {
    header("location: ../game/login.php");
    exit();
}

$usuario = $_SESSION["username"];
$sql = "SELECT DISTINCT comprado FROM logs WHERE usuario = ? ORDER BY comprado ASC";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

$comprados = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $comprados[] = $row['comprado'];
    }
}

$stmt->close();
$conexao->close();

echo json_encode($comprados);
?>
