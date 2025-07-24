<?php

require_once __DIR__ . '/../../vendor/autoload.php';
include 'validarjwtdueño.php';


use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// if (!isset($_COOKIE['token'])) {
//     header("Location: ../login.php");
//     exit();
// }

// $token = $_COOKIE['token'];
// $clave_secreta = $_ENV['CLAVE']; 

// try {
  
//     $decoded = JWT::decode($token, new Key($clave_secreta, 'HS256'));
//     $id_tipo = $decoded->data->id_tipo ?? null;

//     if ($id_tipo !== 3) {
//         header("Location: ../../dashboard.php");
//         exit();
//     }

// } catch (Exception $e) {
//     header("Location: ../../login.php");
//     exit();
// }

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
        header("Location: cargar_promocion.php?error=No se encontró un local asociado a este usuario.");
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
            header("Location: cargar_promocion.php?error=Error al subir la imagen.");
            exit;
        }
    } else {
        header("Location: cargar_promocion.php?error=Formato de imagen no permitido.");
        exit;
    }

    if (empty($textoPromo) || empty($fechaDesdePromo) || empty($fechaHastaPromo) || empty($categoriaCliente)) {
        header("Location: cargar_promocion.php?error=Por favor, complete todos los campos.");
        exit;
    } else {
        $sql = "INSERT INTO promociones (textoPromo, fechaDesdePromo, fechaHastaPromo, idCategoriaCliente, diasSemana, estadoPromo, idcodLocal, rutaImagen) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssis", $textoPromo, $fechaDesdePromo, $fechaHastaPromo, $categoriaCliente, $diasSemanaString, $estPromo, $codLocalDueño, $rutaImagenWeb);

        if ($stmt->execute()) {
            header("Location: cargar_promocion.php?promo=ok");
            exit();
        } else {
            header("Location: cargar_promocion.php?error=" . urlencode($conn->error));
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
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8"> 
                <h2 class="text-center mb-4">Cargar Promoción</h2>
                <button class="btn btn-primary mb-3" onclick="location.href='menu-dueño.php'">Volver al menú</button>
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
                        <label class="form-label">Días de la semana que está vigente</label>
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
            <label class="form-label">Imagen</label>
            <input type="file" name="imagen" id="imagen" class="form-control" accept="image/*" required>
            <div class="mt-3 text-center">
                <img id="preview-img" src="#" alt="Vista previa" style="max-width:300px; display:none; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,0.1);">
            </div>
        </div>
        <div class="text-center">
            <input type="submit" name="submit" class="btn btn-primary" value="Cargar Promoción">
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById("imagen").addEventListener("change", function(e) {
    const preview = document.getElementById("preview-img");
    if (e.target.files && e.target.files[0]) {
        const reader = new FileReader();
        reader.onload = function(ev) {
            preview.src = ev.target.result;
            preview.style.display = "block";
        };
        reader.readAsDataURL(e.target.files[0]);
    }
});
</script>

<?php if (isset($_GET['promo']) && $_GET['promo'] === 'ok') : ?>
<script>
Swal.fire({
    icon: 'success',
    title: '¡Promoción cargada!',
    text: 'La promoción se registró correctamente.',
    confirmButtonColor: '#3085d6'
});
</script>
<?php endif; ?>

<?php if (isset($_GET['error'])) : ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'Error',
    text: <?= json_encode($_GET['error']) ?>,
    confirmButtonColor: '#d33'
});
</script>
<?php endif; ?>

<?php if (isset($_GET['eliminado']) && $_GET['eliminado'] === 'ok') : ?>
<script>
Swal.fire({
    icon: 'success',
    title: '¡Eliminada!',
    text: 'Promoción eliminada correctamente.',
    confirmButtonColor: '#3085d6'
});
</script>
<?php endif; ?>

<script>
document.querySelectorAll('.btn-eliminar-promo').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esto eliminará la promoción permanentemente.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(result => {
            if (result.isConfirmed) {
                window.location.href = 'eliminarPromocion.php?id=' + id;
            }
        });
    });
});
</script>
</body>
</html>