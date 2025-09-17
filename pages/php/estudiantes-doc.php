<?php
session_start();
require 'conecta.php';
$con = conecta();

header('Content-Type: application/json');

// Validar sesión
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol'])) {
  http_response_code(401);
  echo json_encode(["error" => "No autorizado"]);
  exit;
}

$usuario_id = $_SESSION['usuario_id'];
$rol = $_SESSION['rol']; // 1 = docente, 0 = admin, 2 = tutor
$materia_id = isset($_GET['materia_id']) ? intval($_GET['materia_id']) : 0;

$estudiantes = [];

if ($rol == 1) {
  // 🧑‍🏫 DOCENTE: solo estudiantes de sus materias
  $query = "
    SELECT DISTINCT
      e.estudiante_id,
      e.nombre,
      e.apellido,
      e.fecha_nacimiento,
      e.grado,
      e.grupo,
      e.activo,
      e.creado_en,
      e.actualizado_en,
      t.tutor_id,
      CONCAT(t.nombre, ' ', t.apellido) AS tutor_nombre,
      m.materia_id,
      m.nombre AS materia,
      m.ciclo
    FROM docentes d
    INNER JOIN clase_asignacion ca ON d.docente_id = ca.docente_id
    INNER JOIN materias m ON ca.materia_id = m.materia_id
    INNER JOIN clases c ON ca.clase_id = c.clase_id
    INNER JOIN inscripciones i ON c.clase_id = i.clase_id
    INNER JOIN estudiantes e ON i.estudiante_id = e.estudiante_id
    LEFT JOIN tutores t ON e.tutor_id = t.tutor_id
    WHERE d.usuario_id = ?
  ";

  if ($materia_id > 0) {
    $query .= " AND m.materia_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ii", $usuario_id, $materia_id);
  } else {
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $usuario_id);
  }

} elseif ($rol == 0) {
  // 👑 ADMIN: ver todos los estudiantes activos
  $query = "
    SELECT 
      e.estudiante_id,
      e.nombre,
      e.apellido,
      e.fecha_nacimiento,
      e.grado,
      e.grupo,
      e.activo,
      e.creado_en,
      e.actualizado_en,
      t.tutor_id,
      CONCAT(t.nombre, ' ', t.apellido) AS tutor_nombre
    FROM estudiantes e
    LEFT JOIN tutores t ON e.tutor_id = t.tutor_id
    WHERE e.activo = 1
  ";
  $stmt = $con->prepare($query);

} elseif ($rol == 2) {
  // 👨‍👩‍👧 TUTOR: ver solo sus hijos
  $query = "
    SELECT 
      e.estudiante_id,
      e.nombre,
      e.apellido,
      e.fecha_nacimiento,
      e.grado,
      e.grupo,
      e.activo,
      e.creado_en,
      e.actualizado_en,
      t.tutor_id,
      CONCAT(t.nombre, ' ', t.apellido) AS tutor_nombre
    FROM estudiantes e
    INNER JOIN tutores t ON e.tutor_id = t.tutor_id
    WHERE t.usuario_id = ?
  ";
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
