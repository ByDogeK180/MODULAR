<?php
require 'conecta.php';
header('Content-Type: application/json');

$con = conecta();
if (!$con) {
  echo json_encode([]);
  exit;
}

$ciclo_id = isset($_GET['ciclo_id']) ? intval($_GET['ciclo_id']) : 0;
if ($ciclo_id <= 0) {
  echo json_encode([]);
  exit;
}

$stmt = $con->prepare("
  SELECT periodo_id, nombre
  FROM periodos
  WHERE ciclo_id = ?
  ORDER BY periodo_id ASC
");
$stmt->bind_param("i", $ciclo_id);
$stmt->execute();
$res = $stmt->get_result();

$periodos = [];
while ($row = $res->fetch_assoc()) {
  $periodos[] = $row;
}

echo json_encode($periodos);
