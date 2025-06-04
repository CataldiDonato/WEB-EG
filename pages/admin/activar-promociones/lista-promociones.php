<?php
include("../../../include/db.php");

$porPagina = 5;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina - 1) * $porPagina;
$totalPromocionesQuery = $conn->query("SELECT COUNT(*) AS total FROM promociones");
$totalPromociones = $totalPromocionesQuery->fetch_assoc()['total'];
$totalPaginas = max(1, ceil($totalPromociones / $porPagina));
$sql = "SELECT * FROM promociones LIMIT $porPagina OFFSET $offset";
$resultado = $conn->query($sql);
$promociones = [];
if ($resultado && $resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $promociones[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Promociones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="lista-promociones.css">
</head>
<body class="bg-light p-4 body-lista-promociones">

<div class="container">
    <h2 class="mb-4 title-lista-promociones">Listado de Promociones</h2>
    <?php if (count($promociones) > 0): ?>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Texto</th>
                    <th>Desde</th>
                    <th>Hasta</th>
                    <th>ID Categoría</th>
                    <th>Días</th>
                    <th>Estado</th>
                    <th>ID Local</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($promociones as $promo): ?>
                    <tr>
                        <td><?= $promo['id'] ?></td>
                        <td><?= $promo['textoPromo'] ?></td>
                        <td><?= $promo['fechaDesdePromo'] ?></td>
                        <td><?= $promo['fechaHastaPromo'] ?></td>
                        <td><?= $promo['idCategoriaCliente'] ?></td>
                        <td><?= $promo['diasSemana'] ?></td>
                        <td>
                            <span class="badge <?= $promo['estadoPromo'] == 'aprobada' ? 'bg-success' : ($promo['estadoPromo'] == 'denegada' ? 'bg-danger' : 'bg-secondary') ?>">
                                <?= ucfirst($promo['estadoPromo']) ?>
                            </span>
                        </td>
                        <td><?= $promo['idcodLocal'] ?></td>
                        <td>
                            <?php if ($promo['estadoPromo'] == 'pendiente'): ?>
                                <form method="post" action="actualizar-estado-promociones.php" class="d-inline form-aprobar">
                                    <input type="hidden" name="id" value="<?= $promo['id'] ?>">
                                    <input type="hidden" name="accion" value="aprobar">
                                    <button type="button" class="btn btn-sm btn-success btn-aprobar">Aprobar</button>
                                </form>
                                <form method="post" action="actualizar-estado-promociones.php" class="d-inline form-denegar ms-1">
                                    <input type="hidden" name="id" value="<?= $promo['id'] ?>">
                                    <input type="hidden" name="accion" value="denegar">
                                    <button type="button" class="btn btn-sm btn-danger btn-denegar">Denegar</button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted">Sin acciones</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <nav aria-label="Paginación de promociones" class="d-flex justify-content-center my-4">
            <ul class="pagination">
                <?php if ($pagina > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?pagina=<?= $pagina - 1 ?>">Anterior</a>
                    </li>
                <?php else: ?>
                    <li class="page-item disabled"><span class="page-link">Anterior</span></li>
                <?php endif; ?>
                <li class="page-item active"><span class="page-link"><?= $pagina ?></span></li>
                <?php if ($pagina < $totalPaginas): ?>
                    <li class="page-item">
                        <a class="page-link" href="?pagina=<?= $pagina + 1 ?>">Siguiente</a>
                    </li>
                <?php else: ?>
                    <li class="page-item disabled"><span class="page-link">Siguiente</span></li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php else: ?>
        <div class="alert alert-warning text-center">No hay promociones registradas.</div>
    <?php endif; ?>
    <div class="text-center mt-3">
        <a href="../menu-admin.php" class="btn btn-secondary">Volver al menú</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.querySelectorAll('.btn-aprobar').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: "¿Aprobar promoción?",
            text: "¿Estás seguro de aprobar esta promoción?",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sí, aprobar"
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "¡Aprobado!",
                    text: "Su promoción fue aprobada con éxito.",
                    icon: "success",
                    timer: 3000,
                    timerProgressBar: true,
                    didClose: () => {
                        btn.closest('form').submit();
                    }
                });
            }
        });
    });
});
document.querySelectorAll('.btn-denegar').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: "¿Denegar promoción?",
            text: "¿Estás seguro de denegar esta promoción?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Sí, denegar"
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Eliminado!",
                    text: "Su promocion fue denegada con exito.",
                    icon: "success",
                    timer: 3000,
                    timerProgressBar: true,
                    didClose: () => {
                        btn.closest('form').submit();
                    }
            });
            }
        });
    });
});
</script>
</body>
</html>
