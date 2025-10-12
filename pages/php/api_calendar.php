<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/conecta.php';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$cn = conecta();
$cn->set_charset('utf8mb4');

/* Rango que pide FullCalendar (YYYY-MM-DD). Si no llega, usa un rango amplio. */
$start = $_GET['start'] ?? '1900-01-01';
$end   = $_GET['end']   ?? '2100-12-31';
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $start)) $start = '1900-01-01';
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $end))   $end   = '2100-12-31';

$events = [];

/* ===== CICLOS (background) ===== */
$sqlC = "SELECT ciclo_id, nombre, fecha_inicio, fecha_fin
         FROM ciclos_escolares
         WHERE fecha_inicio <= ? AND fecha_fin >= ?";
$stmtC = $cn->prepare($sqlC);
$stmtC->bind_param('ss', $end, $start);
$stmtC->execute();
$resC = $stmtC->get_result();
while ($r = $resC->fetch_assoc()) {
  $events[] = [
    'id'         => 'ciclo_'.$r['ciclo_id'],
    'title'      => 'Ciclo: '.$r['nombre'],
    'start'      => $r['fecha_inicio'],
    'end'        => date('Y-m-d', strtotime($r['fecha_fin'].' +1 day')),
    'allDay'     => true,
    'display'    => 'background',
    'classNames' => ['fc-event-ciclo'],
    'kind'       => 'ciclo',
    'body'       => "<div><b>Ciclo:</b> ".h($r['nombre'])."<br><b>Del:</b> {$r['fecha_inicio']} <b>al</b> {$r['fecha_fin']}</div>"
  ];
}

/* ===== PERIODOS (background) ===== */
$sqlP = "SELECT p.periodo_id, p.nombre, p.fecha_inicio, p.fecha_fin, c.nombre AS ciclo
         FROM periodos p
         LEFT JOIN ciclos_escolares c ON c.ciclo_id = p.ciclo_id
         WHERE p.fecha_inicio <= ? AND p.fecha_fin >= ?";
$stmtP = $cn->prepare($sqlP);
$stmtP->bind_param('ss', $end, $start);
$stmtP->execute();
$resP = $stmtP->get_result();
while ($r = $resP->fetch_assoc()) {
  $events[] = [
    'id'         => 'periodo_'.$r['periodo_id'],
    'title'      => 'Periodo: '.$r['nombre'],
    'start'      => $r['fecha_inicio'],
    'end'        => date('Y-m-d', strtotime($r['fecha_fin'].' +1 day')),
    'allDay'     => true,
    'display'    => 'background',
    'classNames' => ['fc-event-periodo'],
    'kind'       => 'periodo',
    'body'       => "<div><b>".h($r['nombre'])."</b> (".h((string)$r['ciclo']).")<br><b>Del:</b> {$r['fecha_inicio']} <b>al</b> {$r['fecha_fin']}</div>"
  ];
}

/* ===== INCIDENTES / AVISOS (eventos del día) ===== */
$sqlI = "SELECT incidente_id, fecha, descripcion, tipo, alcance
         FROM incidentes
         WHERE fecha >= ? AND fecha <= ?
         ORDER BY fecha, incidente_id";
$stmtI = $cn->prepare($sqlI);
$stmtI->bind_param('ss', $start, $end);
$stmtI->execute();
$resI = $stmtI->get_result();
while ($r = $resI->fetch_assoc()) {
  $titulo = ($r['tipo'] ? ucfirst($r['tipo']).': ' : 'Aviso: ');
  $events[] = [
    'id'         => 'inc_'.$r['incidente_id'],
    'title'      => $titulo . mb_strimwidth((string)($r['descripcion'] ?? ''), 0, 50, '…', 'UTF-8'),
    'start'      => $r['fecha'],
    'allDay'     => true,
    'classNames' => ['fc-event-incidente'],
    'kind'       => 'aviso',
    'raw'        => [
      'tipo'       => (string)$r['tipo'],
      'alcance'    => (string)$r['alcance'],
      'descripcion'=> (string)$r['descripcion']
    ],
    'body'       => "<div><b>Tipo:</b> ".h((string)$r['tipo'])."<br><b>Alcance:</b> ".h((string)$r['alcance'])."<br><b>Descripción:</b> ".h((string)$r['descripcion'])."</div>"
  ];
}

echo json_encode($events, JSON_UNESCAPED_UNICODE);
