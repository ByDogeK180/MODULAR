<?php
require_once './conecta.php';
require_once '../../librerias/fpdf186/fpdf.php';

if (!isset($_GET['pago_id'])) {
  die("ID no especificado");
}

$pago_id = intval($_GET['pago_id']);
$con = conecta();

$sql = "
  SELECT p.*, e.nombre AS estudiante, e.grado, e.grupo
  FROM pagos p
  INNER JOIN estudiantes e ON p.estudiante_id = e.estudiante_id
  WHERE p.pago_id = ?
";

$stmt = $con->prepare($sql);
$stmt->bind_param("i", $pago_id);
$stmt->execute();
$result = $stmt->get_result();
$datos = $result->fetch_assoc();

if (!$datos) {
  die("Pago no encontrado.");
}

$pdf = new FPDF();
$pdf->AddPage();

// Encabezado azul
$pdf->SetFillColor(30, 80, 180);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 18);
$pdf->Cell(0, 15, utf8_decode('RECIBO DE PAGO ESCOLAR'), 0, 1, 'C', true);

$pdf->Ln(10); // Espacio después del encabezado

// Restaurar color de texto
$pdf->SetTextColor(0, 0, 0);

// Datos del estudiante alineados con espacio vertical
$pdf->SetFont('Arial', '', 12);

$pdf->Cell(50, 10, 'Estudiante:', 0, 0);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(100, 10, utf8_decode($datos['estudiante']), 0, 1);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(50, 10, 'Grado y Grupo:', 0, 0);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(100, 10, utf8_decode($datos['grado'] . '° ' . $datos['grupo']), 0, 1);

$pdf->Ln(10); // Espacio antes de la tabla

// Información de pago (caja con fondo)
$pdf->SetFillColor(235, 240, 255);
$pdf->SetDrawColor(180, 180, 180);
$pdf->SetLineWidth(0.2);

$pdf->Cell(180, 30, '', 1, 1, '', true); // Marco contenedor
$pdf->SetY($pdf->GetY() - 30); // Subir para empezar a escribir dentro

$pdf->SetX(15);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(50, 10, 'Monto pagado:', 0, 0);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(60, 10, '$' . number_format($datos['monto'], 2), 0, 1);

$pdf->SetX(15);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(50, 10, 'Fecha de pago:', 0, 0);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(60, 10, $datos['fecha_pago'], 0, 1);

$pdf->SetX(15);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(50, 10, 'Estado:', 0, 0);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(60, 10, strtoupper($datos['estado']), 0, 1);

$pdf->Ln(20); // Espacio antes del mensaje

// Mensaje final en caja clara
$pdf->SetFillColor(235, 240, 255);
$pdf->SetTextColor(80, 80, 80);
$pdf->SetFont('Arial', 'I', 11);
$pdf->Cell(180, 20, utf8_decode("Gracias por su pago. Este comprobante no requiere firma."), 1, 1, 'C', true);

$pdf->Output("I", "Recibo_pago_{$pago_id}.pdf");
