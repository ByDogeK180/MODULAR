<?php
require_once 'conecta.php';
session_start();

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['docente_id'])) {
  echo json_encode([]);
  exit;
}

$docente_id = $_SESSION['docente_id'];
$con = conecta();

$query = "
SELECT 
  e.estudiante_id,
  e.nombre AS estudiante_nombre,
  e.apellido AS estudiante_apellido,
  e.grado,
  e.grupo,
  m.nombre AS materia_nombre,
  m.ciclo
FROM estudiantes e
JOIN inscripciones i ON e.estudiante_id = i.estudiante_id
JOIN clase_asignacion ca ON i.clase_id = ca.clase_id
JOIN materias m ON ca.materia_id = m.materia_id
WHERE ca.docente_id = ?
ORDER BY e.nombre;
";

$stmt = $con->prepare($query);
$stmt->bind_param("i", $docente_id);
$stmt->execute();
$res = $stmt->get_result();

$alumnos = [];
while ($row = $res->fetch_assoc()) {
  $alumnos[] = $row;
}

echo json_encode($alumnos);
