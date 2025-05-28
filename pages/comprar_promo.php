<?php
include '../include/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codPromo = $_POST['codPromo'] ?? null;
    $idUsuario = $_POST['idUsuario'] ?? null;
    $estado = $_POST['estado'] ?? 'pendiente';
    $fechaUso = date('Y-m-d H:i:s');

    if ($codPromo && $idUsuario) {
        $sql = "INSERT INTO usopromociones (codPromo, fechaUso, estado, idUsuario) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issi", $codPromo, $fechaUso, $estado, $idUsuario);
        if ($stmt->execute()) {
            header("Location: promociones.php?compra=ok");
            exit;
        } else {
            echo '<div class="alert alert-danger">Error al registrar el uso de la promoción.</div>';
        }
    } else {
        echo '<div class="alert alert-danger">Datos incompletos.</div>';
    }
}
?>