<?php
include("../../../include/db.php");

$porPagina = 5;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina - 1) * $porPagina;

$usuarios = [];
$total = 0;
$totalPaginas = 1;

try {
    $totalQuery = $conn->query("SELECT COUNT(*) AS total FROM users WHERE id_tipo = 3");
    if ($totalQuery) {
        $total = $totalQuery->fetch_assoc()['total'];
        $totalPaginas = max(1, ceil($total / $porPagina));
    }
    $sql = "SELECT * FROM users WHERE id_tipo = 3 LIMIT $porPagina OFFSET $offset";
    $resultado = $conn->query($sql);
    if ($resultado && $resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
            $usuarios[] = $row;
        }
    }
} catch (Exception $e) {
    echo "Error en la consulta: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Dueños de Locales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="estilos-activacion-cuentas-dueños.css">
</head>
<body class="bg-light p-4 body-activacion-cuentas-dueños">

<div class="container">
    <h2 class="mb-4 title-dueños-locales-admin">Usuarios (Dueños de locales)</h2>
    <?php if (count($usuarios) > 0): ?>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>ID Tipo</th>
                    <th>Email</th>
                    <th>Fecha Ingreso</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= $user['id_tipo'] ?></td>
                        <td><?= $user['emailUser'] ?></td>
                        <td><?= $user['fechaIngreso'] ?></td>
                        <td>
                            <span class="badge <?= $user['aprobado'] == '1' ? 'bg-success' : 'bg-secondary' ?>">
                                <?= $user['aprobado'] == '1' ? 'Aprobado' : 'No aprobado' ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($user['aprobado'] == '0'): ?>
                                <form method="post" action="actualizar-estado-dueño.php" class="d-inline">
                                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                    <input type="hidden" name="accion" value="aprobar">
                                    <button type="submit" class="btn btn-sm btn-success">Aprobar</button>
                                </form>
                                <form method="post" action="actualizar-estado-dueño.php" class="d-inline">
                                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                    <input type="hidden" name="accion" value="denegar">
                                    <button type="submit" class="btn btn-sm btn-danger">Denegar</button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted">Sin acciones</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <nav aria-label="Paginación de locales" class="d-flex justify-content-center my-4">
            <ul class="pagination">
                <li class="page-item <?= $pagina <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?pagina=<?= max(1, $pagina - 1) ?>">Anterior</a>
                </li>
                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                        <a class="page-link" href="?pagina=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $pagina >= $totalPaginas ? 'disabled' : '' ?>">
                    <a class="page-link" href="?pagina=<?= min($totalPaginas, $pagina + 1) ?>">Siguiente</a>
                </li>
            </ul>
        </nav>
    <?php else: ?>
        <p>No hay usuarios disponibles.</p>
    <?php endif; ?>
    <div class="text-center mt-3">
        <a href="../menu-admin.php" class="btn btn-secondary">Volver al menú</a>
    </div>
</div>

</body>
</html>
