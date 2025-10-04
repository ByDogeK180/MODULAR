<?php
// File: pages/php/toggle_estado_ciclo.php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/conecta.php';

function jexit($code, $payload){
  http_response_code($code);
  echo json_encode($payload, JSON_UNESCAPED_UNICODE);
  exit;
}

try {
  $con = conecta(); // mysqli
} catch (Throwable $e) {
  jexit(500, ['ok'=>false, 'msg'=>'No se pudo conectar a BD']);
}

// Aceptar id por 'id' o 'ciclo_id'
$id = $_POST['id'] ?? $_POST['ciclo_id'] ?? null;
$id = filter_var($id, FILTER_VALIDATE_INT);
if (!$id) {
  jexit(400, ['ok'=>false, 'msg'=>'Parámetro id inválido']);
}

// Normalizar estado si viene; si no, hacer toggle
$estado = isset($_POST['estado']) ? strtolower(trim($_POST['estado'])) : null;
if ($estado && !in_array($estado, ['activo','cerrado'], true)) {
  $estado = null; // invalido => forzar toggle
}

if ($estado === null) {
  // Leer estado actual para hacer toggle
  $sel = $con->prepare("SELECT estado FROM ciclos_escolares WHERE ciclo_id=?");
  $sel->bind_param('i', $id);
  if (!$sel->execute()) {
    jexit(500, ['ok'=>false, 'msg'=>'No se pudo leer el estado actual']);
  }
  $sel->bind_result($actual);
  if (!$sel->fetch()) {
    $sel->close();
    jexit(404, ['ok'=>false, 'msg'=>'Ciclo no encontrado']);
  }
  $sel->close();
  $estado = ($actual === 'activo') ? 'cerrado' : 'activo';
}

// Actualizar
$upd = $con->prepare("UPDATE ciclos_escolares SET estado=? WHERE ciclo_id=?");
$upd->bind_param('si', $estado, $id);
if (!$upd->execute()) {
  $err = $con->error ?: 'Error al actualizar';
  $upd->close();
  jexit(500, ['ok'=>false, 'msg'=>$err]);
}
$upd->close();

jexit(200, ['ok'=>true, 'estado'=>$estado]);
