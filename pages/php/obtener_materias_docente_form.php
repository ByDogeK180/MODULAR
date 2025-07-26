<?php
require_once 'conecta.php';
session_start();

$usuario_id = $_SESSION['usuario_id'] ?? null;
if (!$usuario_id) {
  echo json_encode([]);
  exit;
}

$con = conecta();

$query = "
  SELECT DISTINCT m.materia_id, m.nombre
  FROM materias m
  JOIN clase_asignacion ca ON ca.materia_id = m.materia_id
  JOIN docentes d ON d.docente_id = ca.docente_id
  JOIN inscripciones i ON i.clase_id = ca.clase_id
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

echo json_encode($materias);
