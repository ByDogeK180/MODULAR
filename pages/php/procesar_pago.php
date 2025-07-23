<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once './conecta.php';
header('Content-Type: application/json');

$entrada = json_decode(file_get_contents('php://input'), true);
$pago_id = intval($entrada['pago_id'] ?? 0);

if ($pago_id <= 0) {
  echo json_encode(['status' => 'error', 'message' => 'ID inválido']);
  exit;
}

$con = conecta();
$fecha_actual = date('Y-m-d');

$stmt = $con->prepare("UPDATE pagos SET estado = 'pagado', fecha_pago = ?, actualizado_en = NOW() WHERE pago_id = ?");
$stmt->bind_param("si", $fecha_actual, $pago_id);

if ($stmt->execute()) {
  echo json_encode(['status' => 'success']);
} else {
  echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el pago']);
}
