<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../include/db.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

if (isset($_POST['submit'])) {
    $email = $_POST['email'] ?? '';
    $tipoUsuario = $_POST['tipoUser'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $errors = [];

    if (empty($email) || empty($password) || empty($confirm_password) || empty($tipoUsuario)) {
        $errors[] = "Por favor, complete todos los campos.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Las contraseñas no coinciden.";
    }

    if (strlen($password) < 8) {
        $errors[] = "La contraseña debe tener al menos 8 caracteres.";
    }

    $sql_check = "SELECT * FROM users WHERE emailUser = ?";
    $stmt = $conn->prepare($sql_check);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $errors[] = "El correo ya está registrado.";
    }

    if (!empty($errors)) {
        echo '<div class="alert alert-danger">';
        foreach ($errors as $error) {
            echo "<p>$error</p>";
        }
        echo '</div>';
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $validado = 0;
        $aprobado = 0;
        $tokenValidacionCorreo = bin2hex(random_bytes(32));

        if ($tipoUsuario === "cliente") {
            $tipoUsuario = 2;
            $categoria = 1;
        } else {
            $tipoUsuario = 3;
            $categoria = 3;
        }

        $sql_insert = "INSERT INTO users (id_tipo, emailUser, pasUser, validado, fechaIngreso, idCategoria, aprobado, tokenValidacionCorreo)
            VALUES (?, ?, ?, ?, NOW(), ?, ?, ?)";
        $stmt = $conn->prepare($sql_insert);
        $stmt->bind_param("issiiis", $tipoUsuario, $email, $password_hash, $validado, $categoria, $aprobado, $tokenValidacionCorreo);

        if ($stmt->execute()) {
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
                $mail->Subject = 'Valida tu cuenta';
                $mail->Body    = "Hola,<br><br>Haz clic en el siguiente enlace para validar tu correo:<br><br>
                    <a href='http://shoppingdelsol.techphite.com/pages/validarEmail.php?email=$email&token=$tokenValidacionCorreo'>
                    Validar cuenta</a>";

                $mail->send();
                header("Location: login.php");
                exit();
            } catch (Exception $e) {
                echo "Error al enviar el correo: {$mail->ErrorInfo}";
                exit();
            }
        } else {
            echo "Error al registrar: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style-register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .eye-btn {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
        }

        .position-relative {
            position: relative;
        }
    </style>
</head>

<body>
    <div class="container">
        <div id="register-row" class="row justify-content-center align-items-center">
            <div id="register-column" class="col-md-6">
                <div id="register-box" class="col-md-12">
                    <form id="register-form" class="form" action="register.php" method="post">
                        <h3 class="text-center text-reg">Crea tu cuenta</h3>

                        <div class="form-group mb-3">
                            <label for="email" class="text-reg">Correo electrónico:</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="tipoUser" class="text-reg">Tipo de cuenta:</label>
                            <select name="tipoUser" id="tipoUser" class="form-control">
                                <option value="cliente">Cliente</option>
                                <option value="duenio">Dueño</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="password" class="text-reg">Contraseña:</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control" required>
                                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="password" tabindex="-1">
                                    <span class="fa fa-eye"></span>
                                </button>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="confirm-password" class="text-reg">Confirmar contraseña:</label>
                            <div class="input-group">
                                <input type="password" name="confirm_password" id="confirm-password" class="form-control" required>
                                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="confirm-password" tabindex="-1">
                                    <span class="fa fa-eye"></span>
                                </button>
                            </div>
                        </div>
                        <div class="form-group text-center">
                            <input type="submit" name="submit" class="buton-register" value="Registrarse" style="border-radius: 5px; background-color: rgba(226,226,226);">
                        </div>
                        <div class="text-center mt-2">
                            <a href="login.php" class="text-reg link-register">¿Ya tienes una cuenta? Inicia sesión aquí.</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.toggle-password').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const targetId = btn.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const icon = btn.querySelector('span');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
    </script>
</body>

</html>