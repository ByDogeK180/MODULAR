<?php
require_once 'conecta.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
  echo json_encode([]);
  exit;
}

$usuario_id = $_SESSION['usuario_id'];

$con = conecta();

// Obtener docente_id
$stmt = $con->prepare("SELECT docente_id FROM docentes WHERE usuario_id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  echo json_encode([]);
  exit;
}

$docente_id = $result->fetch_assoc()['docente_id'];

// Consulta con materia, grupo y ciclo
$query = "
SELECT DISTINCT 
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
