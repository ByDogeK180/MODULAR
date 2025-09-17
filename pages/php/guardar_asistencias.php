<?php
declare(strict_types=1);
session_start();
require 'conecta.php';
header('Content-Type: application/json; charset=utf-8');

// Evitar que warnings/errores rompan el JSON
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/error_log.txt');
error_reporting(E_ALL);

$con = conecta();

// Recibir datos JSON
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Validar estructura
if (
  empty($data['asistencias']) || !is_array($data['asistencias']) ||
  empty($data['materia_id']) ||
  empty($data['fecha'])
) {
  echo json_encode([
    "success" => false,
    "error" => "Datos inválidos",
    "recibido" => $data
  ]);
  exit;
}

$materia_id = (int)$data['materia_id'];
$fecha = $data['fecha'];

// Verificar sesión
$usuario_id = $_SESSION['usuario_id'] ?? null;
if (!$usuario_id) {
  echo json_encode(["success" => false, "error" => "No autenticado"]);
  exit;
}

// Obtener docente_id
$docenteQuery = $con->prepare("SELECT docente_id FROM docentes WHERE usuario_id = ?");
$docenteQuery->bind_param("i", $usuario_id);
$docenteQuery->execute();
$result = $docenteQuery->get_result();
if ($result->num_rows === 0) {
  echo json_encode(["success" => false, "error" => "Docente no encontrado"]);
  exit;
}
$docente_id = (int)$result->fetch_assoc()['docente_id'];
$docenteQuery->close();

// Preparar queries
$queryCheck = "SELECT asistencia_id 
               FROM asistencias 
               WHERE estudiante_id = ? AND materia_id = ? AND fecha = ?";
$stmtCheck = $con->prepare($queryCheck);

$queryInsert = "INSERT INTO asistencias (estudiante_id, estado, fecha, docente_id, materia_id)
                VALUES (?, ?, ?, ?, ?)";
$stmtInsert = $con->prepare($queryInsert);

$queryUpdate = "UPDATE asistencias 
                SET estado = ?, actualizado_en = NOW()
                WHERE estudiante_id = ? AND materia_id = ? AND fecha = ?";
$stmtUpdate = $con->prepare($queryUpdate);

// Procesar asistencias
foreach ($data['asistencias'] as $a) {
  $estudiante_id = (int)$a['estudiante_id'];
  $estado = $a['estado'];

  // 1. Verificar si ya existe
  $stmtCheck->bind_param("iis", $estudiante_id, $materia_id, $fecha);
  $stmtCheck->execute();
  $res = $stmtCheck->get_result();

  if ($res->num_rows > 0) {
    // 2. Ya existe → UPDATE
    $stmtUpdate->bind_param("siis", $estado, $estudiante_id, $materia_id, $fecha);
    $stmtUpdate->execute();
  } else {
    // 3. No existe → INSERT
    $stmtInsert->bind_param("issii", $estudiante_id, $estado, $fecha, $docente_id, $materia_id);
    $stmtInsert->execute();
  }
}

$stmtCheck->close();
$stmtInsert->close();
$stmtUpdate->close();
$con->close();

echo json_encode(["success" => true, "message" => "Asistencias guardadas/actualizadas"]);
