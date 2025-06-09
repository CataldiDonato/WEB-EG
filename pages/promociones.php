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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2 class="mb-4">Promociones activas</h2>
    <div class="row">
        <?php if ($promos->num_rows > 0): ?>
            <?php while ($promo = $promos->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="d-flex align-items-center h-100">
                        <div class="card w-80">
                            <?php
                            $rutaImagen = !empty($promo['rutaImagen']) ? '../'. $promo['rutaImagen'] : '../assets/img/default.jpg';
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
                            <img src="<?= htmlspecialchars($rutaImagen) ?>" class="card-img-top" alt="Imagen promoción">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($promo['textoPromo']) ?></h5>
                                <p class="card-text">
                                    <strong>Local:</strong> <?= htmlspecialchars($promo['nombreLocal']) ?><br>
                                    <strong>Desde:</strong> <?= htmlspecialchars($promo['fechaDesdePromo']) ?><br>
                                    <strong>Hasta:</strong> <?= htmlspecialchars($promo['fechaHastaPromo']) ?><br>
                                    <strong>Días:</strong> <?= htmlspecialchars($promo['diasSemana']) ?>
                                </p>
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
