<?php
// File: obtener_calificaciones_docente.php
require 'conecta.php';
header('Content-Type: application/json');

$con = conecta();
if (!$con) {
  echo json_encode([]);
  exit;
}

$clase_id = isset($_GET['clase_id']) ? intval($_GET['clase_id']) : 0;
$periodo_id = isset($_GET['periodo_id']) ? intval($_GET['periodo_id']) : 0;
if ($clase_id <= 0 || $periodo_id <= 0) {
  echo json_encode([]);
  exit;
}

$query = "
  SELECT c.calificacion_id, c.estudiante_id, d.numero, d.valor
  FROM calificaciones c
  LEFT JOIN calificaciones_detalle d ON d.calificacion_id = c.calificacion_id
  WHERE c.clase_id = ? AND c.periodo_id = ?
  ORDER BY c.estudiante_id, d.numero
";

$stmt = $con->prepare($query);
$stmt->bind_param('ii', $clase_id, $periodo_id);
$stmt->execute();
$result = $stmt->get_result();

$calificaciones = [];
while ($row = $result->fetch_assoc()) {
  $id = $row['estudiante_id'];
  if (!isset($calificaciones[$id])) {
    $calificaciones[$id] = [
      'estudiante_id' => $id,
      'detalles' => []
    ];
  }
  if ($row['numero'] !== null) {
    $calificaciones[$id]['detalles'][] = [
      'numero' => $row['numero'],
      'valor' => $row['valor']
    ];
  }
}

echo json_encode(array_values($calificaciones));
