<?php require_once __DIR__ . '/pages/php/auth.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>SchoolCare</title>
  <!-- Iconic Fonts -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="vendors/iconic-fonts/font-awesome/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="vendors/iconic-fonts/flat-icons/flaticon.css">

  <!-- Bootstrap core CSS -->
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <!-- jQuery UI -->
  <link href="assets/css/jquery-ui.min.css" rel="stylesheet">
  <!-- Page Specific CSS (Slick Slider.css) -->
  <link href="assets/css/slick.css" rel="stylesheet">
  <!-- Weeducate styles -->
  <link href="assets/css/style.css" rel="stylesheet">
  <!-- css de la pagina -->
  <link href="assets/css/tutor_schoolcare.css" rel="stylesheet">
  <!-- Favicon -->
  <link rel="icon" type="image/png" sizes="32x32" href="assets/img/LogoSchoolCare.png">

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
  </div>

  <!-- Sidebar Navigation Left -->
  <aside id="ms-side-nav" class="side-nav fixed ms-aside-scrollable ms-aside-left">

    <!-- Logo -->
    <div class="logo-sn ms-d-block-lg">
      <a class="pl-0 ml-0 text-center" href="./Tutor.php "><img src="assets/img/LogoSchoolCare.png" alt="logo"> </a>
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
          <li> <a href="./Tutor.php">SchoolCare</a> </li>

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
          <li> <a href="./pages/perfil-hijo/perfil.php">Perfil</a></li>
          <li> <a href="./pages/perfil-hijo/calificaciones-hijo.php">Calificaciones</a></li>
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
          <li> <a href="pages/cursos-tutor/materias-hijo.php">Todas mis materias</a> </li>
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
          <li> <a href="pages/profesores-tutor/profesores-estudiante.php">Mis Docentes</a> </li>
        </ul>
      </li>
      <!-- /Professors End--->

      <!--Holiday Start-->
      <li class="menu-item">
        <a href="./pages/calendario-tutor/calendario-escolar.php">
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
          <li> <a href="./pages/pagos-tutor/pagos-hijo.php">Orden de pago</a></li>
        </ul>
      </li>
      <!-- /Feess End--->
    </ul>

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
        <a class="pl-0 ml-0 text-center navbar-brand mr-0" href="index.php"><img src="assets/img/logo/weeducate-4.png"
            alt="logo"> </a>
      </div>

      <ul class="ms-nav-list ms-inline mb-0" id="ms-nav-options">

        <li class="ms-nav-item ms-nav-user dropdown">
          <a href="#" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <img
              class="ms-user-img ms-img-round float-right" src="assets/img/we-educate/new-student-5.jpg" alt="people">
          </a>
          <ul class="dropdown-menu dropdown-menu-right user-dropdown" aria-labelledby="userDropdown">

            <li class="dropdown-menu-header">
              <h6 class="dropdown-header ms-inline m-0">
                <span class="text-disabled">Bienvenido,
                  <?php echo $_SESSION['nombre'] . ' ' . $_SESSION['apellido']; ?></span>
              </h6>
            </li>

            <li class="dropdown-divider"></li>
            <li class="dropdown-menu-footer">

            </li>
            <li class="dropdown-menu-footer">
              <a class="media fs-14 p-2 logout-link" href="pages/php/logout.php">
                <span><i class="flaticon-shut-down mr-2"></i> Cerrar Sesión</span>
              </a>
            </li>
          </ul>
        </li>
      </ul>



    </nav>

    <!-- Body Content Wrapper -->
    <div class="ml-card">
      <!-- === Filtros centrados dentro del card === -->
      <div class="ml-toolbar">
        <div class="form-group">
          <label for="cicloSelect">Ciclo</label>
          <select id="cicloSelect" class="form-select form-select-sm"></select>
        </div>
        <div class="form-group">
          <label for="periodoSelect">Periodo</label>
          <select id="periodoSelect" class="form-select form-select-sm"></select>
        </div>
        <div class="form-group">
          <label for="claseSelect">Clase</label>
          <select id="claseSelect" class="form-select form-select-sm"></select>
        </div>
        <div class="form-group" style="display:flex;align-items:flex-end">
          <button id="btnReset" type="button" class="btn-ghost btn-ghost-sm">🔄 Reiniciar filtros</button>
        </div>
      </div>


      <!-- Selector de hijo -->
<!-- Filtro: Ver detalle de (estilo píldora como los de arriba) -->
<div class="input-group input-group-sm w-auto filter-pill me-2 mb-2">
  <span class="input-group-text">Ver detalle de</span>
  <select id="selectHijo" class="form-select">
    <option value="">Todos los hijos</option>
    <!-- opciones se llenan por JS -->
  </select>
</div>

      <h5 style="margin-bottom:10px;">Estadísticas de reprobación</h5>


      <!-- Tarjetas resumen -->
      <div class="ml-grid">
        <div class="ml-mini">
          <div class="ml-muted">Alumnos (únicos)</div>
          <div id="statTotal" style="font-size:22px;font-weight:700;">—</div>
        </div>
        <div class="ml-mini">
          <div class="ml-muted">En riesgo (≥0.5 o asistencia &lt; 60%)</div>
          <div id="statRiesgo" style="font-size:22px;font-weight:700;">—</div>
        </div>
        <div class="ml-mini">
          <div class="ml-muted">Tasa de riesgo</div>
          <div id="statRate" style="font-size:22px;font-weight:700;">—</div>
        </div>
      </div>

      <!-- ===== Recomendaciones por Hijo ===== -->
<div class="card shadow-sm mb-4">
  <div class="card-body">
    <h6 class="mb-3">Recomendaciones personalizadas: </h6>
    <div id="recomendacionesContainer" class="row g-3">
      <!-- Tarjetas se llenan dinámicamente -->
    </div>
  </div>
</div>

      <!-- Gráficas -->
      <div class="ml-panels">
        <div class="ml-panel">
          <div class="ml-muted" style="margin-bottom:6px;">Matriz de evaluación (aciertos y errores)</div>
          <canvas id="cmChart" height="200"></canvas>
          <div class="ml-muted small" style="margin-top:6px;">
            Aciertos (Reprobados/Aprobados) y Errores (Falsos Reprobados/Aprobados)
          </div>
        </div>
        <div class="ml-panel">
          <div class="ml-muted" style="margin-bottom:6px;">Distribución de etiquetas reales</div>
          <canvas id="distChart" height="200"></canvas>
          <div class="ml-muted small" style="margin-top:6px;">
            0 = Aprobados · 1 = Reprobados
          </div>
        </div>
      </div>

      <!-- Tabla: alumnos en mayor riesgo -->
      <div class="ml-tablewrap">
        <div class="d-flex justify-content-between align-items-center">
          <h6 class="ml-h6">Top alumnos en riesgo (ordenado por probabilidad)</h6>
          <span class="small ml-muted">Regla: asistencia &lt; 60% ⇒ Reprobado</span>
        </div>
        <div class="table-responsive">
          <table class="table table-striped ml-table align-middle">
            <thead>
              <tr>
                <th>Alumno</th>
                <th>Materia con menor Ren.</th>
                <th>Periodo</th>
                <th>Clase</th>
                <th>Asistencia</th>
                <th>Promedio del periodo</th>
                <th>Prob. Final</th>
                <th>Riesgo</th>
                <th>Req. promedio resto</th>
                <th>Estado ciclo</th>
              </tr>
            </thead>
            <tbody id="tbRiesgo"></tbody>
          </table>
        </div>
      </div>

      <!-- Tabla: resumen por clase / periodo -->
      <div class="ml-tablewrap">
        <h6 class="ml-h6">Resumen por Clase / Periodo</h6>
        <div class="table-responsive">
          <table class="table table-striped ml-table align-middle">
            <thead>
              <tr>
                <th>Periodo</th>
                <th>Clase</th>
                <th>#Alumnos</th>
                <th>#Riesgo</th>
                <th>Tasa Riesgo</th>
              </tr>
            </thead>
            <tbody id="tbResumen"></tbody>
          </table>
        </div>
      </div>

      <!-- ====================== /FIN BLOQUE ====================== -->





<!-- ===== Resumen Global de Hijos ===== -->
<div class="row mb-4">
  <div class="col-md-4">
    <div class="card shadow-sm">
      <div class="card-body text-center">
        <h6>Total de Hijos</h6>
        <h3 id="statHijosTotal">—</h3>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card shadow-sm">
      <div class="card-body text-center">
        <h6>Hijos en Riesgo</h6>
        <h3 id="statHijosRiesgo">—</h3>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card shadow-sm">
      <div class="card-body text-center">
        <h6>Tasa de Riesgo Global</h6>
        <h3 id="statHijosRate">—</h3>
      </div>
    </div>
  </div>
</div>

<!-- Gráfico comparativo por hijo -->
<div class="card shadow-sm mb-4">
  <div class="card-body">
    <h6 class="mb-3">Comparación de Riesgo por Hijo</h6>
    <div class="chart-sm">
      <canvas id="chartHijos"></canvas>
    </div>
  </div>
</div>


  </main>

  <!--   Quick bar -->
  <aside id="ms-quick-bar" class="ms-quick-bar fixed ms-d-block-lg">
  </aside>

  <!-- SCRIPTS -->
  <!-- Global Required Scripts Start -->
  <script src="assets/js/jquery-3.3.1.min.js"></script>
  <script src="assets/js/popper.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>
  <script src="assets/js/perfect-scrollbar.js"> </script>
  <script src="assets/js/jquery-ui.min.js"> </script>
  <!-- Global Required Scripts End -->

  <!-- Page Specific Scripts Start -->
  <script src="assets/js/Chart.bundle.min.js"> </script>
  <script src="assets/js/index.js"> </script>
  <!-- Page Specific Scripts End -->

  <!-- Weeducate core JavaScript -->
  <script src="assets/js/framework.js"></script>

  <!-- Settings -->
  <script src="assets/js/settings.js"></script>
  <!-- Page Specific Scripts Start -->
  <script src="assets/js/datatables.min.js"> </script>
  <script src="assets/js/data-tables.js"> </script>

  <!-- ML.js (tus librerías locales) -->
  <script src="assets/api/ml-cart.min.js"></script>
  <script src="assets/api/random-forest.min.js"></script>
  <script src="assets/api/predicciones_auto_tutores.js"></script>

</body>

</html>