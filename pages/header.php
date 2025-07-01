<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
require '../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$key = 'MESSI';
if (isset($_COOKIE['token'])) {
  $token = $_COOKIE['token'];
  $decoded = JWT::decode($token, new Key($key, 'HS256'));
  $usuario_autenticado = true;
} else {
  $usuario_autenticado = false;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title></title>
  <link rel="stylesheet" href="../assets/css/style-header.css">
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow">
    <div class="container-fluid">
      <a class="navbar-brand" id="titleHeader" href="#">PASEO DEL SOL</a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mx-auto">
          <li class="nav-item">
            <a class="nav-link" href="dashboard.php" id="linkHeader">Inicio</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="promociones.php" id="linkHeader">Promociones</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="contacto.php" id="linkHeader">Contactanos</a>
          </li>
  
        </ul>

        <div class="d-flex">
          <?php if ($usuario_autenticado): ?>
            <a href="perfil.php" class="btn me-2 btn-success">Perfil</a>
            <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
          <?php else: ?>
            <a href="login.php" class="btn btn-outline-primary me-2">Ingresar</a>
            <a href="register.php" class="btn btn-primary">Registrarse</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </nav>
</body>

</html>