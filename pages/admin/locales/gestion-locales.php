<?php
include("../../../include/db.php");

// Configuración de paginacion 
$porPagina = 5;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina - 1) * $porPagina;

// Obtener total de locales
$totalLocalesQuery = $conn->query("SELECT COUNT(*) AS total FROM locales");
$totalLocales = $totalLocalesQuery->fetch_assoc()['total'];

// Obtener locales paginados
$sql = "SELECT * FROM locales LIMIT $porPagina OFFSET $offset";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Locales</title>
</head>
<body>
    <h1>Gestión de Locales</h1>
    <h2>Agregar nuevo local</h2>
    <form action="agregar-local.php" method="POST">
        <input type="text" name="nombreLocal" placeholder="Nombre del local" required>
        <input type="text" name="ubicacionLocal" placeholder="Ubicacion del local" required>
        <input type="text" name="rubroLocal" placeholder="Rubro del local" required>
        <input type="number" name="codUsuario" placeholder="Codigo de usuario" required>
        <button type="submit">Agregar</button>
    </form>
    <h2>Lista de Locales</h2>
    <table>
        <thead>
            <tr>
                <th>Id</th>
                <th>Nombre</th>
                <th>Ubicacion del local</th>
                <th>Rubro del local</th>
                <th>Codigo de usuario</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($local = $resultado->fetch_assoc()) { ?>
                <tr>
                    <td><?= $local['codLocal'] ?></td>
                    <td><?= $local['nombreLocal'] ?></td>
                    <td><?= $local['ubicacionLocal'] ?></td>
                    <td><?= $local['rubroLocal'] ?></td>
                    <td><?= $local['codUsuario'] ?></td>
                    <td>
                        <form action="eliminar-local.php" method="POST" style="display:inline;">
                            <input type="hidden" name="codLocal" value="<?= $local['codLocal'] ?>">
                            <button type="submit">Eliminar</button>
                        </form>
                        <form action="actualizar-local.php" method="POST" style="display:inline;">
                            <input type="hidden" name="codLocal" value="<?= $local['codLocal'] ?>">
                            <button type="submit">Actualizar</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <div">
        <?php if ($pagina > 1): ?>
            <a href="?pagina=<?= $pagina - 1 ?>">Anterior</a>
        <?php endif; ?>
        Página <?= $pagina ?>
        <?php if ($pagina * $porPagina < $totalLocales): ?>
            <a href="?pagina=<?= $pagina + 1 ?>">Siguiente</a>
        <?php endif; ?>
    </div>
    <a href="../menu-admin.php">Volver</a>
</body>
</html>
