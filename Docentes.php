<?php require_once 'pages/php/auth.php'; ?>


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
            <span><i class="fa fa-calendar fs-16"></i>Holidays</span>
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
        <span class="text-disabled">Welcome, <?php echo $_SESSION['correo']; ?></span>
        </h6>
            </li>

            <li class="dropdown-divider"></li>
            <li class="ms-dropdown-list">
              <a class="media fs-14 p-2" href="pages/prebuilt-pages/user-profile.html"> <span><i class="flaticon-user mr-2"></i> Profile</span> </a>
              
            </li>
            <li class="dropdown-divider"></li>
            <li class="dropdown-menu-footer">
              
            </li>
                  <li class="dropdown-menu-footer">
            <a class="media fs-14 p-2" href="pages/php/logout.php">
              <span><i class="flaticon-shut-down mr-2"></i> Logout</span>
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
    <style>
      /* Card base (se mantiene tu estética) */
      .ml-card{
        background:#fff;border:1px solid #e5e7eb;border-radius:12px;
        padding:16px;margin-top:20px;box-shadow:0 1px 2px rgba(0,0,0,.04);
      }
      .ml-muted{color:#6b7280}
      .ml-h6{margin:0 0 8px}
      .ml-grid{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:12px;}
      .ml-mini{flex:1;min-width:220px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:12px}
      .ml-panels{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px}
      .ml-panel{background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:12px}
      .ml-tablewrap{background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:12px;margin-bottom:16px}
      .ml-table thead th{background:#f8fafc}
      @media (max-width: 992px){ .ml-panels{grid-template-columns:1fr} }

      /* Badges */
      .badge-soft-danger{background:#fee2e2;color:#991b1b;border-radius:20px;padding:.25rem .5rem;font-weight:600}
      .badge-soft-success{background:#dcfce7;color:#166534;border-radius:20px;padding:.25rem .5rem;font-weight:600}
      .badge-soft-info{background:#e0f2fe;color:#075985;border-radius:20px;padding:.25rem .5rem;font-weight:600}
      .badge-soft-warning{background:#fef3c7;color:#92400e;border-radius:20px;padding:.25rem .5rem;font-weight:600}

      /* ===== Toolbar de filtros (centrada y “pegada” al card) ===== */
      .ml-toolbar{
        display:flex;flex-wrap:wrap;gap:12px;justify-content:center;align-items:end;
        margin:-4px -4px 16px;          /* expande a borde del card */
        padding:12px;                   /* espacio interno */
        background:#f8fafc;             /* leve contraste */
        border:1px solid #e5e7eb;       /* mismo borde del card */
        border-radius:10px;             /* suaviza esquinas internas */
      }
      .ml-toolbar .form-group{min-width:220px}
      .ml-toolbar label{font-size:.9rem;color:#6b7280;margin-bottom:4px}

      /* ====== SOLO LOS FILTROS (select) MEJORADOS ====== */
      .ml-toolbar .form-group .form-select.form-select-sm{
        appearance:none; -webkit-appearance:none; -moz-appearance:none;
        background:#fff;
        border:1px solid #e5e7eb;
        border-radius:9999px;
        padding:.42rem 2rem .42rem .75rem;   /* espacio + caret */
        height:34px;                          /* alto consistente */
        font-size:.92rem; color:#111827;
        box-shadow:0 1px 2px rgba(16,24,40,.06), inset 0 1px 0 rgba(255,255,255,.65);
        transition:border-color .15s ease, box-shadow .15s ease, background-color .15s ease;
        background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 20 20'%3E%3Cpath d='M6 8l4 4 4-4' fill='none' stroke='%236b7280' stroke-width='1.6' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
        background-repeat:no-repeat;
        background-position:right .55rem center;
        background-size:16px 16px;
      }
      .ml-toolbar .form-group .form-select.form-select-sm:hover{
        border-color:#d1d5db;
      }
      .ml-toolbar .form-group .form-select.form-select-sm:focus{
        outline:0;
        border-color:#93c5fd;
        box-shadow:0 0 0 3px rgba(59,130,246,.25), 0 1px 2px rgba(16,24,40,.06);
      }

      /* Botón “fantasma” a juego con tarjetas (SIN CAMBIOS) */
      .btn-ghost{
        background:#fff;border:1px solid #e5e7eb;border-radius:10px;
        padding:.45rem .8rem;line-height:1.1;box-shadow:0 1px 2px rgba(0,0,0,.04);
        color:#374151;transition:all .15s ease;display:inline-flex;align-items:center;gap:.4rem;
      }
      .btn-ghost:hover{ border-color:#d1d5db; box-shadow:0 2px 6px rgba(0,0,0,.06); }
      .btn-ghost:focus{ outline:3px solid rgba(59,130,246,.25); outline-offset:2px; }
      .btn-ghost:disabled{ opacity:.6; cursor:not-allowed; }
      .btn-ghost-sm{ padding:.35rem .65rem; font-size:.9rem; border-radius:10px; }
    </style>

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