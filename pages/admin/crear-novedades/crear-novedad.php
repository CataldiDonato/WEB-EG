<?php
if (!isset($_COOKIE['token'])) {
    header("Location: ../../login.php");
    exit();
}



$token = $_COOKIE['token'];
$clave_secreta = $_ENV['CLAVE']; 

try {
    $decoded = JWT::decode($token, new Key($clave_secreta, 'HS256'));

    $id_tipo = $decoded->data->id_tipo ?? null;

    if ($id_tipo !== 1) {
        header("Location: ../../dashboard.php");
        exit();
    }

} catch (Exception $e) {
    header("Location: ../../login.php");
    exit();
}

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
