<?php
if (!isset($_COOKIE['token'])) {
    header("Location: ../../login.php");
    exit();
}

$token = $_COOKIE['token'];
$clave_secreta = "MESSI"; 

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
