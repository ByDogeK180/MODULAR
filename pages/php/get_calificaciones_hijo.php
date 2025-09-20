<?php
// ../php/get_calificaciones_hijo.php
declare(strict_types=1);
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once 'conecta.php';
$con = conecta();

function respond($arr, int $code = 200) {
    http_response_code($code);
    echo json_encode($arr, JSON_UNESCAPED_UNICODE);
    exit;
}

// --------- Resolver tutor en sesión ---------
$tutor_id = $_SESSION['tutor_id'] ?? null;
$usuario_id = $_SESSION['usuario_id'] ?? null;
$correo_sesion = $_SESSION['correo'] ?? null;

if (!$tutor_id) {
    if ($usuario_id) {
        $st = $con->prepare("SELECT tutor_id FROM tutores WHERE usuario_id = ? AND activo = 1 LIMIT 1");
        $st->bind_param("i", $usuario_id);
        $st->execute();
        if ($row = $st->get_result()->fetch_assoc()) $tutor_id = (int)$row['tutor_id'];
    }
    if (!$tutor_id && $correo_sesion) {
        $st = $con->prepare("SELECT tutor_id FROM tutores WHERE correo = ? AND activo = 1 LIMIT 1");
        $st->bind_param("s", $correo_sesion);
        $st->execute();
        if ($row = $st->get_result()->fetch_assoc()) $tutor_id = (int)$row['tutor_id'];
    }
}
if (!$tutor_id) respond(['error' => 'No tienes una sesión de tutor válida.'], 401);

// --------- Parámetros ---------
$estudiante_id     = isset($_GET['estudiante_id']) ? (int)$_GET['estudiante_id'] : 0;
$periodo_id        = isset($_GET['periodo_id'])    ? (int)$_GET['periodo_id']    : 0;
$min_aprobatoria   = isset($_GET['min_aprobatoria']) ? (float)$_GET['min_aprobatoria'] : 6.0;

if ($estudiante_id <= 0) respond(['error' => 'Falta parámetro estudiante_id.'], 400);

// --------- Autorización: el alumno pertenece al tutor ---------
$sqlCheck = "
    SELECT 1
    FROM estudiantes e
    WHERE e.estudiante_id = ?
      AND (
            e.tutor_id = ? 
            OR EXISTS (
                SELECT 1 
                FROM tutor_estudiante te
                WHERE te.tutor_id = ? AND te.estudiante_id = e.estudiante_id
            )
          )
    LIMIT 1
";
$st = $con->prepare($sqlCheck);
$st->bind_param("iii", $estudiante_id, $tutor_id, $tutor_id);
$st->execute();
if (!$st->get_result()->fetch_row()) {
    respond(['error' => 'No tienes permiso para ver las calificaciones de este estudiante.'], 403);
}

// --------- Datos de calificaciones ---------
$sql = "
    SELECT 
        c.calificacion_id,
        c.periodo_id,
        p.nombre AS periodo_nombre,        -- NUEVO
        d.numero AS materia_id,
        d.valor   AS valor_materia,
        m.nombre  AS materia,
        m.foto_url
    FROM calificaciones c
    JOIN calificaciones_detalle d 
        ON d.calificacion_id = c.calificacion_id
    LEFT JOIN materias m 
        ON m.materia_id = d.numero
    LEFT JOIN periodos p                 -- NUEVO
        ON p.periodo_id = c.periodo_id   -- NUEVO
    WHERE c.estudiante_id = ?
      AND (? = 0 OR c.periodo_id = ?)
    ORDER BY m.nombre IS NULL, m.nombre, d.numero
";
$st = $con->prepare($sql);
$st->bind_param("iii", $estudiante_id, $periodo_id, $periodo_id);
$st->execute();
$res = $st->get_result();

$materias = [];
while ($row = $res->fetch_assoc()) {
    $matId = (int)$row['materia_id'];
    if (!isset($materias[$matId])) {
        $materias[$matId] = [
            'materia_id'      => $matId,
            'materia'         => $row['materia'] ?? ('Materia #' . $matId),
            'foto_url'        => $row['foto_url'] ?? null,
            'promedio'        => null,           // promedio de esta materia
            'calificacion'    => null,           // alias claro para tu UI
            'calificacion_id' => (int)$row['calificacion_id'],
            'detalles'        => []              // “Evaluación X”
        ];
    }
    $materias[$matId]['detalles'][] = [
        'numero'       => $matId,
        'valor'        => (float)$row['valor_materia'],
        'periodo_id'   => (int)$row['periodo_id'],           // NUEVO
        'periodo'      => $row['periodo_nombre'] ?? null     // NUEVO
    ];
}

// Promedio por materia y calificación (si hay varias evidencias, promedia)
foreach ($materias as $k => $m) {
    $suma = 0.0; $n = 0;
    foreach ($m['detalles'] as $d) { $suma += (float)$d['valor']; $n++; }
    $prom = $n ? round($suma / $n, 2) : null;
    $materias[$k]['promedio']     = $prom;
    $materias[$k]['calificacion'] = $prom; // para “Calificación por MATERIA”
}

// Promedio general de TODAS las materias (solo cuenta materias con número válido)
$sumGen = 0.0; $nGen = 0;
foreach ($materias as $m) {
    if ($m['promedio'] !== null) { $sumGen += (float)$m['promedio']; $nGen++; }
}
$promedio_general = $nGen ? round($sumGen / $nGen, 2) : null;
$aprobado = ($promedio_general !== null) ? ($promedio_general >= $min_aprobatoria) : null;

// --------- Respuesta ---------
respond([
    'estudiante_id'    => $estudiante_id,
    'periodo_id'       => $periodo_id ?: null,
    'promedio_general' => $promedio_general,
    'min_aprobatoria'  => $min_aprobatoria,
    'aprobado'         => $aprobado,
    'materias'         => array_values($materias)
]);
