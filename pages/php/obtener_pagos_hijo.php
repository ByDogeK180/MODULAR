<?php
require_once './conecta.php';
header('Content-Type: application/json');
session_start();

$con = conecta();

$usuario_id = $_SESSION['usuario_id'] ?? 0;

if (!$usuario_id) {
  echo json_encode([]);
  exit;
}

// Obtener tutor_id correspondiente al usuario_id
$tutor_stmt = $con->prepare("SELECT tutor_id FROM tutores WHERE usuario_id = ?");
$tutor_stmt->bind_param("i", $usuario_id);
$tutor_stmt->execute();
$tutor_stmt->bind_result($tutor_id);
$tutor_stmt->fetch();
$tutor_stmt->close();

if (!$tutor_id) {
  echo json_encode([]);
  exit;
}

// Obtener pagos de los hijos del tutor
$sql = "
  SELECT
    p.pago_id,
    p.estudiante_id,
    e.nombre AS nombre_estudiante,
    e.grado,
    e.grupo,
    p.monto,
    p.fecha_pago,
    p.fecha_vencimiento,
    p.estado,
    p.creado_en,
    p.actualizado_en
  FROM pagos p
  INNER JOIN estudiantes e ON p.estudiante_id = e.estudiante_id
  WHERE e.tutor_id = ?
";

$stmt = $con->prepare($sql);
$stmt->bind_param("i", $tutor_id);
$stmt->execute();
$result = $stmt->get_result();

$pagos = [];
while ($row = $result->fetch_assoc()) {
  $pagos[] = $row;
}

// ✅ Solo esto
echo json_encode($pagos);
exit;
