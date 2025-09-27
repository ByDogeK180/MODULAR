<?php
// pages/php/procesar_pago.php
declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

session_start();
require_once __DIR__ . '/conecta.php';

$stmtCheck = null;
$stmtUpd   = null;
$con       = null;

try {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido'], JSON_UNESCAPED_UNICODE);
    return;
  }

  if (!isset($_SESSION['tutor_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'No autorizado'], JSON_UNESCAPED_UNICODE);
    return;
  }

  $raw = file_get_contents('php://input');
  $input = json_decode($raw, true);
  if (!is_array($input)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'JSON inválido'], JSON_UNESCAPED_UNICODE);
    return;
  }

  $pago_id = (int)($input['pago_id'] ?? 0);
  if ($pago_id <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'ID inválido'], JSON_UNESCAPED_UNICODE);
    return;
  }

  $tutor_id = (int)$_SESSION['tutor_id'];

  $con = conecta();
  if (!$con) {
    throw new Exception('No se pudo conectar a la base de datos');
  }
  $con->set_charset('utf8mb4');
  $con->begin_transaction();

  // 1) Verificar que el pago pertenece al tutor (FOR UPDATE para bloquear registro)
  $sqlCheck = "SELECT p.estado
               FROM pagos p
               INNER JOIN estudiantes e ON e.estudiante_id = p.estudiante_id
               WHERE p.pago_id = ? AND e.tutor_id = ?
               FOR UPDATE";

  $stmtCheck = $con->prepare($sqlCheck);
  if (!$stmtCheck) throw new Exception('Error al preparar verificación: '.$con->error);
  $stmtCheck->bind_param('ii', $pago_id, $tutor_id);
  if (!$stmtCheck->execute()) throw new Exception('Error al ejecutar verificación: '.$stmtCheck->error);
  $res = $stmtCheck->get_result();
  $row = $res->fetch_assoc();

  if (!$row) {
    throw new Exception('No tienes permiso para este pago o no existe');
  }

  if (strtolower((string)$row['estado']) === 'pagado') {
    // Idempotente: ya estaba pagado
    $con->commit();
    echo json_encode(['status' => 'success', 'message' => 'El pago ya estaba pagado'], JSON_UNESCAPED_UNICODE);
    return;
  }

  // 2) Actualizar a pagado
  $sqlUpd = "UPDATE pagos
             SET estado = 'pagado',
                 fecha_pago = CURDATE(),
                 actualizado_en = NOW()
             WHERE pago_id = ? AND estado <> 'pagado'";

  $stmtUpd = $con->prepare($sqlUpd);
  if (!$stmtUpd) throw new Exception('Error al preparar actualización: '.$con->error);
  $stmtUpd->bind_param('i', $pago_id);
  if (!$stmtUpd->execute()) throw new Exception('Error al ejecutar actualización: '.$stmtUpd->error);

  if ($stmtUpd->affected_rows === 0) {
    throw new Exception('No se pudo actualizar (¿ya estaba pagado?)');
  }

  $con->commit();
  echo json_encode(['status' => 'success'], JSON_UNESCAPED_UNICODE);
  return;

} catch (Throwable $t) {
  if ($con instanceof mysqli) { $con->rollback(); }
  http_response_code(400);
  echo json_encode(['status' => 'error', 'message' => $t->getMessage()], JSON_UNESCAPED_UNICODE);
  return;

} finally {
  if ($stmtUpd   instanceof mysqli_stmt) { $stmtUpd->close();   }
  if ($stmtCheck instanceof mysqli_stmt) { $stmtCheck->close(); }
  if ($con       instanceof mysqli)      { $con->close();       }
}
