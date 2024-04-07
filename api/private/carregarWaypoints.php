<?php
include "../private/conexao.php";
session_start();

if (!isset($_SESSION["username"])) {
    header("location: ../game/login.php");
    exit();
}
$username = $_SESSION["username"];

$sql = "SELECT * FROM locais";
$result = $conexao->query($sql);

$waypoints = array();
while ($row = $result->fetch_assoc()) {
    $waypoints[] = $row;
}

echo json_encode($waypoints);

$conexao->close();
?>
