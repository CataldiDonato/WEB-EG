<?php
include '../include/db.php';

$mensaje = '';
$email = $_GET['email'] ?? '';
$token = $_GET['token'] ?? '';

if (!$email || !$token) {
    die("Parámetros inválidos.");
}

// Verificar si el token es válido
$sql = "SELECT * FROM recuperacion_tokens WHERE email = ? AND token = ? AND expiracion > NOW()";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $email, $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Token inválido o expirado.");
}

// Si envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevaPass = $_POST['nueva_pass'] ?? '';
    $repetirPass = $_POST['repetir_pass'] ?? '';

    if ($nuevaPass !== $repetirPass) {
        $mensaje = "Las contraseñas no coinciden.";
    } elseif (strlen($nuevaPass) < 6) {
        $mensaje = "La contraseña debe tener al menos 6 caracteres.";
    } else {
        // Actualizar la contraseña
        $hash = password_hash($nuevaPass, PASSWORD_DEFAULT);
        $update = "UPDATE users SET passUser = ? WHERE emailUser = ?";
        $stmtUpdate = $conn->prepare($update);
        $stmtUpdate->bind_param("ss", $hash, $email);
        $stmtUpdate->execute();

        // Borrar el token
        $delete = "DELETE FROM recuperacion_tokens WHERE email = ?";
        $stmtDelete = $conn->prepare($delete);
        $stmtDelete->bind_param("s", $email);
        $stmtDelete->execute();

        // Redirigir al login
        header("Location: login.php?msg=recuperacion_ok");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restaurar contraseña</title>
</head>
<body>
    <h2>Restaurar contraseña</h2>
    <?php if ($mensaje): ?>
        <p style="color:red"><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>
    <form method="post">
        <label for="nueva_pass">Nueva contraseña:</label>
        <input type="password" name="nueva_pass" id="nueva_pass" required><br><br>

        <label for="repetir_pass">Repetir contraseña:</label>
        <input type="password" name="repetir_pass" id="repetir_pass" required><br><br>

        <button type="submit">Guardar nueva contraseña</button>
    </form>
</body>
</html>
