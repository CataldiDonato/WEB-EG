<?php
include("../../../include/db.php");

$porPagina = 5;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina - 1) * $porPagina;

// Obtener total de promociones
$totalPromocionesQuery = $conn->query("SELECT COUNT(*) AS total FROM promociones");
$totalPromociones = $totalPromocionesQuery->fetch_assoc()['total'];
$totalPaginas = ceil($totalPromociones / $porPagina);

// Obtener promociones paginadas
$sql = "SELECT * FROM promociones LIMIT $porPagina OFFSET $offset";
$resultado = $conn->query($sql);

// Verificamos si hay resultados
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
</head>
<body>
    <h2>Promociones</h2>
    <table>
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
        <?php foreach ($promociones as $promo): ?>
            <tr>
                <td><?= $promo['id'] ?></td>
                <td><?= $promo['textoPromo'] ?></td>
                <td><?= $promo['fechaDesdePromo'] ?></td>
                <td><?= $promo['fechaHastaPromo'] ?></td>
                <td><?= $promo['idCategoriaCliente'] ?></td>
                <td><?= $promo['diasSemana'] ?></td>
                <td><?= $promo['estadoPromo'] ?></td>
                <td><?= $promo['idcodLocal'] ?></td>
                <td>
                    <?php if ($promo['estadoPromo'] == 'pendiente'): ?>
                        <form method="post" action="actualizar-estado-promociones.php" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $promo['id'] ?>">
                            <input type="hidden" name="accion" value="aprobar">
                            <button type="submit">Aprobar</button>
                        </form>
                        <form method="post" action="actualizar-estado-promociones.php" style="display:inline; margin-left: 5px;">
                            <input type="hidden" name="id" value="<?= $promo['id'] ?>">
                            <input type="hidden" name="accion" value="denegar">
                            <button type="submit">Denegar</button>
                        </form>
                    <?php else: ?>
                        <?= $promo['estadoPromo'] ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <div>
        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
            <a href="?pagina=<?= $i ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
</body>
</html>
