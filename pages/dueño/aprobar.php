<?php
include 'validarjwtdueño.php';

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
