<?php
// assets/api/dataset_admin.php
ob_start();
header('Content-Type: application/json; charset=utf-8');

// (opcional) silenciar notices en prod
ini_set('display_errors', 0);
error_reporting(0);

require_once '../../pages/php/conecta.php';

function out($arr, $code = 200) {
  http_response_code($code);
  echo json_encode($arr, JSON_UNESCAPED_UNICODE);
  exit;
}

try {
  $con = conecta();
  if (!$con) out(["ok" => false, "error" => "Sin conexión a BD"], 200);
  if (method_exists($con, 'set_charset')) $con->set_charset('utf8mb4');
  
  $sql = "
    SELECT
      d.estudiante_id,
      e.nombre   AS alumno_nombre,
      e.apellido AS alumno_apellido,

      d.clase_id,
      c.grado,
      c.grupo,

      /* Ciclo real desde clases -> ciclos_escolares */
      c.ciclo_id                  AS ciclo_id,
      ce.nombre                   AS ciclo_nombre,

      /* Materia (LEFT por si no hay asignación) */
      m.materia_id,
      m.nombre                    AS materia_nombre,

      /* Periodo real */
      d.periodo_id,
      p.nombre                    AS periodo_nombre,

      /* Métricas del dataset */
      d.asistencia_pct,
      d.incidentes_count,
      d.parciales_avg,
      d.y_reprobado

    FROM ml_dataset d
    JOIN estudiantes       e  ON e.estudiante_id = d.estudiante_id
    JOIN clases            c  ON c.clase_id      = d.clase_id
    JOIN ciclos_escolares  ce ON ce.ciclo_id     = c.ciclo_id
    JOIN periodos          p  ON p.periodo_id    = d.periodo_id
    LEFT JOIN clase_asignacion ca ON ca.clase_id = d.clase_id
    LEFT JOIN materias      m  ON m.materia_id   = ca.materia_id

    /* Evita duplicados exactos; si te aparecen, puedes agrupar por llaves */
    ORDER BY ce.ciclo_id, d.periodo_id, c.clase_id, d.estudiante_id
  ";

  $res = $con->query($sql);
  if (!$res) out(["ok" => false, "error" => $con->error ?: "Error en consulta"], 200);

  $rows = [];
  while ($row = $res->fetch_assoc()) {
    // Asegura tipos numéricos donde aplica
    $row['estudiante_id']   = isset($row['estudiante_id'])   ? (int)$row['estudiante_id']   : null;
    $row['clase_id']        = isset($row['clase_id'])        ? (int)$row['clase_id']        : null;
    $row['grado']           = isset($row['grado'])           ? (string)$row['grado']        : null;
    $row['grupo']           = isset($row['grupo'])           ? (string)$row['grupo']        : null;
    $row['ciclo_id']        = isset($row['ciclo_id'])        ? (int)$row['ciclo_id']        : null;
    $row['materia_id']      = isset($row['materia_id'])      ? (int)$row['materia_id']      : null;
    $row['periodo_id']      = isset($row['periodo_id'])      ? (int)$row['periodo_id']      : null;
    $row['asistencia_pct']  = isset($row['asistencia_pct'])  ? (float)$row['asistencia_pct'] : null;
    $row['incidentes_count']= isset($row['incidentes_count'])? (int)$row['incidentes_count'] : null;
    $row['parciales_avg']   = isset($row['parciales_avg'])   ? (float)$row['parciales_avg']  : null;
    $row['y_reprobado']     = isset($row['y_reprobado'])     ? (int)$row['y_reprobado']      : null;

    // Fallbacks por si algo viniera null
    if (!isset($row['ciclo_nombre']) || $row['ciclo_nombre'] === null) {
      $row['ciclo_nombre'] = 'Ciclo ' . ($row['ciclo_id'] ?? '-');
    }
    if (!isset($row['periodo_nombre']) || $row['periodo_nombre'] === null) {
      $row['periodo_nombre'] = 'Periodo ' . ($row['periodo_id'] ?? '-');
    }

    $rows[] = $row;
  }

  out(["ok" => true, "data" => $rows], 200);

} catch (Throwable $e) {
  out(["ok" => false, "error" => $e->getMessage()], 200);
}
