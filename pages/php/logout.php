<?php
session_start();
session_unset();
session_destroy();
header("Location: /dashboard/Modular/pages/prebuilt-pages/default-login.html");
exit();
?>
