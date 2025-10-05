<?php include '../php/auth.php';
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

  <link rel="icon" type="image/png" sizes="32x32" href="../../assets/img/LogoSchoolCare.png">
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Bootstrap core CSS -->
  <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
  <!-- jQuery UI -->
  <link href="../../assets/css/jquery-ui.min.css" rel="stylesheet">
  <!-- Page Specific CSS (Slick Slider.css) -->
  <link href="../../assets/css/slick.css" rel="stylesheet">
  <!-- Weeducate styles -->
  <link href="../../assets/css/style.css" rel="stylesheet">
  <!-- Favicon -->
  <link href="../../assets/css/tableStyle.css" rel="stylesheet">
  <link href="../../assets/css/datatables.min.css" rel="stylesheet">

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
              <a class="media fs-14 p-2" href="../php/logout.php"> <span><i class="flaticon-shut-down mr-2"></i>Cerrar Sesión</span></a>
            </li>
          </ul>
        </li>
      </ul>
    </nav>

    <!-- Body Content Wrapper -->
    <div class="ms-content-wrapper">
      <div class="row">

        <div class="col-md-12">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb pl-0">
              <li class="breadcrumb-item"><a href="../../index.php"><i class="material-icons">home</i> Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Profesores</li>
              <li class="breadcrumb-item active" aria-current="page">Todos los profesores</li>
            </ol>
          </nav>
        </div>

        <!-- Tabla de docentes -->
        <div class="col-md-12">
          <div class="ms-panel">
            <div class="ms-panel-header d-flex justify-content-between align-items-center">
              <h6>Lista de Docentes</h6>
              <div>
                <!-- Botón Importar -->
                <button id="btnImportarProfesores" class="btn btn-warning btn-sm me-2" data-toggle="modal"
                  data-target="#importProfesoresModal">
                  <i class="fa fa-file-import"></i> Importar
                </button>

                <!-- Botón Exportar -->
                <button id="btnExportarProfesores" class="btn btn-success btn-sm"
                  onclick="window.location.href='../php/csv-profesores.php'">
                  <i class="fa fa-file-export"></i> Exportar
                </button>
              </div>
            </div>

            <div class="ms-panel-body">
              <table id="tablaProfesores" class="table table-striped table-bordered">
                <thead>
                  <tr>
                    <th>Nombre completo</th>
                    <th>Puesto</th>
                    <th>Correo</th>
                    <th>Teléfono</th>
                    <th>Salario</th>
                    <th>Dirección</th>
                    <th>F. Nacimiento</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody id="docentes-lista">
                  <!-- Aquí se inyectarán las filas -->
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Modal para editar profesor -->
        <div class="modal fade" id="modalEditarProfesor" tabindex="-1" role="dialog"
          aria-labelledby="modalEditarProfesorLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header position-relative">
                <h2 class="modal-title w-100 text-center m-0">Editar Profesor</h2>
                <button type="button" class="close position-absolute" style="right:1rem;" data-dismiss="modal"
                  aria-label="Cerrar">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>

              <div class="modal-body">
                <form id="formEditarProfesor" action="../php/editar_docentes.php" method="POST"
                  enctype="multipart/form-data">

                  <!-- ID oculto -->
                  <input type="hidden" id="edit-docente-id" name="id">
                  <!-- URL de la foto actual (campo oculto para el JS) -->
                  <input type="hidden" id="edit-foto-url" name="foto_url">

                  <div class="form-group">
                    <label for="edit-nombre">Nombre:</label>
                    <input type="text" class="form-control" id="edit-nombre" name="nombre" required>
                  </div>

                  <div class="form-group">
                    <label for="edit-apellido">Apellido:</label>
                    <input type="text" class="form-control" id="edit-apellido" name="apellido" required>
                  </div>

                  <div class="form-group">
                    <label for="edit-telefono">Teléfono:</label>
                    <input type="text" class="form-control" id="edit-telefono" name="telefono" required>
                  </div>

                  <div class="form-group">
                    <label for="edit-correo">Correo:</label>
                    <input type="email" class="form-control" id="edit-correo" name="correo" required>
                  </div>

                  <div class="form-group">
                    <label for="edit-password">Nueva Contraseña (opcional):</label>
                    <input type="password" class="form-control" id="edit-password" name="nueva_contraseña"
                      placeholder="Nueva contraseña">
                  </div>

                  <div class="form-group">
                    <label for="edit-password2">Confirmar Contraseña:</label>
                    <input type="password" class="form-control" id="edit-password2" name="confirmar_contraseña"
                      placeholder="Confirmar contraseña">
                  </div>

                  <div class="form-group">
                    <label for="edit-activo">Status:</label>
                    <select class="form-control" id="edit-activo" name="activo" required>
                      <option value="1">Activo</option>
                      <option value="0">Inactivo</option>
                    </select>
                  </div>

                  <div class="form-group">
                    <label for="edit-puesto">Puesto:</label>
                    <input type="text" class="form-control" id="edit-puesto" name="puesto" required>
                  </div>

                  <div class="form-group">
                    <label for="edit-genero">Género:</label>
                    <select id="edit-genero" name="genero" class="form-control" required>
                      <option value="Masculino">Masculino</option>
                      <option value="Femenino">Femenino</option>
                      <option value="Otro">Otro</option>
                    </select>
                  </div>

                  <div class="form-group">
                    <label for="edit-fecha-nacimiento">Fecha de Nacimiento:</label>
                    <input type="date" class="form-control" id="edit-fecha-nacimiento" name="fecha_nacimiento" required>
                  </div>

                  <div class="form-group">
                    <label for="edit-salario">Salario:</label>
                    <input type="number" class="form-control" id="edit-salario" name="salario" required>
                  </div>

                  <div class="form-group">
                    <label for="edit-direccion">Dirección:</label>
                    <input type="text" class="form-control" id="edit-direccion" name="direccion" required>
                  </div>

                  <!-- Previsualización de la foto actual -->
                  <div class="form-group">
                    <label>Foto actual:</label><br>
                    <img id="preview-foto" src="" alt="Foto actual" class="img-fluid mb-2" style="max-height:150px;">
                  </div>

                  <!-- Campo para subir nueva foto -->
                  <div class="form-group">
                    <label for="edit-foto">Cambiar imagen (opcional):</label>
                    <input type="file" class="form-control" id="edit-foto" name="foto" accept="image/*">
                  </div>

                  <div class="form-group">
                    <label for="edit-creado-en">Creado En:</label>
                    <input type="text" class="form-control" id="edit-creado-en" name="creado_en" disabled>
                  </div>

                  <div class="form-group">
                    <label for="edit-actualizado-en">Actualizado En:</label>
                    <input type="text" class="form-control" id="edit-actualizado-en" name="actualizado_en" disabled>
                  </div>

                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                  </div>

                </form>

                <script src="../scripts/animacionActualizarDocente.js"></script>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal Importar Profesores (mismo diseño que Estudiantes) -->
        <div class="modal fade" id="importProfesoresModal" tabindex="-1" aria-labelledby="importProfesoresLabel" aria-hidden="true">
          <div class="modal-dialog">
            <form id="formImportarProfesores" action="../php/importar_profesores.php" method="post" enctype="multipart/form-data" class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="importProfesoresLabel">Importar Profesores</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>

              <div class="modal-body">
                <div class="custom-file">
                  <input type="file" class="custom-file-input" id="csvProfesores" name="archivo_csv" accept=".csv" required>
                  <label class="custom-file-label" for="csvProfesores">Ningún archivo seleccionado</label>
                </div>
              </div>

              <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary">Importar</button>
              </div>
            </form>
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
  <script src="../../assets/js/Chart.bundle.min.js"> </script>
  <script src="../../assets/js/Chart.Financial.js"> </script>

  <!-- Page Specific Scripts Finish -->

  <!-- Weeducate core JavaScript -->
  <script src="../../assets/js/framework.js"></script>
  <!-- Page Specific Scripts Start -->
  <script src="../../assets/js/datatables.min.js"> </script>
  <script src="../../assets/js/data-tables.js"> </script>
  <script src="../scripts/cambiarESDataTablesAllProfessor.js"></script>

  <!-- Settings -->
  <script src="../../assets/js/settings.js"></script>

  <!-- Ver nombre del Archivo -->
  <script src="../../pages/scripts/ImportarProfesores.js"></script>
  <script src="../../pages/scripts/alertaImportacionProfesores.js" defer></script>
  <!-- Script para cargar docentes -->
  <script src="../scripts/cargarDocentes.js"></script>
  <script src="../scripts/customFileLabel.js"></script>

</body>
</html>