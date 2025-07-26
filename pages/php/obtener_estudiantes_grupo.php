<?php
require_once 'conecta.php'; // o ajusta si usas otro nombre de archivo

$clase_id = $_GET['clase_id'] ?? '';
$materia_id = $_GET['materia_id'] ?? '';

if (!$clase_id || !$materia_id) {
  echo json_encode([]);
  exit;
}

$stmt = $conn->prepare("
  SELECT e.id, e.nombre, e.apellido
  FROM estudiantes e
  JOIN inscripciones i ON e.id = i.estudiante_id
  WHERE i.clase_id = ? AND i.materia_id = ?
");
$stmt->bind_param('ii', $clase_id, $materia_id);
$stmt->execute();
$result = $stmt->get_result();

$estudiantes = [];
while ($row = $result->fetch_assoc()) {
  $estudiantes[] = $row;
}

echo json_encode($estudiantes);
