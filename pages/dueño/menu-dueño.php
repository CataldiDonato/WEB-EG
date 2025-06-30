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
    if ($id_tipo !== 3) {
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
    <link rel="stylesheet" href="../admin/admin-menu.css">
    <link rel="stylesheet" href="../../assets/css/bootstrap-css/bootstrap.min.css">
</head>

<body>
    <div class="body-admin">
        <div class="admin-menu">
            <h1>Menú Dueño Local</h1>
            <a href="ver_promociones.php" class="btn btn-success">
                🎁 Ver Promociones
            </a>
            <a href="./cargar_promocion.php" class="btn btn-warning">
                🎁 Cargar Promocion
            </a>
            <a href="mostrarusopromociones.php" class="btn btn-info">
                📰 Ver uso de promociones
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