<?php
require __DIR__ . '/conecta.php';
require __DIR__ . '/auth.php';
header('Content-Type: application/json; charset=utf-8');

$cn = conecta();
if (!$cn) { echo json_encode([]); exit; }

if (session_status() === PHP_SESSION_NONE) session_start();
$usuarioId = (int)($_SESSION['usuario_id'] ?? 0);

/* Resolver docente_id desde usuario */
$docenteId = 0;
if ($usuarioId > 0) {
  $q = $cn->prepare("SELECT docente_id FROM docentes WHERE usuario_id=? LIMIT 1");
  $q->bind_param("i", $usuarioId);
  $q->execute();
  $r = $q->get_result()->fetch_assoc();
  if ($r) $docenteId = (int)$r['docente_id'];
}

/* Rango que pide FullCalendar (YYYY-MM-DD); end es exclusivo */
$start = $_GET['start'] ?? date('Y-m-d', strtotime('-30 days'));
$end   = $_GET['end']   ?? date('Y-m-d', strtotime('+60 days'));

$events = [];

/* === Ciclo activo (background) === */
$resC = $cn->query("SELECT ciclo_id, nombre, fecha_inicio, fecha_fin
                    FROM ciclos_escolares
                    WHERE estado='activo'
                    ORDER BY ciclo_id DESC LIMIT 1");
if ($resC && $resC->num_rows) {
  $c = $resC->fetch_assoc();
  $events[] = [
    'title'   => 'Ciclo: '.$c['nombre'],
    'start'   => $c['fecha_inicio'],
    'end'     => date('Y-m-d', strtotime($c['fecha_fin'].' +1 day')), // end exclusivo
    'display' => 'background',
    'allDay'  => true,
    'kind'    => 'ciclo'
  ];

  // Periodos del ciclo
  $cid = (int)$c['ciclo_id'];
  $resP = $cn->query("SELECT nombre, fecha_inicio, fecha_fin FROM periodos WHERE ciclo_id={$cid}");
  while ($p = $resP && $resP->fetch_assoc()) {
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

/* === Incidentes/Avisos SOLO de alumnos de las clases del docente === */
if ($docenteId > 0) {
  $sql = "
    SELECT 
      i.incidente_id, i.estudiante_id, i.materia_id, i.fecha, i.tipo, i.alcance, i.descripcion,
      CONCAT(e.nombre, ' ', e.apellido) AS alumno,
      m.nombre AS materia
    FROM incidentes i
    INNER JOIN estudiantes e ON e.estudiante_id = i.estudiante_id
    INNER JOIN materias m ON m.materia_id = i.materia_id
    INNER JOIN inscripciones ins ON ins.estudiante_id = i.estudiante_id
    INNER JOIN clase_asignacion ca 
      ON ca.clase_id = ins.clase_id
     AND ca.materia_id = i.materia_id
    WHERE ca.docente_id = ?
      AND i.fecha >= ?
      AND i.fecha < ?
    ORDER BY i.fecha, i.incidente_id
  ";
  $st = $cn->prepare($sql);
  $st->bind_param('iss', $docenteId, $start, $end);
  $st->execute();
  $rs = $st->get_result();
  while ($row = $rs->fetch_assoc()) {
    $events[] = [
      'title' => 'Aviso: '.$row['tipo'],
      'start' => $row['fecha'],
      'allDay'=> true,
      'kind'  => 'incidente',
      'extendedProps' => [
        'tipo'        => $row['tipo'],
        'alcance'     => $row['alcance'],
        'descripcion' => $row['descripcion'] ?? '',
        'estudiante'  => $row['alumno'],
        'materia'     => $row['materia'],
        'body'        => "<div><b>Tipo:</b> ".htmlspecialchars((string)$row['tipo'])." &nbsp; ".
                         "<b>Alcance:</b> ".htmlspecialchars((string)$row['alcance'])."<br>".
                         htmlspecialchars((string)$row['descripcion'])."</div>"
      ]
    ];
  }
}

/* === Recordatorios personales del usuario (opcional) === */
if ($usuarioId > 0) {
  $sr = $cn->prepare("SELECT titulo, descripcion, fecha FROM recordatorios
                      WHERE usuario_id=? AND fecha>=? AND fecha<?");
  $sr->bind_param('iss', $usuarioId, $start, $end);
  $sr->execute();
  $rr = $sr->get_result();
  while ($r = $rr->fetch_assoc()) {
    $events[] = [
      'title' => $r['titulo'] ?: 'Recordatorio',
      'start' => $r['fecha'],
      'allDay'=> true,
      'kind'  => 'recordatorio',
      'extendedProps' => [
        'descripcion' => $r['descripcion'] ?? '',
        'body'        => "<div>".htmlspecialchars((string)$r['descripcion'])."</div>"
      ]
    ];
  }
}

echo json_encode($events);
