<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
                <form method="POST" class="mt-3">
                    <div class="mb-3 position-relative">
                        <label for="nueva_contrasena" class="form-label">Nueva contraseña:</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="nueva_contrasena" name="nueva_contrasena" required>
                            <button type="button" class="btn btn-outline-secondary toggle-password" data-target="nueva_contrasena">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3 position-relative">
                        <label for="confirmar_contrasena" class="form-label">Confirmar contraseña:</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="confirmar_contrasena" name="confirmar_contrasena" required>
                            <button type="button" class="btn btn-outline-secondary toggle-password" data-target="confirmar_contrasena">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-warning">Cambiar contraseña</button>
                </form>
                <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nueva_contrasena'], $_POST['confirmar_contrasena'])) {
                    $nueva = $_POST['nueva_contrasena'];
                    $confirmar = $_POST['confirmar_contrasena'];

                    if ($nueva !== $confirmar) {
                        echo '<div class="alert alert-danger mt-3">Las contraseñas no coinciden.</div>';
                    } elseif (strlen($nueva) < 6) {
                        echo '<div class="alert alert-danger mt-3">La contraseña debe tener al menos 6 caracteres.</div>';
                    } else {
                        $hash = password_hash($nueva, PASSWORD_DEFAULT);
                        $sql_update = "UPDATE users SET pasUser = ? WHERE emailUser = ?";
                        $stmt_update = $conn->prepare($sql_update);
                        $stmt_update->bind_param("ss", $hash, $email);

                        if ($stmt_update->execute()) {
                            echo '<div class="alert alert-success mt-3">Contraseña actualizada correctamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger mt-3">Error al actualizar la contraseña.</div>';
                        }
                    }
                }
                ?>

                <p class="card-text mt-4">
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
<script>
    document.querySelectorAll('.toggle-password').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const targetId = btn.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = btn.querySelector('span');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
</script>

</html>