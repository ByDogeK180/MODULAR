<?php
session_start();
require 'conecta.php';

header('Content-Type: application/json');
$con = conecta();

$usuario_id = $_SESSION['usuario_id'] ?? null;
if (!$usuario_id) {
  echo json_encode(['error' => 'Sesión inválida']);
  exit;
}

// Obtener el tutor_id desde el usuario
$stmtTutor = $con->prepare("SELECT tutor_id FROM tutores WHERE usuario_id = ?");
$stmtTutor->bind_param("i", $usuario_id);
$stmtTutor->execute();
$resTutor = $stmtTutor->get_result();
$tutor = $resTutor->fetch_assoc();
$tutor_id = $tutor['tutor_id'] ?? null;

if (!$tutor_id) {
  echo json_encode(['error' => 'Tutor no encontrado']);
  exit;
}

// Obtener los hijos del tutor
$query = "
SELECT 
  e.estudiante_id, e.nombre AS nombre_estudiante, e.apellido AS apellido_estudiante,
  d.docente_id, d.nombre AS nombre_docente, d.apellido AS apellido_docente,
  d.correo, d.foto_url, m.nombre AS materia
FROM tutor_estudiante te
JOIN estudiantes e ON te.estudiante_id = e.estudiante_id
JOIN inscripciones i ON i.estudiante_id = e.estudiante_id
JOIN clase_asignacion ca ON ca.clase_id = i.clase_id
JOIN docentes d ON d.docente_id = ca.docente_id
JOIN materias m ON m.materia_id = ca.materia_id
WHERE te.tutor_id = ?
ORDER BY e.estudiante_id, d.docente_id
";

$stmt = $con->prepare($query);
$stmt->bind_param("i", $tutor_id);
$stmt->execute();
$res = $stmt->get_result();

$data = [];
while ($row = $res->fetch_assoc()) {
  $data[] = $row;
}

echo json_encode(['data' => $data]);
