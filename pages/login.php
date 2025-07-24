<?php
include '../include/db.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

use Firebase\JWT\JWT;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$error = '';

if (isset($_POST['submit'])) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = '<div class="alert alert-danger">Por favor, complete todos los campos.</div>';
    } else {
        $sql = "SELECT * FROM users WHERE emailUser = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();

            if (password_verify($password, $usuario['pasUser'])) {
                $_SESSION['idUser'] = $usuario['id'];
                $_SESSION['emailUser'] = $usuario['emailUser'];
                $_SESSION['tipoUser'] = $usuario['id_tipo'];
                $keys = $_ENV['CLAVE']; 
                $token = JWT::encode(
                    array(
                        'iat' => time(),
                        'nbf' => time(),
                        'exp' => time() + 3600,
                        'data' => array(
                            'idUser' => $usuario['id'],
                            'emailUser' => $usuario['emailUser'],
                            'id_tipo' => $usuario['id_tipo'],
                        )
                    ),
                    $keys,
                    'HS256'
                );
                setcookie("token", $token, time() + 3600, "/", "", true, true);
                header("Location: dashboard.php");
                exit();
            } else {
                $error = '<div class="alert alert-danger">Contraseña incorrecta.</div>';
            }
        } else {
            $error = '<div class="alert alert-danger">El usuario no existe.</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style-login.css">
</head>

<body>
    <div id="login">
        <div class="container">
            <div id="login-row" class="row justify-content-center align-items-center">
                <div id="login-column" class="col-md-6">
                    <div id="login-box" class="col-md-12">
                        <form id="login-form" class="form" action="" method="post">
                            <h3 class="text-center text-login">Iniciar sesión</h3>
                            <?php if (!empty($error)) echo $error; ?>
                            <div class="form-group">
                                <label for="email" class="text-login">Correo electrónico:</label>
                                <input type="text" name="email" id="email" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="password" class="text-login">Contraseña:</label>
                                <div class="input-group">
                                    <input type="password" name="password" id="password" class="form-control">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary" id="togglePassword" tabindex="-1">
                                            <i id="icon-eye" class="fa-solid fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div>
                                    <input type="submit" name="submit" class="buton-login btn btn-block" value="Iniciar sesión" style="border-radius: 5px; background-color: rgba(226,226,226);">
                                    <a href="register.php" class="text-login link-register">¿Aún no te has registrado? Registrate aquí.</a>
                                </div>
                                <div>
                                    <input type="submit" name="olvidecontraseña" class="buton-login btn btn-block" value="olvidecontraseña" style="border-radius: 5px; background-color: rgba(226,226,226);">
                                    <a href="olvidecontraseña.php" class="text-login link-register">¿Olvidaste tu contraseña?</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

<script>
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const icon = document.getElementById('icon-eye');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
</script>

</html>