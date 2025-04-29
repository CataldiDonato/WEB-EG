<?php
include("../../../include/db.php");

$nombreLocal = $_POST['nombreLocal'];
$ubicacionLocal = $_POST['ubicacionLocal'];
$rubroLocal = $_POST['rubroLocal'];
$codUsuario = $_POST['codUsuario'];

$sql = "INSERT INTO locales (nombreLocal, ubicacionLocal , rubroLocal, codUsuario) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $nombreLocal, $ubicacionLocal , $rubroLocal, $codUsuario);
$stmt->execute();

header("Location: gestion-locales.php");
exit();
