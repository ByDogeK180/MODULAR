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

  // 2) Contadores rápidos para diagnosticar
  if ($DEBUG) {
    $totPagos = (int)$con->query("SELECT COUNT(*) c FROM pagos")->fetch_assoc()['c'];
    $totEst = (int)$con->query("SELECT COUNT(*) c FROM estudiantes")->fetch_assoc()['c'];
    $totJoin = (int)$con->query("SELECT COUNT(*) c FROM pagos p INNER JOIN estudiantes e ON e.estudiante_id=p.estudiante_id")->fetch_assoc()['c'];
    $ses = [
      'tutor_id' => $_SESSION['tutor_id'] ?? null,
      'session_id' => session_id(),
      'cookie_params' => session_get_cookie_params(),
    ];
  }

  // 3) Consulta principal (alineada a tu DataTable)
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
    INNER JOIN estudiantes e ON e.estudiante_id = p.estudiante_id
  ";

  $where = $filtroPorTutor ? " WHERE e.tutor_id = ? " : "";
  $order = "
    ORDER BY
      CASE WHEN p.estado='pendiente' THEN 0 ELSE 1 END,
      p.fecha_vencimiento DESC, p.creado_en DESC
  ";

  $sql = $sqlBase . $where . $order;
  $stmt = $con->prepare($sql);
  if (!$stmt) { throw new Exception('Prepare falló: ' . $con->error); }
  if ($filtroPorTutor) { $stmt->bind_param('i', $tutor_id); }
  if (!$stmt->execute()) { throw new Exception('Execute falló: ' . $stmt->error); }
  $res = $stmt->get_result();

  $rows = [];
  while ($r = $res->fetch_assoc()) { $r['monto'] = (float)$r['monto']; $rows[] = $r; }

  if ($DEBUG) {
    echo json_encode([
      'debug' => [
        'session' => $ses ?? null,
        'counts'  => ['pagos'=>$totPagos, 'estudiantes'=>$totEst, 'join'=>$totJoin],
        'filter'  => $filtroPorTutor ? "WHERE e.tutor_id = $tutor_id" : '(sin filtro por tutor)',
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
