<?php
require 'conecta.php';
$con = conecta();

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['ok'=>false,'success'=>false,'message'=>'Método no permitido']);
  exit;
}

$id              = intval($_POST['estudiante_id'] ?? 0);
$nombre          = trim($_POST['nombre'] ?? '');
$apellido        = trim($_POST['apellido'] ?? '');
$fecha_nacimiento= $_POST['fecha_nacimiento'] ?? null;
$grado           = intval($_POST['grado'] ?? 0);
$grupo           = trim($_POST['grupo'] ?? '');
$tutor_id        = isset($_POST['tutor_id']) && $_POST['tutor_id'] !== '' ? intval($_POST['tutor_id']) : null;

if (!$id || $nombre==='' || $apellido==='' || !$fecha_nacimiento || !$grado || $grupo==='') {
  echo json_encode(['ok'=>false,'success'=>false,'message'=>'Datos incompletos']);
  exit;
}

$con->begin_transaction();

try {
  // 1) Actualizar estudiante
  $stmt1 = $con->prepare("
    UPDATE estudiantes
       SET nombre=?, apellido=?, fecha_nacimiento=?, grado=?, grupo=?, tutor_id=?, actualizado_en=NOW()
     WHERE estudiante_id=?
  ");
  $stmt1->bind_param("sssissi", $nombre, $apellido, $fecha_nacimiento, $grado, $grupo, $tutor_id, $id);
  if (!$stmt1->execute()) throw new Exception('No se pudo actualizar estudiante');
  $stmt1->close();

  // 2) Relación tutor_estudiante
  $stmt2 = $con->prepare("DELETE FROM tutor_estudiante WHERE estudiante_id=?");
  $stmt2->bind_param("i", $id);
  if (!$stmt2->execute()) throw new Exception('No se pudo limpiar relación tutor_estudiante');
  $stmt2->close();

  if ($tutor_id !== null) {
    $stmt3 = $con->prepare("INSERT INTO tutor_estudiante (tutor_id, estudiante_id, asignado_en) VALUES (?, ?, NOW())");
    $stmt3->bind_param("ii", $tutor_id, $id);
    if (!$stmt3->execute()) throw new Exception('No se pudo insertar relación tutor_estudiante');
    $stmt3->close();
  }

  $con->commit();

  // Devuelve AMBAS claves por compatibilidad
  echo json_encode(['ok'=>true, 'success'=>true]);
} catch (Exception $e) {
  $con->rollback();
  echo json_encode(['ok'=>false, 'success'=>false, 'message'=>$e->getMessage()]);
}

$con->close();
