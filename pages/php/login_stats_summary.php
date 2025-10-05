<?php
// File: pages/php/login_stats_summary.php
require 'conecta.php';
header('Content-Type: application/json');

$con = conecta();
if (!$con) { echo json_encode(['error'=>'sin_conexion']); exit; }

$rol   = isset($_GET['rol']) ? strtolower(trim($_GET['rol'])) : ''; // '', 'docente','tutor','admin'
$month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');      // 'YYYY-MM'

$ini = $month . '-01';
$fin = date('Y-m-d', strtotime("$ini +1 month"));

$sql = "SELECT rol, usuario_id, COUNT(*) total
        FROM user_login_audit
        WHERE ts >= ? AND ts < ?";

$types = 'ss';
$params = [$ini, $fin];

if (in_array($rol, ['docente','tutor','admin'])) {
  $sql .= " AND rol = ?";
  $types .= 's';
  $params[] = $rol;
}

$sql .= " GROUP BY rol, usuario_id ORDER BY total DESC";

$stmt = $con->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();

$out = [];
while ($r = $res->fetch_assoc()) {
  $nombre = null;
  if ($r['rol'] === 'docente') {
    $q = $con->prepare("SELECT CONCAT(nombre,' ',apellido) AS nombre FROM docentes WHERE docente_id=? LIMIT 1");
    $q->bind_param("i", $r['usuario_id']); $q->execute();
    $nombre = $q->get_result()->fetch_assoc()['nombre'] ?? null; $q->close();
  } elseif ($r['rol'] === 'tutor') {
    $q = $con->prepare("SELECT CONCAT(nombre,' ',apellido) AS nombre FROM tutores WHERE tutor_id=? LIMIT 1");
    $q->bind_param("i", $r['usuario_id']); $q->execute();
    $nombre = $q->get_result()->fetch_assoc()['nombre'] ?? null; $q->close();
  } else {
    $nombre = "Admin #".$r['usuario_id'];
  }

  $out[] = [
    'rol'        => $r['rol'],
    'usuario_id' => (int)$r['usuario_id'],
    'nombre'     => $nombre ?: "{$r['rol']} #{$r['usuario_id']}",
    'total'      => (int)$r['total'],
];

}
echo json_encode(['month'=>$month, 'users'=>$out]);
