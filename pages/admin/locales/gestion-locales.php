<?php
require_once __DIR__ . '/../../../vendor/autoload.php';


use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Verificar que exista la cookie
if (!isset($_COOKIE['token'])) {
    header("Location: ../../login.php");
    exit();
}

$token = $_COOKIE['token'];
$clave_secreta = $_ENV['CLAVE']; 

try {
    // Decodificar el token
    $decoded = JWT::decode($token, new Key($clave_secreta, 'HS256'));

    // Extraer el tipo de usuario
    $id_tipo = $decoded->data->id_tipo ?? null;

    // Verificar si es admin (id_tipo == 1)
    if ($id_tipo !== 1) {
        header("Location: ../../dashboard.php");
        exit();
    }

    // Si pasó todas las verificaciones, mostrar la página normalmente
} catch (Exception $e) {
    // Token inválido o expirado
    header("Location: ../../login.php");
    exit();
}

include("../../../include/db.php");

// Configuración de paginación 
$porPagina = 5;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina - 1) * $porPagina;

// Filtro de búsqueda
$buscar = isset($_GET['buscar']) ? $conn->real_escape_string(trim($_GET['buscar'])) : '';

if (!empty($buscar)) {
    $sql = "SELECT * FROM locales 
            WHERE nombreLocal LIKE '%$buscar%' 
            OR ubicacionLocal LIKE '%$buscar%' 
            OR rubroLocal LIKE '%$buscar%' 
            LIMIT $porPagina OFFSET $offset";
    
    $totalQuery = "SELECT COUNT(*) AS total FROM locales 
        WHERE nombreLocal LIKE '%$buscar%' 
            OR ubicacionLocal LIKE '%$buscar%' 
            OR rubroLocal LIKE '%$buscar%'";
} else {
    $sql = "SELECT * FROM locales LIMIT $porPagina OFFSET $offset";
    $totalQuery = "SELECT COUNT(*) AS total FROM locales";
}

$resultado = $conn->query($sql);
if (!$resultado) {
    die("Error en la consulta de locales: " . $conn->error);
}
$totalLocalesQuery = $conn->query($totalQuery);
if ($totalLocalesQuery) {
    $totalLocales = $totalLocalesQuery->fetch_assoc()['total'];
} else {
    die("Error en la consulta COUNT: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Gestión de Locales</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="gestion-locales.css" />

</head>
<body class="body-gestion-locales">

<div class="container my-5">
    <h1 class="mb-4 text-center title-gestion-locales">Gestión de Locales</h1>

    <div class="card mb-5 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h2 class="h5 mb-0 ">Agregar nuevo local</h2>
        </div>
        <div class="card-body">
            <form action="agregar-local.php" method="POST" class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="nombreLocal" class="form-control" placeholder="Nombre del local" required />
                </div>
                <div class="col-md-6">
                    <input type="text" name="ubicacionLocal" class="form-control" placeholder="Ubicación del local" required />
                </div>
                <div class="col-md-6">
                    <input type="text" name="rubroLocal" class="form-control" placeholder="Rubro del local" required />
                </div>
                <div class="col-md-6">
                    <input type="number" name="codUsuario" class="form-control" placeholder="Id de usuario" required />
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-success">Agregar</button>
                </div>
            </form>
        </div>
    </div>

    <h2 class="mb-3 title-gestion-locales">Lista de Locales</h2>
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-5">
            <input type="text" name="buscar" class="form-control" placeholder="Buscar por ubicación o rubro" value="<?= isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : '' ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Buscar</button>
        </div>
        <div class="col-md-2">
            <a href="gestion-locales.php" class="btn btn-secondary w-100">Limpiar</a>
        </div>
    </form>
    <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Ubicación</th>
                    <th>Rubro</th>
                    <th>Id Usuario</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($local = $resultado->fetch_assoc()) { ?>
                    <tr>
                        <td class="td-acciones"><?= htmlspecialchars($local['id']) ?></td>
                        <td class="td-acciones"><?= htmlspecialchars($local['nombreLocal']) ?></td>
                        <td class="td-acciones"><?= htmlspecialchars($local['ubicacionLocal']) ?></td>
                        <td class="td-acciones"><?= htmlspecialchars($local['rubroLocal']) ?></td>
                        <td class="td-acciones"><?= htmlspecialchars($local['codUsuario']) ?></td>
                        <td class="td-acciones">
                            <form action="eliminar-local.php" method="POST" class="d-inline">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($local['id']) ?>">
                                <button type="button" class="btn btn-sm btn-danger btn-eliminar">Eliminar</button>
                            </form>
                            <form action="actualizar-local.php" method="POST" class="d-inline">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($local['id']) ?>">
                                <button type="submit" class="btn btn-sm btn-warning">Actualizar</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
                <?php if ($resultado->num_rows === 0): ?>
                    <tr><td colspan="6" class="text-center">No hay locales para mostrar.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <nav aria-label="Paginación de locales" class="d-flex justify-content-center my-4">
        <ul class="pagination">
            <?php if ($pagina > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?pagina=<?= $pagina - 1 ?>&buscar=<?= urlencode($buscar) ?>">Anterior</a>
                </li>
            <?php else: ?>
                <li class="page-item disabled"><span class="page-link">Anterior</span></li>
            <?php endif; ?>

            <li class="page-item active"><span class="page-link"><?= $pagina ?></span></li>

            <?php if ($pagina * $porPagina < $totalLocales): ?>
                <li class="page-item">
                    <a class="page-link" href="?pagina=<?= $pagina + 1 ?>&buscar=<?= urlencode($buscar) ?>">Siguiente</a>
                </li>
            <?php else: ?>
                <li class="page-item disabled"><span class="page-link">Siguiente</span></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="text-center mt-3">
        <a href="../menu-admin.php" class="btn btn-secondary">Volver al menú</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php if (isset($_GET['agregado']) && $_GET['agregado'] === 'ok') : ?>
<script>
    Swal.fire({
        icon: 'success',
        title: '¡Local agregado!',
        text: 'El local se ha agregado exitosamente.',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Aceptar'
    });
</script>
<?php endif; ?>
<script>
document.querySelectorAll('.btn-eliminar').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: "¿Estás seguro?",
            text: "¡No podrás revertir esto!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sí, eliminarlo"
        }).then((result) => {
            if (result.isConfirmed) {
                btn.closest('form').submit();
            }
        });
    });
});
</script>
</body>
</html>
