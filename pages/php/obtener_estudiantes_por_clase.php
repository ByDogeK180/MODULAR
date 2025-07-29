<?php
require 'conecta.php';
header('Content-Type: application/json');

$con = conecta();
if (!$con) {
  echo json_encode([]);
  exit;
}

$clase_id = isset($_GET['clase_id']) ? intval($_GET['clase_id']) : 0;
if ($clase_id <= 0) {
  echo json_encode([]);
  exit;
}

$sql = "
  SELECT e.estudiante_id, e.nombre, e.apellido
  FROM inscripciones i
  JOIN estudiantes e ON i.estudiante_id = e.estudiante_id
  WHERE i.clase_id = ?
  ORDER BY e.apellido, e.nombre
";

$stmt = $con->prepare($sql);
$stmt->bind_param("i", $clase_id);
$stmt->execute();
$res = $stmt->get_result();

$estudiantes = [];
while ($row = $res->fetch_assoc()) {
  $estudiantes[] = $row;
}

echo json_encode($estudiantes);
