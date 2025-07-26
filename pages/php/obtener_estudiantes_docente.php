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
  DISTINCT e.estudiante_id,
  e.nombre AS estudiante_nombre,
  e.apellido AS estudiante_apellido,
  c.clase_id,
  c.grado,
  c.grupo,
  m.materia_id,
  m.nombre AS materia_nombre,
  m.ciclo
FROM estudiantes e
JOIN inscripciones i ON e.estudiante_id = i.estudiante_id
JOIN clases c ON i.clase_id = c.clase_id
JOIN clase_asignacion ca ON ca.clase_id = c.clase_id
JOIN materias m ON m.materia_id = ca.materia_id
WHERE ca.docente_id = ?
ORDER BY c.grado, c.grupo, m.nombre, e.nombre;
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
