<?php
declare(strict_types=1);

require_once 'conecta.php';
session_start();

header('Content-Type: application/json; charset=utf-8');

$tutor_id = $_SESSION['tutor_id'] ?? null;

if (!$tutor_id) {
  echo json_encode([]);
  exit;
}

$con = conecta();
if (method_exists($con, 'set_charset')) {
  $con->set_charset('utf8mb4');
}

$sql = "
  SELECT nota_id, fecha, contenido
  FROM notas_calendario
  WHERE tutor_id = ?
    AND contenido IS NOT NULL
    AND TRIM(contenido) <> ''
  ORDER BY fecha ASC
";

$stmt = $con->prepare($sql);
$stmt->bind_param("i", $tutor_id);
$stmt->execute();
$res = $stmt->get_result();

$notas = [];
while ($row = $res->fetch_assoc()) {
  $notas[] = [
    'id'    => (int)$row['nota_id'],
    'title' => $row['contenido'],
    'start' => $row['fecha'],
    'color' => '#007bff'
  ];
}

$stmt->close();
$con->close();

echo json_encode($notas, JSON_UNESCAPED_UNICODE);
