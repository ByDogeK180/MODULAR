<?php
declare(strict_types=1);

require_once 'conecta.php';
session_start();

header('Content-Type: text/plain; charset=utf-8');

$tutor_id  = $_SESSION['tutor_id'] ?? 0;
$fecha     = isset($_POST['fecha']) ? trim($_POST['fecha']) : '';
$contenido = isset($_POST['contenido']) ? trim($_POST['contenido']) : null;

if (!$tutor_id || $fecha === '') {
  http_response_code(400);
  echo "Datos incompletos.";
  exit;
}

$con = conecta();
if (method_exists($con, 'set_charset')) { $con->set_charset('utf8mb4'); }

/**
 * BORRAR cuando contenido es vacío o NULL
 * (tu JS envía contenido='' al eliminar)
 */
if ($contenido === '' || $contenido === null) {
  $del = $con->prepare("DELETE FROM notas_calendario WHERE tutor_id = ? AND fecha = ?");
  $del->bind_param("is", $tutor_id, $fecha);
  $ok = $del->execute();
  $del->close();
  $con->close();

  if ($ok) { echo "OK"; }
  else { http_response_code(500); echo "Error al eliminar."; }
  exit;
}

/**
 * UPSERT: si existe (por tutor+fecha) -> UPDATE, si no -> INSERT
 */
$check = $con->prepare("SELECT nota_id FROM notas_calendario WHERE tutor_id = ? AND fecha = ? LIMIT 1");
$check->bind_param("is", $tutor_id, $fecha);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
  // UPDATE existente
  $check->bind_result($nota_id);
  $check->fetch();
  $check->close();

  $stmt = $con->prepare("UPDATE notas_calendario SET contenido = ?, actualizado_en = NOW() WHERE nota_id = ? AND tutor_id = ?");
  $stmt->bind_param("sii", $contenido, $nota_id, $tutor_id);
  $ok = $stmt->execute();
  $stmt->close();
} else {
  // INSERT nuevo
  $check->close();
  $stmt = $con->prepare("INSERT INTO notas_calendario (tutor_id, fecha, contenido, actualizado_en) VALUES (?, ?, ?, NOW())");
  $stmt->bind_param("iss", $tutor_id, $fecha, $contenido);
  $ok = $stmt->execute();
  $stmt->close();
}

$con->close();

if ($ok) { echo "OK"; }
else { http_response_code(500); echo "Error al guardar."; }
