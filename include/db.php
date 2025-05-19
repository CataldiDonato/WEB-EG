<?php
$host = "localhost";
$usuario = "root";
$contrasena = "0770"; 
$basededatos = "dbweb"; 

$conn = new mysqli($host, $usuario, $contrasena, $basededatos,3306);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

?>
