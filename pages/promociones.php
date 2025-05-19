<?php
include '../include/db.php';
include 'header.php';
// Traer todas las promociones activas de todos los locales
$sqlPromo = "SELECT promociones.*, locales.nombreLocal FROM promociones 
            INNER JOIN locales ON promociones.idcodLocal = locales.id 
            WHERE estadoPromo = 'aprobada'";
$stmtPromo = $conn->prepare($sqlPromo);
$stmtPromo->execute();
$promos = $stmtPromo->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Promociones activas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2 class="mb-4">Promociones activas</h2>
    <div class="row">
        <?php if ($promos->num_rows > 0): ?>
            <?php while ($promo = $promos->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <?php
                        $rutaImagen = !empty($promo['rutaImagen']) ? str_replace('../../', '', $promo['rutaImagen']) : 'img/default.jpg';
                        ?>
                        <img src="<?= htmlspecialchars($rutaImagen) ?>" class="card-img-top" alt="Imagen promoción">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($promo['textoPromo']) ?></h5>
                            <p class="card-text">
                                <strong>Local:</strong> <?= htmlspecialchars($promo['nombreLocal']) ?><br>
                                <strong>Desde:</strong> <?= htmlspecialchars($promo['fechaDesdePromo']) ?><br>
                                <strong>Hasta:</strong> <?= htmlspecialchars($promo['fechaHastaPromo']) ?><br>
                                <strong>Días:</strong> <?= htmlspecialchars($promo['diasSemana']) ?>
                            </p>
                            <span class="badge bg-info text-dark">Categoría: <?= htmlspecialchars($promo['idCategoriaCliente']) ?></span>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-warning">No hay promociones activas.</div>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
