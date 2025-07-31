<?php
session_start();
require 'conecta.php';
header('Content-Type: application/json');

$con = conecta();
$docente_id = $_SESSION['docente_id'] ?? 0;

if (!$docente_id) {
  echo json_encode([]);
  exit;
}

$sql = "
  SELECT DISTINCT
    c.clase_id,
    ce.nombre AS ciclo,
    c.grado,
    c.grupo,
    c.ciclo_id
  FROM clase_asignacion ca
  JOIN clases c ON ca.clase_id = c.clase_id
  JOIN ciclos_escolares ce ON c.ciclo_id = ce.ciclo_id
  JOIN inscripciones i ON i.clase_id = c.clase_id
  WHERE ca.docente_id = ?
  ORDER BY ce.fecha_inicio DESC, c.grado, c.grupo
";

$stmt = $con->prepare($sql);
$stmt->bind_param("i", $docente_id);
$stmt->execute();
$res = $stmt->get_result();

$clases = [];
while ($row = $res->fetch_assoc()) {
  $clases[] = $row;
}

echo json_encode($clases);
