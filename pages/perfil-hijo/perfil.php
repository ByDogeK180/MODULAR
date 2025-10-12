<?php
// pages/perfil-hijo/perfil.php
require_once __DIR__ . '/../php/perfil_hijo_bootstrap.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>SchoolCare</title>

  <!-- Iconic Fonts -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="stylesheet" href="../../vendors/iconic-fonts/flat-icons/flaticon.css">
  <link rel="stylesheet" href="../../vendors/iconic-fonts/font-awesome/css/all.min.css">

  <!-- Bootstrap -->
  <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">

  <!-- jQuery UI -->
  <link href="../../assets/css/jquery-ui.min.css" rel="stylesheet">

  <!-- Styles -->
  <link href="../../assets/css/style.css" rel="stylesheet">
  <link href="../../assets/css/perfil-hijo.css" rel="stylesheet">

  <link rel="icon" type="image/png" sizes="32x32" href="../../assets/img/LogoSchoolCare.png">
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
  <div class="ms-aside-overlay ms-overlay-right ms-toggler" data-target="#ms-recent-activity" data-toggle="slideRight"></div>

  <!-- Sidebar -->
  <aside id="ms-side-nav" class="side-nav fixed ms-aside-scrollable ms-aside-left">
    <div class="logo-sn ms-d-block-lg">
      <a class="pl-0 ml-0 text-center" href="../../Tutor.php">
        <img src="../../assets/img/LogoSchoolCare.png" alt="logo">
      </a>
    </div>

    <ul class="accordion ms-main-aside fs-14" id="side-nav-accordion">
      <li class="menu-item">
        <a href="#" class="has-chevron" data-toggle="collapse" data-target="#dashboard" aria-expanded="false" aria-controls="dashboard">
          <span><i class="material-icons fs-16">dashboard</i>Home</span>
        </a>
        <ul id="dashboard" class="collapse" aria-labelledby="dashboard" data-parent="#side-nav-accordion">
          <li><a href="../../Tutor.php">SchoolCare</a></li>
        </ul>
      </li>

      <li class="menu-item">
        <a href="#" class="has-chevron" data-toggle="collapse" data-target="#student" aria-expanded="false" aria-controls="dashboard">
          <span><i class="fa fa-users fs-16"></i>Mi hijo</span>
        </a>
        <ul id="student" class="collapse" aria-labelledby="courses" data-parent="#side-nav-accordion">
          <li><a href="./perfil.php">Perfil</a></li>
          <li><a href="./calificaciones-hijo.php">Calificaciones</a></li>
        </ul>
      </li>

      <li class="menu-item">
        <a href="#" class="has-chevron" data-toggle="collapse" data-target="#courses" aria-expanded="false" aria-controls="dashboard">
          <span><i class="fa fa-graduation-cap fs-16"></i>Materias</span>
        </a>
        <ul id="courses" class="collapse" aria-labelledby="courses" data-parent="#side-nav-accordion">
          <li><a href="../cursos-tutor/materias-hijo.php">Todas mis materias</a></li>
        </ul>
      </li>

      <li class="menu-item">
        <a href="#" class="has-chevron" data-toggle="collapse" data-target="#professor" aria-expanded="false" aria-controls="dashboard">
          <span><i class="fa fa-user fs-16"></i>Docentes</span>
        </a>
        <ul id="professor" class="collapse" aria-labelledby="dashboard" data-parent="#side-nav-accordion">
          <li><a href="../profesores-tutor/profesores-estudiante.php">Mis Docentes</a></li>
        </ul>
      </li>

      <li class="menu-item">
        <a href="../calendario-tutor/calendario-escolar.php">
          <span><i class="fa fa-calendar fs-16"></i>Calendario Escolar</span>
        </a>
      </li>

      <li class="menu-item">
        <a href="#" class="has-chevron" data-toggle="collapse" data-target="#fees" aria-expanded="false" aria-controls="dashboard">
          <span><i class="fas fa-dollar-sign"></i>Pagos Escolares</span>
        </a>
        <ul id="fees" class="collapse" aria-labelledby="staff" data-parent="#side-nav-accordion">
          <li><a href="../pagos-tutor/pagos-hijo.php">Orden de pago</a></li>
        </ul>
      </li>
    </ul>
  </aside>

  <!-- Main -->
  <main class="body-content">
    <nav class="navbar ms-navbar">
      <div class="ms-aside-toggler ms-toggler pl-0" data-target="#ms-side-nav" data-toggle="slideLeft">
        <span class="ms-toggler-bar bg-primary"></span>
        <span class="ms-toggler-bar bg-primary"></span>
        <span class="ms-toggler-bar bg-primary"></span>
      </div>

      <div class="logo-sn logo-sm ms-d-block-sm">
        <a class="pl-0 ml-0 text-center navbar-brand mr-0" href="index.php">
          <img src="../../assets/img/logo/weeducate-4.png" alt="logo">
        </a>
      </div>

      <ul class="ms-nav-list ms-inline mb-0" id="ms-nav-options">
        <li class="ms-nav-item ms-nav-user dropdown">
          <a href="#" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <img class="ms-user-img ms-img-round float-right" src="../../assets/img/we-educate/new-student-5.jpg" alt="people">
          </a>
          <ul class="dropdown-menu dropdown-menu-right user-dropdown" aria-labelledby="userDropdown">
            <li class="dropdown-menu-header">
              <h6 class="dropdown-header ms-inline m-0">
                <span class="text-disabled">Bienvenido, <?php echo h($_SESSION['nombre'] . ' ' . $_SESSION['apellido']); ?></span>
              </h6>
            </li>
            <li class="dropdown-divider"></li>
            <li class="dropdown-menu-footer"></li>
            <li class="dropdown-menu-footer">
              <!-- OJO: ruta nueva al logout según tu estructura -->
              <a class="media fs-14 p-2 logout-link" href="../php/logout.php">
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

    <aside id="ms-quick-bar" class="ms-quick-bar fixed ms-d-block-lg"></aside>

    <div class="container py-5">
      <div class="card shadow-lg border-0 rounded-4 overflow-hidden">

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
          <div class="row g-4 mb-5">
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

          </div>

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

        </div>
      </div>
    </div>
  </main>

  <!-- JS base -->
  <script src="../../assets/js/jquery-3.3.1.min.js"></script>
  <script src="../../assets/js/popper.min.js"></script>
  <script src="../../assets/js/bootstrap.min.js"></script>
  <script src="../../assets/js/perfect-scrollbar.js"></script>
  <script src="../../assets/js/jquery-ui.min.js"></script>

  <!-- Core -->
  <script src="../../assets/js/framework.js"></script>
  <script src="../../assets/js/settings.js"></script>

  <!-- Tu JS separado (en carpeta scripts) -->
  <script src="../scripts/perfil-hijo.js"></script>
</body>
</html>
