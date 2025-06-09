<?php
include '../include/db.php';
session_start();
// // require '../php-jwt-token/php-jwt-login/vendor/autoload.php'; 
// //session_start();
// // use Firebase\JWT\JWT;
// // use Firebase\JWT\Key;
// // $key ='MESSI';
// // if(isset($_COOKIE['token'])){
// //     $token = $_COOKIE['token'];
// //     $decoded = JWT::decode($token, new Key($key, 'HS256'));   
// // }
// $sql_check = "SELECT * FROM users WHERE emailUser = ?";
// $stmt = $conn->prepare($sql_check);
// $stmt->bind_param("s", $_SESSION['emailUser']);
// $stmt->execute();
// $resultado = $stmt->get_result();
// var_dump($usuario);
// foreach ($usuario as $campo => $valor) {
//     echo $campo . ": " . $valor . "<br>";
// }
// $usuario = $resultado->fetch_assoc();

// // if ($usuario) {
// //     echo '<pre>';
// //     var_dump($usuario);  // o print_r($usuario); si prefieres
// //     echo '</pre>';
// // } else {
// //     echo "No se encontraron datos para este usuario.";
// // }

// if ($usuario['validado'] == 0){
//     $usuarioValidado = false;
// } else {
//     $usuarioValidado = true;
// }

$email=$_SESSION['emailUser'];
$sql_check = "SELECT * FROM users WHERE emailUser = ?";
$stmt = $conn->prepare($sql_check);
$stmt->bind_param("s", $email);
$stmt->execute();
$resultado = $stmt->get_result();
//$usuarioValidado = $usuario['validado'] ;
if ($resultado->num_rows === 1) {
    $usuario = $resultado->fetch_assoc();
    $usuarioValidado = $usuario['validado'];
} else {
    echo "No se encontró el usuario en la base de datos.";
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <link rel="stylesheet" href="../assets/css/bootstrap-css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style-header.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container mt-4">
        <h1 class="text-center mb-4">Perfil</h1>

        <div class="card mb-4">
            <div class="card-body">
                <h2 class="card-title">Bienvenido, <?php echo $_SESSION['emailUser']; ?></h2>
                <p class="card-text">Tipo de usuario: <strong><?php echo $_SESSION['tipoUser']; ?></strong></p>
                <p class="card-text">
                    <?php 
                    echo $usuarioValidado ? 
                        '<span class="badge bg-success">Email Validado</span>' : 
                        '<span class="badge bg-warning text-dark">Valida tu Email para poder acceder a las promociones</span>'; 
                    ?>
                </p>
            </div>
        </div>
        <!-- el id 3 era admin -->
        <?php if ($_SESSION["tipoUser"] == 3): ?>
            <div class="d-flex gap-3 mb-4">
                <!-- cambiar ruta a admin/menu-admin.php -->
                <a href="dueño/menu-dueño.php" class="btn btn-primary">Ir a Panel del Dueño</a>
                <a href="dueño/mostrarusopromociones.php" class="btn btn-secondary">Ver uso de promociones</a>
            </div>
        <?php endif; ?>

        <?php if ($_SESSION["tipoUser"] == 2): ?>
            <h3 class="mb-4">Lista de promociones:</h3>

            <?php
            $sql = "SELECT * FROM usopromociones WHERE idUsuario = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $_SESSION['idUser']);
            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($resultado->num_rows > 0):
                while ($promo = $resultado->fetch_assoc()):
                    ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h4 class="card-title">Promoción: <?php echo htmlspecialchars($promo['codPromo']); ?></h4>
                            <p class="card-text">Estado: <?php echo htmlspecialchars($promo['estado']); ?></p>

                            <?php
                            $sql2 = "SELECT * FROM promociones WHERE id = ?";
                            $stmt2 = $conn->prepare($sql2);
                            $stmt2->bind_param("i", $promo['codPromo']);
                            $stmt2->execute();
                            $resultado2 = $stmt2->get_result();

                            while ($detalle = $resultado2->fetch_assoc()):
                            ?>
                                <p class="card-text"><strong>Nombre:</strong> <?php echo htmlspecialchars($detalle['textoPromo']); ?></p>
                                <p class="card-text"><strong>Desde:</strong> <?php echo htmlspecialchars($detalle['fechaDesdePromo']); ?></p>
                                <p class="card-text"><strong>Hasta:</strong> <?php echo htmlspecialchars($detalle['fechaHastaPromo']); ?></p>
                                <div class="mb-3">
                                    <strong>Imagen:</strong><br>
                                    <img src="../<?php echo $detalle['rutaImagen']; ?>" alt="Imagen de la promoción" class="img-thumbnail" style="max-width: 200px;">
                                </div>
                                <button class="btn btn-success">Descargar cupón</button>
                            <?php endwhile; ?>
                        </div>
                    </div>
                <?php endwhile;
            else:
                echo '<div class="alert alert-info">No hay promociones.</div>';
            endif;
            ?>
        <?php endif; ?>
    </div>
</body>
</html>
