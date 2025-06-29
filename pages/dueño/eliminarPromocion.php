<?php
// Verificar que exista la cookie
if (!isset($_COOKIE['token'])) {
    header("Location: ../login.php");
    exit();
}

// use Firebase\JWT\JWT;
// use Firebase\JWT\Key;


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

if (isset($_GET['idPromo'])) {
    $id = $_GET['idPromo'];
    $sql = "DELETE FROM promociones WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: ver_promociones.php?eliminado=ok");
}
