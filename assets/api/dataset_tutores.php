<?php
// assets/api/dataset_tutores.php
session_start();
require '../../pages/php/conecta.php';
header('Content-Type: application/json; charset=utf-8');

$cx = conecta();
$tutor_id = $_SESSION['tutor_id'] ?? null;

if (!$tutor_id) {
  echo json_encode(['ok' => false, 'error' => 'No autenticado (tutor_id)']);
  exit;
}

try {
  /*
    Baseline = todas las (estudiante, clase, ciclo, periodo) de los hijos del tutor
    usando tutor_estudiante + inscripciones + periodos(ciclo).
    Luego LEFT JOIN con ml_dataset para traer métricas si existen.
  */
  $sql = "
  SELECT
    b.estudiante_id,
    e.nombre  AS alumno_nombre,
    e.apellido AS alumno_apellido,

    b.clase_id,
    c.grado,
    c.grupo,

    m.materia_id,
    m.nombre AS materia_nombre,

    b.periodo_id,
    p.nombre AS periodo_nombre,

    b.ciclo_id,
    ci.nombre AS ciclo_nombre,

    d.asistencia_pct,
    d.incidentes_count,
    d.parciales_avg,
    d.y_reprobado
  FROM (
    SELECT te.estudiante_id, i.clase_id, i.ciclo_id, per.periodo_id
    FROM tutor_estudiante te
    JOIN inscripciones i ON i.estudiante_id = te.estudiante_id
    JOIN periodos per     ON per.ciclo_id = i.ciclo_id
    WHERE te.tutor_id = ?
  ) AS b
  JOIN estudiantes e       ON e.estudiante_id = b.estudiante_id
  JOIN clases c            ON c.clase_id      = b.clase_id
  JOIN periodos p          ON p.periodo_id    = b.periodo_id
  JOIN ciclos_escolares ci ON ci.ciclo_id     = b.ciclo_id

  -- expandimos a materias de la clase (1 fila por materia)
  JOIN clase_asignacion ca ON ca.clase_id     = b.clase_id
  JOIN materias m          ON m.materia_id    = ca.materia_id

  -- métricas si existen
  LEFT JOIN ml_dataset d
    ON d.estudiante_id = b.estudiante_id
   AND d.clase_id      = b.clase_id
   AND d.periodo_id    = b.periodo_id

  ORDER BY b.periodo_id, b.clase_id, b.estudiante_id, m.materia_id
  ";

  $st = $cx->prepare($sql);
  $st->bind_param('i', $tutor_id);
  $st->execute();
  $rs = $st->get_result();

  $rows = [];
  while ($row = $rs->fetch_assoc()) {
    $rows[] = $row;
  }

  echo json_encode(['ok' => true, 'data' => $rows], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
