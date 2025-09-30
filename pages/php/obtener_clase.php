<?php
// File: obtener_clase.php

require 'conecta.php';
header('Content-Type: application/json');

$con = conecta();
if (!$con) {
    echo json_encode(['error' => 'No hay conexión a la BD']);
    exit;
}

$clase_id = intval($_GET['clase_id'] ?? 0);
if ($clase_id <= 0) {
    echo json_encode(['error' => 'clase_id requerido']);
    exit;
}

// 1) Obtener info de la clase
$sqlClase = "
  SELECT 
    cl.clase_id,
    cl.ciclo_id,
    ce.nombre AS ciclo,
    cl.grado,
    cl.grupo
  FROM clases cl
  JOIN ciclos_escolares ce ON ce.ciclo_id = cl.ciclo_id
  WHERE cl.clase_id = ?
  LIMIT 1
";

$stmt = $con->prepare($sqlClase);
$stmt->bind_param("i", $clase_id);
$stmt->execute();
$res = $stmt->get_result();

$clase = $res->fetch_assoc();

// 2) Obtener asignaciones de la clase
$sqlAsig = "
  SELECT 
    ca.materia_id,
    ca.docente_id
  FROM clase_asignacion ca
  WHERE ca.clase_id = ?
";


$stmt2 = $con->prepare($sqlAsig);
$stmt2->bind_param("i", $clase_id);
$stmt2->execute();
$res2 = $stmt2->get_result();

$asignaciones = [];
while ($row = $res2->fetch_assoc()) {
    $asignaciones[] = $row;
}

// 3) Respuesta unificada
echo json_encode([
    'clase' => $clase,
    'asignaciones' => $asignaciones
]);
