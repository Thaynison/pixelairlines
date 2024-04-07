<?php
include "../private/conexao.php";
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION["username"])) {
    header("location: ../game/login.php");
    exit();
}

$username = $_SESSION["username"];

$query = "SELECT * FROM logsaviao WHERE usuario = '$username'";

$result = mysqli_query($conexao, $query);

$avioes = [];
while ($row = mysqli_fetch_assoc($result)) {
    $avioes[] = $row;
}

echo json_encode($avioes);
?>

