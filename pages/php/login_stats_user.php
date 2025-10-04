<?php
// File: pages/php/login_stats_user.php
require 'conecta.php';
header('Content-Type: application/json');

$con = conecta();
if (!$con) { echo json_encode(['error'=>'sin_conexion']); exit; }

$user_id = intval($_GET['user_id'] ?? 0);
$rol     = strtolower($_GET['rol'] ?? '');
$month   = $_GET['month'] ?? date('Y-m');

if ($user_id <= 0 || !in_array($rol, ['admin','docente','tutor'])) {
  echo json_encode(['error'=>'params_invalidos']); exit;
}

$ini = $month . '-01';
$fin = date('Y-m-d', strtotime("$ini +1 month"));

// nombre
$nombre = null;
if ($rol === 'docente') {
  $q = $con->prepare("SELECT CONCAT(nombre,' ',apellido) AS nombre FROM docentes WHERE docente_id=? LIMIT 1");
  $q->bind_param("i", $user_id); $q->execute();
  $nombre = $q->get_result()->fetch_assoc()['nombre'] ?? null; $q->close();
} elseif ($rol === 'tutor') {
  $q = $con->prepare("SELECT CONCAT(nombre,' ',apellido) AS nombre FROM tutores WHERE tutor_id=? LIMIT 1");
  $q->bind_param("i", $user_id); $q->execute();
  $nombre = $q->get_result()->fetch_assoc()['nombre'] ?? null; $q->close();
} else {
  $nombre = "Admin #$user_id";
}

// serie diaria
$sql = "SELECT DATE(ts) d, COUNT(*) c
        FROM user_login_audit
        WHERE rol=? AND usuario_id=? AND ts>=? AND ts<?
        GROUP BY DATE(ts)";
$stmt = $con->prepare($sql);
$stmt->bind_param("siss", $rol, $user_id, $ini, $fin);
$stmt->execute();
$res = $stmt->get_result();

$counts = []; $total = 0;
while ($r = $res->fetch_assoc()) { $counts[$r['d']] = (int)$r['c']; $total += (int)$r['c']; }
$stmt->close();

// construir serie día a día del mes seleccionado
$daily = [];
$cursor = strtotime($ini); $limit = strtotime($fin);
while ($cursor < $limit) {
  $dstr = date('Y-m-d', $cursor);
  $daily[] = ['day'=>intval(date('d',$cursor)), 'date'=>$dstr, 'count'=>$counts[$dstr] ?? 0];
  $cursor = strtotime('+1 day', $cursor);
}

// listado detallado
$detail = [];
$sql = "SELECT ts, ip, user_agent FROM user_login_audit
        WHERE rol=? AND usuario_id=? AND ts>=? AND ts<?
        ORDER BY ts DESC";
$stmt = $con->prepare($sql);
$stmt->bind_param("siss", $rol, $user_id, $ini, $fin);
$stmt->execute();
$res = $stmt->get_result();
while ($r = $res->fetch_assoc()) {
  $detail[] = ['ts'=>$r['ts'], 'ip'=>$r['ip'], 'ua'=>$r['user_agent']];
}
$stmt->close();

echo json_encode([
  'user'  => ['user_id'=>$user_id, 'rol'=>$rol, 'nombre'=>$nombre],
  'month' => $month,
  'total' => $total,
  'daily' => $daily,
  'logins'=> $detail
]);
