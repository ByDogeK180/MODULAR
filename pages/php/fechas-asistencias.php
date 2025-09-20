<?php
require 'conecta.php';
header('Content-Type: application/json; charset=utf-8');

// 🔒 evitar que se impriman warnings/notices en la respuesta
ini_set('display_errors', '0');
error_reporting(0);

$con = conecta();

$materia_id = isset($_GET['materia_id']) ? (int)$_GET['materia_id'] : 0;

if (!$materia_id) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT DISTINCT fecha 
        FROM asistencias 
        WHERE materia_id = ? 
        ORDER BY fecha DESC";

$stmt = $con->prepare($sql);

if (!$stmt) {
    echo json_encode([]);
    exit;
}

$stmt->bind_param("i", $materia_id);
$stmt->execute();
$result = $stmt->get_result();

$fechas = [];
while ($row = $result->fetch_assoc()) {
    $fechas[] = $row['fecha'];
}

$stmt->close();
$con->close();

echo json_encode($fechas ?? []);
