<?php
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