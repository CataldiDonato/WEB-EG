<?php
$host = "localhost";
$usuario = "root";
$contrasena = ""; 
$basededatos = "dbweb"; 

$conn = new mysqli($host, $usuario, $contrasena, $basededatos);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$conn->set_charset("utf8");

?>
