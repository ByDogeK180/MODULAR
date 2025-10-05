<?php
header('Content-Type: application/json; charset=utf-8');
require '../php/conecta.php';

$con = conecta();
if (!$con) {
  echo json_encode(['error' => 'Error de conexión'], JSON_UNESCAPED_UNICODE);
  exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
  echo json_encode(['error' => 'ID inválido'], JSON_UNESCAPED_UNICODE);
  exit;
}

// Materia
$stmt = $con->prepare("SELECT materia_id, nombre, nivel_grado, descripcion, foto_url
                       FROM materias WHERE materia_id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$mat = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$mat) {
  echo json_encode(['error' => 'Materia no encontrada'], JSON_UNESCAPED_UNICODE);
  exit;
}

// Docente (si hay clase_asignacion con esta materia)
$docente = null;
$stmt = $con->prepare("
  SELECT d.nombre, d.apellido, d.correo, d.foto_url
  FROM clase_asignacion ca
  INNER JOIN docentes d ON d.docente_id = ca.docente_id
  WHERE ca.materia_id = ?
  LIMIT 1
");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
  $docente = [
    'nombre'   => trim($row['nombre'].' '.$row['apellido']),
    'correo'   => $row['correo'],
    'foto_url' => $row['foto_url'] ?: 'assets/img/uploads/profesor-default.png',
  ];
}
$stmt->close();

// (Opcional) Demo de temario/recursos/opiniones.
// Sustituye por datos reales si los guardas en BD.
$temario = [
  ['titulo' => 'Unidad 1: Introducción', 'subtemas' => ['Subtema A', 'Subtema B']],
  ['titulo' => 'Unidad 2: Desarrollo',    'subtemas' => ['Subtema C', 'Subtema D']],
  ['titulo' => 'Unidad 3: Avanzado',      'subtemas' => ['Subtema E', 'Subtema F']],
];

$recursos = [
  ['titulo' => 'Guía PDF',          'url' => '#'],
  ['titulo' => 'Video explicativo', 'url' => '#'],
];

$opiniones = [
  ['autor' => 'Ana G.',    'estrellas' => '★★★★★', 'texto' => '¡Excelente materia!'],
  ['autor' => 'Carlos R.', 'estrellas' => '★★★★☆', 'texto' => 'Muy buena, faltaron más ejercicios.'],
];

// Ensamblar respuesta
$mat['foto_url']    = $mat['foto_url'] ?: 'assets/img/default.jpg';
$mat['descripcion'] = trim((string)($mat['descripcion'] ?? ''));

echo json_encode([
  'materia_id'  => $mat['materia_id'],
  'nombre'      => $mat['nombre'],
  'nivel_grado' => $mat['nivel_grado'],
  'descripcion' => $mat['descripcion'],
  'foto_url'    => $mat['foto_url'],
  'docente'     => $docente,
  'temario'     => $temario,
  'recursos'    => $recursos,
  'opiniones'   => $opiniones,
], JSON_UNESCAPED_UNICODE);
