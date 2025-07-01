<?php
if (!isset($_COOKIE['token'])) {
    header("Location: ../login.php");
    exit();
}


$token = $_COOKIE['token'];
$clave_secreta = "MESSI"; 

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

if (isset($_GET['id'])) {
    $idUso = intval($_GET['id']);

    $sql = "UPDATE usopromociones SET estado = 'aprobada' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idUso);
    $stmt->execute();

    header("Location: mostrarusopromociones.php?promo=ok");
    exit();
}
