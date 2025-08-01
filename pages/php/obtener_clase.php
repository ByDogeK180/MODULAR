<?php
// File: obtener_clase.php

require 'conecta.php';
header('Content-Type: application/json');

$con = conecta();
if (!$con) {
    echo json_encode([]);
    exit;
}

// Incluye ciclo_id en el SELECT
$sql = "
  SELECT 
    cl.clase_id,
    cl.ciclo_id,
    ce.nombre AS ciclo,
    cl.grado,
    cl.grupo
  FROM clases cl
  JOIN ciclos_escolares ce ON ce.ciclo_id = cl.ciclo_id
  WHERE ce.estado = 'activo'
  ORDER BY ce.fecha_inicio DESC, cl.grado, cl.grupo
";

$res = $con->query($sql);
$clases = [];

if ($res) {
    while ($row = $res->fetch_assoc()) {
        $clases[] = [
            'clase_id'  => $row['clase_id'],
            'ciclo_id'  => $row['ciclo_id'], // necesario para filtros por ciclo
            'ciclo'     => $row['ciclo'],
            'grado'     => $row['grado'],
            'grupo'     => $row['grupo']
        ];
    }
}

echo json_encode($clases);
