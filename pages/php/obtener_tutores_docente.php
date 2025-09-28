<?php
header('Content-Type: application/json');
require 'conecta.php';
session_start();

$rol = $_SESSION['rol'] ?? null;
$docente_id = $_SESSION['docente_id'] ?? null;

if (!$docente_id) {
    echo json_encode(['error' => 'No hay sesión activa de docente']);
    exit;
}

$con = conecta();
if (!$con) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión']);
    exit;
}

$tutores = [];

if ($rol == 1) {
    // DOCENTE: solo ve tutores de sus clases/materias
    $query = "
        SELECT DISTINCT 
            t.tutor_id,
            t.nombre,
            t.apellido,
            t.telefono,
            t.correo,
            t.direccion,
            t.activo
        FROM tutores t
        INNER JOIN estudiantes e ON e.tutor_id = t.tutor_id
        INNER JOIN inscripciones i ON i.estudiante_id = e.estudiante_id
        INNER JOIN clase_asignacion ca ON ca.clase_id = i.clase_id
        INNER JOIN clases c ON c.clase_id = ca.clase_id
        WHERE ca.docente_id = ?
        ORDER BY t.apellido, t.nombre
    ";

    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $docente_id);

} else {
    // ADMIN: ve todos los tutores
    $query = "SELECT tutor_id, nombre, apellido, telefono, correo, direccion, activo FROM tutores";
    $stmt = $con->prepare($query);
}

if ($stmt) {
    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $row['activo'] = $row['activo'] ? true : false;
        $tutores[] = $row;
    }

    echo json_encode($tutores, JSON_UNESCAPED_UNICODE);
    $stmt->close();
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Error al preparar consulta']);
}

$con->close();
