<?php
include '../include/db.php';
include 'header.php';

$idUsuario = $_SESSION['idUser'] ?? 0;
$categoriaCliente = null;

if ($idUsuario) {
    $sqlCategoria = "SELECT idCategoria FROM users WHERE id = ?";
    $stmtCategoria = $conn->prepare($sqlCategoria);
    $stmtCategoria->bind_param("i", $idUsuario);
    $stmtCategoria->execute();
    $stmtCategoria->bind_result($categoriaCliente);
    $stmtCategoria->fetch();
    $stmtCategoria->close();
}

$sqlPromo = "SELECT promociones.*, locales.nombreLocal FROM promociones 
             INNER JOIN locales ON promociones.idcodLocal = locales.id 
             WHERE estadoPromo = 'aprobada' AND promociones.idCategoriaCliente <= ?";
$stmtPromo = $conn->prepare($sqlPromo);
$stmtPromo->bind_param("i", $categoriaCliente);
$stmtPromo->execute();
$promos = $stmtPromo->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Promociones activas</title>
    <link rel="stylesheet" href="../assets/css/bootstrap-css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style-dashboard.css">
</head>
<body>
<main class="container py-4">
    <h2 class="mb-4 text-center titleDestacadas">Promociones Activas</h2>
    <div class="row">
        <?php if ($promos->num_rows > 0): ?>
            <?php while ($promo = $promos->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="img-zoom-container">
                            <img src="<?= !empty($promo['rutaImagen']) ? '../'. htmlspecialchars($promo['rutaImagen']) : '../assets/img/default.jpg' ?>"
                                class="promo-destacada-img" alt="Imagen promoción">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($promo['textoPromo']) ?></h5>
                            <p class="card-text">
                                <strong>Local:</strong> <?= htmlspecialchars($promo['nombreLocal']) ?><br>
                                <strong>Desde:</strong> <?= date('d/m/Y', strtotime($promo['fechaDesdePromo'])) ?><br>
                                <strong>Hasta:</strong> <?= date('d/m/Y', strtotime($promo['fechaHastaPromo'])) ?><br>
                                <strong>Días:</strong> <?= htmlspecialchars($promo['diasSemana']) ?>
                            </p>
                            <?php
                            $yaComprada = false;
                            if ($idUsuario) {
                                $sqlCheck = "SELECT id FROM usopromociones WHERE codPromo = ? AND idUsuario = ?";
                                $stmtCheck = $conn->prepare($sqlCheck);
                                $stmtCheck->bind_param("ii", $promo['id'], $idUsuario);
                                $stmtCheck->execute();
                                $stmtCheck->store_result();
                                $yaComprada = $stmtCheck->num_rows > 0;
                                $stmtCheck->close();
                            }
                            ?>
                            <?php if ($idUsuario && !$yaComprada): ?>
                                <form action="comprar_promo.php" method="post" class="mt-3">
                                    <input type="hidden" name="codPromo" value="<?= $promo['id'] ?>">
                                    <input type="hidden" name="idUsuario" value="<?= $idUsuario ?>">
                                    <input type="hidden" name="estado" value="pendiente">
                                    <button type="submit" class="btn btn-success w-100">Comprar</button>
                                </form>
                            <?php elseif ($yaComprada): ?>
                                <div class="alert alert-secondary mt-3 text-center">Ya compraste esta promoción</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-warning text-center">No hay promociones activas.</div>
            </div>
        <?php endif; ?>
    </div>
</main>

<footer class="bg-dark text-white text-center py-3">
    <?php include 'footer.php'; ?>
</footer>
</body>
</html>
