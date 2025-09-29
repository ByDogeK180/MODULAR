<?php
session_start();
require '../../pages/php/conecta.php';
header('Content-Type: application/json; charset=utf-8');

$conexion = conecta();

$docente_id = $_SESSION['docente_id'] ?? null; 

if (!$docente_id) {
  echo json_encode(["ok" => false, "error" => "No autenticado"]);
  exit;
}

try {
  $sql = "
  SELECT
    d.estudiante_id,
    e.nombre AS alumno_nombre,
    e.apellido AS alumno_apellido,
    d.clase_id,
    c.grado,
    c.grupo,
    m.materia_id,
    m.nombre AS materia_nombre,
    d.periodo_id,
    p.nombre AS periodo_nombre,
    ci.ciclo_id,
    ci.nombre AS ciclo_nombre,
    d.asistencia_pct,
    d.incidentes_count,
    d.parciales_avg,
    d.y_reprobado
  FROM ml_dataset d
  JOIN estudiantes e ON e.estudiante_id = d.estudiante_id
  JOIN clases c ON c.clase_id = d.clase_id
  JOIN periodos p ON p.periodo_id = d.periodo_id
  JOIN ciclos_escolares ci ON ci.ciclo_id = p.ciclo_id
  JOIN clase_asignacion ca ON ca.clase_id = d.clase_id
  JOIN materias m ON m.materia_id = ca.materia_id
  WHERE ca.docente_id = ?
  ORDER BY d.periodo_id, d.clase_id, d.estudiante_id
";


  $stmt = $conexion->prepare($sql);
  $stmt->bind_param("i", $docente_id);
  $stmt->execute();
  $res = $stmt->get_result();

  $rows = [];
  while ($row = $res->fetch_assoc()) {
    $rows[] = $row;
  }

  echo json_encode(["ok" => true, "data" => $rows], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(["ok" => false, "error" => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
