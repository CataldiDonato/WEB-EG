<?php
include '../include/db.php';
include 'header.php'; 

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$idUsuario = null;
$categoriaCliente = null;
$promos = false;
$totalPaginas = 1;
$porPagina = 9;
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$inicio = ($pagina - 1) * $porPagina;


if ($usuario_autenticado && isset($decoded) ) {
    $idUsuario = $decoded->data->idUser ?? null;

    $sqlemailverificado = "SELECT validado FROM users WHERE id = ?";
    $stmtEmail = $conn->prepare($sqlemailverificado);
    $stmtEmail->bind_param("i", $idUsuario);
    $stmtEmail->execute();
    $stmtEmail->bind_result($emailVerificado);
    $stmtEmail->fetch();
    $stmtEmail->close();

    if ($idUsuario) {
        $sqlCategoria = "SELECT idCategoria FROM users WHERE id = ?";
        $stmtCategoria = $conn->prepare($sqlCategoria);
        $stmtCategoria->bind_param("i", $idUsuario);
        $stmtCategoria->execute();
        $stmtCategoria->bind_result($categoriaCliente);
        $stmtCategoria->fetch();
        $stmtCategoria->close();

        if ($categoriaCliente) {
            $sqlCount = "SELECT COUNT(*) FROM promociones WHERE estadoPromo = 'aprobada' AND promociones.idCategoriaCliente <= ?";
            $stmtCount = $conn->prepare($sqlCount);
            $stmtCount->bind_param("i", $categoriaCliente);
            $stmtCount->execute();
            $stmtCount->bind_result($totalPromos);
            $stmtCount->fetch();
            $stmtCount->close();

            $totalPaginas = max(1, ceil($totalPromos / $porPagina));

            $sqlPromo = "SELECT promociones.*, locales.nombreLocal FROM promociones 
                        INNER JOIN locales ON promociones.idcodLocal = locales.id 
                        WHERE estadoPromo = 'aprobada' AND promociones.idCategoriaCliente <= ?
                        LIMIT ?, ?";
            $stmtPromo = $conn->prepare($sqlPromo);
            $stmtPromo->bind_param("iii", $categoriaCliente, $inicio, $porPagina);
            $stmtPromo->execute();
            $promos = $stmtPromo->get_result();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Promociones activas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body { height: 100%; }
        body { display: flex; flex-direction: column; }
        main { flex: 1; }
    </style>
</head>
<body>
<main class="flex-grow-1">
    <div class="container mt-4">
        <h2 class="mb-4">Promociones activas</h2>
        <div class="row">

            <?php if ($usuario_autenticado && !$emailVerificado): ?>
                    <div class="col-12">
                        <div class="alert alert-warning text-center">
                            Debes verificar tu correo electrónico para ver las promociones.
                        </div>
                    </div>
            <?php elseif ($usuario_autenticado && $promos && $promos->num_rows > 0): ?>
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
                                    <?php if (!$yaComprada): ?>
                                        <form action="comprar_promo.php" method="post" class="mt-3">
                                            <input type="hidden" name="codPromo" value="<?= $promo['id'] ?>">
                                            <input type="hidden" name="idUsuario" value="<?= $idUsuario ?>">
                                            <input type="hidden" name="estado" value="pendiente">
                                            <button type="submit" class="btn btn-success w-100">Comprar</button>
                                        </form>
                                    <?php else: ?>
                                        <div class="alert alert-secondary mt-3 text-center">Ya compraste esta promoción</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php elseif ($usuario_autenticado): ?>
                <div class="col-12">
                    <div class="alert alert-warning">No hay promociones activas.</div>
                </div>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-warning text-center">
                        Debes iniciar sesión para ver las promociones.
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php if ($usuario_autenticado && $totalPaginas > 1): ?>
            <nav aria-label="Paginación de promociones" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                        <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                            <a class="page-link" href="?pagina=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</main>

<footer class="bg-dark text-white text-center py-3">
    <?php include 'footer.php'; ?>
</footer>
</body>
</html>