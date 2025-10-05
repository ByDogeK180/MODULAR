<?php
// File: pages/php/audit_helpers.php
require_once __DIR__ . '/conecta.php';

/**
 * Registra un inicio de sesión.
 * @param string $rol       'admin' | 'docente' | 'tutor'
 * @param int    $usuario_id  id en su tabla correspondiente
 * @return bool
 */
function audit_login(string $rol, int $usuario_id): bool {
  $con = conecta();
  if (!$con) return false;

  $rol = strtolower(trim($rol));
  if (!in_array($rol, ['admin','docente','tutor'], true)) return false;
  if ($usuario_id <= 0) return false;

  $ip = $_SERVER['REMOTE_ADDR'] ?? null;
  $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 255);

  $stmt = $con->prepare(
    "INSERT INTO user_login_audit (rol, usuario_id, ip, user_agent)
     VALUES (?,?,?,?)"
  );
  $stmt->bind_param('siss', $rol, $usuario_id, $ip, $ua);
  $ok = $stmt->execute();
  $stmt->close();
  $con->close();
  return $ok;
}

/**
 * Versión práctica: intenta inferir el rol e id desde la sesión
 * y registra el login. Úsala si ya seteaste $_SESSION en login.php.
 */
function audit_login_from_session(): bool {
  if (session_status() !== PHP_SESSION_ACTIVE) @session_start();

  if (!empty($_SESSION['admin_id'])) {
    return audit_login('admin', (int)$_SESSION['admin_id']);
  }
  if (!empty($_SESSION['docente_id'])) {
    return audit_login('docente', (int)$_SESSION['docente_id']);
  }
  if (!empty($_SESSION['tutor_id'])) {
    return audit_login('tutor', (int)$_SESSION['tutor_id']);
  }
  return false;
}
