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

  // Validación de datos
  if (!$clase_id || !$materia_id || !$tipo || !$descripcion || !$fecha) {
    echo json_encode(['status' => 'error', 'msg' => 'Faltan datos obligatorios']);
    exit;
  }

  // Consulta corregida usando clase_asignacion
  $stmt = $conexion->prepare("
    SELECT i.estudiante_id
    FROM inscripciones i
    JOIN clase_asignacion ca ON i.clase_id = ca.clase_id
    WHERE ca.clase_id = ? AND ca.materia_id = ?
  ");
  $stmt->bind_param('ii', $clase_id, $materia_id);
  $stmt->execute();
  $resultado = $stmt->get_result();

  if ($resultado->num_rows === 0) {
    echo json_encode(['status' => 'error', 'msg' => 'No hay estudiantes en la clase seleccionada']);
    exit;
  }

  $insert = $conexion->prepare("
    INSERT INTO incidentes (estudiante_id, fecha, descripcion, tipo)
    VALUES (?, ?, ?, ?)
  ");

  while ($row = $resultado->fetch_assoc()) {
    $insert->bind_param('isss', $row['estudiante_id'], $fecha, $descripcion, $tipo);
    $insert->execute();
  }

  echo json_encode(['status' => 'success']);
} else {
  echo json_encode(['status' => 'error', 'msg' => 'Método no permitido']);
}
?>
