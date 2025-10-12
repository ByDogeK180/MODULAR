<?php
// Entorno: local | produccion
define('APP_ENV', 'local'); // ← para XAMPP. Cámbialo a 'produccion' al subir.

// SMTP (Hostinger)
define('MAIL_HOST', 'smtp.hostinger.com');
define('MAIL_USER', 'notificaciones@schoolcare.site');
define('MAIL_PASS', 'Proyecmodular8@');  // considera mover esto a variables de entorno
define('MAIL_PORT', 465);
define('MAIL_SECURE', 'ssl'); // si usas 587, cambia a 'tls'

// URL base (usa la carpeta real: MODULAR)
if (APP_ENV === 'local') {
    define('BASE_URL', 'http://localhost/dashboard/MODULAR');
} else {
    define('BASE_URL', 'https://schoolcare.site');
}
