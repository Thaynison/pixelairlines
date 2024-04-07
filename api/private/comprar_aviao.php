<?php
include "../private/conexao.php";
session_start();

if (!isset($_SESSION["username"])) {
    // Se o usuário não estiver logado, retorne uma resposta de erro
    echo json_encode(["success" => false, "message" => "Usuário não está logado"]);
    exit();
}

$username = $_SESSION["username"];

// Obtenha os dados enviados via POST
$data = json_decode(file_get_contents("php://input"), true);

$modelo = $data['modelo'];
$valor = $data['valor'];
$img = $data['img'];
$fabricante = $data['fabricante'];
$capacidade = $data['capacidade'];
$velocidade = $data['velocidade'];

// Consulte o banco de dados para obter os dados do usuário, incluindo
$query = "SELECT * FROM usuarios WHERE email = '$username' OR usuario = '$username'";
$result = mysqli_query($conexao, $query);

if (mysqli_num_rows($result) == 1) {
    $dadosUsuario = mysqli_fetch_assoc($result);
    $vip = $dadosUsuario["vip"];
    $money = $dadosUsuario["money"];

    // Verifique se o usuário tem dinheiro suficiente para comprar o avião
    if ($money >= $valor) {
        // Subtrai o valor do avião do saldo do usuário
        $novoSaldo = $money - $valor;

        // Atualiza o saldo do usuário no banco de dados
        $updateQuery = "UPDATE usuarios SET money = '$novoSaldo' WHERE email = '$username' OR usuario = '$username'";
        mysqli_query($conexao, $updateQuery);

        // Gera o ID único para o avião
        do {
            $aviao_id = generateUniqueID();
        } while (idExists($conexao, $aviao_id));

        // Salva os detalhes da compra no log, incluindo
        $insertQuery = "INSERT INTO logsaviao (usuario, aviao_id, modelo, fabricante, valor, capacidade, velocidade, img) VALUES ('$username', '$aviao_id', '$modelo', '$fabricante', '$valor', '$capacidade', '$velocidade', '$img')";
        mysqli_query($conexao, $insertQuery);

        // Retorna uma resposta de sucesso
        echo json_encode(["success" => true]);
        exit();
    } else {
        // Se o usuário não tiver dinheiro suficiente, retorne uma resposta de erro
        echo json_encode(["success" => false, "message" => "Saldo insuficiente"]);
        exit();
    }
} else {
    // Se não encontrar o usuário, retorne uma resposta de erro
    echo json_encode(["success" => false, "message" => "Usuário não encontrado"]);
    exit();
}

// Função para verificar se o ID já existe no banco de dados
function idExists($conexao, $aviao_id) {
    $query = "SELECT COUNT(*) AS count FROM logsaviao WHERE aviao_id = '$aviao_id'";
    $result = mysqli_query($conexao, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['count'] > 0;
}

// Função para gerar um ID único para o avião
function generateUniqueID() {
    return '#' . substr(uniqid(), -5) . '-LA';
}

mysqli_close($conexao);
?>
