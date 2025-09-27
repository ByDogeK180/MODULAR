<?php
// pages/php/obtener_materia_resumen.php
declare(strict_types=1);
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/conecta.php';

try {
  $materia_id    = isset($_GET['materia_id'])    ? (int)$_GET['materia_id']    : 0;
  $estudiante_id = isset($_GET['estudiante_id']) ? (int)$_GET['estudiante_id'] : 0;
  if ($materia_id <= 0 || $estudiante_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Parámetros inválidos']); exit;
  }

  $con = conecta();
  if (!$con) throw new Exception('Sin conexión DB');
  $con->set_charset('utf8mb4');

  // 1) Info base de la materia + posible docente directo
  $sqlInfo = "SELECT m.materia_id, m.nombre, m.nivel_grado, m.ciclo, m.descripcion, m.foto_url,
                     d.docente_id, CONCAT(d.nombre,' ',d.apellido) AS docente, d.correo, d.foto_url AS docente_foto
              FROM materias m
              LEFT JOIN docentes d ON d.docente_id = m.docente_id
              WHERE m.materia_id = ?";
  $stmt = $con->prepare($sqlInfo);
  $stmt->bind_param('i', $materia_id);
  $stmt->execute();
  $info = $stmt->get_result()->fetch_assoc();
  $stmt->close();
  if (!$info) { http_response_code(404); echo json_encode(['error'=>'Materia no encontrada']); exit; }

  // 2) Si la materia NO tiene docente asignado, buscarlo por la clase del estudiante
  if (empty($info['docente_id'])) {
    $sqlDoc = "SELECT d.docente_id, CONCAT(d.nombre,' ',d.apellido) AS docente, d.correo, d.foto_url AS docente_foto
               FROM estudiantes e
               JOIN clases c            ON c.grado = e.grado AND c.grupo = e.grupo
               JOIN clase_asignacion ca ON ca.clase_id = c.clase_id AND ca.materia_id = ?
               JOIN docentes d          ON d.docente_id = ca.docente_id
               WHERE e.estudiante_id = ?
               LIMIT 1";
    $stmt = $con->prepare($sqlDoc);
    $stmt->bind_param('ii', $materia_id, $estudiante_id);
    $stmt->execute();
    $doc = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($doc) {
      $info['docente_id']   = (int)$doc['docente_id'];
      $info['docente']      = $doc['docente'];
      $info['correo']       = $doc['correo'];
      $info['docente_foto'] = $doc['docente_foto'];
    }
  }

  // 3) Asistencia del estudiante en esa materia
  $sqlAs = "SELECT
              COUNT(*) AS total,
              SUM(CASE WHEN estado='presente' THEN 1 ELSE 0 END) AS presentes,
              SUM(CASE WHEN estado='ausente'  THEN 1 ELSE 0 END) AS ausentes
            FROM asistencias
            WHERE estudiante_id = ? AND materia_id = ?";
  $stmt = $con->prepare($sqlAs);
  $stmt->bind_param('ii', $estudiante_id, $materia_id);
  $stmt->execute();
  $as = $stmt->get_result()->fetch_assoc() ?: ['total'=>0,'presentes'=>0,'ausentes'=>0];
  $stmt->close();
  $porcAsistencia = ($as['total'] > 0) ? round(($as['presentes'] / $as['total']) * 100) : null;

  // 4) Promedio del estudiante en esa materia
  $sqlProm = "SELECT ROUND(AVG(c.promedio), 2) AS promedio
              FROM calificaciones c
              WHERE c.estudiante_id = ?
                AND c.clase_id IN (SELECT ca.clase_id FROM clase_asignacion ca WHERE ca.materia_id = ?)
                AND c.promedio IS NOT NULL";
  $stmt = $con->prepare($sqlProm);
  $stmt->bind_param('ii', $estudiante_id, $materia_id);
  $stmt->execute();
  $prom = $stmt->get_result()->fetch_assoc();
  $stmt->close();

  echo json_encode([
    'materia' => [
      'materia_id'  => (int)$info['materia_id'],
      'nombre'      => $info['nombre'],
      'nivel'       => $info['nivel_grado'],
      'ciclo'       => $info['ciclo'],
      'descripcion' => $info['descripcion'],
      'foto_url'    => $info['foto_url'],
      'docente'     => [
        'docente_id' => isset($info['docente_id']) ? (int)$info['docente_id'] : null,
        'nombre'     => $info['docente'] ?? null,
        'correo'     => $info['correo'] ?? null,
        'foto_url'   => $info['docente_foto'] ?? null,
      ],
    ],
    'stats' => [
      'promedio'   => $prom['promedio'] !== null ? (float)$prom['promedio'] : null,
      'asistencia' => [
        'porcentaje' => $porcAsistencia,
        'presentes'  => (int)($as['presentes'] ?? 0),
        'ausentes'   => (int)($as['ausentes'] ?? 0),
        'total'      => (int)($as['total'] ?? 0),
      ],
    ],
  ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $t) {
  http_response_code(500);
  echo json_encode(['error'=>$t->getMessage()]);
}
