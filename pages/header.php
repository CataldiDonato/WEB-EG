<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
require '../vendor/autoload.php'; 
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
$key ='MESSI';
if(isset($_COOKIE['token'])){
    $token = $_COOKIE['token'];
    $decoded = JWT::decode($token, new Key($key, 'HS256'));
    $usuario_autenticado = true;
}else{
    $usuario_autenticado = false;
} 

?>
<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Shopping Promos</a>

    <!-- Botón hamburguesa en móvil -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Contenido de la navbar -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav mx-auto">
        <li class="nav-item">
          <a class="nav-link" href="dashboard.php">Inicio</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="promociones.php">Promociones</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="contacto.php">Contactanos</a>
        </li>
        <!-- <li class="nav-item">
          <a class="nav-link" href="novedades.php">Novedades</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="contacto.php">Contacto</a>
        </li> -->
      </ul>

      <!-- Botones a la derecha -->
      <!-- <div class="d-flex">
        <a href="login.php" class="btn btn-outline-primary me-2">Login</a>
        <a href="register.php" class="btn btn-primary">Sign-up</a>
      </div> -->
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
