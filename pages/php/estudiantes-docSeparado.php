<?php
session_start();
require 'conecta.php';
$con = conecta();

header('Content-Type: application/json');

// Validar sesión
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 1) {
  http_response_code(401);
  echo json_encode(["error" => "No autorizado"]);
  exit;
}

$usuario_id = $_SESSION['usuario_id'];
$materia_id = isset($_GET['materia_id']) ? intval($_GET['materia_id']) : 0;
$clase_id = isset($_GET['clase_id']) ? intval($_GET['clase_id']) : 0;

$estudiantes = [];

$query = "
  SELECT DISTINCT
    e.estudiante_id,
    e.nombre,
    e.apellido,
    e.fecha_nacimiento,
    e.activo,
    e.creado_en,
    e.actualizado_en,
    t.tutor_id,
    CONCAT(t.nombre, ' ', t.apellido) AS tutor_nombre,
    m.materia_id,
    m.nombre AS materia,
    c.clase_id,
    CONCAT(c.grado, '-', c.grupo) AS clase,
    ce.nombre AS ciclo
  FROM docentes d
  INNER JOIN clase_asignacion ca ON d.docente_id = ca.docente_id
  INNER JOIN materias m ON ca.materia_id = m.materia_id
  INNER JOIN clases c ON ca.clase_id = c.clase_id
  INNER JOIN ciclos_escolares ce ON c.ciclo_id = ce.ciclo_id
  INNER JOIN inscripciones i ON i.clase_id = c.clase_id
  INNER JOIN estudiantes e ON e.estudiante_id = i.estudiante_id
  LEFT JOIN tutores t ON e.tutor_id = t.tutor_id
  WHERE d.usuario_id = ?
";

if ($materia_id > 0 && $clase_id > 0) {
  $query .= " AND ca.materia_id = ? AND c.clase_id = ?";
  $stmt = $con->prepare($query);
  $stmt->bind_param("iii", $usuario_id, $materia_id, $clase_id);
} elseif ($materia_id > 0) {
  $query .= " AND ca.materia_id = ?";
  $stmt = $con->prepare($query);
  $stmt->bind_param("ii", $usuario_id, $materia_id);
} else {
  $stmt = $con->prepare($query);
  $stmt->bind_param("i", $usuario_id);
}

if (!$stmt) {
  echo json_encode(["error" => "Error en la preparación de la consulta"]);
  exit;
}

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
  $estudiantes[] = $row;
}

echo json_encode($estudiantes, JSON_UNESCAPED_UNICODE);

$stmt->close();
$con->close();
?>
