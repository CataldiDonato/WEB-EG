<?php
include '../../../include/db.php';

require_once __DIR__ . '/../../../vendor/autoload.php';


use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if (!isset($_COOKIE['token'])) {
    header("Location: ../../login.php");
    exit();
}

$token = $_COOKIE['token'];
$clave_secreta = "MESSI";

try {
    $decoded = JWT::decode($token, new Key($clave_secreta, 'HS256'));

    $id_tipo = $decoded->data->id_tipo ?? null;

    if ($id_tipo !== 1) {
        header("Location: ../../dashboard.php");
        exit();
    }

} catch (Exception $e) {
    header("Location: ../../login.php");
    exit();
}

$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';

$porPagina = 6;
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$inicio = ($pagina - 1) * $porPagina;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['destacadas'])) {
    $conn->query("UPDATE promociones SET destacada = 0");
    $seleccionadas = array_slice($_POST['destacadas'], 0, 3);
    if (!empty($seleccionadas)) {
        $in = implode(',', array_map('intval', $seleccionadas));
        $conn->query("UPDATE promociones SET destacada = 1 WHERE id IN ($in)");
    }
    $redir = 'promocionesDestacadas.php?mensaje=Promociones destacadas actualizadas';
    if ($busqueda !== '') $redir .= '&busqueda=' . urlencode($busqueda);
    if (isset($_GET['pagina'])) $redir .= '&pagina=' . intval($_GET['pagina']);
    header("Location: $redir");
    exit;
}

if ($busqueda !== '') {
    $sqlCount = "SELECT COUNT(*) FROM promociones WHERE textoPromo LIKE ?";
    $stmtCount = $conn->prepare($sqlCount);
    $like = "%$busqueda%";
    $stmtCount->bind_param("s", $like);
    $stmtCount->execute();
    $stmtCount->bind_result($totalPromos);
    $stmtCount->fetch();
    $stmtCount->close();

    $sql = "SELECT id, textoPromo, fechaDesdePromo, fechaHastaPromo, idCategoriaCliente, estadoPromo, destacada, rutaImagen 
            FROM promociones WHERE textoPromo LIKE ? AND estadoPromo = 'aprobada' LIMIT ?, ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $like, $inicio, $porPagina);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $totalPromos = $conn->query("SELECT COUNT(*) FROM promociones")->fetch_row()[0];
    $sql = "SELECT id, textoPromo, fechaDesdePromo, fechaHastaPromo, idCategoriaCliente, estadoPromo, destacada, rutaImagen 
            FROM promociones WHERE estadoPromo = 'aprobada' LIMIT $inicio, $porPagina";
    $result = $conn->query($sql);
}
$totalPaginas = ceil($totalPromos / $porPagina);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Promociones Destacadas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../assets/css/style-destacadas.css"> 
    <script>
    function limitarSeleccion(max) {
        const checkboxes = document.querySelectorAll('input[name="destacadas[]"]');
        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const seleccionados = Array.from(checkboxes).filter(c => c.checked);
                if (seleccionados.length > max) {
                    this.checked = false;
                    alert('Solo puedes seleccionar hasta ' + max + ' promociones destacadas.');
                }
            });
        });
    }
    window.onload = function() { limitarSeleccion(3); }
    </script>
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Gestionar Promociones Destacadas</h2>
    <?php if (isset($_GET['mensaje'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['mensaje']) ?></div>
    <?php endif; ?>

    <form class="mb-4" method="get" action="">
        <div class="input-group">
            <input type="text" class="form-control" name="busqueda" placeholder="Buscar promoción..." value="<?= htmlspecialchars($busqueda) ?>">
            <button class="btn btn-outline-primary" type="submit">Buscar</button>
            <?php if ($busqueda !== ''): ?>
                <a href="promocionesDestacadas.php" class="btn btn-outline-secondary">Limpiar</a>
            <?php endif; ?>
        </div>
    </form>

    <form method="POST">
        <div class="row">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($promo = $result->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4 d-flex">
                        <div class="promo-card w-100 d-flex flex-column">
                            <div class="position-relative">
                                <img src="<?= !empty($promo['rutaImagen']) ? '../../../' . htmlspecialchars($promo['rutaImagen']) : '../../../assets/img/default.jpg' ?>" class="promo-img" alt="Imagen promoción">
                                <input class="form-check-input promo-checkbox position-absolute top-0 start-0 m-2" type="checkbox" name="destacadas[]" value="<?= $promo['id'] ?>" id="promo<?= $promo['id'] ?>" <?= $promo['destacada'] ? 'checked' : '' ?>>
                            </div>
                            <div class="flex-grow-1 p-3 d-flex flex-column">
                                <div class="fw-bold fs-5 mb-1"><?= htmlspecialchars($promo['textoPromo']) ?></div>
                                <div class="mb-2">
                                    <span class="badge bg-info text-dark">Desde: <?= htmlspecialchars($promo['fechaDesdePromo']) ?></span>
                                    <span class="badge bg-info text-dark">Hasta: <?= htmlspecialchars($promo['fechaHastaPromo']) ?></span>
                                </div>
                                <div class="mb-2">
                                    <span class="badge bg-secondary">Categoría: <?= htmlspecialchars($promo['idCategoriaCliente']) ?></span>
                                    <span class="badge bg-<?= $promo['estadoPromo'] === 'aprobada' ? 'success' : 'warning' ?>">
                                        <?= htmlspecialchars($promo['estadoPromo']) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-warning text-center">No se encontraron promociones.</div>
                </div>
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Guardar destacadas</button>
    </form>

    <nav aria-label="Paginación de promociones" class="mt-4">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                    <a class="page-link" href="?pagina=<?= $i ?>&busqueda=<?= urlencode($busqueda) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>

    <div class="text-center mt-3">
        <a href="../menu-admin.php" class="btn btn-secondary">Volver al menú</a>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>