<?php
// api/get_hijos.php
session_start();
require 'conecta.php';
header('Content-Type: application/json');

// Conexión
$conn = conecta();
if (!$conn) {
    echo json_encode([]);
    exit;
}
// ------------------------------------------------------------------------------

$tutor_id = null;
if (!empty($_SESSION['tutor_id'])) {
    $tutor_id = intval($_SESSION['tutor_id']);
} elseif (!empty($_SESSION['usuario_id'])) {
    // si solo tienes usuario_id en sesión, buscar tutor_id
    $usuario_id = intval($_SESSION['usuario_id']);
    $stmt = $conn->prepare("SELECT tutor_id FROM tutores WHERE usuario_id = ? LIMIT 1");
    $stmt->bind_param('i', $usuario_id);
    $stmt->execute();
    $stmt->bind_result($tmp_tutor_id);
    if ($stmt->fetch()) $tutor_id = intval($tmp_tutor_id);
    $stmt->close();
}

if (!$tutor_id) {
    echo json_encode(['error' => 'Tutor no autenticado']);
    exit;
}

// obtener hijos asignados
$stmt = $conn->prepare("
    SELECT te.estudiante_id, e.nombre, e.apellido, e.grado, e.grupo
    FROM tutor_estudiante te
    JOIN estudiantes e ON te.estudiante_id = e.estudiante_id
    WHERE te.tutor_id = ? AND e.activo = 1
    ORDER BY e.nombre, e.apellido
");
$stmt->bind_param('i', $tutor_id);
$stmt->execute();
$res = $stmt->get_result();

$hijos = [];
while ($row = $res->fetch_assoc()) {
    $hijos[] = [
        'estudiante_id' => intval($row['estudiante_id']),
        'nombre' => trim($row['nombre'] . ' ' . $row['apellido']),
        'grado' => $row['grado'],
        'grupo' => $row['grupo']
    ];
}
$stmt->close();
echo json_encode(['hijos' => $hijos]);
