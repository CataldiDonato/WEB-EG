<?php
include("../../../include/db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nombreLocal = $_POST['nombreLocal'];
    $ubicacionLocal = $_POST['ubicacionLocal'];
    $rubroLocal = $_POST['rubroLocal'];
    $codUsuario = $_POST['codUsuario'];

    $stmt = $conn->prepare("UPDATE locales SET nombreLocal = ?, ubicacionLocal = ?, rubroLocal = ?, codUsuario = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $nombreLocal, $ubicacionLocal, $rubroLocal, $codUsuario, $id);
    $stmt->execute();

    header("Location: gestion-locales.php");
    exit();
}
?>
