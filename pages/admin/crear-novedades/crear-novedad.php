<?php

include '../../validarjwt.php';

include("../../../include/db.php");


$textoNovedad = $_POST['textoNovedad'];
$fechaDesdeNovedad = $_POST['fechaDesdeNovedad'];
$fechaHastaNovedad = $_POST['fechaHastaNovedad'];
$categoriaCliente = $_POST['categoriaCliente'];


$sql = "INSERT INTO novedades (textoNovedad, fechaDesdeNovedad , fechaHastaNovedad, idTipoUsuario) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssi", $textoNovedad, $fechaDesdeNovedad, $fechaHastaNovedad, $categoriaCliente);
$stmt->execute();

header("Location: novedades.php?agregado=ok");
exit();
