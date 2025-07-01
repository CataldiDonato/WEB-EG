<?php

if (!isset($_COOKIE['token'])) {
    header("Location: login.php");
    exit();
}

$token = $_COOKIE['token'];
$clave_secreta = "MESSI"; 

try {
    
    $decoded = JWT::decode($token, new Key($clave_secreta, 'HS256'));

    $id_tipo = $decoded->data->id_tipo ?? null;
} catch (Exception $e) {
    header("Location: login.php");
    exit();
}

include '../include/db.php';

$email = $_GET['email'] ?? '';
$token = $_GET['token'] ?? '';
$mensaje = '';
$validado = false;

if ($email && $token) {
    $query = "SELECT * FROM users WHERE emailUser = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        if ($row = $result->fetch_assoc()) {
            if ($row['tokenValidacionCorreo'] === $token) {
                $update = "UPDATE users SET validado = 1 WHERE tokenValidacionCorreo = ?";
                $stmt = $conn->prepare($update);
                $stmt->bind_param("s", $token);
                $stmt->execute();
                $mensaje = "✔️ Correo validado correctamente.";
                $validado = true;
            } else {
                $mensaje = "❌ Token inválido.";
            }
        }
    } else {
        $mensaje = "❌ Correo no encontrado.";
    }
} else {
    $mensaje = "❌ Parámetros incompletos.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Validación de Correo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="card shadow p-4 text-center" style="max-width: 500px; width: 100%;">
        <h2 class="mb-3">Verificación de Email</h2>
        <div class="alert <?= $validado ? 'alert-success' : 'alert-danger' ?>" role="alert">
            <?= htmlspecialchars($mensaje) ?>
        </div>
        <?php if ($validado): ?>
            <a href="login.php" class="btn btn-primary">Ir al Login</a>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
