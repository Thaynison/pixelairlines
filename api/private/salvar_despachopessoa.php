    <?php
    include "../private/conexao.php";
    session_start();

    date_default_timezone_set('America/Sao_Paulo');

    if (!isset($_SESSION["username"])) {
        echo json_encode(["success" => false, "message" => "Usuário não está logado"]);
        exit();
    }

    $username = $_SESSION["username"];

    $data = json_decode(file_get_contents("php://input"), true);

    $rota_id = $data['rota_id'];
    $pontoA = $data['pontoA'];
    $pontoB = $data['pontoB'];
    $tempo = $data['tempo'];
    $receber = $data['receber'];
    $aviao_id = $data['aviao_id'];
    $modelo = $data['modelo'];

    $query = "SELECT * FROM usuarios WHERE email = '$username' OR usuario = '$username'";
    $result = mysqli_query($conexao, $query);

    if (mysqli_num_rows($result) == 1) {
        $dadosUsuario = mysqli_fetch_assoc($result);
        $vip = $dadosUsuario["vip"];
        $money = $dadosUsuario["money"];

        $novoSaldo = $money + $receber;

        $agora = date('Y-m-d H:i:s');

        $updateQuery = "UPDATE usuarios SET money = '$novoSaldo' WHERE email = '$username' OR usuario = '$username'";
        mysqli_query($conexao, $updateQuery);

        $insertQuery = "INSERT INTO logsrotas (usuario, aviao_id, rota_id, pontaA, pontoB, tempo, receber, liberacao_valor) VALUES ('$username', '$aviao_id', '$rota_id', '$pontoA', '$pontoB', '$agora', '$receber', '$tempo')";
        mysqli_query($conexao, $insertQuery);

        echo json_encode(["success" => true]);
        exit();
    } else {
        echo json_encode(["success" => false, "message" => "Usuário não encontrado"]);
        exit();
    }

    mysqli_close($conexao);
    ?>

