<?php
require_once __DIR__ . '/../../vendor/autoload.php';
include 'validarjwtdueño.php';
include '../../include/db.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Obtener el local del dueño
$sql = "SELECT * FROM locales WHERE codUsuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['idUser']);
$stmt->execute();
$resultado = $stmt->get_result();

$usos = [];
if ($resultado->num_rows === 1) {
    $local = $resultado->fetch_assoc();
    $idlocal = $local['id'];

    // Obtener los usos de promociones de ese local
    $sqlPromo = "SELECT p.textoPromo, u.fechaUso, u.estado, u.idUsuario, u.codPromo
                 FROM usopromociones u
                 INNER JOIN promociones p ON u.codPromo = p.id
                 WHERE p.idcodLocal = ?
                 ORDER BY u.fechaUso DESC";
    $stmtPromo = $conn->prepare($sqlPromo);
    $stmtPromo->bind_param("i", $idlocal);
    $stmtPromo->execute();
    $resultPromo = $stmtPromo->get_result();

    while ($row = $resultPromo->fetch_assoc()) {
        $usos[] = $row;
    }
    $stmtPromo->close();
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Uso de Promociones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .btn-imprimir, .btn-volver { display: none; }
        }
    </style>
</head>
<body class="bg-light">
<div class="container py-5">
    <h1 class="mb-4">Historial de Uso de Promociones</h1>
    <button class="btn btn-primary btn-volver mb-3" onclick="window.location.href='menu-dueño.php'">Volver</button>
    <button class="btn btn-success btn-imprimir mb-3" onclick="window.print()">Imprimir</button>
    <?php if (!empty($usos)): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Promoción</th>
                        <th>Fecha de uso</th>
                        <th>Estado</th>
                        <th>ID Usuario</th>
                        <th>ID Promoción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usos as $uso): ?>
                        <tr>
                            <td><?= htmlspecialchars($uso['textoPromo']) ?></td>
                            <td><?= htmlspecialchars($uso['fechaUso']) ?></td>
                            <td>
                                <span class="badge <?= $uso['estado'] == 'aprobada' ? 'bg-success' : ($uso['estado'] == 'pendiente' ? 'bg-warning' : 'bg-secondary') ?>">
                                    <?= ucfirst($uso['estado']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($uso['idUsuario']) ?></td>
                            <td><?= htmlspecialchars($uso['codPromo']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No se encontraron usos de promociones para este local.</div>
    <?php endif; ?>
</div>
</body>
</html>