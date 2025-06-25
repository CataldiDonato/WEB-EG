<?php 
if (!isset($_COOKIE['token'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../../vendor/autoload.php'; // Asegúrate de que la ruta sea correcta

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$token = $_COOKIE['token'];
$clave_secreta = "MESSI"; // misma usada al generar el token

try {
    // Decodificar el token
    $decoded = JWT::decode($token, new Key($clave_secreta, 'HS256'));

    // Extraer el tipo de usuario
    $id_tipo = $decoded->data->id_tipo ?? null;

    // Verificar si es dueño (id_tipo == 3)
    if ($id_tipo !== 3) {
        header("Location: ../../dashboard.php");
        exit();
    }

    // Si pasó todas las verificaciones, mostrar la página normalmente
} catch (Exception $e) {
    // Token inválido o expirado
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
    <title>Ver Promociones</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
</head>
<body>
    
    <h1 style="text-align: center;">Promociones activas</h1>
    
    <div class="container" style="max-width: 70vw;">
    <button class="btn btn-primary" onclick="location.href='menu-dueño.php'">Volver al menú</button>
    <br><br>
        <?php
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

                if ($resultado->num_rows > 0) {
                    echo '<div class="row">';

                    while ($promocion = $resultado->fetch_assoc()) {
                        echo '<div class="col-12 col-md-4 mb-4">';
                        echo '<div class="p-3 h-100" style="background-color: #f8f9fa; border: 1px solid #ddd; border-radius: 5px;">';
                        echo '<div><strong>Promoción:</strong> ' . htmlspecialchars($promocion['textoPromo']) . '</div>';
                        echo '<div><strong>Estado:</strong> ' . htmlspecialchars($promocion['estadoPromo']) . '</div>';
                        echo '<div><strong>Fecha de inicio:</strong> ' . htmlspecialchars($promocion['fechaDesdePromo']) . '</div>';
                        echo '<div><strong>Fecha de fin:</strong> ' . htmlspecialchars($promocion['fechaHastaPromo']) . '</div>';
                        echo '<div><strong>Días de la semana:</strong> ' . htmlspecialchars($promocion['diasSemana']) . '</div>';
                        echo '<div><strong>Categoría de cliente:</strong> ' . htmlspecialchars($promocion['idCategoriaCliente']) . '</div>';
                        echo '<div><strong>Imagen:</strong><br><img src="../../' . htmlspecialchars($promocion['rutaImagen']) . '" alt="Imagen de la promoción" style="max-width: 100%; height: auto;"></div>';
                        echo '<br>';
                        echo '<button class="btn btn-danger btn-eliminar-promo" data-id="' . $promocion['id'] . '">Eliminar</button>';
                        echo '</div>';
                        echo '</div>';
                    }

                    echo '</div>'; // Fin de la grilla
                } else {
                    echo '<div class="alert alert-warning">No hay promociones activas.</div>';
                }
            }
            ?>
    </div>
</body>
</html>
