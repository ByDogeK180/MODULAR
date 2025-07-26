<?php
require_once 'conecta.php';
session_start();

$tutor_id = $_SESSION['tutor_id'] ?? null;
$fecha = $_POST['fecha'] ?? null;
$contenido = $_POST['contenido'] ?? null;

if (!$tutor_id || !$fecha) {
  http_response_code(400);
  echo "Datos incompletos.";
  exit;
}

$con = conecta();

// Verificar si ya existe una nota en esa fecha para este tutor
$check = $con->prepare("SELECT nota_id FROM notas_calendario WHERE tutor_id = ? AND fecha = ?");
$check->bind_param("is", $tutor_id, $fecha);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
  // Actualizar
  $stmt = $con->prepare("UPDATE notas_calendario SET contenido = ?, actualizado_en = NOW() WHERE tutor_id = ? AND fecha = ?");
  $stmt->bind_param("sis", $contenido, $tutor_id, $fecha);
} else {
  // Insertar
  $stmt = $con->prepare("INSERT INTO notas_calendario (tutor_id, fecha, contenido) VALUES (?, ?, ?)");
  $stmt->bind_param("iss", $tutor_id, $fecha, $contenido);
}

if ($stmt->execute()) {
  echo "OK";
} else {
  http_response_code(500);
  echo "Error al guardar.";
}
?>
