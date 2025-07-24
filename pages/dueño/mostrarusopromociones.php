<?php

require_once __DIR__ . '/../../vendor/autoload.php';


use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if (!isset($_COOKIE['token'])) {
    header("Location: ../login.php");
    exit();
}

$token = $_COOKIE['token'];
$clave_secreta = $_ENV['CLAVE']; 

try {
    $decoded = JWT::decode($token, new Key($clave_secreta, 'HS256'));

    $id_tipo = $decoded->data->id_tipo ?? null;

    if ($id_tipo !== 3) {
        header("Location: ../../dashboard.php");
        exit();
    }

} catch (Exception $e) {
    header("Location: ../../login.php");
    exit();
}

include '../../include/db.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usos de Promociones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <h1 class="mb-4">Historial de Uso de Promociones</h1>
    <button class="btn btn-primary mt-3" onclick="window.location.href='menu-dueño.php'">Volver</button>
<?php
echo "<p class='text-muted'>ID de Usuario: <strong>" . $_SESSION['idUser'] . "</strong></p>";

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
        $listaIds = implode(',', array_map('intval', $idsPromociones));

        $sqlUso = "SELECT id, fechaUso, estado, idUsuario, codPromo FROM usopromociones WHERE codPromo IN ($listaIds)";
        $resultadoUso = $conn->query($sqlUso);

        if ($resultadoUso->num_rows > 0) {
            echo '<div class="card shadow-sm">';
            echo '<div class="card-body">';
            echo '<table class="table table-bordered table-hover">';
            echo '<thead class="table-dark">';
            echo '<tr>
                    <th>ID</th>
                    <th>Fecha de uso</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                    <th>ID Usuario</th>
                    <th>ID Promo</th>
                  </tr>';
            echo '</thead><tbody>';

            while ($uso = $resultadoUso->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($uso['id']) . '</td>';
                echo '<td>' . htmlspecialchars($uso['fechaUso']) . '</td>';
                echo '<td>' . htmlspecialchars($uso['estado']) . '</td>';
                if($uso['estado']=='pendiente') {
                    echo '<td><a class="btn btn-warning btn-sm" href="aprobar.php?id=' . htmlspecialchars($uso['id']) . '">Aprobar</a></td>';
                }else{
                    echo '<td><button class="btn btn-secondary btn-sm" disabled>Estado Finalizado</button></td>';
                }
                echo '<td>' . htmlspecialchars($uso['idUsuario']) . '</td>';
                echo '<td>' . htmlspecialchars($uso['codPromo']) . '</td>';
                echo '</tr>';
            }

            echo '</tbody></table>';
            echo '</div></div>';
        } else {
            echo '<div class="alert alert-warning">No se encontraron usos de promociones.</div>';
        }
    } else {
        echo '<div class="alert alert-info">No hay promociones asociadas a este local.</div>';
    }
} else {
    echo '<div class="alert alert-danger">No se encontró el local en la base de datos.</div>';
}
?>
</body>
</html>
