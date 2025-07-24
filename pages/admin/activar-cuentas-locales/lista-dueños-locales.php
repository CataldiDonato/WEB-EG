<?php

require_once __DIR__ . '/../../../vendor/autoload.php';


use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if (!isset($_COOKIE['token'])) {
    header("Location: ../../login.php");
    exit();
}

$token = $_COOKIE['token'];
$clave_secreta = $_ENV['CLAVE'];  

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

include("../../../include/db.php");

$porPagina = 5;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina - 1) * $porPagina;

$buscar = isset($_GET['buscar']) ? $conn->real_escape_string(trim($_GET['buscar'])) : '';

$usuarios = [];
$total = 0;
$totalPaginas = 1;

try {
    if (!empty($buscar)) {
        $totalQuery = $conn->query("SELECT COUNT(*) AS total FROM users 
            WHERE id_tipo = 3 AND emailUser LIKE '%$buscar%'");

        $sql = "SELECT * FROM users 
                WHERE id_tipo = 3 AND emailUser LIKE '%$buscar%' 
                LIMIT $porPagina OFFSET $offset";
    } else {
        $totalQuery = $conn->query("SELECT COUNT(*) AS total FROM users WHERE id_tipo = 3");
        $sql = "SELECT * FROM users WHERE id_tipo = 3 LIMIT $porPagina OFFSET $offset";
    }

    if ($totalQuery) {
        $total = $totalQuery->fetch_assoc()['total'];
        $totalPaginas = max(1, ceil($total / $porPagina));
    }

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
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-5">
            <input type="text" name="buscar" class="form-control" placeholder="Buscar por email" value="<?= isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : '' ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Buscar</button>
        </div>
        <div class="col-md-2">
            <a href="lista-dueños-locales.php" class="btn btn-secondary w-100">Limpiar</a>
        </div>
    </form>
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
                                <form method="post" action="actualizar-estado-dueño.php" class="d-inline form-aprobar">
                                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                    <input type="hidden" name="accion" value="aprobar">
                                    <button type="button" class="btn btn-sm btn-success btn-aprobar">Aprobar</button>
                                </form>
                                <form method="post" action="actualizar-estado-dueño.php" class="d-inline form-denegar">
                                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
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
        <nav aria-label="Paginación de locales" class="d-flex justify-content-center my-4">
            <ul class="pagination">
                <li class="page-item <?= $pagina <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?pagina=<?= $pagina - 1 ?>&buscar=<?= urlencode($buscar) ?>">Anterior</a>
                </li>
                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                        <a class="page-link" href="?pagina=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $pagina >= $totalPaginas ? 'disabled' : '' ?>">
                        <a class="page-link" href="?pagina=<?= $pagina + 1 ?>&buscar=<?= urlencode($buscar) ?>">Siguiente</a>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.querySelectorAll('.btn-aprobar').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: "¿Aprobar cuenta dueño local?",
            text: "¿Estás seguro de aprobar esta cuenta?",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sí, aprobar"
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "¡Aprobado!",
                    text: "Su cuenta fue aprobada con éxito.",
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
            title: "¿Denegar cuenta?",
            text: "¿Estás seguro de denegar esta cuenta?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Sí, denegar"
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Denegada!",
                    text: "Esta cuenta fue denegada con exito.",
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
