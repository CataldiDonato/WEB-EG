<?php
include("../../../include/db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $stmt = $conn->prepare("SELECT * FROM locales WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $local = $resultado->fetch_assoc();
} else {
    header("Location: gestion-locales.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar Local</title>
</head>
<body>
    <h1>Actualizar Local</h1>

    <form action="guardar-local.php" method="POST">
        <input type="hidden" name="id" value="<?= $local['id'] ?>">

        <input type="text" name="nombreLocal" value="<?= $local['nombreLocal'] ?>" required>
        <input type="text" name="ubicacionLocal" value="<?= $local['ubicacionLocal'] ?>" required>
        <input type="text" name="rubroLocal" value="<?= $local['rubroLocal'] ?>" required>
        <input type="number" name="codUsuario" value="<?= $local['codUsuario'] ?>" required>
        <button type="submit">Guardar Cambios</button>
    </form>

    <a href="gestion-locales.php">Volver</a>
</body>
</html>
