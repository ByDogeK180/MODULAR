<?php
session_start();

// 🚨 Validar sesión
if (!isset($_SESSION['correo'])) {
    header("Location: /dashboard/Modular/pages/prebuilt-pages/default-login.html");
    exit();
}

// 🚫 Evitar que el navegador guarde caché
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
?>
