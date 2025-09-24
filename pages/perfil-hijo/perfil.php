<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require_once '../php/auth.php';      // Maneja la sesión
require_once '../php/conecta.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

/* Helper seguro para escapar */
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$con = conecta();
$con->set_charset('utf8mb4');

$tutor_id = (int)($_SESSION['tutor_id'] ?? 0);
if ($tutor_id === 0) { die('<div class="alert alert-danger m-3">No autenticado.</div>'); }

/* Hijos del tutor (para las pestañas) */
$hijos = [];
$sqlH = "SELECT e.estudiante_id,
                CONCAT(e.nombre,' ',e.apellido) AS nombre,
                e.grado, e.grupo
         FROM tutor_estudiante te
         JOIN estudiantes e ON e.estudiante_id = te.estudiante_id
         WHERE te.tutor_id = ?
         ORDER BY e.nombre";
$stmt = $con->prepare($sqlH);
$stmt->bind_param('i', $tutor_id);
$stmt->execute();
$resH = $stmt->get_result();
while ($row = $resH->fetch_assoc()) { $hijos[] = $row; }
$stmt->close();

/* estudiante seleccionado (o primero de la lista) */
$estudiante_id = isset($_GET['estudiante_id']) ? (int)$_GET['estudiante_id'] : 0;
if ($estudiante_id === 0 && !empty($hijos)) {
  $estudiante_id = (int)$hijos[0]['estudiante_id'];
}

/* datos del estudiante (validando que pertenezca al tutor) */
$estudiante = null;
if ($estudiante_id > 0) {
  $sql = "SELECT e.estudiante_id, e.nombre, e.apellido, e.fecha_nacimiento,
                 e.grado, e.grupo, e.activo, e.creado_en,
                 t.nombre AS tutor_nombre, t.apellido AS tutor_apellido
          FROM estudiantes e
          JOIN tutor_estudiante te ON te.estudiante_id = e.estudiante_id
          JOIN tutores t          ON t.tutor_id        = te.tutor_id
          WHERE e.estudiante_id = ? AND te.tutor_id = ?
          LIMIT 1";
  $stmt = $con->prepare($sql);
  $stmt->bind_param('ii', $estudiante_id, $tutor_id);
  $stmt->execute();
  $estudiante = $stmt->get_result()->fetch_assoc();
  $stmt->close();
}
if (!$estudiante) { die('<div class="alert alert-danger m-3">Alumno no encontrado o no pertenece a tu cuenta.</div>'); }

/* promedio general (tabla calificaciones) */
$promedio_general = null;
$stmt = $con->prepare("SELECT ROUND(AVG(promedio),2) AS prom
                       FROM calificaciones
                       WHERE estudiante_id = ?");
$stmt->bind_param('i', $estudiante_id);
$stmt->execute();
if ($p = $stmt->get_result()->fetch_assoc()) {
  $promedio_general = ($p['prom'] !== null) ? (float)$p['prom'] : null;
}
$stmt->close();

/* MATERIAS del alumno usando tu esquema:
   inscripciones → clase_asignacion → materias (+ docente) */
$materias = [];
$sql = "SELECT 
          m.materia_id,
          m.nombre,
          COALESCE(m.foto_url, '') AS foto_url,
          d.nombre  AS docente_nombre,
          d.apellido AS docente_apellido
        FROM inscripciones i
        JOIN clase_asignacion ca ON ca.clase_id   = i.clase_id
        JOIN materias         m  ON m.materia_id  = ca.materia_id
        LEFT JOIN docentes    d  ON d.docente_id  = ca.docente_id
        WHERE i.estudiante_id = ?
        ORDER BY m.nombre";
$stmt = $con->prepare($sql);
$stmt->bind_param('i', $estudiante_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) { $materias[] = $row; }
$stmt->close();

/* flag para mostrar pestañas (si hay 2+ hijos) */
$mostrar_selector_hijos = count($hijos) > 1;
?>

<!DOCTYPE html>
<html lang="en">

<head>

  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Weeducate </title>
  <!-- Iconic Fonts -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="stylesheet" href="../../vendors/iconic-fonts/flat-icons/flaticon.css">
  <link rel="stylesheet" href="../../vendors/iconic-fonts/font-awesome/css/all.min.css">
  <!-- Bootstrap core CSS -->
  <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
  <!-- jQuery UI -->
  <link href="../../assets/css/jquery-ui.min.css" rel="stylesheet">
  <!-- Weeducate styles -->
  <link href="../../assets/css/style.css" rel="stylesheet">
  <link href="../../assets/css/perfil-hijo.css" rel="stylesheet">
  <!-- Favicon -->
  <link rel="icon" type="image/png" sizes="32x32" href="../../assets/img/weicon/weicon.ico">

  <!-- Estilos para pestañas de hijos -->
  <style>
    .nav-pills.child-tabs{ gap:.5rem; flex-wrap:wrap; }
    .child-tabs .nav-link{
      display:flex; align-items:center; gap:.6rem;
      padding:.5rem .75rem; border-radius:999px;
      background:#f8fafc; border:1px solid #e5e7eb;
      box-shadow:0 1px 2px rgba(16,24,40,.05);
      transition:background .15s,border-color .15s,box-shadow .15s;
    }
    .child-tabs .nav-link:hover{ background:#f1f5f9; border-color:#d1d5db; }
    .child-tabs .nav-link.active{
      background:#eef2ff; border-color:#c7d2fe; color:#1f2937;
      box-shadow:inset 0 1px 0 rgba(255,255,255,.4);
    }
    .child-tabs .avatar{
      width:28px; height:28px; border-radius:50%;
      background:#4e73df; color:#fff; font-weight:700; font-size:.9rem;
      display:inline-flex; align-items:center; justify-content:center;
    }
    .child-tabs .name{ line-height:1; font-weight:600; }
    .child-tabs .sub{ display:block; margin-top:-2px; font-size:.72rem; color:#6b7280; }
  </style>
</head>

<body class="ms-body ms-aside-left-open ms-primary-theme ms-has-quickbar">



  <!-- Preloader -->
  <div id="preloader-wrap">
    <div class="spinner spinner-8">
      <div class="ms-circle1 ms-child"></div>
      <div class="ms-circle2 ms-child"></div>
      <div class="ms-circle3 ms-child"></div>
      <div class="ms-circle4 ms-child"></div>
      <div class="ms-circle5 ms-child"></div>
      <div class="ms-circle6 ms-child"></div>
      <div class="ms-circle7 ms-child"></div>
      <div class="ms-circle8 ms-child"></div>
      <div class="ms-circle9 ms-child"></div>
      <div class="ms-circle10 ms-child"></div>
      <div class="ms-circle11 ms-child"></div>
      <div class="ms-circle12 ms-child"></div>
    </div>
  </div>

  <!-- Overlays -->
  <div class="ms-aside-overlay ms-overlay-left ms-toggler" data-target="#ms-side-nav" data-toggle="slideLeft"></div>
  <div class="ms-aside-overlay ms-overlay-right ms-toggler" data-target="#ms-recent-activity" data-toggle="slideRight">
  </div>

  <!-- Sidebar Navigation Left -->
  <aside id="ms-side-nav" class="side-nav fixed ms-aside-scrollable ms-aside-left">

    <!-- Logo -->
    <div class="logo-sn ms-d-block-lg">
      <a class="pl-0 ml-0 text-center" href="../../Tutor.php"> <img src="../../assets/img/logo/weeducate-4.png"
          alt="logo"> </a>
    </div>

    <!-- Navigation -->
    <ul class="accordion ms-main-aside fs-14" id="side-nav-accordion">
      <!-- Dashboard -->
      <li class="menu-item">
        <a href="#" class="has-chevron" data-toggle="collapse" data-target="#dashboard" aria-expanded="false"
          aria-controls="dashboard">
          <span><i class="material-icons fs-16">dashboard</i>Home </span>
        </a>
        <ul id="dashboard" class="collapse" aria-labelledby="dashboard" data-parent="#side-nav-accordion">
          <li> <a href="../../Tutor.php">SchoolCare</a> </li>

        </ul>
      </li>
      <!-- /Dashboard -->


      <!--Student Start-->
      <li class="menu-item">
        <a href="#" class="has-chevron" data-toggle="collapse" data-target="#student" aria-expanded="false"
          aria-controls="dashboard">
          <span><i class="fa fa-users fs-16"></i>Mi hijo</span>
        </a>
        <ul id="student" class="collapse" aria-labelledby="courses" data-parent="#side-nav-accordion">
          <li> <a href="./perfil.php">Perfil</a></li>
          <li> <a href="./calificaciones-hijo.php">Calificaciones</a></li>
        </ul>
      </li>
      <!-- /Student End--->

      <!--Courses Start-->
      <li class="menu-item">
        <a href="#" class="has-chevron" data-toggle="collapse" data-target="#courses" aria-expanded="false"
          aria-controls="dashboard">
          <span><i class="fa fa-graduation-cap fs-16"></i>Materias</span>
        </a>
        <ul id="courses" class="collapse" aria-labelledby="courses" data-parent="#side-nav-accordion">
          <li> <a href="../cursos-tutor/materias-hijo.php">Todas mis materias</a> </li>
        </ul>
      </li>
      <!-- /Courses End--->

      <!--Professors Start-->
      <li class="menu-item">
        <a href="#" class="has-chevron" data-toggle="collapse" data-target="#professor" aria-expanded="false"
          aria-controls="dashboard">
          <span><i class="fa fa-user fs-16"></i>Docentes</span>
        </a>
        <ul id="professor" class="collapse" aria-labelledby="dashboard" data-parent="#side-nav-accordion">
          <li> <a href="../profesores-tutor/profesores-estudiante.php">Mis Docentes</a> </li>
        </ul>
      </li>
      <!-- /Professors End--->

      <!--Holiday Start-->
      <li class="menu-item">
        <a href="../calendario-tutor/calendario-escolar.php">
          <span><i class="fa fa-calendar fs-16"></i>Calendario Escolar</span>
        </a>
      </li>
      <!-- /Holiday End--->

      <!--Fees Start-->
      <li class="menu-item">
        <a href="#" class="has-chevron" data-toggle="collapse" data-target="#fees" aria-expanded="false"
          aria-controls="dashboard">
          <span><i class="fas fa-dollar-sign"></i>Pagos Escolares</span>
        </a>
        <ul id="fees" class="collapse" aria-labelledby="staff" data-parent="#side-nav-accordion">
          <li> <a href="../pagos-tutor/pagos-hijo.php">Orden de pago</a></li>
        </ul>
      </li>
      <!-- /Feess End--->
    </ul>

  </aside>

  <!-- Sidebar Right -->
  <aside id="ms-recent-activity" class="side-nav fixed ms-aside-right ms-scrollable">

    <div class="ms-aside-header">
      <ul class="nav nav-tabs tabs-bordered d-flex nav-justified mb-3" role="tablist">
        <li role="presentation" class="fs-12"><a href="#activityLog" aria-controls="activityLog" class="active"
            role="tab" data-toggle="tab"> Activity Log</a></li>
        <li role="presentation" class="fs-12"><a href="#recentPosts" aria-controls="recentPosts" role="tab"
            data-toggle="tab"> Settings </a></li>
        <li><button type="button" class="close ms-toggler text-center" data-target="#ms-recent-activity"
            data-toggle="slideRight"><span aria-hidden="true">&times;</span></button></li>
      </ul>
    </div>

    <div class="ms-aside-body">

      <div class="tab-content">

        <div role="tabpanel" class="tab-pane active fade show" id="activityLog">
          <ul class="ms-activity-log">
            <li>
              <div class="ms-btn-icon btn-pill icon btn-light">
                <i class="flaticon-gear"></i>
              </div>
              <h6>Update 1.0.0 Pushed</h6>
              <span> <i class="material-icons">event</i>1 January, 2021</span>
              <p class="fs-14">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque scelerisque diam non
                nisi semper, ula in sodales vehicula....</p>
            </li>
            <li>
              <div class="ms-btn-icon btn-pill icon btn-success">
                <i class="flaticon-tick-inside-circle"></i>
              </div>
              <h6>Profile Updated</h6>
              <span> <i class="material-icons">event</i>4 March, 2018</span>
              <p class="fs-14">Curabitur purus sem, malesuada eu luctus eget, suscipit sed turpis. Nam pellentesque
                felis vitae justo accumsan, sed semper nisi sollicitudin...</p>
            </li>
            <li>
              <div class="ms-btn-icon btn-pill icon btn-warning">
                <i class="flaticon-alert-1"></i>
              </div>
              <h6>Your payment is due</h6>
              <span> <i class="material-icons">event</i>1 January, 2021</span>
              <p class="fs-14">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque scelerisque diam non
                nisi semper, ula in sodales vehicula....</p>
            </li>
            <li>
              <div class="ms-btn-icon btn-pill icon btn-danger">
                <i class="flaticon-alert"></i>
              </div>
              <h6>Database Error</h6>
              <span> <i class="material-icons">event</i>4 March, 2018</span>
              <p class="fs-14">Curabitur purus sem, malesuada eu luctus eget, suscipit sed turpis. Nam pellentesque
                felis vitae justo accumsan, sed semper nisi sollicitudin...</p>
            </li>
            <li>
              <div class="ms-btn-icon btn-pill icon btn-info">
                <i class="flaticon-information"></i>
              </div>
              <h6>Checkout what's Trending</h6>
              <span> <i class="material-icons">event</i>1 January, 2021</span>
              <p class="fs-14">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque scelerisque diam non
                nisi semper, ula in sodales vehicula....</p>
            </li>
            <li>
              <div class="ms-btn-icon btn-pill icon btn-secondary">
                <i class="flaticon-diamond"></i>
              </div>
              <h6>Your Dashboard is ready</h6>
              <span> <i class="material-icons">event</i>4 March, 2018</span>
              <p class="fs-14">Curabitur purus sem, malesuada eu luctus eget, suscipit sed turpis. Nam pellentesque
                felis vitae justo accumsan, sed semper nisi sollicitudin...</p>
            </li>
          </ul>
          <a href="#" class="btn btn-primary d-block"> View All </a>
        </div>

        <div role="tabpanel" class="tab-pane fade" id="recentPosts">

          <h6>General Settings</h6>
          <div class="ms-form-group">
            <span class="ms-option-name fs-14">Location Tracking</span>
            <label class="ms-switch float-right">
              <input type="checkbox">
              <span class="ms-switch-slider round"></span>
            </label>
          </div>
          <div class="ms-form-group">
            <span class="ms-option-name fs-14">Allow Notifications</span>
            <label class="ms-switch float-right">
              <input type="checkbox">
              <span class="ms-switch-slider round"></span>
            </label>
          </div>
          <div class="ms-form-group">
            <span class="ms-option-name fs-14">Allow Popups</span>
            <label class="ms-switch float-right">
              <input type="checkbox" checked>
              <span class="ms-switch-slider round"></span>
            </label>
          </div>
          <h6>Log Settings</h6>
          <div class="ms-form-group">
            <span class="ms-option-name fs-14">Enable Logging</span>
            <label class="ms-switch float-right">
              <input type="checkbox" checked>
              <span class="ms-switch-slider round"></span>
            </label>
          </div>
          <div class="ms-form-group">
            <span class="ms-option-name fs-14">Audit Logs</span>
            <label class="ms-switch float-right">
              <input type="checkbox">
              <span class="ms-switch-slider round"></span>
            </label>
          </div>
          <div class="ms-form-group">
            <span class="ms-option-name fs-14">Error Logs</span>
            <label class="ms-switch float-right">
              <input type="checkbox" checked>
              <span class="ms-switch-slider round"></span>
            </label>
          </div>
          <h6>Advanced Settings</h6>
          <div class="ms-form-group">
            <span class="ms-option-name fs-14">Enable Logging</span>
            <label class="ms-switch float-right">
              <input type="checkbox" checked>
              <span class="ms-switch-slider round"></span>
            </label>
          </div>
          <div class="ms-form-group">
            <span class="ms-option-name fs-14">Audit Logs</span>
            <label class="ms-switch float-right">
              <input type="checkbox">
              <span class="ms-switch-slider round"></span>
            </label>
          </div>
          <div class="ms-form-group">
            <span class="ms-option-name fs-14">Error Logs</span>
            <label class="ms-switch float-right">
              <input type="checkbox" checked>
              <span class="ms-switch-slider round"></span>
            </label>
          </div>

        </div>

      </div>

    </div>

  </aside>

  <!-- Main Content -->
  <main class="body-content">

    <!-- Navigation Bar -->
    <nav class="navbar ms-navbar">

      <div class="ms-aside-toggler ms-toggler pl-0" data-target="#ms-side-nav" data-toggle="slideLeft">
        <span class="ms-toggler-bar bg-primary"></span>
        <span class="ms-toggler-bar bg-primary"></span>
        <span class="ms-toggler-bar bg-primary"></span>
      </div>

      <div class="logo-sn logo-sm ms-d-block-sm">
        <a class="pl-0 ml-0 text-center navbar-brand mr-0" href="../../index.html"><img
            src="../../assets/img/logo/weeducate-4.png" alt="logo"> </a>
      </div>

      <ul class="ms-nav-list ms-inline mb-0" id="ms-nav-options">
        <li class="ms-nav-item ms-search-form pb-0 py-0">
          <form class="ms-form" method="post">
            <div class="ms-form-group my-0 mb-0 has-icon fs-14">
              <input type="search" class="ms-form-input" name="search" placeholder="Search here..." value="">
              <i class="flaticon-search text-disabled"></i>
            </div>
          </form>
        </li>

        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="mailDropdown">
          <li class="dropdown-menu-header">
            <h6 class="dropdown-header ms-inline m-0"><span class="text-disabled">Mail</span></h6><span
              class="badge badge-pill badge-success">3 New</span>
          </li>
          <li class="dropdown-divider"></li>
          <li class="ms-scrollable ms-dropdown-list">
            <a class="media p-2" href="#">
              <div class="ms-chat-status ms-status-offline ms-chat-img mr-2 align-self-center">
                <img src="../../assets/img/we-educate/topper-4.jpg" class="ms-img-round" alt="people">
              </div>
              <div class="media-body">
                <span>Hey man, looking forward to your new project.</span>
                <p class="fs-10 my-1 text-disabled"><i class="material-icons">access_time</i> 30 seconds ago</p>
              </div>
            </a>
            <a class="media p-2" href="#">
              <div class="ms-chat-status ms-status-online ms-chat-img mr-2 align-self-center">
                <img src="../../assets/img/we-educate/topper-2.jpg" class="ms-img-round" alt="people">
              </div>
              <div class="media-body">
                <span>Dear John, I was told you bought Weeducate! Send me your feedback</span>
                <p class="fs-10 my-1 text-disabled"><i class="material-icons">access_time</i> 28 minutes ago</p>
              </div>
            </a>
            <a class="media p-2" href="#">
              <div class="ms-chat-status ms-status-offline ms-chat-img mr-2 align-self-center">
                <img src="../../assets/img/we-educate/topper-3.jpg" class="ms-img-round" alt="people">
              </div>
              <div class="media-body">
                <span>How many people are we inviting to the dashboard?</span>
                <p class="fs-10 my-1 text-disabled"><i class="material-icons">access_time</i> 6 hours ago</p>
              </div>
            </a>
          </li>
          <li class="dropdown-divider"></li>
          <li class="dropdown-menu-footer text-center">
            <a href="../apps/email.html">Go to Inbox</a>
          </li>
        </ul>
        </li>

        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="notificationDropdown">
          <li class="dropdown-menu-header">
            <h6 class="dropdown-header ms-inline m-0"><span class="text-disabled">Notifications</span></h6><span
              class="badge badge-pill badge-info">4 New</span>
          </li>
          <li class="dropdown-divider"></li>
          <li class="ms-scrollable ms-dropdown-list">
            <a class="media p-2" href="#">
              <div class="media-body">
                <span>12 ways to improve your crypto dashboard</span>
                <p class="fs-10 my-1 text-disabled"><i class="material-icons">access_time</i> 30 seconds ago</p>
              </div>
            </a>
            <a class="media p-2" href="#">
              <div class="media-body">
                <span>You have newly registered users</span>
                <p class="fs-10 my-1 text-disabled"><i class="material-icons">access_time</i> 45 minutes ago</p>
              </div>
            </a>
            <a class="media p-2" href="#">
              <div class="media-body">
                <span>Your account was logged in from an unauthorized IP</span>
                <p class="fs-10 my-1 text-disabled"><i class="material-icons">access_time</i> 2 hours ago</p>
              </div>
            </a>
            <a class="media p-2" href="#">
              <div class="media-body">
                <span>An application form has been submitted</span>
                <p class="fs-10 my-1 text-disabled"><i class="material-icons">access_time</i> 1 day ago</p>
              </div>
            </a>
          </li>
          <li class="dropdown-divider"></li>
          <li class="dropdown-menu-footer text-center">
            <a href="#">View all Notifications</a>
          </li>
        </ul>
        </li>

        </li>
        <li class="ms-nav-item ms-nav-user dropdown">
          <a href="#" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <img
              class="ms-user-img ms-img-round float-right" src="../../assets/img/we-educate/new-student-5.jpg"
              alt="people"> </a>
          <ul class="dropdown-menu dropdown-menu-right user-dropdown" aria-labelledby="userDropdown">
            <li class="dropdown-menu-header">
              <h6 class="dropdown-header ms-inline m-0"><span class="text-disabled">Bienvenido,
                  <?php echo $_SESSION['nombre'] . ' ' . $_SESSION['apellido']; ?></span></h6>
            </li>
            <li class="dropdown-divider"></li>
            <li class="ms-dropdown-list">
              <a class="media fs-14 p-2" href="pages/prebuilt-pages/user-profile.html"> <span><i
                    class="flaticon-user mr-2"></i> Perfil</span> </a>

            </li>
            <li class="dropdown-divider"></li>
            <li class="dropdown-menu-footer">

            </li>
            <li class="dropdown-menu-footer">
              <a class="media fs-14 p-2 logout-link" href="pages/php/auth.php?logout=true">
                <span><i class="flaticon-shut-down mr-2"></i> Cerrar Sesión</span>
              </a>
            </li>
          </ul>
        </li>
      </ul>

      <div class="ms-toggler ms-d-block-sm pr-0 ms-nav-toggler" data-toggle="slideDown" data-target="#ms-nav-options">
        <span class="ms-toggler-bar bg-primary"></span>
        <span class="ms-toggler-bar bg-primary"></span>
        <span class="ms-toggler-bar bg-primary"></span>
      </div>

    </nav>

    <!-- Quick bar (vacío) -->
    <aside id="ms-quick-bar" class="ms-quick-bar fixed ms-d-block-lg"></aside>

    <!-- Body Content Wrapper -->
    <div class="container py-5">
      <div class="card shadow-lg border-0 rounded-4 overflow-hidden">

        <!-- Pestañas de hijos (si hay más de uno) -->
        <?php if ($mostrar_selector_hijos): ?>
          <div class="card-body pt-4 px-4 pb-0">
            <ul class="nav nav-pills child-tabs" role="tablist">
              <?php foreach ($hijos as $h):
                $isActive = (int)$h['estudiante_id'] === (int)$estudiante['estudiante_id'];
                $ini = mb_strtoupper(mb_substr($h['nombre'],0,1,'UTF-8'),'UTF-8');
              ?>
                <li class="nav-item" role="presentation">
                  <a class="nav-link <?php echo $isActive ? 'active' : ''; ?>"
                     href="?estudiante_id=<?php echo (int)$h['estudiante_id']; ?>">
                    <span class="avatar"><?php echo h($ini); ?></span>
                    <span>
                      <span class="name"><?php echo h($h['nombre']); ?></span>
                      <small class="sub">
                        Grado <?php echo h($h['grado']); ?>
                        <?php echo !empty($h['grupo']) ? ' · Grupo '.h($h['grupo']) : ''; ?>
                      </small>
                    </span>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <!-- Header del perfil -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
          <div class="profile-header">
            <i class="ph-icon fas fa-user-graduate" aria-hidden="true"></i>

            <div class="ph-body">
              <div class="ph-top">
                <h2 class="ph-title mb-0">
                  Perfil de <?php echo h(($estudiante['nombre'] ?? '').' '.($estudiante['apellido'] ?? '')); ?>
                </h2>
                <span class="status-inline <?php echo ((int)($estudiante['activo'] ?? 0)===1) ? 'ok' : 'off'; ?>">
                  <i class="fas <?php echo ((int)($estudiante['activo'] ?? 0)===1) ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i>
                  <?php echo ((int)($estudiante['activo'] ?? 0)===1) ? 'Activo' : 'Inactivo'; ?>
                </span>
              </div>

              <div class="ph-registered">
                <i class="far fa-calendar-alt"></i>
                Registrado el <?php echo h($estudiante['creado_en'] ?? '—'); ?>
              </div>
            </div>
          </div>
        </div>

        <div class="card-body p-4">

          <!-- Información básica y académica -->
          <div class="row g-4 mb-5">

            <!-- Información básica -->
            <div class="col-lg-6">
              <div class="info-card shadow-sm h-100">
                <h5 class="section-title text-primary mb-3">
                  <span class="title-icon"><i class="fas fa-info-circle"></i></span> Información básica
                </h5>

                <div class="info-grid">
                  <div class="info-item">
<span class="info-icon" aria-hidden="true"><i class="fas fa-birthday-cake"></i></span>
                    <div>
                      <p class="info-label">Fecha de nacimiento</p>
                      <p class="info-value"><?php echo h($estudiante['fecha_nacimiento'] ?? '—'); ?></p>
                    </div>
                  </div>

                  <div class="info-item">
                    <span class="info-icon"><i class="fas fa-graduation-cap"></i></span>
                    <div>
                      <p class="info-label">Grado y Grupo</p>
                      <p class="info-value">
                        <?php
                          $gradoGrupo = trim(($estudiante['grado'] ?? '').' '.($estudiante['grupo'] ?? ''));
                          echo h($gradoGrupo !== '' ? $gradoGrupo : '—');
                        ?>
                      </p>
                    </div>
                  </div>

                  <div class="info-item">
                    <span class="info-icon"><i class="fas fa-user-tie"></i></span>
                    <div>
                      <p class="info-label">Tutor</p>
                      <p class="info-value">
                        <?php echo h(trim(($estudiante['tutor_nombre'] ?? '').' '.($estudiante['tutor_apellido'] ?? '')) ?: '—'); ?>
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Datos académicos -->
            <div class="col-lg-6">
              <div class="info-card shadow-sm h-100">
                <h5 class="section-title text-primary mb-3">
                  <span class="title-icon"><i class="fas fa-id-card"></i></span> Datos académicos
                </h5>

                <div class="info-grid">
                  <div class="info-item">
                    <span class="info-icon"><i class="fas fa-hashtag"></i></span>
                    <div>
                      <p class="info-label">ID Estudiante</p>
                      <p class="info-value"><?php echo (int)($estudiante['estudiante_id'] ?? 0); ?></p>
                    </div>
                  </div>

                  <div class="info-item">
                    <span class="info-icon"><i class="fas fa-chart-line"></i></span>
                    <div>
                      <p class="info-label">Promedio general</p>
                      <p class="info-value">
                        <?php echo ($promedio_general !== null) ? number_format($promedio_general, 2) : '—'; ?>
                      </p>
                    </div>
                  </div>

                  
                </div>
              </div>
            </div>

          </div><!-- /row info -->

          <!-- Materias -->
          <div class="d-flex justify-content-between align-items-center mb-4 materias-header">
            <h4 class="section-title text-dark">
              <span class="title-icon"><i class="fas fa-book-open"></i></span> Materias actuales
            </h4>
            <span class="contador"><?php echo count($materias ?? []); ?> materias</span>
          </div>

          <?php if (!empty($materias)): ?>
            <div class="row g-4">
              <?php foreach ($materias as $materia):
                $img = !empty($materia['foto_url']) ? $materia['foto_url'] : '../../assets/img/placeholder-materia.jpg';
                $doc = trim(($materia['docente_nombre'] ?? '').' '.($materia['docente_apellido'] ?? ''));
                if ($doc === '') $doc = 'Docente por asignar';
              ?>
                <div class="col-sm-6 col-lg-4 col-xl-3">
                  <div class="card materia-card border-0 shadow-sm h-100 rounded-3">
                    <div class="position-relative">
                      <img src="<?php echo h($img); ?>" class="card-img-top" alt="Imagen materia">
                      <span class="badge badge-status bg-success">En curso</span>
                    </div>
                    <div class="card-body">
                      <h6 class="fw-bold text-dark mb-2"><?php echo h($materia['nombre'] ?? $materia['materia'] ?? 'Materia'); ?></h6>
                      <div class="d-flex align-items-center gap-2 text-muted small mb-3">
                        <i class="fas fa-user-tie"></i>
                        <span><?php echo h($doc); ?></span>
                      </div>
                      <div class="d-flex justify-content-between align-items-center">
                        <a href="../perfil-hijo/calificaciones-hijo.php?estudiante_id=<?php echo (int)($estudiante['estudiante_id'] ?? 0); ?>"
                          class="text-decoration-none small">
                          Ver detalles <i class="fas fa-chevron-right ms-1"></i>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <div class="alert alert-warning d-flex align-items-center gap-3 py-3">
              <i class="fas fa-info-circle fa-lg"></i>
              <div>
                <h6 class="alert-heading mb-1">Sin materias asignadas</h6>
                <p class="mb-0 small">Este estudiante aún no tiene materias asignadas para este periodo.</p>
              </div>
            </div>
          <?php endif; ?>

        </div><!-- /card-body -->
      </div><!-- /card -->
    </div><!-- /container -->

  </main>

  <!-- Global Required Scripts Start -->
  <script src="../../assets/js/jquery-3.3.1.min.js"></script>
  <script src="../../assets/js/popper.min.js"></script>
  <script src="../../assets/js/bootstrap.min.js"></script>
  <script src="../../assets/js/perfect-scrollbar.js"></script>
  <script src="../../assets/js/jquery-ui.min.js"></script>
  <!-- Global Required Scripts End -->

  <!-- Weeducate core JavaScript -->
  <script src="../../assets/js/framework.js"></script>
  <script src="../../assets/js/settings.js"></script>

  <!-- Fallback: quita el preloader aunque algo falle -->
  <script>
    (function () {
      function hidePreloader() {
        var p = document.getElementById('preloader-wrap');
        if (p) p.style.display = 'none';
      }
      window.addEventListener('load', hidePreloader);
      setTimeout(hidePreloader, 2000);
    })();
  </script>

</body>

</html>
