<?php
// pages/php/obtener_pagos_hijo.php
declare(strict_types=1);
session_start();

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/conecta.php';

try {
  $con = conecta();
  if (!$con) { throw new Exception('No se pudo conectar a la base de datos'); }
  $con->set_charset('utf8mb4');

  // ---- DEBUG OPCIONAL: ?debug=1 muestra info y desactiva filtro por tutor
  $DEBUG = isset($_GET['debug']) && $_GET['debug'] == '1';

  // 1) Verifica sesión
  $tutor_id = 0;
  $filtroPorTutor = true;
  if (!isset($_SESSION['tutor_id']) || !is_numeric($_SESSION['tutor_id'])) {
    if ($DEBUG) {
      $filtroPorTutor = false; // ignora filtro para ver si hay datos
    } else {
      http_response_code(401);
      echo json_encode(['error' => 'No autorizado (sin tutor_id en sesión)']);
      exit;
    }
  } else {
    $tutor_id = (int)$_SESSION['tutor_id'];
  }

  // 2) Consulta principal (alineada a tu DataTable)
  //    Prioriza el vínculo en tutor_estudiante; acepta también estudiantes.tutor_id (fallback).
  $sqlBase = "
    SELECT
      p.pago_id,
      CONCAT(e.nombre,' ',e.apellido) AS nombre_estudiante,
      e.grado,
      e.grupo,
      p.monto,
      COALESCE(DATE(p.fecha_pago),'')        AS fecha_pago,
      COALESCE(DATE(p.fecha_vencimiento),'') AS fecha_vencimiento,
      p.estado,
      COALESCE(DATE(p.creado_en),'')         AS creado_en,
      COALESCE(DATE(p.actualizado_en),'')    AS actualizado_en
    FROM pagos p
    INNER JOIN estudiantes e
      ON e.estudiante_id = p.estudiante_id
    LEFT JOIN tutor_estudiante te
      ON te.estudiante_id = e.estudiante_id
  ";

  // Filtro: por relación en tutor_estudiante O por la columna legacy e.tutor_id
  $where = "";
  $order = "
    ORDER BY
      CASE WHEN p.estado='pendiente' THEN 0 ELSE 1 END,
      COALESCE(p.fecha_vencimiento, p.fecha_pago) ASC,
      p.pago_id DESC
  ";

  if ($filtroPorTutor) {
    $where = " WHERE (te.tutor_id = ? OR e.tutor_id = ?) ";
  }

  // Importante: evitar duplicados si hay match por ambas vías.
  $group = " GROUP BY p.pago_id ";

  if ($filtroPorTutor) {
    $stmt = $con->prepare($sqlBase . $where . $group . $order);
    $stmt->bind_param("ii", $tutor_id, $tutor_id);
  } else {
    $stmt = $con->prepare($sqlBase . $group . $order);
  }

  $stmt->execute();
  $res  = $stmt->get_result();

  $rows = [];
  while ($r = $res->fetch_assoc()) {
    $rows[] = $r;
  }

  // 3) Modo debug con metadatos
  if ($DEBUG) {
    $totPagos = (int)$con->query("SELECT COUNT(*) c FROM pagos")->fetch_assoc()['c'];
    $totEst   = (int)$con->query("SELECT COUNT(*) c FROM estudiantes")->fetch_assoc()['c'];
    $totTE    = (int)$con->query("SELECT COUNT(*) c FROM tutor_estudiante")->fetch_assoc()['c'];
    echo json_encode([
      'debug' => [
        'session_tutor_id' => $_SESSION['tutor_id'] ?? null,
        'counts'  => ['pagos'=>$totPagos, 'estudiantes'=>$totEst, 'tutor_estudiante'=>$totTE],
        'filter'  => $filtroPorTutor ? "(te.tutor_id = {$tutor_id} OR e.tutor_id = {$tutor_id})" : '(sin filtro por tutor)',
        'rows_found' => count($rows),
      ],
      'data' => $rows
    ], JSON_UNESCAPED_UNICODE);
  } else {
    echo json_encode($rows, JSON_UNESCAPED_UNICODE);
  }

} catch (Throwable $t) {
  http_response_code(500);
  echo json_encode(['error'=>$t->getMessage()]);
} finally {
  if (isset($stmt) && $stmt instanceof mysqli_stmt) $stmt->close();
  if (isset($con) && $con instanceof mysqli) $con->close();
}
