<?php
require_once 'conecta.php';
$con = conecta();

$estudiante_id = intval($_GET['estudiante_id'] ?? 0);

$sql = "
SELECT DISTINCT p.periodo_id, p.nombre
FROM calificaciones c
JOIN periodos p ON c.periodo_id = p.periodo_id
WHERE c.estudiante_id = ?
ORDER BY p.periodo_id
";

$stmt = $con->prepare($sql);
$stmt->bind_param("i", $estudiante_id);
$stmt->execute();
$res = $stmt->get_result();

$periodos = [];
while ($row = $res->fetch_assoc()) {
  $periodos[] = $row;
}

echo json_encode($periodos);
