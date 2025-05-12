<?php
include '../../include/db.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM promociones WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    $stmt->execute();
    header("Location: ../dueño/menu-dueño.php");
}
?>