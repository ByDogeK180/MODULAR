<?php
require_once 'conecta.php';
header('Content-Type: application/json');

$con = conecta();
if (!$con) {
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Error de conexión']);
  exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$clase_id = intval($input['clase_id']);
$periodo_id = intval($input['periodo_id']);
$calificaciones = $input['calificaciones'] ?? [];

$con->begin_transaction();
try {
  foreach ($calificaciones as $registro) {
    $est_id = intval($registro['estudiante_id']);
    $valores = $registro['calificaciones'];

    // Calcular promedio solo con valores válidos
    $promedio = 0;
    $validos = array_filter($valores, fn($c) => is_numeric($c['valor']));
    if (count($validos) > 0) {
      $suma = array_sum(array_column($validos, 'valor'));
      $promedio = round($suma / count($validos), 2);
    }

    // Verificar si ya existe calificacion_id
    $stmt = $con->prepare("SELECT calificacion_id FROM calificaciones WHERE estudiante_id = ? AND clase_id = ? AND periodo_id = ?");
    $stmt->bind_param('iii', $est_id, $clase_id, $periodo_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $calif = $res->fetch_assoc();
    $stmt->close();

    if ($calif) {
      $calif_id = $calif['calificacion_id'];

      // Borrar detalle existente
      $del = $con->prepare("DELETE FROM calificaciones_detalle WHERE calificacion_id = ?");
      $del->bind_param('i', $calif_id);
      $del->execute();
      $del->close();

      // Actualizar promedio
      $up = $con->prepare("UPDATE calificaciones SET promedio = ? WHERE calificacion_id = ?");
      $up->bind_param('di', $promedio, $calif_id);
      $up->execute();
      $up->close();

    } else {
      // Insertar nueva fila en calificaciones
      $insert = $con->prepare("INSERT INTO calificaciones (estudiante_id, clase_id, ciclo_id, periodo_id, promedio)
        SELECT ?, ?, c.ciclo_id, ?, ? FROM clases c WHERE c.clase_id = ? LIMIT 1");
      $insert->bind_param('iiidi', $est_id, $clase_id, $periodo_id, $promedio, $clase_id);
      $insert->execute();
      $calif_id = $insert->insert_id;
      $insert->close();
    }

    // Insertar calificaciones_detalle con numero = materia_id
    $insert_det = $con->prepare("INSERT INTO calificaciones_detalle (calificacion_id, numero, valor) VALUES (?, ?, ?)");
    foreach ($valores as $cal) {
      $numero = intval($cal['materia_id']); // usamos el id de la materia como "numero"
      $val = floatval($cal['valor']);
      $insert_det->bind_param('iid', $calif_id, $numero, $val);
      $insert_det->execute();
    }
    $insert_det->close();
  }

  $con->commit();
  echo json_encode(['success' => true]);
} catch (Exception $e) {
  $con->rollback();
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
