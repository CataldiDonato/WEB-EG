<?php
require_once __DIR__ . '/../../vendor/autoload.php';
include '../validarjwt.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;


if (!isset($_COOKIE['token'])) {
    header("Location: login.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="admin-menu.css">
    <link rel="stylesheet" href="../../assets/css/bootstrap-css/bootstrap.min.css">
</head>

<body>
    <div class="body-admin">
        <div class="admin-menu">
            <h1>Menú Administrador</h1>
            <a href="./locales/gestion-locales.php" class="btn btn-primary">
                🏬 Gestión de Locales
            </a>
            <a href="./activar-promociones/lista-promociones.php" class="btn btn-success">
                🎁 Activar Promociones
            </a>
            <a href="./activar-cuentas-locales/lista-dueños-locales.php" class="btn btn-warning">
                👤 Activar Cuenta de Dueños
            </a>
            <a href="./crear-novedades/novedades.php" class="btn btn-info">
                📰 Gestionar Novedades
            </a>
            <a href="./gestionar-promociones-destacadas/promocionesDestacadas.php" class="btn btn-secondary">
                🗞️​ Promociones destacadas
            </a>
            <a href="../perfil.php" class="btn btn-danger">
                🛒 volver al perfil
            </a>
        </div>
    </div>
    <footer class="bg-dark text-white text-center py-3">
        <?php include '../footer.php'; ?>
    </footer>
</body>


</html>