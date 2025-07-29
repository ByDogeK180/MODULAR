<?php
header('Content-Type: application/json');
require_once 'conecta.php';
$conexion = conecta();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $clase_id = $_POST['clase_id'] ?? null;
  $materia_id = $_POST['materia_id'] ?? null;
  $tipo = $_POST['tipo'] ?? null;
  $descripcion = trim($_POST['descripcion'] ?? '');
  $fecha = $_POST['fecha'] ?? null;
  $alcance = 'general';

  if (!$clase_id || !$materia_id || !$tipo || !$descripcion || !$fecha) {
    echo json_encode(['status' => 'error', 'msg' => 'Faltan datos obligatorios']);
    exit;
  }

  // Obtener estudiantes inscritos en esa clase y materia
  $stmt = $conexion->prepare("
    SELECT i.estudiante_id
    FROM inscripciones i
    JOIN clase_asignacion ca ON ca.clase_id = i.clase_id
    WHERE i.clase_id = ? AND ca.materia_id = ?
  ");
  $stmt->bind_param('ii', $clase_id, $materia_id);
  $stmt->execute();
  $resultado = $stmt->get_result();

  if ($resultado->num_rows === 0) {
    echo json_encode(['status' => 'error', 'msg' => 'No hay estudiantes en la clase seleccionada']);
    exit;
  }

  $insert = $conexion->prepare("
    INSERT INTO incidentes (estudiante_id, materia_id, fecha, descripcion, tipo, alcance)
    VALUES (?, ?, ?, ?, ?, ?)
  ");

  while ($row = $resultado->fetch_assoc()) {
    $estudiante_id = $row['estudiante_id'];
    $insert->bind_param('iissss', $estudiante_id, $materia_id, $fecha, $descripcion, $tipo, $alcance);
    $insert->execute();
  }

  echo json_encode(['status' => 'success']);
} else {
  echo json_encode(['status' => 'error', 'msg' => 'Método no permitido']);
}
