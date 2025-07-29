<?php
require_once 'conecta.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
  echo json_encode([]);
  exit;
}

$con = conecta();
$usuario_id = $_SESSION['usuario_id'];

// Obtener el docente_id
$stmtDoc = $con->prepare("SELECT docente_id FROM docentes WHERE usuario_id = ?");
$stmtDoc->bind_param("i", $usuario_id);
$stmtDoc->execute();
$resDoc = $stmtDoc->get_result();
if ($resDoc->num_rows === 0) {
  echo json_encode([]);
  exit;
}

$docente_id = $resDoc->fetch_assoc()['docente_id'];

$query = "
  SELECT 
    i.*, 
    e.nombre AS estudiante_nombre, 
    e.apellido AS estudiante_apellido,
    CONCAT(c.grado, '°', c.grupo) AS clase_nombre,
    m.nombre AS materia_nombre,
    ce.nombre AS ciclo_nombre,
    (
      SELECT nombre 
      FROM periodos 
      WHERE ciclo_id = ce.ciclo_id 
      ORDER BY periodo_id DESC 
      LIMIT 1
    ) AS periodo_nombre
  FROM incidentes i
  INNER JOIN estudiantes e ON e.estudiante_id = i.estudiante_id
  INNER JOIN inscripciones ins ON ins.estudiante_id = e.estudiante_id
  INNER JOIN clases c ON c.clase_id = ins.clase_id
  INNER JOIN clase_asignacion ca ON ca.clase_id = c.clase_id AND ca.materia_id = i.materia_id
  INNER JOIN materias m ON m.materia_id = i.materia_id
  INNER JOIN ciclos_escolares ce ON ce.ciclo_id = c.ciclo_id
  WHERE ca.docente_id = ?
  ORDER BY i.fecha DESC
";

$stmt = $con->prepare($query);
$stmt->bind_param("i", $docente_id);
$stmt->execute();
$res = $stmt->get_result();

$datos = [];
while ($row = $res->fetch_assoc()) {
  $datos[] = $row;
}

// Agrupar para evitar duplicados de generales
$final = [];
$vistos = [];

foreach ($datos as $inc) {
  if ($inc['alcance'] === 'general') {
    $key = 'G-' . $inc['fecha'] . '-' . $inc['tipo'] . '-' . $inc['descripcion'] . '-' . $inc['materia_id'] . '-' . $inc['clase_nombre'];
  } else {
    $key = 'P-' . $inc['fecha'] . '-' . $inc['tipo'] . '-' . $inc['descripcion'] . '-' . $inc['materia_id'] . '-' . $inc['estudiante_id'];
  }

  if (!isset($vistos[$key])) {
    $vistos[$key] = true;
    $final[] = $inc;
  }
}

echo json_encode($final, JSON_UNESCAPED_UNICODE);
