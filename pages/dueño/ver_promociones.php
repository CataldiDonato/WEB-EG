<?php 
if (!isset($_COOKIE['token'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../../vendor/autoload.php'; 

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

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
    <title>Ver Promociones</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.form-eliminar').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); 
            Swal.fire({
                title: "¿Eliminar promoción?",
                text: "Esta acción no se puede deshacer.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = form.action + "?idPromo=" + form.querySelector('input[name="idPromo"]').value;
                }
            });
        });
    });
});
</script>

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
                        echo '<form method="GET" action="eliminarPromocion.php" class="form-eliminar">';
                        echo '<input type="hidden" name="idPromo" value="' . $promocion['id'] . '">';
                        echo '<button type="submit" class="btn btn-outline-danger"><i class="fas fa-trash-alt"></i> Eliminar</button>';
                        echo '</form>';
                        echo '</div>';
                        echo '</div>';
                    }

                    echo '</div>'; 
                } else {
                    echo '<div class="alert alert-warning">No hay promociones activas.</div>';
                }
            }
            ?>
    </div>
</body>


</html>
