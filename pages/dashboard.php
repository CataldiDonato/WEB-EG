<?php
session_start();
require '../php-jwt-token/php-jwt-login/vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
$key ='MESSI';
if(isset($_COOKIE['token'])){
    $token = $_COOKIE['token'];
    $decoded = JWT::decode($token, new Key($key, 'HS256'));
}else{
    header('Location: login.php');
    exit();
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
    <h1>Bienvenido a tu dashboard<b><?php echo $decoded->data->emailUser; ?></b></h1>
    <a href="logout.php">Cerrar sesión</a>
</body>
</html>