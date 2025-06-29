<?php
// Verificar que exista la cookie
if (!isset($_COOKIE['token'])) {
    header("Location: ../../login.php");
    exit();
}

// use Firebase\JWT\JWT;
// use Firebase\JWT\Key;


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

$nombreLocal = $_POST['nombreLocal'];
$ubicacionLocal = $_POST['ubicacionLocal'];
$rubroLocal = $_POST['rubroLocal'];
$codUsuario = $_POST['codUsuario'];

$sql = "INSERT INTO locales (nombreLocal, ubicacionLocal , rubroLocal, codUsuario) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $nombreLocal, $ubicacionLocal, $rubroLocal, $codUsuario);
$stmt->execute();

header("Location: gestion-locales.php?agregado=ok");
exit();
