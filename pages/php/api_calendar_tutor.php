<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

// Estamos en: pages/php/api_calendar_tutor.php
require_once __DIR__ . '/auth.php';     // ✅ mismo folder
require_once __DIR__ . '/conecta.php';  // ✅ mismo folder

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

$cn = conecta();
if (!$cn) {
  http_response_code(500);
  echo json_encode(['error' => 'No se pudo conectar a la BD']);
  exit;
}
$cn->set_charset('utf8mb4');

$usuarioId = (int)($_SESSION['usuario_id'] ?? 0);

/* 1) tutor_id a partir del usuario logueado */
$tutorId = 0;
if ($usuarioId > 0) {
  if ($q = $cn->prepare("SELECT tutor_id FROM tutores WHERE usuario_id=? LIMIT 1")) {
    $q->bind_param('i', $usuarioId);
    $q->execute();
    if ($row = $q->get_result()->fetch_assoc()) {
      $tutorId = (int)$row['tutor_id'];
    }
    $q->close();
  }
}

/* 2) Rango que envía FullCalendar (end exclusivo) */
$start = $_GET['start'] ?? date('Y-m-d', strtotime('-45 days'));
$end   = $_GET['end']   ?? date('Y-m-d', strtotime('+90 days'));

$events = [];

/* 3) Ciclo activo + periodos (background) */
if ($rc = $cn->query("SELECT ciclo_id, nombre, fecha_inicio, fecha_fin
                      FROM ciclos_escolares
                      WHERE estado='activo'
                      ORDER BY ciclo_id DESC
                      LIMIT 1")) {
  if ($rc->num_rows) {
    $c = $rc->fetch_assoc();
    $events[] = [
      'title'   => 'Ciclo: '.$c['nombre'],
      'start'   => $c['fecha_inicio'],
      'end'     => date('Y-m-d', strtotime($c['fecha_fin'].' +1 day')),
      'display' => 'background',
      'allDay'  => true,
      'kind'    => 'ciclo'
    ];
    $cid = (int)$c['ciclo_id'];
    if ($rp = $cn->query("SELECT nombre, fecha_inicio, fecha_fin FROM periodos WHERE ciclo_id={$cid}")) {
      while ($p = $rp->fetch_assoc()) {
        $events[] = [
          'title'   => 'Periodo: '.$p['nombre'],
          'start'   => $p['fecha_inicio'],
          'end'     => date('Y-m-d', strtotime($p['fecha_fin'].' +1 day')),
          'display' => 'background',
          'allDay'  => true,
          'kind'    => 'periodo'
        ];
      }
    }
  }
}

/* 4) Incidentes (avisos) de los HIJOS del tutor
   Ajusta el nombre de la tabla puente si es distinto (tutor_estudiante).
*/
if ($tutorId > 0) {
  $sql = "
    SELECT 
      i.fecha, i.tipo, i.alcance, i.descripcion,
      CONCAT(e.nombre, ' ', e.apellido) AS alumno,
      m.nombre AS materia
    FROM incidentes i
    INNER JOIN estudiantes e       ON e.estudiante_id = i.estudiante_id
    INNER JOIN materias m          ON m.materia_id    = i.materia_id
    INNER JOIN tutor_estudiante te ON te.estudiante_id = i.estudiante_id
    WHERE te.tutor_id = ?
      AND i.fecha >= ?
      AND i.fecha < ?
    ORDER BY i.fecha
  ";
  if ($st = $cn->prepare($sql)) {
    $st->bind_param('iss', $tutorId, $start, $end);
    $st->execute();
    $rs = $st->get_result();
    while ($r = $rs->fetch_assoc()) {
      $events[] = [
        'title' => 'Aviso: '.($r['tipo'] ?: 'Incidente'),
        'start' => $r['fecha'],
        'allDay'=> true,
        'kind'  => 'incidente',
        'extendedProps' => [
          'tipo'        => (string)$r['tipo'],
          'alcance'     => (string)$r['alcance'],
          'descripcion' => (string)($r['descripcion'] ?? ''),
          'estudiante'  => (string)$r['alumno'],
          'materia'     => (string)$r['materia']
        ]
      ];
    }
    $st->close();
  }
}

echo json_encode($events, JSON_UNESCAPED_UNICODE);
