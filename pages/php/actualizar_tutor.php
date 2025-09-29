<?php
// File: actualizar_tutor.php
header('Content-Type: application/json');
require 'conecta.php';

$con = conecta();
if (!$con) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de conexión']);
    exit;
}

// Leer JSON del body
$input = json_decode(file_get_contents("php://input"), true);

$tutor_id  = intval($input['tutor_id'] ?? 0);
$nombre    = trim($input['nombre'] ?? '');
$apellido  = trim($input['apellido'] ?? '');
$telefono  = trim($input['telefono'] ?? '');
$correo    = trim($input['correo'] ?? '');
$direccion = trim($input['direccion'] ?? '');
$activo    = isset($input['activo']) ? intval($input['activo']) : 1;

if ($tutor_id <= 0 || $nombre === '' || $apellido === '') {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

$sql = "UPDATE tutores 
        SET nombre=?, apellido=?, telefono=?, correo=?, direccion=?, activo=? 
        WHERE tutor_id=?";

$stmt = $con->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error preparando consulta']);
    exit;
}

$stmt->bind_param("ssssiii", $nombre, $apellido, $telefono, $correo, $direccion, $activo, $tutor_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'No se pudo actualizar']);
}

$stmt->close();
$con->close();
