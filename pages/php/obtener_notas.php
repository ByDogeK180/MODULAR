<?php
require_once 'conecta.php';
session_start();

$tutor_id = $_SESSION['tutor_id'] ?? null;

if (!$tutor_id) {
  echo json_encode([]);
  exit;
}

$con = conecta();
$stmt = $con->prepare("SELECT nota_id, fecha, contenido FROM notas_calendario WHERE tutor_id = ?");
$stmt->bind_param("i", $tutor_id);
$stmt->execute();
$res = $stmt->get_result();

$notas = [];
while ($row = $res->fetch_assoc()) {
  $notas[] = [
    'id' => $row['nota_id'],
    'title' => $row['contenido'],
    'start' => $row['fecha'],
    'color' => '#007bff'
  ];
}

echo json_encode($notas);
?>
