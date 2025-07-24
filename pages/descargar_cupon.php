<?php
//ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1); 
error_reporting(E_ALL);
require_once __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

use setasign\Fpdi\Tcpdf\Fpdi;

session_start();

if (!isset($_COOKIE['token'])) {
    header("Location: login.php");
    exit();
}

$token = $_COOKIE['token'];
$clave_secreta = $_ENV['CLAVE']; 



try {

    $decoded = JWT::decode($token, new Key($clave_secreta, 'HS256'));

    $id_tipo = $decoded->data->id_tipo ?? null;


} catch (Exception $e) {

    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    die('ID de cupón no especificado');
}

$idPromo = intval($_GET['id'] ?? 0);
$texto = $_GET['texto'] ?? '';
$desde = $_GET['desde'] ?? '';
$hasta = $_GET['hasta'] ?? '';
$desdeFormateado = date('d/m/Y', strtotime($desde));
$hastaFormateado = date('d/m/Y', strtotime($hasta));

$pdf = new Fpdi();
$pdf->AddPage();

$archivoPdf = __DIR__ . '/../assets/cupon/cupon.pdf';

$pageCount = $pdf->setSourceFile($archivoPdf);

$tpl = $pdf->importPage(1);
$pdf->useTemplate($tpl);


$pdf->SetFont('Helvetica', '', 14);
$pdf->SetTextColor(0, 0, 0);

$pdf->StartTransform();                         
$pdf->Rotate(90, 45, 50);                       
$pdf->SetFont('Helvetica', 'B', 16);            
$pdf->SetXY(20, 50);                            
$pdf->Write(0, "id: " . $idPromo);              
$pdf->StopTransform();                          
 


$pdf->SetXY(90, 55);
$pdf->Write(0, "Promoción: " . $texto);

$pdf->SetXY(90, 66);
$pdf->Write(0, "Válida desde: " . $desdeFormateado);

$pdf->SetXY(90, 77);
$pdf->Write(0, "Hasta: " . $hastaFormateado);


$pdf->Output('cupon_' . $idPromo . '.pdf', 'D');
exit;
