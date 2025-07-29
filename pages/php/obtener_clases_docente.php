<?php
require 'conecta.php';
header('Content-Type: application/json');

$con = conecta();
if (!$con) {
    echo json_encode([]);
    exit;
}

// Trae todas las clases con su ciclo escolar
$sql = "
  SELECT 
    c.clase_id,
    ce.nombre AS ciclo,
    c.grado,
    c.grupo,
    c.ciclo_id
  FROM clases c
  JOIN ciclos_escolares ce ON ce.ciclo_id = c.ciclo_id
  ORDER BY ce.fecha_inicio DESC, c.grado, c.grupo
";

$res = $con->query($sql);
$clases = [];
if ($res) {
  while ($row = $res->fetch_assoc()) {
    $clases[] = $row;
  }
}

echo json_encode($clases);
