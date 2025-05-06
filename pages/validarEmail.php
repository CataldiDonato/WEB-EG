<?php
include '../include/db.php';

$email = $_GET['email'];
$token = $_GET['token'];

$query = "SELECT * FROM users WHERE emailUser = ? AND tokenValidacionCorreo = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $email, $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $update = "UPDATE users SET validado = 1, tokenValidacionCorreo = NULL WHERE emailUser = ?";
    $stmt = $conn->prepare($update);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    echo "Correo validado correctamente.";
} else {
    echo "Token inválido o correo no encontrado.";
}
