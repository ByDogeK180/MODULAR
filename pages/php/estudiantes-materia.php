<?php
session_start();
require 'conecta.php';
header('Content-Type: application/json');

$con = conecta();

if (!isset($_GET['materia_id'])) {
  echo json_encode([]);
  exit;
}

$materia_id = intval($_GET['materia_id']);
$usuario_id = $_SESSION['usuario_id'] ?? null;
$rol = $_SESSION['rol'] ?? null;

$estudiantes = [];

if ($rol == 1) {
  // 🧑‍🏫 DOCENTE: solo alumnos de la materia que imparte
  $query = "
    SELECT DISTINCT 
      e.estudiante_id, 
      e.nombre, 
      e.apellido, 
      e.grado, 
      e.grupo
    FROM docentes d
    INNER JOIN clase_asignacion ca ON d.docente_id = ca.docente_id
    INNER JOIN clases c ON ca.clase_id = c.clase_id
    INNER JOIN inscripciones i ON c.clase_id = i.clase_id
    INNER JOIN estudiantes e ON i.estudiante_id = e.estudiante_id
    WHERE d.usuario_id = ? AND ca.materia_id = ?
  ";
  $stmt = $con->prepare($query);
  $stmt->bind_param("ii", $usuario_id, $materia_id);

} elseif ($rol == 0) {
  // 👑 ADMIN: todos los alumnos de esa materia
  $query = "
    SELECT DISTINCT 
      e.estudiante_id, 
      e.nombre, 
      e.apellido, 
      e.grado, 
      e.grupo
    FROM clase_asignacion ca
    INNER JOIN clases c ON ca.clase_id = c.clase_id
    INNER JOIN inscripciones i ON c.clase_id = i.clase_id
    INNER JOIN estudiantes e ON i.estudiante_id = e.estudiante_id
    WHERE ca.materia_id = ?
  ";
  $stmt = $con->prepare($query);
  $stmt->bind_param("i", $materia_id);

} elseif ($rol == 2) {
  // 👨‍👩‍👧 TUTOR: solo sus hijos (independiente de materia)
  $query = "
    SELECT 
      e.estudiante_id, 
      e.nombre, 
      e.apellido, 
      e.grado, 
      e.grupo
    FROM estudiantes e
    INNER JOIN tutores t ON e.tutor_id = t.tutor_id
    WHERE t.usuario_id = ?
  ";
  $stmt = $con->prepare($query);
  $stmt->bind_param("i", $usuario_id);

} else {
  echo json_encode([]);
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
