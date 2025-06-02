<?php
include("../../../include/db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $stmt = $conn->prepare("SELECT * FROM locales WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $local = $resultado->fetch_assoc();
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
                    <form action="guardar-local.php" method="POST">
                        <input type="hidden" name="id" value="<?= $local['id'] ?>">
                        <div class="mb-3">
                            <label for="nombreLocal" class="form-label">Nombre del Local</label>
                            <input type="text" class="form-control" name="nombreLocal" id="nombreLocal" value="<?= $local['nombreLocal'] ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="ubicacionLocal" class="form-label">Ubicación</label>
                            <input type="text" class="form-control" name="ubicacionLocal" id="ubicacionLocal" value="<?= $local['ubicacionLocal'] ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="rubroLocal" class="form-label">Rubro</label>
                            <input type="text" class="form-control" name="rubroLocal" id="rubroLocal" value="<?= $local['rubroLocal'] ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="codUsuario" class="form-label">Código de Usuario</label>
                            <input type="number" class="form-control" name="codUsuario" id="codUsuario" value="<?= $local['codUsuario'] ?>" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <a href="gestion-locales.php" class="btn btn-link">← Volver a la gestión</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
