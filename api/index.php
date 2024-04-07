<?php
include "conexao.php";
// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Incluir o arquivo de conexão com o banco de dados
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Consultar o banco de dados para verificar as credenciais
    $query = "SELECT * FROM usuarios WHERE (email = '$username' OR usuario = '$username') AND senha = '$password'";
    $result = mysqli_query($conexao, $query);

    // Verificar se encontrou algum registro
    if (mysqli_num_rows($result) == 1) {
        // Iniciar a sessão
        session_start();

        // Armazenar o nome de usuário na sessão (ou outros dados que desejar)
        $_SESSION["username"] = $username;
        $_SESSION["vip"] = $vip;

        // Redirecionar para a página do jogo
        header("location: ./game/index.php");
        exit();
    } else {
        $erro = "Usuário ou senha inválidos.";
    }

    // Fechar a conexão com o banco de dados
    mysqli_close($conexao);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pixel Airlines - Login</title>
  <link rel="icon" href="https://i.imgur.com/ne2cLMe.png" type="image/png">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <!-- Custom CSS -->
  <style>
    body {
      background-color: #f0f0f0;
    }
    .login-container {
      margin-top: 100px;
    }
    .bb-logo {
      width: 250px;
      margin-bottom: 20px;
    }
    .login-card {
      max-width: 400px;
      margin: 0 auto;
      border: 1px solid #ddd;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    .login-card-header {
      color: #fff;
      border-top-left-radius: 10px;
      border-top-right-radius: 10px;
      padding: 20px;
      text-align: center;
      color: #cb0000;
    }
    .login-card-body {
      padding: 30px;
    }
    .login-card-body form .form-group {
      margin-bottom: 20px;
    }
    .login-card-body form label {
      font-weight: bold;
    }
    .login-card-body form input[type="text"],
    .login-card-body form input[type="password"] {
      padding: 12px;
      border-radius: 5px;
      border: 1px solid #ccc;
      width: 100%;
    }
    .login-card-body form button[type="submit"] {
      padding: 12px;
      border-radius: 5px;
      border: none;
      background-color: #cb0000;
      color: #fff;
      width: 100%;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-6 login-container">
      <div class="login-card">
        <div class="login-card-header">
          <img src="https://i.imgur.com/vkvrSWk.png" alt="Latam Airlines" class="bb-logo">
          <h4 class="mb-0">Acesse sua conta</h4>
        </div>
        <div class="login-card-body">
          <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <?php if(isset($erro)): ?>
            <div class="alert alert-danger" role="alert">
              <?php echo $erro; ?>
            </div>
            <?php endif; ?>
            <div class="form-group">
              <label for="username">Código do cliente</label>
              <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
              <label for="password">Senha de 8 dígitos</label>
              <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Entrar</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS e jQuery (opcional) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
