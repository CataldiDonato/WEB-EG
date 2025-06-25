<?php
include '../include/db.php';

// Obtener las 3 promociones destacadas
$destacadas = $conn->query("SELECT textoPromo, fechaDesdePromo, fechaHastaPromo, rutaImagen FROM promociones WHERE destacada = 1 AND estadoPromo = 'aprobada' LIMIT 3");

// Obtener novedades activas (puedes ajustar la lógica de fechas si lo deseas)
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
    <main>
        <section class="mb-5">
            <h2 class="mb-4">Promociones Destacadas</h2>
            <div class="row">
                <?php if ($destacadas && $destacadas->num_rows > 0): ?>
                    <?php while ($promo = $destacadas->fetch_assoc()): ?>
                        <div class="col-md-4 mb-3 d-flex">
                            <div class="card w-100">
                                <img src="<?= !empty($promo['rutaImagen']) ? '../' . htmlspecialchars($promo['rutaImagen']) : '../assets/img/default.jpg' ?>" class="promo-destacada-img" alt="Imagen promoción">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($promo['textoPromo']) ?></h5>
                                    <p class="card-text">
                                        <span class="badge bg-info text-dark">Desde: <?= htmlspecialchars($promo['fechaDesdePromo']) ?></span>
                                        <span class="badge bg-info text-dark">Hasta: <?= htmlspecialchars($promo['fechaHastaPromo']) ?></span>
                                    </p>
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
                                <div class="card mx-auto" style="max-width: 600px;">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($novedad['textoNovedad']) ?></h5>
                                        <p class="card-text">
                                            <span class="badge bg-secondary">Desde: <?= htmlspecialchars($novedad['fechaDesdeNovedad']) ?></span>
                                            <span class="badge bg-secondary">Hasta: <?= htmlspecialchars($novedad['fechaHastaNovedad']) ?></span>
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
    <br>
    <footer>
       <?php
        include 'footer.php';
        ?>
    </footer>
    
    <script src="../assets/js/bootstrap-js/bootstrap.bundle.min.js"></script>
</body>
</html>

