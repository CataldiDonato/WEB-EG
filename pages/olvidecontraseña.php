<?php
include '../include/db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);
if (isset($_POST['submit'])) {
    $email = $_POST['email'] ?? '';

    $sql = "SELECT * FROM users WHERE emailUser = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();
        $tokenOlvidoContraseña = bin2hex(random_bytes(50));
        $fechaCreacion = date('Y-m-d H:i:s');
        $expiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));
        try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'donatocataldicode@gmail.com';
                $mail->Password   = 'zdwi jcdm lyys coaf';
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;

                $mail->setFrom('donatocataldicode@gmail.com', 'Shopping del Sol');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Recuperación de contraseña';
                $mail->Body    = "Hola,<br><br>Ingrese para recuperar contraseña<br><br>
                    <a href='http://shoppingdelsol.techphite.com/pages/restaurarContraseña.php?email=$email&token=$tokenOlvidoContraseña'>
                    Recuperar contraseña</a>";

                $mail->send();
    
                exit();
            } catch (Exception $e) {
                echo "Error al enviar el correo: {$mail->ErrorInfo}";
                exit();
            }
        $sqlInsertToken = "
            INSERT INTO recuperacion_tokens (email, token, fecha_creacion, expiracion)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                token = VALUES(token),
                fecha_creacion = VALUES(fecha_creacion),
                expiracion = VALUES(expiracion)
        ";
        $stmtInsert = $conn->prepare($sqlInsertToken);
        $stmtInsert->bind_param("ssss", $email, $tokenOlvidoContraseña, $fechaCreacion, $expiracion);
        $stmtInsert->execute();
        $stmtInsert->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form id="olvidecontraseña" class="form" method="post">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <button type="submit">Submit</button>
    </form>
</body>
</html>