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
