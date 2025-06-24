<?php

require_once __DIR__ . '/../../vendor/autoload.php';


use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Verificar que exista la cookie
if (!isset($_COOKIE['token'])) {
    header("Location: ../login.php");
    exit();
}

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

if (isset($_POST['submit'])) {
    $textoPromo = $_POST['descripcionPromocion'] ?? '';
    $fechaDesdePromo = $_POST['diaComienzaPromocion'] ?? '';
    $fechaHastaPromo = $_POST['diaTerminaPromocion'] ?? '';
    $categoriaCliente = $_POST['categoriaCliente'] ?? '';
    $diasSemana = $_POST['diasSemana'] ?? [];
    $diasSemanaString = implode(',', $diasSemana);
    $estPromo = "pendiente";

    $sql_check = "SELECT * FROM locales WHERE codUsuario = ?";
    $stmt = $conn->prepare($sql_check);
    $stmt->bind_param("i", $_SESSION['idUser']);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $fila = $resultado->fetch_assoc();

    if (!$fila) {
        header("Location: menu-dueño.php?error=No se encontró un local asociado a este usuario.");
        exit;
    }

    $codLocalDueño = $fila['id'];

    $rutaCarpeta = __DIR__ . '/../../assets/img/';
    if (!is_dir($rutaCarpeta)) {
        mkdir($rutaCarpeta, 0777, true);
    }
    $nombreArchivo = time() . '_' . basename($_FILES['imagen']['name']);
    $rutaImagenAbsoluta = $rutaCarpeta . $nombreArchivo;
    $rutaImagenWeb = 'assets/img/' . $nombreArchivo;

    $tipoPermitido = ['image/jpeg', 'image/png', 'image/gif'];
    if (in_array($_FILES['imagen']['type'], $tipoPermitido)) {
        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaImagenAbsoluta)) {
            header("Location: menu-dueño.php?error=Error al subir la imagen.");
            exit;
        }
    } else {
        header("Location: menu-dueño.php?error=Formato de imagen no permitido.");
        exit;
    }

    if (empty($textoPromo) || empty($fechaDesdePromo) || empty($fechaHastaPromo) || empty($categoriaCliente)) {
        header("Location: menu-dueño.php?error=Por favor, complete todos los campos.");
        exit;
    } else {
        $sql = "INSERT INTO promociones (textoPromo, fechaDesdePromo, fechaHastaPromo, idCategoriaCliente, diasSemana, estadoPromo, idcodLocal, rutaImagen) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssis", $textoPromo, $fechaDesdePromo, $fechaHastaPromo, $categoriaCliente, $diasSemanaString, $estPromo, $codLocalDueño, $rutaImagenWeb);

        if ($stmt->execute()) {
            header("Location: menu-dueño.php?promo=ok");
            exit();
        } else {
            header("Location: menu-dueño.php?error=" . urlencode($conn->error));
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MenuDueño</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
</head>
<body>
    <div class="container mt-5">
        <button class="btn btn-primary" onclick="window.location.href='../perfil.php'">Volver</button>
        <h1>Promociones activas</h1>
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
                while ($promocion = $resultado->fetch_assoc()) {
                    echo '<div class="p-3 mb-3" style="background-color: #f8f9fa; border: 1px solid #ddd; border-radius: 5px;">';
                    echo '<div><strong>Promoción:</strong> ' . $promocion['textoPromo'] . '</div>';
                    echo '<div><strong>Estado:</strong> ' . $promocion['estadoPromo'] . '</div>';
                    echo '<div><strong>Fecha de inicio:</strong> ' . $promocion['fechaDesdePromo'] . '</div>';
                    echo '<div><strong>Fecha de fin:</strong> ' . $promocion['fechaHastaPromo'] . '</div>';
                    echo '<div><strong>Días de la semana:</strong> ' . $promocion['diasSemana'] . '</div>';
                    echo '<div><strong>Categoría de cliente:</strong> ' . $promocion['idCategoriaCliente'] . '</div>';
                    echo '<div><strong>Imagen:</strong> <img src="../../' . $promocion['rutaImagen'] . '" alt="Imagen de la promoción" style="max-width: 200px; max-height: 200px;"></div>';
                    echo '<br>';  
                    echo '<button class="btn btn-danger btn-eliminar-promo" data-id="' . $promocion['id'] . '">Eliminar</button>';                  
                    echo '</div>';
                }
            } else {
                echo '<div class="alert alert-warning">No hay promociones activas.</div>';
            }
        }
        ?>
        <h1>Cargar Promocion</h1>
        <form id="cargarPromocion-form" class="form" action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="descripcionPromocion" class="form-label">Descripción Promoción</label>
                <input type="text" class="form-control" id="descripcionPromocion" name="descripcionPromocion" required>
            </div>
            <div class="mb-3">
                <label for="diaComienzaPromocion" class="form-label">Día Comienza Promoción</label>
                <input type="date" class="form-control" id="diaComienzaPromocion" name="diaComienzaPromocion" required>
            </div>
            <div class="mb-3">
                <label for="diaTerminaPromocion" class="form-label">Día Termina Promoción</label>
                <input type="date" class="form-control" id="diaTerminaPromocion" name="diaTerminaPromocion" required>
            </div>
            <div class="mb-3">
                <label for="categoriaCliente" class="form-label">Categoría Cliente</label>
                <select class="form-control" id="categoriaCliente" name="categoriaCliente" required>
                    <option value="1">Inicial</option>
                    <option value="2">Medium</option>
                    <option value="3">Premium</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="diasSemana" class="form-label">Días de la semana que está vigente</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="lunes" name="diasSemana[]" value="l">
                    <label class="form-check-label" for="lunes">Lunes</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="martes" name="diasSemana[]" value="m">
                    <label class="form-check-label" for="martes">Martes</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="miercoles" name="diasSemana[]" value="x">
                    <label class="form-check-label" for="miercoles">Miércoles</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="jueves" name="diasSemana[]" value="j">
                    <label class="form-check-label" for="jueves">Jueves</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="viernes" name="diasSemana[]" value="v">
                    <label class="form-check-label" for="viernes">Viernes</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="sabado" name="diasSemana[]" value="s">
                    <label class="form-check-label" for="sabado">Sábado</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="domingo" name="diasSemana[]" value="d">
                    <label class="form-check-label" for="domingo">Domingo</label>
                </div>
            </div>
            <div class="mb-3">
                <label for="imagen" class="form-label">Selecciona una imagen</label>
                <input type="file" name="imagen" id="imagen" class="form-control" required>
            </div>
            <div class="text-center">
                <input type="submit" name="submit" class="btn btn-primary" value="Cargar Promoción">
            </div>
        </form>
    </div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php if (isset($_GET['promo']) && $_GET['promo'] === 'ok') : ?>
<script>
Swal.fire({
    icon: 'success',
    title: '¡Promoción cargada!',
    text: 'La promoción se cargó exitosamente.',
    confirmButtonColor: '#3085d6',
    confirmButtonText: 'Aceptar'
});
</script>
<?php endif; ?>
<?php if (isset($_GET['error'])) : ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'Error al cargar la promoción',
    text: <?= json_encode($_GET['error']) ?>,
    confirmButtonColor: '#d33',
    confirmButtonText: 'Aceptar'
});
</script>
<?php endif; ?>
<?php if (isset($_GET['eliminado']) && $_GET['eliminado'] === 'ok') : ?>
<script>
Swal.fire({
    icon: 'success',
    title: '¡Promoción eliminada!',
    text: 'La promoción se eliminó correctamente.',
    confirmButtonColor: '#3085d6',
    confirmButtonText: 'Aceptar'
});
</script>
<?php endif; ?>
<script>
document.querySelectorAll('.btn-eliminar-promo').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const promoId = btn.getAttribute('data-id');
        Swal.fire({
            title: "¿Estás seguro?",
            text: "¡Esta acción eliminará la promoción!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'eliminarPromocion.php?id=' + promoId;
            }
        });
    });
});
</script>
</body>
</html>