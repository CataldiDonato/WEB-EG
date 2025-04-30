<?php
include("../../../include/db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codLocal = $_POST['codLocal'];
    $nombreLocal = $_POST['nombreLocal'];
    $ubicacionLocal = $_POST['ubicacionLocal'];
    $rubroLocal = $_POST['rubroLocal'];
    $codUsuario = $_POST['codUsuario'];

    $stmt = $conn->prepare("UPDATE locales SET nombreLocal = ?, ubicacionLocal = ?, rubroLocal = ?, codUsuario = ? WHERE codLocal = ?");
    $stmt->bind_param("ssssi", $nombreLocal, $ubicacionLocal, $rubroLocal, $codUsuario, $codLocal);
    $stmt->execute();

    header("Location: gestion-locales.php");
    exit();
}
?>
