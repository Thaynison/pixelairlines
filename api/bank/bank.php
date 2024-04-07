
<?php
// Iniciar a sessão
include "../private/conexao.php";
session_start();

// Verificar se o usuário está logado, se não, redirecionar para a página de login
if (!isset($_SESSION["username"])) {
    header("location: ../game/login.php");
    exit();
}

// Incluir o arquivo de conexão com o banco de dados
// Obter o nome de usuário da sessão
$username = $_SESSION["username"];

// Consultar o banco de dados para obter os dados do usuário
$query = "SELECT * FROM usuarios WHERE email = '$username' OR usuario = '$username'";
$result = mysqli_query($conexao, $query);

// Verificar se encontrou algum registro
if (mysqli_num_rows($result) == 1) {
    $dadosUsuario = mysqli_fetch_assoc($result);
    // Dados do usuário
    $name = $dadosUsuario["usuario"]; // Alterado para "nome" em vez de "email"
    $money = $dadosUsuario["money"];
    $coins = $dadosUsuario["coins"];
    $vip = $dadosUsuario["vip"];
    $icon = $dadosUsuario["icon"];
} else {
    // Se não encontrar o usuário, redirecionar para a página de login
    header("location: ../index.php");
    exit();
}

// Fechar a conexão com o banco de dados
mysqli_close($conexao);
?>

<!doctype html>
<html lang="pt">
  <head>
    <!-- Meta tags obrigatórias -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="https://i.imgur.com/ne2cLMe.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font-awesome.min.css">
    <title>Pixel Airlines - Shops</title>
  </head>
  <body>
    <header>
      <!-- Navbar -->
      <nav class="navbar navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
        <a class="navbar-brand" href="#">Shop Aviões</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasDarkNavbar" aria-controls="offcanvasDarkNavbar" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="offcanvasDarkNavbar" aria-labelledby="offcanvasDarkNavbarLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasDarkNavbarLabel">Olá, <?php echo $name; ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                    <li class="nav-item">
                    <h1 class="nav-link active" href="">Grupo: <?php echo $vip; ?></h1>
                </li>
                <li class="nav-item">
                    <h1 class="nav-link" href="">Saldo: R$ <?php echo number_format($money, 2, ',', '.'); ?></h1>
                </li>
                <li class="nav-item">
                    <h1 class="nav-link" href="">Coins: $ <?php echo number_format($coins, 2, ',', '.'); ?></h1>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Mais Opções
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark">
                    <li><a class="dropdown-item" href="../game/index.php">Principal</a></li>
                    <li><a class="dropdown-item" href="#">Banco do Airlines</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="../private/logout.php">Logout</a></li>
                    </ul>
                </li>
                </ul>
                <button class="btn btn-primary mt-3" id="salvarLocal" hidden>Salvar Aeroporto</button>
                <form class="d-flex mt-3" role="search">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-success" type="submit">Search</button>
                </form>
            </div>
            </div>
        </div>
        </nav>
    </header>

    <!-- Pricing section -->
    <div class="container py-5" style="margin-top: 50px;">
      <div class="row row-cols-1 row-cols-md-3 mb-3 text-center">
        <script src="bank.js"></script>
      </div>
    </div>

    <!-- Rodapé aprimorado -->
    <footer class="bg-dark text-white fixed-bottom">
      <div class="container py-2">
        <div class="row">
          <div class="col-12">
            <p class="text-center mb-0">© 2024 Latam Airlines - Todos os direitos reservados</p>
            <p class="text-center mb-0">Siga-nos nas redes sociais: <a href="#" class="text-white me-2"><i class="bi bi-facebook"></i></a> <a href="#" class="text-white me-2"><i class="bi bi-twitter"></i></a> <a href="#" class="text-white me-2"><i class="bi bi-instagram"></i></a></p>
          </div>
        </div>
      </div>
    </footer>

    <!-- Bootstrap Icons -->
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">Confirmação de Compra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="modalMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="confirmButton" class="btn btn-primary">Confirmar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="errorModalLabel">Erro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="errorMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">Compra Efetuada</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="successMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
