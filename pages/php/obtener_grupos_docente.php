<?php
session_start();
header('Content-Type: application/json');
require 'conecta.php';

if (!isset($_SESSION['docente_id'])) {
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

$docente_id = $_SESSION['docente_id'];
$con = conecta();

if (!$con) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión a la base de datos']);
    exit;
}

$query = "
    SELECT DISTINCT 
        ca.materia_id,
        m.nombre AS materia_nombre,
        c.grado,
        c.grupo,
        ci.nombre AS ciclo
    FROM clase_asignacion ca
    INNER JOIN clases c ON c.clase_id = ca.clase_id
    INNER JOIN materias m ON m.materia_id = ca.materia_id
    INNER JOIN ciclos_escolares ci ON ci.ciclo_id = c.ciclo_id
    WHERE ca.docente_id = ?
    ORDER BY c.grado, c.grupo, m.nombre
";



$stmt = $con->prepare($query);
$stmt->bind_param("i", $docente_id);
$stmt->execute();
$res = $stmt->get_result();

$grupos = [];
while ($row = $res->fetch_assoc()) {
    $grupos[] = $row;
}

echo json_encode($grupos, JSON_UNESCAPED_UNICODE);

$stmt->close();
$con->close();
