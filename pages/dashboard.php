<?php
// session_start();
// require_once __DIR__ . '/../vendor/autoload.php';
// use Firebase\JWT\JWT;
// use Firebase\JWT\Key;
// $key ='MESSI';
// if(isset($_COOKIE['token'])){
//     $token = $_COOKIE['token'];
//     $decoded = JWT::decode($token, new Key($key, 'HS256'));
// }else{
//     header('Location: login.php');
//     exit();
// }


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Shopping Promos - Inicio</title>
    <link rel="stylesheet" href="../assets/css/bootstrap-css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style-header.css">
</head>
<body>
    <?php include'header.php'; ?>
    <main>
        <section>
            <h2>Promociones Destacadas</h2>
            <article>
                <h3>Descuento en Tienda A</h3>
                <p>20% de descuento en toda la tienda hasta el 30 de abril.</p>
            </article>
            <article>
                <h3>Promo 2x1 en Comidas</h3>
                <p>Válido de lunes a miércoles en Patio de Comidas.</p>
            </article>
        </section>
        <section>
            <h2>Novedades</h2>
            <ul>
                <li>Nuevo local de tecnología abrió en el segundo piso.</li>
                <li>Horarios especiales por feriado este fin de semana.</li>
            </ul>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Shopping Promos. Todos los derechos reservados.</p>
    </footer>
    <script src="../assets/js/bootstrap-js/bootstrap.bundle.min.js"></script>
</body>
</html>