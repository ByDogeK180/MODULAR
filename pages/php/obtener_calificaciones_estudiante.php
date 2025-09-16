<?php
require_once 'conecta.php';
$con = conecta();

$estudiante_id = $_GET['estudiante_id'] ?? 0;
$periodo_id = $_GET['periodo_id'] ?? 0;

header('Content-Type: application/json');

$sql = "
  SELECT m.nombre AS materia, cd.numero, cd.valor
  FROM calificaciones c
  INNER JOIN calificaciones_detalle cd ON cd.calificacion_id = c.calificacion_id
  INNER JOIN clase_asignacion ca ON c.clase_id = ca.clase_id
  INNER JOIN materias m ON ca.materia_id = m.materia_id
  WHERE c.estudiante_id = ? AND c.periodo_id = ?
  ORDER BY m.nombre, cd.numero
";

$stmt = $con->prepare($sql);
$stmt->bind_param("ii", $estudiante_id, $periodo_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
  $data[] = $row;
}

echo json_encode($data);
