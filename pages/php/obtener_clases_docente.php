<?php
require_once 'conecta.php';
session_start();

$usuario_id = $_SESSION['usuario_id'] ?? null;
if (!$usuario_id) {
  echo json_encode([]);
  exit;
}

$con = conecta();

$query = "
  SELECT DISTINCT c.clase_id, c.grado, c.grupo
  FROM clase_asignacion ca
  JOIN clases c ON c.clase_id = ca.clase_id
  JOIN docentes d ON d.docente_id = ca.docente_id
  JOIN inscripciones i ON i.clase_id = c.clase_id
  WHERE d.usuario_id = ?
";
$stmt = $con->prepare($query);
$stmt->bind_param('i', $usuario_id);
$stmt->execute();
$res = $stmt->get_result();

$clases = [];
while ($row = $res->fetch_assoc()) {
  $clases[] = $row;
}

echo json_encode($clases);
