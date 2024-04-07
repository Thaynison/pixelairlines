<?php
// Iniciar a sessão
session_start();

// Apagar todas as variáveis de sessão
$_SESSION = array();

// Se necessário, destruir a sessão
session_destroy();

// Redirecionar para a página de login
header("location: ../index.php");
exit();
?>
