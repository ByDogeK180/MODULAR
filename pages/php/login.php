<?php
session_start();
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'conecta.php';

/** Separa "Nombre(s) Apellidos" en nombre y apellido */
function split_nombre_apellido($displayName) {
    $displayName = trim(preg_replace('/\s+/', ' ', (string)$displayName));
    if ($displayName === '') return ['', ''];
    $parts = explode(' ', $displayName);
    if (count($parts) === 1) return [$parts[0], ''];
    $nombre   = array_shift($parts);
    $apellido = implode(' ', $parts);
    return [trim($nombre), trim($apellido)];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = trim($_POST["email"] ?? '');
    $password = trim($_POST["password"] ?? '');

    if ($email === '' || $password === '') {
        echo json_encode(["status" => "error", "message" => "⚠️ Por favor, complete todos los campos."]);
        exit;
    }

    $con = conecta();

    // Traemos también nombre_usuario para tener un display name por defecto
    $stmt = $con->prepare("SELECT usuario_id, correo, contraseña, rol, nombre_usuario FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($db_usuario_id, $db_email, $db_password, $db_rol, $db_nombre_usuario);
        $stmt->fetch();

        if (password_verify($password, $db_password)) {
            // Base de sesión
            $_SESSION['correo']     = $db_email;
            $_SESSION['rol']        = (int)$db_rol;
            $_SESSION['usuario_id'] = (int)$db_usuario_id;
            $_SESSION['logueado']   = 1;

            // Nombre/apellido base desde usuarios.nombre_usuario
            [$baseNombre, $baseApellido] = split_nombre_apellido($db_nombre_usuario);
            $_SESSION['nombre']   = $baseNombre;
            $_SESSION['apellido'] = $baseApellido;

            // ---- ADMIN (rol = 0) -> tabla admin ----
            if ((int)$db_rol === 0) {
                // Tu BD tiene la tabla `admin` con nombre/apellido
                $adm = $con->prepare("SELECT admin_id, nombre, apellido, correo FROM admin WHERE usuario_id = ? LIMIT 1");
                if ($adm) {
                    $adm->bind_param("i", $db_usuario_id);
                    $adm->execute();
                    $adm->store_result();
                    if ($adm->num_rows > 0) {
                        $adm->bind_result($admin_id, $anombre, $aapellido, $acorreo);
                        $adm->fetch();
                        $_SESSION['admin_id'] = (int)$admin_id;
                        if (trim((string)$anombre)   !== '') $_SESSION['nombre']   = $anombre;
                        if (trim((string)$aapellido) !== '') $_SESSION['apellido'] = $aapellido;
                        // Si prefieres usar el correo de perfil admin:
                        // $_SESSION['correo'] = $acorreo ?: $_SESSION['correo'];
                    }
                    $adm->close();
                }
            }

            // ---- DOCENTE (rol = 1) -> tabla docentes ----
            if ((int)$db_rol === 1) {
                $stmt_doc = $con->prepare("SELECT docente_id, nombre, apellido, correo FROM docentes WHERE usuario_id = ? LIMIT 1");
                $stmt_doc->bind_param("i", $db_usuario_id);
                $stmt_doc->execute();
                $stmt_doc->store_result();
                if ($stmt_doc->num_rows > 0) {
                    $stmt_doc->bind_result($docente_id, $dnombre, $dapellido, $dcorreo);
                    $stmt_doc->fetch();
                    $_SESSION['docente_id'] = (int)$docente_id;
                    if (trim((string)$dnombre)   !== '') $_SESSION['nombre']   = $dnombre;
                    if (trim((string)$dapellido) !== '') $_SESSION['apellido'] = $dapellido;
                    // $_SESSION['correo'] = $dcorreo ?: $_SESSION['correo'];
                }
                $stmt_doc->close();
            }

            // ---- TUTOR (rol = 2) -> tabla tutores ----
            if ((int)$db_rol === 2) {
                $stmt_tutor = $con->prepare("SELECT tutor_id, nombre, apellido, correo FROM tutores WHERE usuario_id = ? LIMIT 1");
                $stmt_tutor->bind_param("i", $db_usuario_id);
                $stmt_tutor->execute();
                $stmt_tutor->store_result();
                if ($stmt_tutor->num_rows > 0) {
                    $stmt_tutor->bind_result($tutor_id, $tnombre, $tapellido, $tcorreo);
                    $stmt_tutor->fetch();
                    $_SESSION['tutor_id'] = (int)$tutor_id;
                    if (trim((string)$tnombre)   !== '') $_SESSION['nombre']   = $tnombre;
                    if (trim((string)$tapellido) !== '') $_SESSION['apellido'] = $tapellido;
                    $_SESSION['correo'] = $tcorreo ?: $_SESSION['correo'];
                }
                $stmt_tutor->close();
            }

            // >>>>>>>>>>>>>>> AÑADIDO: registrar auditoría del login <<<<<<<<<<<<<<<
            require_once __DIR__ . '/audit_helpers.php';
            audit_login_from_session();   // registra el inicio de sesión según la sesión actual
            // >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

            // Redirección por rol
            switch ((int)$db_rol) {
                case 0: $redirect = '../../index.php';    break; // Admin
                case 1: $redirect = '../../Docentes.php'; break; // Docente
                case 2: $redirect = '../../Tutor.php';    break; // Tutor
                default:
                    echo json_encode(["status" => "error", "message" => "❌ Rol no válido."]);
                    exit;
            }

            echo json_encode(["status" => "success", "redirect" => $redirect]);
        } else {
            echo json_encode(["status" => "error", "message" => "❌ Contraseña incorrecta."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "❌ Usuario no encontrado."]);
    }

    $stmt->close();
    $con->close();
} else {
    echo json_encode(["status" => "error", "message" => "⛔ Acceso no permitido."]);
}
