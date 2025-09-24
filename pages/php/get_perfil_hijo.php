<?php
// pages/php/get_perfil_hijo.php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/conecta.php';

function out($code, $payload) {
  http_response_code($code);
  echo json_encode($payload, JSON_UNESCAPED_UNICODE);
  exit;
}

session_start();

$con = conecta();
if (!$con) out(500, ['ok'=>false,'error'=>'No se pudo conectar a la BD.']);
$con->set_charset('utf8mb4');

$tutor_id = (int)($_SESSION['tutor_id'] ?? 0);
if ($tutor_id === 0) out(401, ['ok'=>false,'error'=>'No autenticado o sin tutor_id en sesión.']);

$estudiante_id = isset($_GET['estudiante_id']) ? (int)$_GET['estudiante_id'] : 0;

/* 1) Si no mandan estudiante, tomar el primero del tutor (ordenado por nombre) */
if ($estudiante_id === 0) {
  $sql = "SELECT e.estudiante_id
          FROM tutor_estudiante te
          JOIN estudiantes e ON e.estudiante_id = te.estudiante_id
          WHERE te.tutor_id = ?
          ORDER BY e.nombre
          LIMIT 1";
  $stmt = $con->prepare($sql);
  $stmt->bind_param('i', $tutor_id);
  $stmt->execute();
  $r = $stmt->get_result();
  if ($row = $r->fetch_assoc()) $estudiante_id = (int)$row['estudiante_id'];
  $stmt->close();
}

/* 2) Traer lista de hijos (para el selector) */
$hijos = [];
$sqlH = "SELECT e.estudiante_id,
                CONCAT(e.nombre,' ',e.apellido) AS nombre,
                e.grado, e.grupo
         FROM tutor_estudiante te
         JOIN estudiantes e ON e.estudiante_id = te.estudiante_id
         WHERE te.tutor_id = ?
         ORDER BY e.nombre";
$stmt = $con->prepare($sqlH);
$stmt->bind_param('i', $tutor_id);
$stmt->execute();
$resH = $stmt->get_result();
while ($row = $resH->fetch_assoc()) {
  $hijos[] = [
    'estudiante_id' => (int)$row['estudiante_id'],
    'nombre'        => $row['nombre'],
    'grado'         => $row['grado'],
    'grupo'         => $row['grupo']
  ];
}
$stmt->close();

if ($estudiante_id === 0) out(404, ['ok'=>false,'error'=>'El tutor no tiene estudiantes asignados.', 'hijos'=>$hijos]);

/* 3) Datos del alumno (validando pertenezca al tutor) */
$sqlA = "SELECT 
           e.estudiante_id, e.nombre, e.apellido, e.fecha_nacimiento,
           e.grado, e.grupo, e.activo, e.creado_en,
           CONCAT(t.nombre,' ',t.apellido) AS tutor_nombre
         FROM estudiantes e
         LEFT JOIN tutor_estudiante te ON te.estudiante_id = e.estudiante_id
         LEFT JOIN tutores t ON t.tutor_id = (CASE WHEN te.tutor_id IS NOT NULL THEN te.tutor_id ELSE e.tutor_id END)
         WHERE e.estudiante_id = ?
           AND (te.tutor_id = ? OR e.tutor_id = ?)
         LIMIT 1";
$stmt = $con->prepare($sqlA);
$stmt->bind_param('iii', $estudiante_id, $tutor_id, $tutor_id);
$stmt->execute();
$alumno = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$alumno) out(404, ['ok'=>false,'error'=>'Alumno no encontrado o no pertenece al tutor.', 'hijos'=>$hijos]);

/* 4) Promedio general (si no hay, null) */
$sqlP = "SELECT ROUND(AVG(c.promedio), 2) AS prom
         FROM calificaciones c
         WHERE c.estudiante_id = ?";
$stmt = $con->prepare($sqlP);
$stmt->bind_param('i', $estudiante_id);
$stmt->execute();
$promedio = $stmt->get_result()->fetch_assoc();
$stmt->close();
$promedio_general = ($promedio && $promedio['prom'] !== null) ? (float)$promedio['prom'] : null;

/* 5) Materias actuales
   Preferencia: inscripciones (clase vigente) → clase_asignacion → materias (+ docente)
   Si no hay resultados, fallback a asignacion_materias. */
$materias = [];

/* 5.a) Por clase vigente (máximo ciclo_id del alumno) */
$sqlM1 = "SELECT m.materia_id,
                 m.nombre AS nombre,
                 m.foto_url,
                 d.nombre AS docente_nombre,
                 d.apellido AS docente_apellido
          FROM inscripciones i
          JOIN clase_asignacion ca ON ca.clase_id = i.clase_id
          JOIN materias m ON m.materia_id = ca.materia_id
          LEFT JOIN docentes d ON d.docente_id = ca.docente_id
          WHERE i.estudiante_id = ?
            AND i.ciclo_id = (
              SELECT MAX(i2.ciclo_id) FROM inscripciones i2 WHERE i2.estudiante_id = ?
            )
          ORDER BY m.nombre";
$stmt = $con->prepare($sqlM1);
$stmt->bind_param('ii', $estudiante_id, $estudiante_id);
$stmt->execute();
$resM1 = $stmt->get_result();
while ($row = $resM1->fetch_assoc()) {
  $materias[] = [
    'materia_id'       => (int)$row['materia_id'],
    'materia'          => $row['nombre'],            // compatibilidad
    'nombre'           => $row['nombre'],
    'foto_url'         => $row['foto_url'],
    'docente_nombre'   => $row['docente_nombre'],
    'docente_apellido' => $row['docente_apellido']
  ];
}
$stmt->close();

/* 5.b) Fallback: asignacion_materias directa (si no se encontró nada) */
if (count($materias) === 0) {
  $sqlM2 = "SELECT m.materia_id, m.nombre AS nombre, m.foto_url
            FROM asignacion_materias am
            JOIN materias m ON m.materia_id = am.materia_id
            WHERE am.estudiante_id = ?
            ORDER BY m.nombre";
  $stmt = $con->prepare($sqlM2);
  $stmt->bind_param('i', $estudiante_id);
  $stmt->execute();
  $resM2 = $stmt->get_result();
  while ($row = $resM2->fetch_assoc()) {
    $materias[] = [
      'materia_id'       => (int)$row['materia_id'],
      'materia'          => $row['nombre'],
      'nombre'           => $row['nombre'],
      'foto_url'         => $row['foto_url'],
      'docente_nombre'   => null,
      'docente_apellido' => null
    ];
  }
  $stmt->close();
}

/* 6) Respuesta */
out(200, [
  'ok' => true,
  'estudiante_id'    => (int)$alumno['estudiante_id'],
  'alumno' => [
    'nombre'           => $alumno['nombre'],
    'apellido'         => $alumno['apellido'],
    'fecha_nacimiento' => $alumno['fecha_nacimiento'],
    'grado'            => $alumno['grado'],
    'grupo'            => $alumno['grupo'],
    'activo'           => (int)$alumno['activo'] === 1,
    'creado_en'        => $alumno['creado_en'],
    'tutor_nombre'     => $alumno['tutor_nombre']
  ],
  'promedio_general' => $promedio_general,
  'materias'         => $materias,
  'hijos'            => $hijos
]);
