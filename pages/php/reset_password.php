<?php
require '../php/conecta.php';
$conexion = conecta();

$mensaje = "";
$redirect = false;

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // 1) Verificar si el token existe y no ha expirado
    $stmt = $conexion->prepare("SELECT usuario_id FROM usuarios WHERE reset_token=? AND reset_expira > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();
        $id = $usuario['usuario_id'];

        // 2) Si se envió el formulario con la nueva contraseña
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $nueva     = $_POST['nueva_contraseña'] ?? '';
            $confirmar = $_POST['confirmar_contraseña'] ?? '';

            if ($nueva === '' || $confirmar === '') {
                $mensaje = "<p class='text-danger'>⚠️ Debes llenar ambos campos.</p>";
            } elseif ($nueva !== $confirmar) {
                $mensaje = "<p class='text-danger'>❌ Las contraseñas no coinciden.</p>";
            } else {
                // 3) Guardar la nueva contraseña
                $hash = password_hash($nueva, PASSWORD_BCRYPT);

                $update = $conexion->prepare("UPDATE usuarios 
                                              SET contraseña=?, reset_token=NULL, reset_expira=NULL 
                                              WHERE usuario_id=?");
                $update->bind_param("si", $hash, $id);
                $update->execute();

                $mensaje = "<p class='text-success fw-bold'>✅ Contraseña actualizada con éxito. Serás redirigido al login...</p>";
                $redirect = true;
            }
        }
    } else {
        $mensaje = "<p class='text-danger fw-bold'>❌ Token inválido o expirado.</p>";
    }
} else {
    $mensaje = "<p class='text-danger fw-bold'>❌ No se proporcionó token.</p>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Restablecer Contraseña</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: url('../../assets/img/escuela-activa.jpg') no-repeat center center fixed;
      background-size: cover;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      position: relative;
    }
    body::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.4);
      backdrop-filter: blur(3px);
    }
    .reset-container {
      background: rgba(255, 255, 255, 0.95);
      padding: 2rem;
      border-radius: 12px;
      box-shadow: 0px 5px 15px rgba(0,0,0,0.2);
      width: 100%;
      max-width: 420px;
      position: relative;
      z-index: 2;
    }
  </style>
</head>
<body>
  <div class="reset-container">
    <h3 class="mb-3 fw-bold text-center">🔑 Restablecer Contraseña</h3>
    <?php if (!empty($mensaje)) echo $mensaje; ?>

    <?php if (!$redirect): ?>
      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Nueva contraseña:</label>
          <input type="password" class="form-control" name="nueva_contraseña" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Confirmar contraseña:</label>
          <input type="password" class="form-control" name="confirmar_contraseña" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Cambiar contraseña</button>
      </form>
    <?php endif; ?>
  </div>

  <?php if ($redirect): ?>
    <script>
      setTimeout(() => {
        window.location.href = "../prebuilt-pages/default-login.html";
      }, 3000); // redirige en 3 segundos
    </script>
  <?php endif; ?>
</body>
</html>
