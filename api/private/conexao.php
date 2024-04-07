<?php
$host = 'roundhouse.proxy.rlwy.net';
$usuario = 'root';
$senha = 'PChdAMyuFiBRQwJNOFDsXUffLhWdQcbq';
$bancoDeDados = "americanairlines";
$conexao = new mysqli($host, $usuario, $senha, $bancoDeDados);
if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}
$informacaoParaIndex = "Conexão bem-sucedida!";
?>