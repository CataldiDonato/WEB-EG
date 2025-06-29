<?php
// Verificar que exista la cookie
if (!isset($_COOKIE['token'])) {
    header("Location: ../../login.php");
    exit();
}

$token = $_COOKIE['token'];
$clave_secreta = "MESSI"; // misma usada al generar el token

try {
    // Decodificar el token
    $decoded = JWT::decode($token, new Key($clave_secreta, 'HS256'));

    // Extraer el tipo de usuario
    $id_tipo = $decoded->data->id_tipo ?? null;

    // Verificar si es admin (id_tipo == 1)
    if ($id_tipo !== 1) {
        header("Location: ../../dashboard.php");
        exit();
    }

    // Si pasó todas las verificaciones, mostrar la página normalmente
} catch (Exception $e) {
    // Token inválido o expirado
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
$stmt->bind_param("sssi", $textoNovedad, $fechaDesdeNovedad , $fechaHastaNovedad, $categoriaCliente);
$stmt->execute();

header("Location: novedades.php?agregado=ok");
exit();