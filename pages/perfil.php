<?php
include '../include/db.php';
require '../php-jwt-token/php-jwt-login/vendor/autoload.php'; 
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
$key ='MESSI';
if(isset($_COOKIE['token'])){
    $token = $_COOKIE['token'];
    $decoded = JWT::decode($token, new Key($key, 'HS256'));   
}
$sql_check = "SELECT * FROM users WHERE emailUser = ?";
$stmt = $conn->prepare($sql_check);
$stmt->bind_param("s", $decoded->data->emailUser);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();
if ($usuario['validado'] == 0){
    $usuarioValidado = false;
} else {
    $usuarioValidado = true;
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
        <h2>Bienvenido, <?php echo $decoded->data->emailUser; ?></h2>
        <p>Tipo de usuario: <?php echo $decoded->data->tipoUser; ?></p>
        <?php echo $usuarioValidado ? 'Email Validado' : 'Valida tu Email para poder acceder a las promociones'; ?>
    </div>
</body>
</html>