<?php
include '../include/db.php';

// Obtener promociones destacadas
$destacadas = $conn->query("SELECT textoPromo, fechaDesdePromo, fechaHastaPromo, rutaImagen FROM promociones WHERE destacada = 1 AND estadoPromo = 'aprobada' LIMIT 3");

// Obtener novedades
$novedades = $conn->query("SELECT textoNovedad, fechaDesdeNovedad, fechaHastaNovedad FROM novedades ORDER BY fechaDesdeNovedad DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Shopping Promos - Inicio</title>
    <link rel="stylesheet" href="../assets/css/bootstrap-css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style-header.css">
    <link rel="stylesheet" href="../assets/css/style-dashboard.css">
</head>
<body>
<?php include 'header.php'; ?>
<main class="container py-4">
    <section class="mb-5">
        <h2 class="mb-4 titleDestacadas">Promociones Destacadas</h2>
        <div class="row">
            <?php if ($destacadas && $destacadas->num_rows > 0): ?>
                <?php while ($promo = $destacadas->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="img-zoom-container">
                                <img src="<?= !empty($promo['rutaImagen']) ? '../' . htmlspecialchars($promo['rutaImagen']) : '../assets/img/default.jpg' ?>"
                                    class="promo-destacada-img" alt="Imagen promoción">
                            </div>
                            <div class="card-body text-center">
                                <h5 class="card-title mb-3"><?= htmlspecialchars($promo['textoPromo']) ?></h5>
                                <div class="d-flex justify-content-center gap-2">
                                    <span class="fecha-badge desde">Desde: <?= date('d/m/Y', strtotime($promo['fechaDesdePromo'])) ?></span>
                                    <span class="fecha-badge hasta">Hasta: <?= date('d/m/Y', strtotime($promo['fechaHastaPromo'])) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-warning text-center">No hay promociones destacadas.</div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section>
        <h2 class="mb-4">Novedades</h2>
        <?php if ($novedades && $novedades->num_rows > 0): ?>
            <div id="novedadesCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php $i = 0; while ($novedad = $novedades->fetch_assoc()): ?>
                        <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                            <div class="card mx-auto shadow-sm" style="max-width: 600px;">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($novedad['textoNovedad']) ?></h5>
                                    <p class="card-text">
                                        <span class="badge bg-secondary">Desde: <?= date('d/m/Y', strtotime($novedad['fechaDesdeNovedad'])) ?></span>
                                        <span class="badge bg-secondary">Hasta: <?= date('d/m/Y', strtotime($novedad['fechaHastaNovedad'])) ?></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php $i++; endwhile; ?>
                </div>
                <?php if ($novedades->num_rows > 1): ?>
                    <button class="carousel-control-prev" type="button" data-bs-target="#novedadesCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#novedadesCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                        <span class="visually-hidden">Siguiente</span>
                    </button>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">No hay novedades para mostrar.</div>
        <?php endif; ?>
    </section>
</main>

<?php include 'footer.php'; ?>
<script src="../assets/js/bootstrap-js/bootstrap.bundle.min.js"></script>
</body>
</html>
