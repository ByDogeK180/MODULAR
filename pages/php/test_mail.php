<?php
require __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'j04qin@gmail.com'; 
    $mail->Password = 'clave_de_aplicacion'; 
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('tu_correo@gmail.com', 'SchoolCare');
    $mail->addAddress('destinatario@ejemplo.com');

    $mail->isHTML(true);
    $mail->Subject = 'Prueba de PHPMailer';
    $mail->Body    = '<h1>¡Funciona PHPMailer!</h1>';

    $mail->send();
    echo "✅ Correo enviado con éxito.";
} catch (Exception $e) {
    echo "❌ Error: {$mail->ErrorInfo}";
}
