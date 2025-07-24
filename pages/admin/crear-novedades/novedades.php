<?php

include '../../validarjwt.php';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Novedad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Crear Novedad</h2>

    <?php if (isset($_GET['mensaje'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['mensaje']) ?></div>
    <?php endif; ?>

    <form method="POST" action="crear-novedad.php">
        <div class="mb-3">
            <label for="textoNovedad" class="form-label">Texto de la Novedad</label>
            <textarea class="form-control" id="textoNovedad" name="textoNovedad" rows="3" required></textarea>
        </div>
        <div class="mb-3">
            <label for="fechaDesdeNovedad" class="form-label">Fecha Desde</label>
            <input type="date" class="form-control" id="fechaDesdeNovedad" name="fechaDesdeNovedad" required>
        </div>
        <div class="mb-3">
            <label for="fechaHastaNovedad" class="form-label">Fecha Hasta</label>
            <input type="date" class="form-control" id="fechaHastaNovedad" name="fechaHastaNovedad" required>
        </div>
        <div class="mb-3">
            <label for="categoriaCliente" class="form-label">Categoría Cliente</label>
            <select class="form-control" id="categoriaCliente" name="categoriaCliente" required>
                <option value="1">Inicial</option>
                <option value="2">Medium</option>
                <option value="3">Premium</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Guardar Novedad</button>
    </form>
    <div class="text-center mt-3">
        <a href="../menu-admin.php" class="btn btn-secondary">Volver al menú</a>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php if (isset($_GET['agregado']) && $_GET['agregado'] === 'ok') : ?>
<script>
    Swal.fire({
        icon: 'success',
        title: '¡Local agregado!',
        text: 'El local se ha agregado exitosamente.',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Aceptar'
    });
</script>
<?php endif; ?>
</body>
</html>
