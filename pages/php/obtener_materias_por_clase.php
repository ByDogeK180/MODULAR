<?php
require_once 'conecta.php';
$con = conecta();

$clase_id = $_GET['clase_id'] ?? 0;
$data = [];

$stmt = $con->prepare("
  SELECT m.materia_id, m.nombre
  FROM clase_asignacion ca
  JOIN materias m ON ca.materia_id = m.materia_id
  WHERE ca.clase_id = ?
");
$stmt->bind_param("i", $clase_id);
$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {
  $data[] = $row;
}

echo json_encode($data);
