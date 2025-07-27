<?php
include '../include/db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);
$mensaje = '';

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

        // Guardar el token en la base de datos
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

        // Enviar el correo
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
            $mail->Body    = "Hola,<br><br>Hacé clic en el siguiente enlace para recuperar tu contraseña:<br><br>
                <a href='http://shoppingdelsol.techphite.com/pages/restaurarContraseña.php?email=$email&token=$tokenOlvidoContraseña'>
                Recuperar contraseña</a>";

            $mail->send();
            // Redirigir o mostrar mensaje
            header("Location: login.php?msg=correo_enviado");
            exit();
        } catch (Exception $e) {
            $mensaje = "Error al enviar el correo: {$mail->ErrorInfo}";
        }
    } else {
        $mensaje = "Ese email no está registrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Olvidé mi contraseña</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" />
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="../assets/css/style-login.css" />
</head>

<body>
    <div id="olvidecontraseña">
        <div class="container">
            <div id="olvidecontraseña-row" class="row justify-content-center align-items-center" style="min-height: 100vh;">
                <div id="olvidecontraseña-column" class="col-md-6">
                    <div id="olvidecontraseña-box" class="col-md-12">
                        <form id="olvidecontraseña-form" class="form" method="post" action="">
                            <h3 class="text-center text-login mb-4">Recuperar contraseña</h3>
                            <?php if (!empty($mensaje)): ?>
                                <div class="alert alert-danger"><?= htmlspecialchars($mensaje) ?></div>
                            <?php endif; ?>
                            <div class="form-group">
                                <label for="email" class="text-login">Correo electrónico:</label>
                                <input type="email" name="email" id="email" class="form-control" required />
                            </div>
                            <div class="form-group">
                                <button type="submit" name="submit" class="buton-login btn btn-block" style="border-radius: 5px; background-color: rgba(226,226,226);">
                                    Enviar enlace de recuperación
                                </button>
                            </div>
                            <div class="text-center">
                                <a href="login.php" class="text-login link-register">Volver al login</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
