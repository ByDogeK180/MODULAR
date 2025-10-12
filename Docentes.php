<?php 
require_once __DIR__ . '/pages/php/auth.php';
// session_start();
// echo '<pre>'; print_r($_SESSION); echo '</pre>';
if (!function_exists('h')) {
  function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Schoolcare</title>
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
  <link href="assets/css/docentes_schoolcare.css" rel="stylesheet">
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
  <div class="ms-aside-overlay ms-overlay-right ms-toggler" data-target="#ms-recent-activity" data-toggle="slideRight"></div>

  <!-- Sidebar Navigation Left -->
  <aside id="ms-side-nav" class="side-nav fixed ms-aside-scrollable ms-aside-left">

    <!-- Logo -->
    <div class="logo-sn ms-d-block-lg">
      <a class="pl-0 ml-0 text-center" href="Docentes.php"><img src="assets/img/LogoSchoolCare.png" alt="logo">  </a>
    </div>

    <!-- Navigation -->
    <ul class="accordion ms-main-aside fs-14" id="side-nav-accordion">
        <!-- Dashboard -->
        <li class="menu-item">
            <a href="#" class="has-chevron" data-toggle="collapse" data-target="#dashboard" aria-expanded="false" aria-controls="dashboard">
               <span><i class="material-icons fs-16">dashboard</i>Dashboard </span>
             </a>
            <ul id="dashboard" class="collapse" aria-labelledby="dashboard" data-parent="#side-nav-accordion">
              <li> <a href="Docentes.php">Schoolcare</a> </li>

            </ul>
        </li>
        <!-- /Dashboard -->
        
        <!--Proessors Start-->
        <li class="menu-item">
            <a href="#" class="has-chevron" data-toggle="collapse" data-target="#professor" aria-expanded="false" aria-controls="dashboard">
               <span><i class="fa fa-user fs-16"></i>Estudiantes</span>
             </a>
            <ul id="professor" class="collapse" aria-labelledby="dashboard" data-parent="#side-nav-accordion">
              <li> <a href="pages/students-doc/studendoc.php">Todos los Estudiantes</a> </li>
               <li> <a href="pages/students-doc/asistencias.php">Asistencias</a> </li>
                 <li> <a href="pages/students-doc/scoredoc.php">Acerca de los Estudiantes</a> </li>
                  <li> <a href="pages/students-doc/formulario_incidentes.php">Recordatorios  </a> </li>
                  <li> <a href="pages/students-doc/recordatorios_docente.php">Todos los Recordatorios  </a> </li>
            </ul>
        </li>
        <!-- /Proessors End--->
        
         <!--Courses Start-->
        <li class="menu-item">
            <a href="#" class="has-chevron" data-toggle="collapse" data-target="#courses" aria-expanded="false" aria-controls="dashboard">
               <span><i class="fa fa-graduation-cap fs-16"></i>Materias</span>
             </a>
            <ul id="courses" class="collapse" aria-labelledby="courses" data-parent="#side-nav-accordion">
              <li> <a href="pages/students-doc/allcoursesDoc.php">Todas las Materias</a> </li>
            </ul>
        </li>
        <!-- /Courses End--->
          <!--tutor Start-->
        <li class="menu-item">
            <a href="#" class="has-chevron" data-toggle="collapse" data-target="#staff" aria-expanded="false" aria-controls="dashboard">
               <span><i class="fa fa-user-circle fs-16"></i>Tutores</span>
             </a>
            <ul id="staff" class="collapse" aria-labelledby="staff" data-parent="#side-nav-accordion">
               <li> <a href="pages/students-doc/tutoresDoc.php">Tabla de Tutores</a> </li>
            </ul>
        </li>
        <!-- /tutor End--->
        


       <!--Holiday Start-->
        <li class="menu-item">
          <a href="pages/holidays/holiday_docente.php">
            <span><i class="fa fa-calendar fs-16"></i>Calendario</span>
          </a>
        </li>
        <!-- /Holiday End--->
 
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
        <a class="pl-0 ml-0 text-center navbar-brand mr-0" href="index.php"><img src="assets/img/logo/weeducate-4.png" alt="logo"> </a>
      </div>

      <ul class="ms-nav-list ms-inline mb-0" id="ms-nav-options">
        <li class="ms-nav-item ms-search-form pb-0 py-0">
          <form class="ms-form" method="post">
            <div class="ms-form-group my-0 mb-0 has-icon fs-14">
            </div>
          </form>
        </li>
        <li class="ms-nav-item">
        </li>
        <li class="ms-nav-item ms-nav-user dropdown">
          <a href="#"  id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <img class="ms-user-img ms-img-round float-right" src="assets/img/we-educate/new-student-5.jpg" alt="people"> </a>
           <ul class="dropdown-menu dropdown-menu-right user-dropdown" aria-labelledby="userDropdown">
            <li class="dropdown-menu-header">
              <h6 class="dropdown-header ms-inline m-0">
                <span class="text-disabled">
                  Bienvenido, <?= h((($_SESSION['nombre'] ?? '') . ' ' . ($_SESSION['apellido'] ?? ''))) ?>
                </span>
              </h6>
            </li>
            <li class="dropdown-divider"></li>
            <li class="dropdown-menu-footer">
              <a class="media fs-14 p-2" href="../php/logout.php">
                <span><i class="flaticon-shut-down mr-2"></i> Cerrar sesión</span>
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
      
    <!-- Body Content Wrapper -->
    <div class="ms-content-wrapper">
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
    </div>
      
</main>

<!--   Quick bar -->
  <aside id="ms-quick-bar" class="ms-quick-bar fixed ms-d-block-lg">

  </aside>

  <!-- MODALS -->

 
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
  <script src="assets/api/predicciones_auto_profesores.js"></script>


</body>

</html>