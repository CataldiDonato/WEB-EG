<?php
$host = "localhost";
$usuario = "root";
$contrasena = "donato"; 
$basededatos = "dbweb"; 

$conn = new mysqli($host, $usuario, $contrasena, $basededatos,3307);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

?>
