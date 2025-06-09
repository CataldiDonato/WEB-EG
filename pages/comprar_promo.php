<?php
include '../include/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codPromo = $_POST['codPromo'] ?? null;
    $idUsuario = $_POST['idUsuario'] ?? null;
    $estado = $_POST['estado'] ?? 'pendiente';
    $fechaUso = date('Y-m-d H:i:s');

    if ($codPromo && $idUsuario) {
        $sql = "INSERT INTO usopromociones (codPromo, fechaUso, estado, idUsuario) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issi", $codPromo, $fechaUso, $estado, $idUsuario);
        if ($stmt->execute()) {
            $sql = "SELECT * FROM users WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $idUsuario);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $usuario = $result->fetch_assoc();
                $usuario['usosPromociones'] += 1; 
                if ($usuario['usosPromociones'] > 5 and $usuario['idCategoria'] == 1) {
                    $usuario['idCategoria'] = 2; 
                }
                if ($usuario['usosPromociones'] > 10 and $usuario['idCategoria'] == 2) {
                    $usuario['idCategoria'] = 3; 
                }
                $updateSql = "UPDATE users SET usosPromociones = ?, idCategoria = ? WHERE id = ?";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param("iii", $usuario['usosPromociones'], $usuario['idCategoria'], $idUsuario);
                $updateStmt->execute();
            }

            header("Location: promociones.php?compra=ok");
            exit;
        } else {
            echo '<div class="alert alert-danger">Error al registrar el uso de la promoción.</div>';
        }
    } else {
        echo '<div class="alert alert-danger">Datos incompletos.</div>';
    }

}
?>