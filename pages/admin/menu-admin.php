<?php
require_once __DIR__ . '/../../vendor/autoload.php';


use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Verificar que exista la cookie
if (!isset($_COOKIE['token'])) {
    header("Location: ../login.php");
    exit();
}

$token = $_COOKIE['token'];
$clave_secreta = "MESSI"; // misma usada al generar el token

try {
    // Decodificar el token
    $decoded = JWT::decode($token, new Key($clave_secreta, 'HS256'));

    // Extraer el tipo de usuario
    $id_tipo = $decoded->data->id_tipo ?? null;

    // Verificar si es admin (id_tipo == 1)
    if ($id_tipo !== 1) {
        header("Location: ../dashboard.php");
        exit();
    }

    // Si pasó todas las verificaciones, mostrar la página normalmente
} catch (Exception $e) {
    // Token inválido o expirado
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
</head>
<body class="body-admin">
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
        <a href="../perfil.php" class="btn btn-danger">
            🛒 volver al perfil
        </a>
    </div>
</body>
</html>
