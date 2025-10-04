<?php
// File: obtener_clase.php
require 'conecta.php';
header('Content-Type: application/json; charset=utf-8');

$con = conecta();
if (!$con) {
  echo json_encode(['error' => 'No hay conexión a la BD']);
  exit;
}
$con->set_charset('utf8mb4');

$clase_id = isset($_GET['clase_id']) ? intval($_GET['clase_id']) : 0;

if ($clase_id > 0) {
  // ── MODO DETALLE: una clase + asignaciones ────────────────────────────────
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
  if (!$stmt) {
    echo json_encode(['error' => 'Error preparando consulta de clase']);
    exit;
  }
  $stmt->bind_param('i', $clase_id);
  $stmt->execute();
  $res = $stmt->get_result();
  $clase = $res->fetch_assoc();

  if (!$clase) {
    echo json_encode(['error' => 'Clase no encontrada']);
    exit;
  }

  $sqlAsig = "
    SELECT 
      ca.materia_id,
      ca.docente_id
    FROM clase_asignacion ca
    WHERE ca.clase_id = ?
  ";
  $stmt2 = $con->prepare($sqlAsig);
  if (!$stmt2) {
    echo json_encode(['error' => 'Error preparando consulta de asignaciones']);
    exit;
  }
  $stmt2->bind_param('i', $clase_id);
  $stmt2->execute();
  $res2 = $stmt2->get_result();

  $asignaciones = [];
  while ($row = $res2->fetch_assoc()) {
    $asignaciones[] = $row;
  }

  echo json_encode([
    'clase' => $clase,
    'asignaciones' => $asignaciones
  ]);
  exit;
}

// ── MODO LISTA: todas las clases (para poblar el <select>) ───────────────────
$sqlLista = "
  SELECT
    cl.clase_id,
    cl.ciclo_id,
    ce.nombre AS ciclo,
    cl.grado,
    cl.grupo
  FROM clases cl
  JOIN ciclos_escolares ce ON ce.ciclo_id = cl.ciclo_id
  ORDER BY 
    /* si no tienes ce.fecha_inicio, cambia por ce.nombre */
    ce.fecha_inicio DESC, cl.grado ASC, cl.grupo ASC
";
$res = $con->query($sqlLista);
if (!$res) {
  echo json_encode(['error' => 'Error consultando clases']);
  exit;
}

$clases = [];
while ($r = $res->fetch_assoc()) {
  $clases[] = $r;
}

echo json_encode($clases);
