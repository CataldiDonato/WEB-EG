<?php
include '../include/db.php';
include 'header.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_POST['submit'])) {
    $asunto = $_POST['asunto'];
    $mensaje = $_POST['mensaje'];
    $email = $_SESSION['emailUser'];

    $asuntomail = "Usuario: " . $email . " tiene una consulta de: " . $asunto;


    $mail = new PHPMailer(true); 

try {
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'donatocataldicode@gmail.com'; 
    $mail->Password   = 'zdwi jcdm lyys coaf'; 
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('donatocataldicode@gmail.com', 'Nombre del Sitio');
    $mail->addAddress('donatocataldifacultad@gmail.com');

    $mail->isHTML(true);
    $mail->Subject = $asuntomail;
    $mail->Body    = nl2br(htmlspecialchars($mensaje)); 
    echo "<script>
        alert('Mensaje enviado!');
        </script>";
    $mail->send();
    } catch (Exception $e) {
        echo "Error al enviar correo: {$mail->ErrorInfo}";
        exit();
    }

}


?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Formulario de Contacto</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    html, body {
      height: 100%;
      margin: 0;
    }
    body {
      display: flex;
      flex-direction: column;
    }
    main {
      flex: 1;
    }
  </style>
</head>
<body class="bg-light py-5">

  <main>
    <br>

    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
          <div class="card shadow-sm rounded-4">
            <div class="card-body p-4">
              <h4 class="mb-4 text-center">Formulario de Contacto</h4>
              <form method="post">

                <div class="mb-3">
                  <label for="asunto" class="form-label">Asunto:</label>
                  <select name="asunto" id="asunto" class="form-select" required>
                    <option value="" disabled selected>Seleccioná un asunto</option>
                    <option value="Consultas sobre locales y tiendas">Consultas sobre locales y tiendas</option>
                    <option value="Promociones y descuentos">Promociones y descuentos</option>
                    <option value="Gastronomía y patio de comidas">Gastronomía y patio de comidas</option>
                    <option value="Estacionamiento">Estacionamiento</option>
                    <option value="Eventos y actividades">Eventos y actividades</option>
                    <option value="Horarios de atención">Horarios de atención</option>
                    <option value="Objetos perdidos">Objetos perdidos</option>
                    <option value="Alquiler de espacios comerciales">Alquiler de espacios comerciales</option>
                    <option value="Accesibilidad">Accesibilidad</option>
                    <option value="Mantenimiento e instalaciones">Mantenimiento e instalaciones</option>
                    <option value="Seguridad">Seguridad</option>
                    <option value="Otras consultas">Otras consultas</option>
                  </select>
                </div>

                <div class="mb-3">
                  <label for="mensaje" class="form-label">Mensaje:</label>
                  <textarea id="mensaje" name="mensaje" class="form-control" rows="6" placeholder="Escribí tu mensaje aquí..." required></textarea>
                </div>

                <div class="d-grid">
                  <button type="submit" name="submit" class="btn btn-primary btn-lg rounded-3">Enviar</button>
                </div>

              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <br>
  </main>

  <footer class="bg-dark text-white text-center py-3">
    <?php include 'footer.php'; ?>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>



