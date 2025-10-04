<?php include '../php/auth.php';
if (!function_exists('h')) {
  function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}?>

<!DOCTYPE html>
<html lang="es">

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
  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
  <!-- jQuery UI -->
  <link href="../../assets/css/jquery-ui.min.css" rel="stylesheet">
  <!-- Page Specific CSS (Slick Slider.css) -->
  <link href="../../assets/css/slick.css" rel="stylesheet">
  <!-- Weeducate styles -->
  <link href="../../assets/css/style.css" rel="stylesheet">
  <!-- Favicon -->
  <link rel="icon" type="image/png" sizes="32x32" href="../../assets/img/LogoSchoolCare.png">

  <!-- SweetAlert2 (necesario para Swal.fire) -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="ms-body ms-aside-left-open ms-primary-theme ms-has-quickbar">

  <!-- Preloader -->
  <div id="preloader-wrap">
    <div class="spinner spinner-8">
      <div class="ms-circle1 ms-child"></div><div class="ms-circle2 ms-child"></div>
      <div class="ms-circle3 ms-child"></div><div class="ms-circle4 ms-child"></div>
      <div class="ms-circle5 ms-child"></div><div class="ms-circle6 ms-child"></div>
      <div class="ms-circle7 ms-child"></div><div class="ms-circle8 ms-child"></div>
      <div class="ms-circle9 ms-child"></div><div class="ms-circle10 ms-child"></div>
      <div class="ms-circle11 ms-child"></div><div class="ms-circle12 ms-child"></div>
    </div>
  </div>

  <!-- Overlays -->
  <div class="ms-aside-overlay ms-overlay-left ms-toggler" data-target="#ms-side-nav" data-toggle="slideLeft"></div>
  <div class="ms-aside-overlay ms-overlay-right ms-toggler" data-target="#ms-recent-activity" data-toggle="slideRight"></div>

  <!-- Sidebar Navigation Left -->
  <aside id="ms-side-nav" class="side-nav fixed ms-aside-scrollable ms-aside-left">
    <div class="logo-sn ms-d-block-lg">
      <a class="pl-0 ml-0 text-center" href="../../index.php"><img src="../../assets/img/LogoSchoolCare.png" alt="logo"></a>
    </div>

    <ul class="accordion ms-main-aside fs-14" id="side-nav-accordion">
      <li class="menu-item">
        <a href="#" class="has-chevron" data-toggle="collapse" data-target="#dashboard" aria-expanded="false" aria-controls="dashboard">
          <span><i class="material-icons fs-16">dashboard</i>Dashboard </span>
        </a>
        <ul id="dashboard" class="collapse" aria-labelledby="dashboard" data-parent="#side-nav-accordion">
          <li><a href="../../index.php">SchoolCare</a></li>
        </ul>
      </li>

      <li class="menu-item">
        <a href="#" class="has-chevron" data-toggle="collapse" data-target="#professor" aria-expanded="false" aria-controls="dashboard">
          <span><i class="fa fa-user fs-16"></i>Profesores</span>
        </a>
        <ul id="professor" class="collapse" aria-labelledby="dashboard" data-parent="#side-nav-accordion">
          <li><a href="../professors/allprofessor.php">Todos los Profesores</a></li>
          <li><a href="../professors/addprofessor.php">Añadir Profesores</a></li>
          <li><a href="../professors/aboutprofessor.php">Acerca de Profesores</a></li>
        </ul>
      </li>

      <li class="menu-item">
        <a href="#" class="has-chevron" data-toggle="collapse" data-target="#courses" aria-expanded="false" aria-controls="dashboard">
          <span><i class="fa fa-graduation-cap fs-16"></i>Materias</span>
        </a>
        <ul id="courses" class="collapse" aria-labelledby="courses" data-parent="#side-nav-accordion">
          <li><a href="../courses/allcourses.php">Todas las Materias</a></li>
          <li><a href="../courses/addcourses.php">Añadir Materias</a></li>
          <li><a href="../courses/ciclos.php">Ciclos</a></li>
          <li><a href="../courses/clases.php">Clases</a></li>
          <li><a href="../courses/inscripciones.php">Inscripciones</a></li>
        </ul>
      </li>

      <li class="menu-item">
        <a href="#" class="has-chevron" data-toggle="collapse" data-target="#student" aria-expanded="false" aria-controls="dashboard">
          <span><i class="fa fa-users fs-16"></i>Estudiantes</span>
        </a>
        <ul id="student" class="collapse" aria-labelledby="courses" data-parent="#side-nav-accordion">
          <li><a href="../student/studentadd.php">Añadir Estudiante</a></li>
          <li><a href="../student/studenttable.php">Tabla de Estudiantes</a></li>
          <li><a href="../student/scoretable.php">Tabla de Calificaciones</a></li>
        </ul>
      </li>

      <li class="menu-item">
        <a href="#" class="has-chevron" data-toggle="collapse" data-target="#staff" aria-expanded="false" aria-controls="dashboard">
          <span><i class="fa fa-user-circle fs-16"></i>Tutores</span>
        </a>
        <ul id="staff" class="collapse" aria-labelledby="staff" data-parent="#side-nav-accordion">
          <li><a href="../tutor/addtutor.php">Añadir Tutor</a></li>
          <li><a href="../tutor/tutorprofile.php">Tabla de Tutores</a></li>
        </ul>
      </li>

      <li class="menu-item">
        <a href="#" class="has-chevron" data-toggle="collapse" data-target="#fees" aria-expanded="false" aria-controls="dashboard">
          <span><i class="fas fa-dollar-sign"></i>Orden de Pago</span>
        </a>
        <ul id="fees" class="collapse" aria-labelledby="staff" data-parent="#side-nav-accordion">
          <li><a href="../fees/feescollection.php">Colección de Pagos</a></li>
          <li><a href="../fees/addfees.php">Añadir Pago</a></li>
          <li><a href="../fees/feesrecepit.php">Recibo de Pago</a></li>
        </ul>
      </li>

      <li class="menu-item">
        <a href="../holidays/holiday.php">
          <span><i class="fa fa-calendar fs-16"></i>Calendario Escolar</span>
        </a>
      </li>
    </ul>
  </aside>

  <!-- Sidebar Right -->
  <aside id="ms-recent-activity" class="side-nav fixed ms-aside-right ms-scrollable"></aside>

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
        <a class="pl-0 ml-0 text-center navbar-brand mr-0" href="../../index.php">
          <img src="../../assets/img/LogoSchoolCare.png" alt="logo">
        </a>
      </div>

      <ul class="ms-nav-list ms-inline mb-0" id="ms-nav-options">
        <li class="ms-nav-item ms-search-form pb-0 py-0"><form class="ms-form" method="post"></form></li>
        <li class="ms-nav-item ms-nav-user dropdown">
          <a href="#" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <img class="ms-user-img ms-img-round float-right" src="../../assets/img/we-educate/new-student-5.jpg" alt="people">
          </a>
          <ul class="dropdown-menu dropdown-menu-right user-dropdown" aria-labelledby="userDropdown">
            <li class="dropdown-menu-header">
              <h6 class="dropdown-header ms-inline m-0">
                <span class="text-disabled">Bienvenido, <?php echo $_SESSION['nombre'] . ' ' . $_SESSION['apellido']; ?></span>
              </h6>
            </li>
            <li class="dropdown-divider"></li>
            <li class="dropdown-menu-footer">
              <a class="media fs-14 p-2" href="../php/logout.php"><span><i class="flaticon-shut-down mr-2"></i> Cerrar Sesión</span></a>
            </li>
          </ul>
        </li>
      </ul>

      <div class="ms-toggler ms-d-block-sm pr-0 ms-nav-toggler" data-toggle="slideDown" data-target="#ms-nav-options">
        <span class="ms-toggler-bar bg-primary"></span><span class="ms-toggler-bar bg-primary"></span><span class="ms-toggler-bar bg-primary"></span>
      </div>
    </nav>

    <!-- Body Content Wrapper -->
    <div class="ms-content-wrapper">
      <!-- BOTÓN NUEVO CICLO -->
      <div class="row mb-3">
        <div class="col-md-12 text-right">
          <button id="newCicloBtn" class="btn btn-primary">
            <i class="fa fa-plus"></i> Nuevo Ciclo Escolar
          </button>
        </div>
      </div>

      <!-- TABLA DE CICLOS -->
      <table id="tabla-ciclos" class="table table-striped">
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Inicio</th>
            <th>Fin</th>
            <th>Estado</th>
            <th>Observaciones</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>

      <!-- MODAL CREAR/EDITAR CICLO -->
      <div class="modal fade" id="cicloModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
          <form id="cicloForm">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="cicloModalLabel">Nuevo Ciclo</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <div class="modal-body">
                <!-- DATOS DEL CICLO -->
                <div class="form-group">
                  <label>Nombre</label>
                  <input type="text" id="nombreCiclo" class="form-control" required>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label>Fecha Inicio</label>
                    <input type="date" id="fechaInicioCiclo" class="form-control" required>
                  </div>
                  <div class="form-group col-md-6">
                    <label>Fecha Fin</label>
                    <input type="date" id="fechaFinCiclo" class="form-control" required>
                  </div>
                </div>
                <div class="form-group">
                  <label>Estado</label>
                  <select id="estadoCiclo" class="form-control">
                    <option value="activo">Activo</option>
                    <option value="cerrado">Cerrado</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Observaciones</label>
                  <textarea id="observacionesCiclo" class="form-control"></textarea>
                </div>
                <hr>

                <!-- Repeater dinámico de periodos -->
                <button type="button" id="addPeriodoBtn" class="btn btn-sm btn-outline-primary mb-2">+ Agregar periodo</button>
                <div id="periodosContainer"></div>

                <!-- Plantilla para cada periodo -->
                <template id="periodoTemplate">
                  <div class="row periodo-row mb-2 align-items-end">
                    <input type="hidden" class="periodo-id">
                    <div class="col-12 col-md-4">
                      <label class="form-label">Nombre Periodo</label>
                      <input type="text" class="form-control periodo-nombre" required>
                    </div>
                    <div class="col-6 col-md-3">
                      <label class="form-label">Inicio</label>
                      <input type="date" class="form-control periodo-inicio" required>
                    </div>
                    <div class="col-6 col-md-4">
                      <label class="form-label">Fin</label>
                      <input type="date" class="form-control periodo-fin" required>
                    </div>
                    <div class="col-auto d-flex align-items-end">
                      <button type="button" class="btn btn-danger btn-sm px-2 py-1 btn-remove-periodo"></button>
                    </div>
                  </div>
                </template>

              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-success">Guardar</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal de Edición -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-x1" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editModalLabel">Editar Materia</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          </div>
          <div class="modal-body">
            <form id="editForm">
              <div class="form-group">
                <label for="materiaNombre">Nombre de la Materia</label>
                <input type="text" class="form-control" id="materiaNombre" required>
              </div>
              <div class="form-group">
                <label for="materiaNivel">Nivel de la Materia</label>
                <input type="text" class="form-control" id="materiaNivel" required>
              </div>
              <input type="hidden" id="materiaId">
              <button type="submit" class="btn btn-primary">Guardar cambios</button>
            </form>
          </div>
        </div>
      </div>
    </div>

  </main>

  <!-- Quick bar -->
  <aside id="ms-quick-bar" class="ms-quick-bar fixed ms-d-block-lg"></aside>

  <!-- SCRIPTS -->
  <script src="../../assets/js/jquery-3.3.1.min.js"></script>
  <script src="../../assets/js/jquery-ui.min.js"></script>
  <script src="../../assets/js/popper.min.js"></script>
  <script src="../../assets/js/bootstrap.min.js"></script>

  <!-- DataTables -->
  <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>

  <script src="../../assets/js/perfect-scrollbar.js"></script>
  <script src="../../assets/js/slick.min.js"></script>
  <script src="../../assets/js/moment.js"></script>
  <script src="../../assets/js/jquery.webticker.min.js"></script>

  <script src="../../assets/js/framework.js"></script>
  <script src="../../assets/js/settings.js"></script>

  <!-- Tu script que carga la tabla dinámicamente -->
  <script src="../scripts/cargarCiclos.js"></script>

</body>
</html>
