<?php
require_once 'conecta.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
  echo json_encode([]);
  exit;
}

$con = conecta();

// Obtener el docente_id
$stmt = $con->prepare("SELECT docente_id FROM docentes WHERE usuario_id = ?");
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
  echo json_encode([]);
  exit;
}

$docente_id = $res->fetch_assoc()['docente_id'];
$clase_id = $_GET['clase_id'] ?? null;

if (!$clase_id) {
  echo json_encode([]);
  exit;
}

$query = "
  SELECT m.materia_id, m.nombre AS materia_nombre
  FROM clase_asignacion ca
  INNER JOIN materias m ON m.materia_id = ca.materia_id
  WHERE ca.docente_id = ? AND ca.clase_id = ?
";

$stmt = $con->prepare($query);
$stmt->bind_param("ii", $docente_id, $clase_id);
$stmt->execute();
$res = $stmt->get_result();

$materias = [];
while ($row = $res->fetch_assoc()) {
  $materias[] = $row;
}

echo json_encode($materias, JSON_UNESCAPED_UNICODE);
