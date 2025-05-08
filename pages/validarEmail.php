<?php
include '../include/db.php';

$email = $_GET['email'];
$token = $_GET['token'];

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
            echo "Correo validado correctamente.";
        } else {
            echo "Token inválido.";
            exit;
        }
    }
} else {
    echo "Correo no encontrado.";
}
//http://localhost/web-eg/pages/validarEmail.php?email=donato@gmail.com&token=76138a9d41fcd1c34e9fb09ca0d87b6819860af51c583dc11c142d9c7e176b36