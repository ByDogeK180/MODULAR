<?php
// pages/perfil-hijo/perfil_hijo_bootstrap.php
declare(strict_types=1);

// Mostrar errores (puedes desactivar en producción)
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Rutas seguras relativas a este archivo
$BASE_DIR = dirname(__DIR__, 1);             // pages/
$ROOT_DIR = dirname($BASE_DIR, 1);           // (raíz del proyecto)

require_once $ROOT_DIR . '/pages/php/auth.php';   // Maneja la sesión
require_once $ROOT_DIR . '/pages/php/conecta.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

/** Helper seguro para escapar HTML */
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$con = conecta();
$con->set_charset('utf8mb4');

$tutor_id = (int)($_SESSION['tutor_id'] ?? 0);
if ($tutor_id === 0) {
  // Nota: devolvemos un HTML corto y terminamos
  die('<div class="alert alert-danger m-3">No autenticado.</div>');
}

/** Hijos del tutor (para las pestañas) */
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
while ($row = $resH->fetch_assoc()) { $hijos[] = $row; }
$stmt->close();

/** estudiante seleccionado (o primero de la lista) */
$estudiante_id = isset($_GET['estudiante_id']) ? (int)$_GET['estudiante_id'] : 0;
if ($estudiante_id === 0 && !empty($hijos)) {
  $estudiante_id = (int)$hijos[0]['estudiante_id'];
}

/** datos del estudiante (validando que pertenezca al tutor) */
$estudiante = null;
if ($estudiante_id > 0) {
  $sql = "SELECT e.estudiante_id, e.nombre, e.apellido, e.fecha_nacimiento,
                 e.grado, e.grupo, e.activo, e.creado_en,
                 t.nombre AS tutor_nombre, t.apellido AS tutor_apellido
          FROM estudiantes e
          JOIN tutor_estudiante te ON te.estudiante_id = e.estudiante_id
          JOIN tutores t          ON t.tutor_id        = te.tutor_id
          WHERE e.estudiante_id = ? AND te.tutor_id = ?
          LIMIT 1";
  $stmt = $con->prepare($sql);
  $stmt->bind_param('ii', $estudiante_id, $tutor_id);
  $stmt->execute();
  $estudiante = $stmt->get_result()->fetch_assoc();
  $stmt->close();
}
if (!$estudiante) {
  die('<div class="alert alert-danger m-3">Alumno no encontrado o no pertenece a tu cuenta.</div>');
}

/** promedio general (tabla calificaciones) */
$promedio_general = null;
$stmt = $con->prepare("SELECT ROUND(AVG(promedio),2) AS prom
                       FROM calificaciones
                       WHERE estudiante_id = ?");
$stmt->bind_param('i', $estudiante_id);
$stmt->execute();
if ($p = $stmt->get_result()->fetch_assoc()) {
  $promedio_general = ($p['prom'] !== null) ? (float)$p['prom'] : null;
}
$stmt->close();

/** MATERIAS del alumno (inscripciones → clase_asignacion → materias + docente) */
$materias = [];
$sql = "SELECT 
          m.materia_id,
          m.nombre,
          COALESCE(m.foto_url, '') AS foto_url,
          d.nombre  AS docente_nombre,
          d.apellido AS docente_apellido
        FROM inscripciones i
        JOIN clase_asignacion ca ON ca.clase_id   = i.clase_id
        JOIN materias         m  ON m.materia_id  = ca.materia_id
        LEFT JOIN docentes    d  ON d.docente_id  = ca.docente_id
        WHERE i.estudiante_id = ?
        ORDER BY m.nombre";
$stmt = $con->prepare($sql);
$stmt->bind_param('i', $estudiante_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) { $materias[] = $row; }
$stmt->close();

/** flag para mostrar pestañas (si hay 2+ hijos) */
$mostrar_selector_hijos = count($hijos) > 1;
