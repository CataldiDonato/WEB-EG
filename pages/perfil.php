<?php
include '../include/db.php';
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if (!isset($_COOKIE['token'])) {
    header("Location: login.php");
    exit();
}

$token = $_COOKIE['token'];
$clave_secreta = "MESSI"; 

try {
    $decoded = JWT::decode($token, new Key($clave_secreta, 'HS256'));

    $id_tipo = $decoded->data->id_tipo ?? null;

} catch (Exception $e) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['emailUser'];
$sql_check = "SELECT * FROM users WHERE emailUser = ?";
$stmt = $conn->prepare($sql_check);
$stmt->bind_param("s", $email);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 1) {
    $usuario = $resultado->fetch_assoc();
    $usuarioValidado = $usuario['validado'];
} else {
    echo "No se encontró el usuario en la base de datos.";
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <link rel="stylesheet" href="../assets/css/bootstrap-css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style-header.css">
    <style>
        .footerPerfil {
            margin-top: 190px;
        }

        #footerAdmin {
            margin-bottom: 80px;
        }

        #footerDueño {
            margin-bottom: 80px;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container mt-4">
        <h1 class="text-center mb-4">Perfil</h1>

        <div class="card mb-4">
            <div class="card-body">
                <h2 class="card-title">Bienvenido, <?php echo $_SESSION['emailUser']; ?></h2>
                <p class="card-text">Tipo de usuario: <strong><?php echo $_SESSION['tipoUser']; ?></strong></p>
                <p class="card-text">
                    <?php
                    echo $usuarioValidado ?
                        '<span class="badge bg-success">Email Validado</span>' :
                        '<span class="badge bg-warning text-dark">Valida tu Email para poder acceder a las promociones</span>';
                    ?>
                </p>
            </div>
        </div>
        <?php if ($_SESSION["tipoUser"] == 3): ?>
            <div class="d-flex gap-3 mb-4">
                <a href="dueño/menu-dueño.php" class="btn btn-primary" id="footerDueño">Ir a Panel del Dueño</a>
            </div>
        <?php endif; ?>
        <?php if ($_SESSION["tipoUser"] == 1): ?>
            <div class="d-flex gap-3 mb-4">
                <a href="admin/menu-admin.php" class="btn btn-primary" id="footerAdmin">Ir a Panel del Admin</a>
            </div>
        <?php endif; ?>

        <?php if ($_SESSION["tipoUser"] == 2): ?>
            <h3 class="mb-4">Lista de promociones:</h3>

            <?php
            $sql = "SELECT * FROM usopromociones WHERE idUsuario = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $_SESSION['idUser']);
            $stmt->execute();
            $resultado = $stmt->get_result();
            ?>

            <?php if ($resultado->num_rows > 0): ?>
                <div class="row">
                    <?php while ($promo = $resultado->fetch_assoc()): ?>
                        <div class="col-12 col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h4 class="card-title">Promoción: <?= htmlspecialchars($promo['codPromo']) ?></h4>
                                    <p class="card-text">Estado: <?= htmlspecialchars($promo['estado']) ?></p>

                                    <?php
                                    $sql2 = "SELECT * FROM promociones WHERE id = ?";
                                    $stmt2 = $conn->prepare($sql2);
                                    $stmt2->bind_param("i", $promo['codPromo']);
                                    $stmt2->execute();
                                    $resultado2 = $stmt2->get_result();

                                    while ($detalle = $resultado2->fetch_assoc()):
                                    ?>
                                        <p class="card-text"><strong>Nombre:</strong> <?= htmlspecialchars($detalle['textoPromo']) ?></p>
                                        <p class="card-text"><strong>Desde:</strong> <?= htmlspecialchars($detalle['fechaDesdePromo']) ?></p>
                                        <p class="card-text"><strong>Hasta:</strong> <?= htmlspecialchars($detalle['fechaHastaPromo']) ?></p>
                                        <div class="mb-3">
                                            <strong>Imagen:</strong><br>
                                            <img src="../<?= $detalle['rutaImagen'] ?>" alt="Imagen de la promoción" class="img-thumbnail" style="max-width: 200px;">
                                        </div>
                                        <a href="descargar_cupon.php?
                                            id=<?= $promo['codPromo'] ?>&
                                            texto=<?= urlencode($detalle['textoPromo']) ?>&
                                            desde=<?= urlencode($detalle['fechaDesdePromo']) ?>&
                                            hasta=<?= urlencode($detalle['fechaHastaPromo']) ?>"
                                            class="btn btn-success">
                                            Descargar cupón
                                        </a>

                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">No hay promociones.</div>
            <?php endif; ?>
        <?php endif; ?>

    </div>
    <footer class="bg-dark text-white text-center py-3 footerPerfil">
        <?php include 'footer.php'; ?>
    </footer>
</body>

</html>