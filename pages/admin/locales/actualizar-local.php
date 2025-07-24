<?php
require_once __DIR__ . '/../../../vendor/autoload.php';


use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Verificar que exista la cookie
if (!isset($_COOKIE['token'])) {
    header("Location: ../../login.php");
    exit();
}

$token = $_COOKIE['token'];
$clave_secreta = $_ENV['CLAVE']; 

try {
    // Decodificar el token
    $decoded = JWT::decode($token, new Key($clave_secreta, 'HS256'));

    // Extraer el tipo de usuario
    $id_tipo = $decoded->data->id_tipo ?? null;

    // Verificar si es admin (id_tipo == 1)
    if ($id_tipo !== 1) {
        header("Location: ../../dashboard.php");
        exit();
    }

    // Si pasó todas las verificaciones, mostrar la página normalmente
} catch (Exception $e) {
    // Token inválido o expirado
    header("Location: ../../login.php");
    exit();
}

include("../../../include/db.php");

// Si se envió el formulario para actualizar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['nombreLocal'])) {
    $id = $_POST['id'];
    $nombreLocal = $_POST['nombreLocal'];
    $ubicacionLocal = $_POST['ubicacionLocal'];
    $rubroLocal = $_POST['rubroLocal'];
    $codUsuario = $_POST['codUsuario'];

    $stmt = $conn->prepare("UPDATE locales SET nombreLocal = ?, ubicacionLocal = ?, rubroLocal = ?, codUsuario = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $nombreLocal, $ubicacionLocal, $rubroLocal, $codUsuario, $id);
    $stmt->execute();

    // Redirige con mensaje de éxito
    header("Location: actualizar-local.php?actualizado=ok&id=" . $id);
    exit();
}

// Si solo se quiere mostrar el formulario (GET o POST solo con id)
if (isset($_GET['id']) || isset($_POST['id'])) {
    $id = $_GET['id'] ?? $_POST['id'];
    $stmt = $conn->prepare("SELECT * FROM locales WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $local = $resultado->fetch_assoc();
    if (!$local) {
        header("Location: gestion-locales.php");
        exit();
    }
} else {
    header("Location: gestion-locales.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar Local</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./actualizaciones.css">
</head>
<body class="fondo-claro">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card sombra">
                <div class="card-header encabezado">
                    <h4>Actualizar Local</h4>
                </div>
                <div class="card-body">
                    <form action="" method="POST">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($local['id']) ?>">
                        <div class="mb-3">
                            <label for="nombreLocal" class="form-label">Nombre del Local</label>
                            <input type="text" class="form-control" name="nombreLocal" id="nombreLocal" value="<?= htmlspecialchars($local['nombreLocal']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="ubicacionLocal" class="form-label">Ubicación</label>
                            <input type="text" class="form-control" name="ubicacionLocal" id="ubicacionLocal" value="<?= htmlspecialchars($local['ubicacionLocal']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="rubroLocal" class="form-label">Rubro</label>
                            <input type="text" class="form-control" name="rubroLocal" id="rubroLocal" value="<?= htmlspecialchars($local['rubroLocal']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="codUsuario" class="form-label">Código de Usuario</label>
                            <input type="number" class="form-control" name="codUsuario" id="codUsuario" value="<?= htmlspecialchars($local['codUsuario']) ?>" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
                <div class="text-center mt-3">
                    <a href="./gestion-locales.php" class="btn btn-secondary">Volver al menú anterior</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php if (isset($_GET['actualizado']) && $_GET['actualizado'] === 'ok') : ?>
<script>
    Swal.fire({
        icon: 'success',
        title: '¡Local actualizado!',
        text: 'Los cambios se guardaron correctamente.',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Aceptar'
    });
    
</script>
<?php endif; ?>

</body>
</html>