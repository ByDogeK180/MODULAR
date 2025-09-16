<?php
// api/dataset_admin.php
ob_start();
ini_set('display_errors', 0);
error_reporting(0);

require '../../pages/php/conecta.php';
header('Content-Type: application/json; charset=utf-8');

$conexion = conecta();

try {
  // ml_dataset debe existir (de ml_views.sql)
  // Unimos nombres de alumno, clase (grado/grupo), materia y periodo
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
      d.asistencia_pct,
      d.incidentes_count,
      d.parciales_avg,
      d.y_reprobado
    FROM ml_dataset d
    JOIN estudiantes e      ON e.estudiante_id = d.estudiante_id
    JOIN clases c           ON c.clase_id = d.clase_id
    JOIN periodos p         ON p.periodo_id = d.periodo_id
    LEFT JOIN clase_asignacion ca ON ca.clase_id = d.clase_id
    LEFT JOIN materias m    ON m.materia_id = ca.materia_id
    ORDER BY d.periodo_id, d.clase_id, d.estudiante_id
  ";
  $res = $conexion->query($sql);

  $rows = [];
  while ($row = $res->fetch_assoc()) {
    $rows[] = $row;
  }

  echo json_encode(["ok" => true, "data" => $rows], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(["ok" => false, "error" => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
