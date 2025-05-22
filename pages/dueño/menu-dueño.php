<?php
include '../../include/db.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['submit'])) {
    $textoPromo = $_POST['descripcionPromocion'] ?? '';
    $fechaDesdePromo = $_POST['diaComienzaPromocion'] ?? '';
    $fechaHastaPromo = $_POST['diaTerminaPromocion'] ?? '';
    $categoriaCliente = $_POST['categoriaCliente'] ?? '';
    $diasSemana = $_POST['diasSemana'] ?? [];
    $diasSemanaString = implode(',', $diasSemana); // Convertir el array a una cadena separada por comas
    $estPromo ="pendiente";
    
    $sql_check = "SELECT * FROM locales WHERE codUsuario = ?";
    $stmt = $conn->prepare($sql_check);
    $stmt->bind_param("i", $_SESSION['idUser']);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $fila = $resultado->fetch_assoc();
    
    $codLocalDueño = $fila['id'];

    $rutaCarpeta = '../../images/';
    $rutaImagen = ''; 
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $nombreArchivo = basename($_FILES['imagen']['name']);
        $rutaCarpeta = 'assets/img/';
        $rutaImagen = $rutaCarpeta . time() . '_' . $nombreArchivo;
    
    $tipoPermitido = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['imagen']['type'], $tipoPermitido)) {
            if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaImagen)) {
                echo '<div class="alert alert-danger">Error al subir la imagen.</div>';
                $rutaImagen = '';
            }
        } else {
            echo '<div class="alert alert-warning">Formato de imagen no permitido.</div>';
            $rutaImagen = '';
        }
    }

    if (empty($textoPromo) || empty($fechaDesdePromo) || empty($fechaHastaPromo) || empty($categoriaCliente)) {
        echo '<div class="alert alert-danger">Por favor, complete todos los campos.</div>';
    } else {
        $sql = "INSERT INTO promociones (textoPromo, fechaDesdePromo, fechaHastaPromo, idCategoriaCliente, diasSemana, estadoPromo, idcodLocal, rutaImagen) VALUES (?, ?, ?, ?, ?, ?, ?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssis", $textoPromo, $fechaDesdePromo, $fechaHastaPromo, $categoriaCliente, $diasSemanaString, $estPromo, $codLocalDueño, $rutaImagen);

        if ($stmt->execute()) {
            echo '<div class="alert alert-success">Promoción cargada exitosamente.</div>';
        } else {
            echo '<div class="alert alert-danger">Error al cargar la promoción: ' . $conn->error . '</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MenuDueño</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
</head>
<body>
    <div class="container mt-5">
        <button class="btn btn-primary" onclick="window.location.href='../perfil.php'">Volver</button>
        <h1>Promociones activas</h1>
        <?php
        $sql = "SELECT * FROM locales WHERE codUsuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $_SESSION['idUser']);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado->num_rows === 1) {
            $local = $resultado->fetch_assoc();
            $idlocal = $local['id'];

            $sql = "SELECT * FROM promociones WHERE idcodLocal = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $idlocal);
            $stmt->execute();
            $resultado = $stmt->get_result();
            if ($resultado->num_rows > 0) {
                while ($promocion = $resultado->fetch_assoc()) {
                    echo '<div class="p-3 mb-3" style="background-color: #f8f9fa; border: 1px solid #ddd; border-radius: 5px;">';
                    echo '<div><strong>Promoción:</strong> ' . $promocion['textoPromo'] . '</div>';
                    echo '<div><strong>Estado:</strong> ' . $promocion['estadoPromo'] . '</div>';
                    echo '<div><strong>Fecha de inicio:</strong> ' . $promocion['fechaDesdePromo'] . '</div>';
                    echo '<div><strong>Fecha de fin:</strong> ' . $promocion['fechaHastaPromo'] . '</div>';
                    echo '<div><strong>Días de la semana:</strong> ' . $promocion['diasSemana'] . '</div>';
                    echo '<div><strong>Categoría de cliente:</strong> ' . $promocion['idCategoriaCliente'] . '</div>';
                    echo '<div><strong>Imagen:</strong> <img src="' . $promocion['rutaImagen'] . '" alt="Imagen de la promoción" style="max-width: 200px; max-height: 200px;"></div>';
                    echo '<br>';  
                    echo '<button class="btn btn-danger" onclick="window.location.href=\'eliminarPromocion.php?id=' . $promocion['id'] . '\'">Eliminar</button>';
                    echo '</div>';
                }
            } else {
                echo '<div class="alert alert-warning">No hay promociones activas.</div>';
            }
        }
        ?>
        <h1>Cargar Promocion</h1>
        <form id="cargarPromocion-form" class="form" action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="descripcionPromocion" class="form-label">Descripción Promoción</label>
                <input type="text" class="form-control" id="descripcionPromocion" name="descripcionPromocion" required>
            </div>
            <div class="mb-3">
                <label for="diaComienzaPromocion" class="form-label">Día Comienza Promoción</label>
                <input type="date" class="form-control" id="diaComienzaPromocion" name="diaComienzaPromocion" required>
            </div>
            <div class="mb-3">
                <label for="diaTerminaPromocion" class="form-label">Día Termina Promoción</label>
                <input type="date" class="form-control" id="diaTerminaPromocion" name="diaTerminaPromocion" required>
            </div>
            <div class="mb-3">
                <label for="categoriaCliente" class="form-label">Categoría Cliente</label>
                <select class="form-control" id="categoriaCliente" name="categoriaCliente" required>
                    <option value="1">Inicial</option>
                    <option value="2">Medium</option>
                    <option value="3">Premium</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="diasSemana" class="form-label">Días de la semana que está vigente</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="lunes" name="diasSemana[]" value="l">
                    <label class="form-check-label" for="lunes">Lunes</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="martes" name="diasSemana[]" value="m">
                    <label class="form-check-label" for="martes">Martes</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="miercoles" name="diasSemana[]" value="x">
                    <label class="form-check-label" for="miercoles">Miércoles</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="jueves" name="diasSemana[]" value="j">
                    <label class="form-check-label" for="jueves">Jueves</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="viernes" name="diasSemana[]" value="v">
                    <label class="form-check-label" for="viernes">Viernes</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="sabado" name="diasSemana[]" value="s">
                    <label class="form-check-label" for="sabado">Sábado</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="domingo" name="diasSemana[]" value="d">
                    <label class="form-check-label" for="domingo">Domingo</label>
                </div>
            </div>
            <div class="mb-3">
                <label for="imagen" class="form-label">Selecciona una imagen</label>
                <input type="file" name="imagen" id="imagen" class="form-control" required>
            </div>
            <div class="text-center">
                <input type="submit" name="submit" class="btn btn-primary" value="Cargar Promoción">
            </div>
        </form>
                </div>
                </div>
            </div>
            <div>
                <input type="submit" name="submit" class="buton-login" value="submit">
            </div>
        </form>
    </div>


</body>
</html>