<?php
require_once __DIR__ . '/../../vendor/autoload.php';
include 'validarjwtdueño.php';
include '../../include/db.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Verifica que el usuario sea dueño (id_tipo == 3)
$token = $_COOKIE['token'] ?? '';
$clave_secreta = $_ENV['CLAVE'];
$idUser = null;
$id_tipo = null;

try {
    $decoded = JWT::decode($token, new Key($clave_secreta, 'HS256'));
    $idUser = $decoded->data->idUser ?? null;
    $id_tipo = $decoded->data->id_tipo ?? null;
    if ($id_tipo !== 3) {
        header("Location: ../dashboard.php");
        exit();
    }
} catch (Exception $e) {
    header("Location: ../login.php");
    exit();
}

// Verifica si el dueño tiene local asignado
$tieneLocal = false;
if ($idUser) {
    $sql = "SELECT id FROM locales WHERE codUsuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idUser);
    $stmt->execute();
    $resultado = $stmt->get_result();
    if ($resultado->num_rows > 0) {
        $tieneLocal = true;
    }
    $stmt->close();
}

if (!$tieneLocal) {
    // Muestra mensaje y no carga el menú
    echo '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Acceso Denegado</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
        <div class="container py-5">
            <div class="alert alert-warning text-center">
                <h4>Acceso restringido</h4>
                <p>Tu usuario no tiene un local asignado. Solicita a un administrador que te asigne un local para acceder al menú de dueño.</p>
                <a href="../perfil.php" class="btn btn-primary mt-3">Volver al perfil</a>
            </div>
        </div>
    </body>
    </html>';
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
                ✅ Aprobar uso de promocion
            </a>
            <a href="imprimirpromociones.php" class="btn btn-info">
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