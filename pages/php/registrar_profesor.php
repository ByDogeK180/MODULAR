<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

require '../php/conecta.php';
$cn = conecta();
if (!$cn) {
  http_response_code(500);
  echo json_encode(['success'=>false,'message'=>'No se pudo conectar a la base de datos.']); exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['success'=>false,'message'=>'Método no permitido']); exit;
}

function fail(string $msg, int $code=400){
  http_response_code($code);
  echo json_encode(['success'=>false,'message'=>$msg], JSON_UNESCAPED_UNICODE);
  exit;
}

// ==== 1) Captura ====
$nombre     = trim($_POST['nombre']              ?? '');
$apellido   = trim($_POST['apellido']            ?? '');
$correo     = strtolower(trim($_POST['email']    ?? ''));  // asegúrate que name="email" en el form
$pass1      = $_POST['contraseña']               ?? '';    // ojo con name con ñ en el form
$pass2      = $_POST['confirmar_contraseña']     ?? '';
$rol        = (int)($_POST['rol']                ?? 0);
$puesto     = trim($_POST['puesto']              ?? '');
$genero     = trim($_POST['genero']              ?? '');
$telefono   = trim($_POST['telefono']            ?? '');
$nacimiento = $_POST['nacimiento']               ?? '';
$salario    = (float)($_POST['salario']          ?? 0);
$direccion  = trim($_POST['direccion']           ?? '');

// ==== 2) Validaciones ====
if ($pass1 !== $pass2)                           fail('Las contraseñas no coinciden');
if (!preg_match('/^(?=.*[A-Z])(?=.*\d).{8,}$/', $pass1))
                                                fail('La contraseña debe tener mínimo 8 caracteres, una mayúscula y un número');
$pass_hash = password_hash($pass1, PASSWORD_BCRYPT);

if (!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,}$/u', $nombre))   fail('Nombre inválido');
if (!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,}$/u', $apellido)) fail('Apellido inválido');

if (!filter_var($correo, FILTER_VALIDATE_EMAIL))               fail('Correo inválido');

if (!in_array($puesto, ['profesor','coordinador'], true))      fail('Puesto no válido');
if (!in_array($genero, ['masculino','femenino','otro'], true)) fail('Género no válido');

if (!preg_match('/^\d{7,15}$/', $telefono))                    fail('Teléfono inválido');

$d = DateTime::createFromFormat('Y-m-d', $nacimiento);
if (!$d || $d->format('Y-m-d') !== $nacimiento)                fail('Fecha de nacimiento inválida');

if (!is_numeric($salario) || $salario <= 0)                    fail('Salario inválido');

if (mb_strlen($direccion) < 5)                                 fail('Dirección demasiado corta');

// ==== 3) Imagen ====
if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK)
  fail('Problema al subir la imagen');

$info = getimagesize($_FILES['imagen']['tmp_name']);
if (!$info || !in_array($info['mime'], ['image/jpeg','image/png'], true))
  fail('Formato de imagen no válido');

if ($_FILES['imagen']['size'] > 2*1024*1024)                   fail('Imagen supera 2 MB');

// Carpeta destino (ajusta si tu árbol difiere)
$destDirFs = realpath(__DIR__ . '/../../assets/img/uploads');
if ($destDirFs === false) {
  $destDirFs = __DIR__ . '/../../assets/img/uploads';
  if (!is_dir($destDirFs) && !mkdir($destDirFs, 0775, true)) {
    fail('No se pudo preparar la carpeta de imágenes');
  }
}
$ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
$ext = in_array($ext, ['jpg','jpeg','png'], true) ? $ext : ($info['mime']==='image/png'?'png':'jpg');
$nombreArchivo = sprintf('%s.%s', bin2hex(random_bytes(8)), $ext);

if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $destDirFs . DIRECTORY_SEPARATOR . $nombreArchivo))
  fail('No se pudo guardar la imagen');

$fotoUrl = 'assets/img/uploads/' . $nombreArchivo; // ruta web

// ==== 4) Lógica con TX ====
$cn->begin_transaction();

try {
  // 4.1 único correo
  $stmt = $cn->prepare("SELECT 1 FROM usuarios WHERE correo = ? LIMIT 1");
  $stmt->bind_param('s', $correo);
  $stmt->execute();
  $stmt->store_result();
  if ($stmt->num_rows) {
    $stmt->close();
    $cn->rollback();
    fail('El correo ya está registrado');
  }
  $stmt->close();

  // 4.2 insertar usuario
  $stmt = $cn->prepare("INSERT INTO usuarios (nombre_usuario, contraseña, correo, rol) VALUES (?, ?, ?, ?)");
  $nombre_usuario = $nombre; // o "$nombre $apellido" si prefieres
  $stmt->bind_param('sssi', $nombre_usuario, $pass_hash, $correo, $rol);
  if (!$stmt->execute()) {
    throw new RuntimeException('No se pudo registrar el usuario');
  }
  $userId = $stmt->insert_id;
  $stmt->close();

  // 4.3 insertar docente (TIPOS CORRECTOS)
  $stmt = $cn->prepare(
    "INSERT INTO docentes
     (usuario_id, nombre, apellido, telefono, correo, rol, puesto, genero,
      fecha_nacimiento, salario, direccion, foto_url)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
  );
  //               i s s s s i s s s d s s
  $stmt->bind_param(
    'issssisssdss',
    $userId, $nombre, $apellido, $telefono, $correo,
    $rol, $puesto, $genero, $nacimiento, $salario,
    $direccion, $fotoUrl
  );
  if (!$stmt->execute()) {
    throw new RuntimeException('No se pudo registrar el docente');
  }
  $stmt->close();

  $cn->commit();
  echo json_encode(['success'=>true,'message'=>'Profesor registrado con éxito'], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
  $cn->rollback();

  // si el usuario se alcanzó a crear y falló docentes, limpia (opcional)
  // $cn->query("DELETE FROM usuarios WHERE id = {$userId}") ...

  http_response_code(500);
  echo json_encode(['success'=>false,'message'=>$e->getMessage()], JSON_UNESCAPED_UNICODE);
} finally {
  $cn->close();
}
