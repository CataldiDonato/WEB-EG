<?php
include("../../../include/db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['accion'])) {
    $id = (int) $_POST['id'];
    $accion = $_POST['accion'];
    if ($accion === 'aprobar') {
        $nuevoEstado = 'aprobada';
    } elseif ($accion === 'denegar') {
        $nuevoEstado = 'denegada';
    } else {
        header("Location: lista-promociones.php");
        exit;
    }
    $stmt = $conn->prepare("UPDATE promociones SET estadoPromo = ? WHERE id = ?");
    $stmt->bind_param("si", $nuevoEstado, $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: lista-promociones.php");
exit;
?>
