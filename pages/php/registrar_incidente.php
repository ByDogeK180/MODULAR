<?php
require_once 'conecta.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
  echo json_encode([]);
  exit;
}

$con = conecta();

$estudiante_id = $_POST['estudiante_id'] ?? null;
$materia_id = $_POST['materia_id'] ?? null;
$tipo = $_POST['tipo'] ?? null;
$descripcion = trim($_POST['descripcion'] ?? '');
$fecha = $_POST['fecha'] ?? null;
$alcance = 'particular';

if (!$estudiante_id || !$materia_id || !$tipo || !$descripcion || !$fecha) {
  echo json_encode(['status' => 'error', 'msg' => 'Faltan datos']);
  exit;
}

$stmt = $con->prepare("
  INSERT INTO incidentes (estudiante_id, materia_id, fecha, descripcion, tipo, alcance)
  VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->bind_param('iissss', $estudiante_id, $materia_id, $fecha, $descripcion, $tipo, $alcance);
$stmt->execute();

echo json_encode(['status' => 'success']);
