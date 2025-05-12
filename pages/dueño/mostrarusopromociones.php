<?php
include '../../include/db.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();}
echo $_SESSION['idUser'];
$sql = "SELECT * FROM locales WHERE codUsuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['idUser']);
$stmt->execute();
$resultado = $stmt->get_result();
if ($resultado->num_rows === 1) {
    $local = $resultado->fetch_assoc();
    $idlocal = $local['id'];

    $sql = "SELECT * FROM promociones WHERE idcodLocal = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idlocal);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $idsPromociones = [];
    while ($promo = $resultado->fetch_assoc()) {
        $idsPromociones[] = $promo['id'];
    }
    if (!empty($idsPromociones)) {
        // Convertimos los IDs en una lista separada por comas
        $listaIds = implode(',', array_map('intval', $idsPromociones));
    
        // Armamos la consulta
        $sqlUso = "SELECT id, fechaUso, estado, idUsuario, codPromo FROM usopromociones WHERE codPromo IN ($listaIds)";
        $resultadoUso = $conn->query($sqlUso);
    
        while ($uso = $resultadoUso->fetch_assoc()) {
            echo "ID: " . $uso['id'] . "<br>";
            echo "Fecha de uso: " . $uso['fechaUso'] . "<br>";
            echo "Estado: " . $uso['estado'] . "<br>";
            echo "ID Usuario: " . $uso['idUsuario'] . "<br>";
            echo "ID Promo: " . $uso['codPromo'] . "<br>";
            echo "<hr>";
        }
    } else {
        echo "No hay promociones asociadas a este local.";
    }


} else {
    echo "No se encontró el local en la base de datos.";
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
    
</body>
</html>