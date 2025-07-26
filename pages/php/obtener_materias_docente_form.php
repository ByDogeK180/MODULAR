<?php
header('Content-Type: application/json');
require_once 'conecta.php';
session_start();

$conexion = conecta();
$docente_id = $_SESSION['usuario_id'] ?? 0;

if ($docente_id === 0) {
  echo json_encode(['error' => 'Sesión no iniciada']);
  exit;
}

$stmt = $conexion->prepare("
  SELECT m.materia_id, m.nombre
  FROM clase_asignacion ca
  INNER JOIN materias m ON ca.materia_id = m.materia_id
  WHERE ca.docente_id = ?
  GROUP BY m.materia_id
");

$stmt->bind_param("i", $docente_id);
$stmt->execute();
$result = $stmt->get_result();

$materias = [];
while ($row = $result->fetch_assoc()) {
  $materias[] = $row;
}

echo json_encode($materias);
