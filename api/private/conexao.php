<?php
$host = 'localhost';
$usuario = 'root';
$senha = 'root';
$bancoDeDados = "americanairlines";
$conexao = new mysqli($host, $usuario, $senha, $bancoDeDados);
if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}
$informacaoParaIndex = "Conexão bem-sucedida!";
?>