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
    <?php include'header.php'; ?>
    <div>
        <h1>Perfil</h1>
    </div>
    <div>
        <h2>Bienvenido, <?php echo $_SESSION['emailUser']; ?></h2>
        <p>Tipo de usuario: <?php echo $_SESSION['tipoUser']; ?></p>
        <?php 
        echo $usuarioValidado ? 'Email Validado' : 'Valida tu Email para poder acceder a las promociones'; 
        ?><br><br><?php
        if ($_SESSION["tipoUser"] == 3) {
            echo '<a href="dueño/menu-dueño.php"><button>Ir a Panel del Dueño</button></a><br><br>';
            echo '<a href="dueño/mostrarusopromociones.php"><button>Ver uso de promociones</button></a>';
        }
        ?>
    </div>
</body>
</html>