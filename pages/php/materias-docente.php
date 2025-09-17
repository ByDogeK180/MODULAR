<?php
session_start();
require 'conecta.php';
$con = conecta();

header('Content-Type: application/json');

$usuario_id = $_SESSION['usuario_id'] ?? null;
if (!$usuario_id) {
  echo json_encode([]);
  exit;
}

$query = "
  SELECT DISTINCT m.materia_id, m.nombre, m.nivel_grado, m.ciclo
  FROM docentes d
  INNER JOIN clase_asignacion ca ON d.docente_id = ca.docente_id
  INNER JOIN materias m ON ca.materia_id = m.materia_id
  WHERE d.usuario_id = ?
";

$stmt = $con->prepare($query);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

$materias = [];
while ($row = $result->fetch_assoc()) {
  $materias[] = $row;
}

echo json_encode($materias, JSON_UNESCAPED_UNICODE);

$stmt->close();
$con->close();
