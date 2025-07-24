<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

if (!isset($_COOKIE['token'])) {
    header("Location: login.php");
    exit();
}

$token = $_COOKIE['token'];
$clave_secreta = $_ENV['CLAVE']; 

try {
    $decoded = JWT::decode($token, new Key($clave_secreta, 'HS256'));

    $id_tipo = $decoded->data->id_tipo ?? null;

    if ($id_tipo !== 1) {
        header("Location: dashboard.php");
        exit();
    }

} catch (Exception $e) {
    header("Location: login.php");
    exit();
}
?>