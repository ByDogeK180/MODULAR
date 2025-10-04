<?php
require 'conecta.php';
$con = conecta();

header('Content-Type: application/json');

if (isset($_GET['id'])) {
  $id = (int)$_GET['id'];
  $sql = "
    SELECT e.estudiante_id, e.nombre, e.apellido, e.fecha_nacimiento, e.grado, e.grupo,
           COALESCE(te.tutor_id, e.tutor_id) AS tutor_id
    FROM estudiantes e
    LEFT JOIN tutor_estudiante te ON te.estudiante_id = e.estudiante_id
    WHERE e.estudiante_id = ? AND e.activo = 1
  ";
  $st = $con->prepare($sql);
  $st->bind_param("i", $id);
  $st->execute();
  $res = $st->get_result();
  echo json_encode($res->fetch_assoc() ?: ['error'=>'Estudiante no encontrado']);
  $st->close();
} else {
  $sql = "
    SELECT e.estudiante_id, e.nombre, e.apellido, e.fecha_nacimiento, e.grado, e.grupo,
           COALESCE(te.tutor_id, e.tutor_id) AS tutor_id
    FROM estudiantes e
    LEFT JOIN tutor_estudiante te ON te.estudiante_id = e.estudiante_id
    WHERE e.activo = 1
  ";
  $res = $con->query($sql);
  $rows = [];
  while ($r = $res->fetch_assoc()) $rows[] = $r;
  echo json_encode($rows);
}
$con->close();
