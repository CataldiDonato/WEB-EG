<?php
require_once __DIR__ . '/../vendor/autoload.php';

use setasign\Fpdi\Tcpdf\Fpdi;

session_start();

if (!isset($_GET['id'])) {
    die('ID de cupón no especificado');
}

$idPromo = intval($_GET['id'] ?? 0);
$texto = $_GET['texto'] ?? '';
$desde = $_GET['desde'] ?? '';
$hasta = $_GET['hasta'] ?? '';
$desdeFormateado = date('d/m/Y', strtotime($desde));
$hastaFormateado = date('d/m/Y', strtotime($hasta));

// Cargamos la plantilla PDF
$pdf = new Fpdi();
$pdf->AddPage();

$pageCount = $pdf->setSourceFile('../assets/cupon/cupon.pdf');
$tpl = $pdf->importPage(1);
$pdf->useTemplate($tpl);

// Personalizamos datos
$pdf->SetFont('Helvetica', '', 14);
$pdf->SetTextColor(0, 0, 0);

$pdf->StartTransform();                         // Iniciar transformación
$pdf->Rotate(90, 45, 50);                       // (ángulo, x, y) → centro de rotación
$pdf->SetFont('Helvetica', 'B', 16);            // B = negrita, 16 = tamaño
$pdf->SetXY(20, 50);                            // Posición del texto
$pdf->Write(0, "id: " . $idPromo);              // Escribir texto
$pdf->StopTransform();                          // Finalizar transformación
               // Finalizar transformación


$pdf->SetXY(90, 55);
$pdf->Write(0, "Promoción: " . $texto);

$pdf->SetXY(90, 66);
$pdf->Write(0, "Válida desde: " . $desdeFormateado);

$pdf->SetXY(90, 77);
$pdf->Write(0, "Hasta: " . $hastaFormateado);

// ✅ Envía el PDF al navegador
$pdf->Output('cupon_' . $idPromo . '.pdf', 'D');
exit;
