<?php
include '../include/db.php';
if (isset($_POST['submit'])) {
    $descripcionPromocion = $_POST['descripcionPromocion'] ?? '';
    $diaComienzaPromocion = $_POST['diaComienzaPromocion'] ?? '';
    $diaTerminaPromocion = $_POST['diaTerminaPromocion'] ?? '';
    $categoriaCliente = $_POST['categoriaCliente'] ?? '';
    $diasSemana = $_POST['diasSemana'] ?? [];
    $diasSemanaString = implode(',', $diasSemana); // Convertir el array a una cadena separada por comas
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
        <h1>Cargar Promocion</h1>
        <form id="cargarPromocion-form" class="form" action="" method="post">
            <div class="mb-3">
                <label for="descripcionPromocion" class="form-label"> descripcion Promocion</label>
                <input type="text" class="form-control" id="descripcionPromocion" name="descripcionPromocion" required></input>
            </div>
            <div class="mb-3">
                <label for="diaComienzaPromocion" class="form-label"> Día Comienza Promocion</label>
                <input type="date" class="form-control" id="diaComienzaPromocion" name="diaComienzaPromocion" required></input>
            </div>
            <div class="mb-3">
                <label for="diaTerminaPromocion" class="form-label"> Día Termina Promocion</label>
                <input type="date" class="form-control" id="diaTerminaPromocion" name="diaTerminaPromocion" required></input>
            </div>
            <div class="mb-3">
                <label for="categoriaCliente" class="form-label">Categoria Cliente</label>
                <select class="form-control" id="categoriaCliente" name="categoriaCliente" required>
                    <option value="inicial">Inicial</option>
                    <option value="medium">Medium</option>
                    <option value="premium">Premium</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="diasSemana" class="form-label"> Días de la semana que está vigente</label>
                <div id="diasSemana">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="lunes" name="diasSemana[]" value="lunes">
                    <label class="form-check-label" for="lunes">Lunes</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="martes" name="diasSemana[]" value="martes">
                    <label class="form-check-label" for="martes">Martes</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="miercoles" name="diasSemana[]" value="miercoles">
                    <label class="form-check-label" for="miercoles">Miércoles</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="jueves" name="diasSemana[]" value="jueves">
                    <label class="form-check-label" for="jueves">Jueves</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="viernes" name="diasSemana[]" value="viernes">
                    <label class="form-check-label" for="viernes">Viernes</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="sabado" name="diasSemana[]" value="sabado">
                    <label class="form-check-label" for="sabado">Sábado</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="domingo" name="diasSemana[]" value="domingo">
                    <label class="form-check-label" for="domingo">Domingo</label>
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