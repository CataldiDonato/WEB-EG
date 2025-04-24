<?php
include '../include/db.php';

if (isset($_POST['submit'])) {
    $email = $_POST['email'] ?? '';
    $tipoUsuario = $_POST['tipoUser'] ?? "";
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $errors = [];

    if ($password !== $confirm_password) {
        $errors[] = "Las contraseñas no coinciden.";
    }
    if (empty($email) || empty($password) || empty($confirm_password) || empty($tipoUsuario)) {
        $errors[] = "Por favor, complete todos los campos.";
    }

    if (strlen($password) < 8) {
        $errors[] = "La contraseña debe tener al menos 8 caracteres.";
    }

    $sql_check = "SELECT * FROM usuarios WHERE emailUser = ?";
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
    }
    

    if (count($errors) === 0) { 
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $validado = 0;
        $aprobado = 0; 
        if ($tipoUsuario =="cliente"){
            $categoria = "Inicial";
        }else{
            $categoria = "Dueño";
        }
        $sql_insert = "INSERT INTO usuarios (emailUser, pasUser, tipoUser, validado, fechaIngreso, categoriaUser, aprobado)
               VALUES (?, ?, ?, ?, NOW(), ?, ?)";
        $stmt = $conn->prepare($sql_insert);
        $stmt->bind_param("sssisi", $email, $password_hash, $tipoUsuario, $validado, $categoria, $aprobado);

        if ($stmt->execute()) {
            echo "Usuario registrado con éxito.";
            header("Location: login.php");
            exit();
        } else {
            echo "Error al registrar: " . $stmt->error;
        }
    }

    $stmt->close();
    $conn->close();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link rel="stylesheet" href="../assets/css/style-register.css">
</head>
<body>
    
    <div class="container">
        <div id="register-row" class="row justify-content-center align-items-center">
            <div id="register-column" class="col-md-6">
                <div id="register-box" class="col-md-12">
                    <form id="register-form" class="form" action="register.php" method="post">
                        <h3 class="text-center text-reg">Create Account</h3>
                        <div class="form-group mb-3">
                            <label for="email" class="text-reg">Email:</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>
                        <div id="tipoUser">
                            <input type="radio" name="tipoUser" id="cliente" value="cliente" checked>
                            <label for="cliente" class="text-reg">Cliente</label><br>
                            <input type="radio" name="tipoUser" id="duenio" value="dueño">
                            <label for="duenio" class="text-reg">Dueño</label><br>
                        </div>
                        <div class="form-group mb-3">
                            <label for="password" class="text-reg">Password:</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="confirm-password" class="text-reg">Confirm Password:</label>
                            <input type="password" name="confirm_password" id="confirm-password" class="form-control" required>
                        </div>
                        <div class="form-group text-center">
                            <input type="submit" name="submit" class="buton-register" value="Register">
                        </div>
                        <div id="login-link" class="text-center mt-2">
                            <a href="login.php" class="text-reg">Already have an account? Login here</a>
                        </div>
                        
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
