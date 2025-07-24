<?php
if (!isset($_COOKIE['token'])) {
    header("Location: ../login.php");
    exit();
}



$token = $_COOKIE['token'];
$clave_secreta = $_ENV['CLAVE']; 

try {
    $decoded = JWT::decode($token, new Key($clave_secreta, 'HS256'));

    $id_tipo = $decoded->data->id_tipo ?? null;

    if ($id_tipo !== 3) {
        header("Location: ../../dashboard.php");
        exit();
    }

} catch (Exception $e) {
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
