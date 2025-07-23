<?php
require_once 'conecta.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
  echo json_encode(['status' => 'error', 'msg' => 'No logueado']);
  exit;
}

$docente_id = $_SESSION['usuario_id'];
$estudiante_id = $_POST['estudiante_id'] ?? null;
$descripcion = $_POST['descripcion'] ?? null;
$tipo = $_POST['tipo'] ?? null;
$fecha = $_POST['fecha'] ?? date('Y-m-d');

$con = conecta();

$stmt = $con->prepare("INSERT INTO incidentes (estudiante_id, fecha, descripcion, tipo) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isss", $estudiante_id, $fecha, $descripcion, $tipo);

if ($stmt->execute()) {
  echo json_encode(['status' => 'success']);
} else {
  echo json_encode(['status' => 'error', 'msg' => $stmt->error]);
}
