<?php
include("../../../include/db.php");

$textoNovedad = $_POST['textoNovedad'];
$fechaDesdeNovedad = $_POST['fechaDesdeNovedad'];
$fechaHastaNovedad = $_POST['fechaHastaNovedad'];


$sql = "INSERT INTO novedades (textoNovedad, fechaDesdeNovedad , fechaHastaNovedad) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $textoNovedad, $fechaDesdeNovedad , $fechaHastaNovedad);
$stmt->execute();

header("Location: novedades.php?agregado=ok");
exit();