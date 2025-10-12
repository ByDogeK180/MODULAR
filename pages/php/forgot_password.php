<?php
require '../php/conecta.php'; 
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../config/config/config.php';

header('Content-Type: application/json; charset=utf-8');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$conexion = conecta();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Método no permitido"]);
    exit;
}

$correo = trim($_POST["email"] ?? '');

if ($correo === '') {
    echo json_encode(["status" => "error", "message" => "Correo requerido"]);
    exit;
}

// 1) Verificar si existe el usuario
$stmt = $conexion->prepare("SELECT usuario_id FROM usuarios WHERE correo=?");
$stmt->bind_param("s", $correo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // 2) Generar token y expiración
    $token  = bin2hex(random_bytes(50));
    $expira = date("Y-m-d H:i:s", strtotime("+1 hour"));

    // 3) Guardar token en BD
    $update = $conexion->prepare("UPDATE usuarios SET reset_token=?, reset_expira=? WHERE correo=?");
    $update->bind_param("sss", $token, $expira, $correo);
    $update->execute();

    // 4) Crear link de recuperación
    $resetLink = BASE_URL . "/pages/php/reset_password.php?token=" . $token;

    // 5) Enviar correo
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USER;
        $mail->Password   = MAIL_PASS;
        $mail->SMTPSecure = MAIL_SECURE;
        $mail->Port       = MAIL_PORT;

        // ✅ Corrección para acentos y ñ
        $mail->CharSet    = 'UTF-8';
        $mail->Encoding   = 'base64';

        $mail->setFrom(MAIL_USER, 'SchoolCare');
        $mail->addAddress($correo);

        $mail->isHTML(true);
        $mail->Subject = 'Recuperación de contraseña';
        $mail->Body    = "
            <h2>Solicitud de restablecimiento de contraseña</h2>
            <p>Haz clic en el siguiente enlace para restablecer tu contraseña:</p>
            <a href='$resetLink'>$resetLink</a>
            <p><small>Este enlace expirará en 1 hora.</small></p>
        ";

        $mail->send();
        echo json_encode(["status" => "success", "message" => "✅ Se envió un correo con instrucciones a $correo"]);
    } catch (Exception $e) {
        error_log("Error PHPMailer: " . $mail->ErrorInfo);
        echo json_encode(["status" => "error", "message" => "❌ No se pudo enviar el correo. Inténtalo más tarde."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Correo no registrado"]);
}
