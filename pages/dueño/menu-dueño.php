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

    // Verificar si es dueño (id_tipo == 3)
    if ($id_tipo !== 3) {
        header("Location: ../../dashboard.php");
        exit();
    }

    // Si pasó todas las verificaciones, mostrar la página normalmente
} catch (Exception $e) {
    // Token inválido o expirado
    header("Location: ../../login.php");
    exit();
}

include '../../include/db.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MenuDueño</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
</head>
<body class="bg-light">
    <div class="container mt-5 text-center">
        <h2 class="mb-4">Panel del Dueño</h2>

        <div class="mb-3">
            <button class="btn btn-primary" onclick="window.location.href='../perfil.php'">Volver</button>
        </div>
        <div class="mb-3">
            <button class="btn btn-success" onclick="window.location.href='ver_promociones.php'">Ver promociones</button>
        </div>
        <div class="mb-3">
            <button class="btn btn-info" onclick="window.location.href='cargar_promocion.php'">Cargar promoción</button>
        </div>
        <div class="mb-3">
            <button class="btn btn-warning" onclick="window.location.href='mostrarusopromociones.php'">Ver uso de promociones</button>
        </div>
    </div>
</body>
</html>