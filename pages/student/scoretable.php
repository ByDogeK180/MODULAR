<?php 
include '../php/auth.php';
// session_start();
// echo '<pre>';
// print_r($_SESSION);
// echo '</pre>';
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
  <title>SchoolCare</title>
  <!-- Iconic Fonts -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="../../vendors/iconic-fonts/font-awesome/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../vendors/iconic-fonts/flat-icons/flaticon.css">

  <!-- Bootstrap core CSS -->
  <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
  <!-- jQuery UI -->
  <link href="../../assets/css/jquery-ui.min.css" rel="stylesheet">
  <!-- Page Specific CSS (Slick Slider.css) -->
  <link href="../../assets/css/slick.css" rel="stylesheet">
  <!-- Weeducate styles -->
  <link href="../../assets/css/style.css" rel="stylesheet">
  <!-- Favicon -->
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
  <div class="ms-aside-overlay ms-overlay-right ms-toggler" data-target="#ms-recent-activity" data-toggle="slideRight">
  </div>

  <!-- Sidebar Navigation Left -->
  <aside id="ms-side-nav" class="side-nav fixed ms-aside-scrollable ms-aside-left">

    <!-- Logo -->
    <div class="logo-sn ms-d-block-lg">
      <a class="pl-0 ml-0 text-center" href="../../index.php"> <img src="../../assets/img/LogoSchoolCare.png"
          alt="logo"> </a>
    </div>

    <!-- Navigation -->
    <ul class="accordion ms-main-aside fs-14" id="side-nav-accordion">
      <!-- Dashboard -->
      <li class="menu-item">
        <a href="#" class="has-chevron" data-toggle="collapse" data-target="#dashboard" aria-expanded="false"
          aria-controls="dashboard">
          <span><i class="material-icons fs-16">dashboard</i>Dashboard </span>
        </a>
        <ul id="dashboard" class="collapse" aria-labelledby="dashboard" data-parent="#side-nav-accordion">
          <li> <a href="../../index.php">SchoolCare</a> </li>

        </ul>
      </li>
      <!-- /Dashboard -->

      <!--Proessors Start-->
      <li class="menu-item">
        <a href="#" class="has-chevron" data-toggle="collapse" data-target="#professor" aria-expanded="false"
          aria-controls="dashboard">
          <span><i class="fa fa-user fs-16"></i>Profesores</span>
        </a>
        <ul id="professor" class="collapse" aria-labelledby="dashboard" data-parent="#side-nav-accordion">
          <li> <a href="../professors/allprofessor.php">Todos los Profesores</a> </li>
          <li> <a href="../professors/addprofessor.php">Añadir Profesores</a> </li>
          <li> <a href="../professors/aboutprofessor.php">Acerca de Profesores</a> </li>
        </ul>
      </li>
      <!-- /Proessors End--->

      <!--Courses Start-->
      <li class="menu-item">
        <a href="#" class="has-chevron" data-toggle="collapse" data-target="#courses" aria-expanded="false"
          aria-controls="dashboard">
          <span><i class="fa fa-graduation-cap fs-16"></i>Materias</span>
        </a>
        <ul id="courses" class="collapse" aria-labelledby="courses" data-parent="#side-nav-accordion">
          <li> <a href="../courses/allcourses.php">Todas las Materias</a> </li>
          <li> <a href="../courses/addcourses.php">Añadir Materias</a> </li>
          <li> <a href="../courses/aboutcourses.php">Acerca de Materias</a> </li>
          <li> <a href="../courses/ciclos.php">Ciclos</a> </li>
          <li> <a href="../courses/clases.php">Clases</a> </li>
          <li> <a href="../courses/inscripciones.php">Inscripciones</a> </li>
        </ul>
      </li>
      <!-- /Courses End--->

      <!--Student Start-->
      <li class="menu-item">
        <a href="#" class="has-chevron" data-toggle="collapse" data-target="#student" aria-expanded="false"
          aria-controls="dashboard">
          <span><i class="fa fa-users fs-16"></i>Estudiantes</span>
        </a>
        <ul id="student" class="collapse" aria-labelledby="courses" data-parent="#side-nav-accordion">
          <li> <a href="../student/studentadd.php">Añadir Estudiante</a> </li>
          <li> <a href="../student/studenttable.php">Tabla de Estudiantes</a> </li>
          <li> <a href="../student/scoretable.php">Tabla de Calificaciones</a> </li>
        </ul>
      </li>
      <!-- /Student End--->


      <!--tutor Start-->
      <li class="menu-item">
        <a href="#" class="has-chevron" data-toggle="collapse" data-target="#staff" aria-expanded="false"
          aria-controls="dashboard">
          <span><i class="fa fa-user-circle fs-16"></i>Tutores</span>
        </a>
        <ul id="staff" class="collapse" aria-labelledby="staff" data-parent="#side-nav-accordion">
          <li> <a href="../tutor/addtutor.php">Añadir Tutor</a> </li>
          <li> <a href="../tutor/tutorprofile.php">Tabla de Tutores</a> </li>

        </ul>
      </li>
      <!-- /tutor End--->

      <!--Fees Start-->
      <li class="menu-item">
        <a href="#" class="has-chevron" data-toggle="collapse" data-target="#fees" aria-expanded="false"
          aria-controls="dashboard">
          <span><i class="fas fa-dollar-sign"></i>Orden de Pago</span>
        </a>
        <ul id="fees" class="collapse" aria-labelledby="staff" data-parent="#side-nav-accordion">
          <li> <a href="../fees/feescollection.php">Colección de Pagos</a> </li>
          <li> <a href="../fees/addfees.php">Añadir Pago</a> </li>
          <li> <a href="../fees/feesrecepit.php">Recibo de Pago</a> </li>
        </ul>
      </li>
      <!-- /Fees End--->

      <!--Holiday Start-->
      <li class="menu-item">
        <a href="../holidays/holiday.php">
          <span><i class="fa fa-calendar fs-16"></i>Calendario Escolar</span>
        </a>
      </li>
      <!-- /Holiday End--->


    </ul>
  </aside>

  <!-- Sidebar Right -->
  <aside id="ms-recent-activity" class="side-nav fixed ms-aside-right ms-scrollable">
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
        <a class="pl-0 ml-0 text-center navbar-brand mr-0" href="../../index.php"><img
            src="../../assets/img/LogoSchoolCare.png" alt="logo"> </a>
      </div>

      <ul class="ms-nav-list ms-inline mb-0" id="ms-nav-options">
        <li class="ms-nav-item ms-search-form pb-0 py-0">
          <form class="ms-form" method="post">
          </form>
        </li>

        <li class="ms-nav-item ms-nav-user dropdown">
          <a href="#" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <img
              class="ms-user-img ms-img-round float-right" src="../../assets/img/we-educate/new-student-5.jpg"
              alt="people"> </a>
          <ul class="dropdown-menu dropdown-menu-right user-dropdown" aria-labelledby="userDropdown">
            <li class="dropdown-menu-header">
              <h6 class="dropdown-header ms-inline m-0">
                <span class="text-disabled">
                  Bienvenido, <?= h( (($_SESSION['nombre'] ?? '') . ' ' . ($_SESSION['apellido'] ?? '')) ) ?>
                </span>
              </h6>
            </li>
            <li class="dropdown-divider"></li>
            <li class="dropdown-menu-footer">
              <a class="media fs-14 p-2" href="../php/logout.php"> <span><i class="flaticon-shut-down mr-2"></i>
                  Logout</span> </a>
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
      <div class="d-flex justify-content-center my-5">
        <div class="card shadow-lg p-4 rounded-4 w-100" style="max-width: 1100px;">
          <div class="ms-panel-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0 text-primary fw-bold">Registro de Calificaciones por Periodo</h6>
          </div>

          <div class="form-row mb-3 select-row">
            <div class="col-md-6 mb-2">
              <label class="text-muted small mb-1">Clase</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fa fa-book-open"></i></span>
                </div>
                <select id="selectClase" class="custom-select" required>
                  <option value="">Selecciona la clase</option>
                </select>
              </div>
            </div>

            <div class="col-md-6 mb-2">
              <label class="text-muted small mb-1">Periodo</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
                </div>
                <select id="selectPeriodo" class="custom-select" required>
                  <option value="">Selecciona el periodo</option>
                </select>
              </div>
            </div>
          </div>

              <div class="table-responsive">
                <table class="table table-bordered align-middle text-center" id="tablaCalificaciones">
                  <thead class="table-light">
                    <tr id="headerFila">
                      <th>Estudiante</th>
                      <!-- Materias dinámicas -->
                      <th>Promedio</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- Cuerpo generado dinámicamente -->
                  </tbody>
                </table>
              </div>

              <div class="text-end mt-3">
                <button type="submit" class="btn btn-warning">Guardar Calificaciones</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

  </main>

  <!-- Quick bar -->
  <aside id="ms-quick-bar" class="ms-quick-bar fixed ms-d-block-lg">
  </aside>

  <!-- SCRIPTS -->
  <!-- Global Required Scripts Start -->
  <script src="../../assets/js/jquery-3.3.1.min.js"></script>
  <script src="../../assets/js/popper.min.js"></script>
  <script src="../../assets/js/bootstrap.min.js"></script>
  <script src="../../assets/js/perfect-scrollbar.js"> </script>
  <script src="../../assets/js/jquery-ui.min.js"> </script>
  <!-- Global Required Scripts End -->

  <!-- Page Specific Scripts Start -->
  <script src="../../assets/js/slick.min.js"> </script>
  <script src="../../assets/js/moment.js"> </script>
  <script src="../../assets/js/jquery.webticker.min.js"> </script>

  <!-- Page Specific Scripts Finish -->

  <!-- Weeducate core JavaScript -->
  <script src="../../assets/js/framework.js"></script>

  <!-- Settings -->
  <script src="../../assets/js/settings.js"></script>
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="../scripts/cargarCalificacionesDoc.js"></script>

</body>

</html>