<?php
include '../include/db.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

if (class_exists(JWT::class)) {
    echo "JWT cargado correctamente.";
} else {
    echo "JWT NO cargado.";
}

use Firebase\JWT\JWT;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


/*

Tabla de usuarios para la base de datos (la base se llama dbweb)
Si usas el login cambia la contraseña por la tuya y el puerto que uses vs

'CREATE TABLE `usuarios` (
  `idUser` int NOT NULL AUTO_INCREMENT,
  `emailUser` varchar(50) NOT NULL,
  `pasUser` varchar(255) DEFAULT NULL,
  `tipoUser` varchar(15) NOT NULL,
  `validado` tinyint(1) NOT NULL,
  `fechaIngreso` date NOT NULL,
  `categoriaUser` varchar(15) NOT NULL,
  `aprobado` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`idUser`)
) ;


*/


$error = '';  // Variable para almacenar el mensaje de error

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
                //$data = $stmt->fetch(PDO::FETCH_ASSOC);
                $keys = 'MESSI';
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
                    header("Location:dashboard.php");
                
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link rel="stylesheet" href="../assets/css/style-login.css">
    <title>login</title>
</head>
<body>
    <div action="" id="login">
        <div class="container">
            <div id="login-row" class="row justify-content-center align-items-center">
                <div id="login-column" class="col-md-6">
                    <div id="login-box" class="col-md-12">
                        <form id="login-form" class="form" action="" method="post">
                            <h3 class="text-center text-login">Login</h3>
                            
                            <!-- Mostrar mensaje de error si existe -->
                            <?php if (!empty($error)) echo $error; ?>

                            <div class="form-group">
                                <label for="email" class="text-login">Email:</label><br>
                                <input type="text" name="email" id="email" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="password" class="text-login">Password:</label><br>
                                <input type="password" name="password" id="password" class="form-control">
                            </div>
                            <div class="form-group">
                                <input type="submit" name="submit" class="buton-login" value="submit">
                                <a href="register.php" class="text-login">Register here</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>


