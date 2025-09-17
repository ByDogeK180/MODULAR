<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../php/conecta.php';
$con = conecta();

if (!$con) {
    echo json_encode(["error" => "Error de conexión"]);
    exit;
}

$usuario_id = $_SESSION['usuario_id'] ?? null;
$rol = $_SESSION['rol'] ?? null;

if (!$usuario_id || !isset($rol)) {
    echo json_encode(["error" => "Sesión inválida"]);
    exit;
}

$materias = [];

/**
 * ROLES:
 * 0 = Admin
 * 1 = Docente
 * 2 = Tutor
 */

if ($rol == 1) {
    // DOCENTE → solo sus materias
    $stmtDocente = $con->prepare("SELECT docente_id FROM docentes WHERE usuario_id = ?");
    $stmtDocente->bind_param("i", $usuario_id);
    $stmtDocente->execute();
    $resDocente = $stmtDocente->get_result();

    if ($resDocente->num_rows > 0) {
        $docente_id = $resDocente->fetch_assoc()['docente_id'];

        $stmt = $con->prepare("
            SELECT DISTINCT m.materia_id, m.nombre, m.nivel_grado, m.descripcion, m.foto_url
            FROM clase_asignacion ca
            INNER JOIN materias m ON ca.materia_id = m.materia_id
            WHERE ca.docente_id = ?
        ");
        $stmt->bind_param("i", $docente_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $row['foto_url'] = $row['foto_url'] ?: 'assets/img/default.jpg';
            $materias[] = $row;
        }
    }

} elseif ($rol == 0) {
    // ADMIN → todas las materias
    $sql = "SELECT materia_id, nombre, nivel_grado, descripcion, foto_url FROM materias";
    $result = $con->query($sql);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $row['foto_url'] = $row['foto_url'] ?: 'assets/img/default.jpg';
            $materias[] = $row;
        }
    }

} elseif ($rol == 2) {
    // TUTOR → materias de sus hijos
    $stmtTutor = $con->prepare("SELECT tutor_id FROM tutores WHERE usuario_id = ?");
    $stmtTutor->bind_param("i", $usuario_id);
    $stmtTutor->execute();
    $resTutor = $stmtTutor->get_result();

    if ($resTutor->num_rows > 0) {
        $tutor_id = $resTutor->fetch_assoc()['tutor_id'];

        $stmt = $con->prepare("
            SELECT DISTINCT m.materia_id, m.nombre, m.nivel_grado, m.descripcion, m.foto_url
            FROM estudiantes e
            INNER JOIN inscripciones i ON e.estudiante_id = i.estudiante_id
            INNER JOIN clases c ON i.clase_id = c.clase_id
            INNER JOIN clase_asignacion ca ON c.clase_id = ca.clase_id
            INNER JOIN materias m ON ca.materia_id = m.materia_id
            WHERE e.tutor_id = ?
        ");
        $stmt->bind_param("i", $tutor_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $row['foto_url'] = $row['foto_url'] ?: 'assets/img/default.jpg';
            $materias[] = $row;
        }
    }
}

header('Content-Type: application/json');
echo json_encode([
    "rol" => $rol,
    "materias" => $materias
]);

$con->close();
