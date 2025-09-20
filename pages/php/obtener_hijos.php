<?php
header('Content-Type: application/json');
require 'conecta.php';

$tutor_id = isset($_GET['tutor_id']) ? intval($_GET['tutor_id']) : 0;

if ($tutor_id <= 0) {
    echo json_encode(['error' => 'Tutor inválido']);
    exit;
}

$con = conecta();

// 🔹 Traer hijos ya sea por la columna tutor_id o por la tabla tutor_estudiante
$sql = "
    SELECT e.estudiante_id, e.nombre, e.apellido, e.grado, e.grupo
    FROM estudiantes e
    WHERE e.tutor_id = ?
    
    UNION
    
    SELECT e.estudiante_id, e.nombre, e.apellido, e.grado, e.grupo
    FROM estudiantes e
    INNER JOIN tutor_estudiante te ON e.estudiante_id = te.estudiante_id
    WHERE te.tutor_id = ?
";

$stmt = $con->prepare($sql);
$stmt->bind_param("ii", $tutor_id, $tutor_id);
$stmt->execute();
$res = $stmt->get_result();

$hijos = [];
while ($row = $res->fetch_assoc()) {
    $hijos[] = $row;
}

echo json_encode($hijos, JSON_UNESCAPED_UNICODE);

$stmt->close();
$con->close();
