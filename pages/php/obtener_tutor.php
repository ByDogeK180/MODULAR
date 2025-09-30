<?php
// File: obtener_tutor.php
header('Content-Type: application/json');
require 'conecta.php';

$con = conecta();
if (!$con) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión']);
    exit;
}

$tutor_id = intval($_GET['tutor_id'] ?? 0);
if ($tutor_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'tutor_id requerido']);
    exit;
}

$sql = "SELECT tutor_id, nombre, apellido, telefono, correo, direccion, activo
        FROM tutores
        WHERE tutor_id = ?
        LIMIT 1";

$stmt = $con->prepare($sql);
$stmt->bind_param("i", $tutor_id);
$stmt->execute();
$res = $stmt->get_result();
$tutor = $res->fetch_assoc();

if ($tutor) {
    $tutor['activo'] = $tutor['activo'] ? true : false;
    echo json_encode(['tutor' => $tutor], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['error' => 'Tutor no encontrado']);
}

$stmt->close();
$con->close();
