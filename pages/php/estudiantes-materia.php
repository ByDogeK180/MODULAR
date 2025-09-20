<?php
require 'conecta.php';
header('Content-Type: application/json; charset=utf-8');

ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/error_log.txt');
error_reporting(E_ALL);

session_start();
$con = conecta();

$materia_id = isset($_GET['materia_id']) ? (int)$_GET['materia_id'] : 0;
$fecha = $_GET['fecha'] ?? null;

$usuario_id = $_SESSION['usuario_id'] ?? null;

if (!$materia_id || !$usuario_id) {
    echo json_encode([
        "success" => false,
        "error" => "Faltan parámetros",
        "materia_id" => $materia_id,
        "fecha" => $fecha,
        "usuario_id" => $usuario_id
    ]);
    exit;
}

// Obtener docente_id desde usuario
$stmtDoc = $con->prepare("SELECT docente_id FROM docentes WHERE usuario_id = ?");
$stmtDoc->bind_param("i", $usuario_id);
$stmtDoc->execute();
$resDoc = $stmtDoc->get_result();
if ($resDoc->num_rows === 0) {
    echo json_encode(["success" => false, "error" => "Docente no encontrado"]);
    exit;
}
$docente_id = (int) $resDoc->fetch_assoc()['docente_id'];
$stmtDoc->close();

// Si es nueva fecha (se envía como 0000-00-00) → traer solo inscritos
if ($fecha === "0000-00-00") {
    $sql = "SELECT e.estudiante_id, e.nombre, e.apellido
            FROM estudiantes e
            INNER JOIN inscripciones i ON i.estudiante_id = e.estudiante_id
            INNER JOIN clase_asignacion ca ON ca.clase_id = i.clase_id
            WHERE ca.materia_id = ? AND ca.docente_id = ?
            ORDER BY e.apellido, e.nombre";

    $stmt = $con->prepare($sql);
    $stmt->bind_param("ii", $materia_id, $docente_id);
} else {
    // Si es fecha existente → traer asistencias guardadas
    $sql = "SELECT e.estudiante_id, e.nombre, e.apellido, a.estado
            FROM estudiantes e
            INNER JOIN inscripciones i ON i.estudiante_id = e.estudiante_id
            INNER JOIN clase_asignacion ca ON ca.clase_id = i.clase_id
            INNER JOIN asistencias a 
                   ON a.estudiante_id = e.estudiante_id
                  AND a.materia_id = ca.materia_id
                  AND a.fecha = ?
            WHERE ca.materia_id = ? AND ca.docente_id = ?
            ORDER BY e.apellido, e.nombre";

    $stmt = $con->prepare($sql);
    $stmt->bind_param("sii", $fecha, $materia_id, $docente_id);
}

if (!$stmt->execute()) {
    echo json_encode([
        "success" => false,
        "error" => "Error en execute",
        "detalle" => $stmt->error
    ]);
    exit;
}

$result = $stmt->get_result();
$estudiantes = [];
while ($row = $result->fetch_assoc()) {
    // Si es nueva fecha, marcamos "ausente" por defecto
    if ($fecha === "0000-00-00") {
        $row['estado'] = "ausente";
    }
    $estudiantes[] = $row;
}

$stmt->close();
$con->close();

echo json_encode([
    "success" => true,
    "data" => $estudiantes
]);
