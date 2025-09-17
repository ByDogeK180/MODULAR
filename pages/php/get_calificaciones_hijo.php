<?php
// api/get_hijos.php
session_start();
require 'conecta.php';
header('Content-Type: application/json');

// Conexión
$con = conecta();
if (!$con) {
    echo json_encode([]);
    exit;
}
// ------------------------------------------------------------------------------

$estudiante_id = isset($_GET['estudiante_id']) ? intval($_GET['estudiante_id']) : 0;
$periodo_id = isset($_GET['periodo_id']) ? intval($_GET['periodo_id']) : null;

if (!$estudiante_id) {
    echo json_encode(['error' => 'Falta estudiante_id']);
    exit;
}

// ------------- Estrategia A: calificaciones.clase_id -> clase_asignacion.id --------------
$sqlA = "
    SELECT m.materia_id, m.nombre AS materia, m.foto_url,
           c.promedio, c.calificacion_id, p.nombre AS periodo
    FROM calificaciones c
    JOIN clase_asignacion ca ON c.clase_id = ca.id
    JOIN materias m ON ca.materia_id = m.materia_id
    LEFT JOIN periodos p ON p.periodo_id = c.periodo_id
    WHERE c.estudiante_id = ?
";
$params = [$estudiante_id];
$types = "i";
if ($periodo_id) {
    $sqlA .= " AND c.periodo_id = ?";
    $types .= "i";
    $params[] = $periodo_id;
}

$stmt = $con->prepare($sqlA);
if ($periodo_id) {
    $stmt->bind_param($types, $params[0], $params[1]);
} else {
    $stmt->bind_param($types, $params[0]);
}
$stmt->execute();
$res = $stmt->get_result();

$materias = [];
while ($r = $res->fetch_assoc()) {
    $materias[] = $r;
}
$stmt->close();

if (count($materias) === 0) {
    // ---------- Estrategia B: buscar clase del estudiante y materias asignadas a esa clase ----------
    $stmt = $con->prepare("
        SELECT i.clase_id
        FROM inscripciones i
        JOIN ciclos_escolares ce ON i.ciclo_id = ce.ciclo_id
        WHERE i.estudiante_id = ? AND ce.estado = 'activo'
        ORDER BY i.inscripcion_id DESC
        LIMIT 1
    ");
    $stmt->bind_param('i', $estudiante_id);
    $stmt->execute();
    $stmt->bind_result($clase_id);
    $clase_id = null;
    if ($stmt->fetch()) $clase_id = intval($clase_id);
    $stmt->close();

    if (!$clase_id) {
        // No hay inscripcion activa -> intentar usar la última inscripcion
        $stmt = $con->prepare("SELECT clase_id FROM inscripciones WHERE estudiante_id = ? ORDER BY inscripcion_id DESC LIMIT 1");
        $stmt->bind_param('i', $estudiante_id);
        $stmt->execute();
        $stmt->bind_result($clase_idTmp);
        if ($stmt->fetch()) $clase_id = intval($clase_idTmp);
        $stmt->close();
    }

    if ($clase_id) {
        $sqlB = "
            SELECT ca.id as clase_asignacion_id, m.materia_id, m.nombre AS materia, m.foto_url,
                   c.promedio, c.calificacion_id, p.nombre AS periodo
            FROM clase_asignacion ca
            JOIN materias m ON ca.materia_id = m.materia_id
            LEFT JOIN calificaciones c ON (c.estudiante_id = ? AND c.clase_id = ca.clase_id" . ($periodo_id ? " AND c.periodo_id = ?" : "") . ")
            LEFT JOIN periodos p ON p.periodo_id = c.periodo_id
            WHERE ca.clase_id = ?
            ORDER BY m.nombre
        ";
        if ($periodo_id) {
            $stmt = $con->prepare($sqlB);
            $stmt->bind_param('iii', $estudiante_id, $periodo_id, $clase_id);
        } else {
            $stmt = $con->prepare($sqlB);
            $stmt->bind_param('ii', $estudiante_id, $clase_id);
        }
        $stmt->execute();
        $res = $stmt->get_result();
        while ($r = $res->fetch_assoc()) {
            $materias[] = $r;
        }
        $stmt->close();
    }
}

// Si hay calificacion_id's recabar detalles en una sola consulta:
$califIds = [];
foreach ($materias as $m) {
    if (!empty($m['calificacion_id'])) $califIds[] = intval($m['calificacion_id']);
}

$detallesMap = [];
if (count($califIds) > 0) {
    $in = implode(',', array_fill(0, count($califIds), '?'));
    // prepare types string
    $types = str_repeat('i', count($califIds));
    $sqlD = "SELECT calificacion_id, numero, valor FROM calificaciones_detalle WHERE calificacion_id IN ($in) ORDER BY numero";
    $stmt = $con->prepare($sqlD);
    // bind params dynamically
    $stmt->bind_param($types, ...$califIds);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $cid = intval($r['calificacion_id']);
        if (!isset($detallesMap[$cid])) $detallesMap[$cid] = [];
        $detallesMap[$cid][] = ['numero' => intval($r['numero']), 'valor' => floatval($r['valor'])];
    }
    $stmt->close();
}

// adjuntar detalles a las materias
foreach ($materias as &$m) {
    $cid = !empty($m['calificacion_id']) ? intval($m['calificacion_id']) : null;
    $m['detalles'] = $cid && isset($detallesMap[$cid]) ? $detallesMap[$cid] : [];
    // normalizar promedio
    $m['promedio'] = isset($m['promedio']) && $m['promedio'] !== null ? floatval($m['promedio']) : null;
}

echo json_encode(['materias' => $materias]);