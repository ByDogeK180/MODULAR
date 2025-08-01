<?php
require_once 'conecta.php';
$con = conecta();

$clase_id = $_GET['clase_id'] ?? 0;
$periodo_id = $_GET['periodo_id'] ?? 0;

$sql = "
SELECT 
  c.estudiante_id,
  d.numero AS materia_id,
  d.valor
FROM calificaciones c
JOIN calificaciones_detalle d ON d.calificacion_id = c.calificacion_id
WHERE c.clase_id = ? AND c.periodo_id = ?
";

$stmt = $con->prepare($sql);
$stmt->bind_param("ii", $clase_id, $periodo_id);
$stmt->execute();
$res = $stmt->get_result();

$data = [];
while ($row = $res->fetch_assoc()) {
  $data[] = [
    'estudiante_id' => $row['estudiante_id'],
    'materia_id' => $row['materia_id'],
    'valor' => $row['valor']
  ];
}

echo json_encode($data);
