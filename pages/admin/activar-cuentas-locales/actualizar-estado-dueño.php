<?php
include '../../validarjwt.php';

include("../../../include/db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $accion = $_POST['accion'];
    $nuevoEstado = $accion === 'aprobar' ? 1 : 0;
    $stmt = $conn->prepare("UPDATE users SET aprobado = ? WHERE id = ? AND id_tipo = 3");
    $stmt->bind_param("ii", $nuevoEstado, $id);
    $stmt->execute();

    header("Location: lista-dueños-locales.php");
    exit;
}
?>
