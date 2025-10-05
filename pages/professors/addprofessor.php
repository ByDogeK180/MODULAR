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
  
  <!-- Bootstrap core CSS -->
  <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
  <!-- jQuery UI -->
  <link href="../../assets/css/jquery-ui.min.css" rel="stylesheet">
  <!-- Page Specific CSS (Slick Slider.css) -->
  <link href="../../assets/css/slick.css" rel="stylesheet">
  <!-- Weeducate styles -->
  <link href="../../assets/css/style.css" rel="stylesheet">
  <link href="../../assets/css/styleAddProfessor.css" rel="stylesheet">
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Favicon -->
  <link rel="icon" type="image/png" sizes="32x32" href="../../assets/img/LogoSchoolCare.png">

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

    <!-- Logo -->
    <div class="logo-sn ms-d-block-lg">
      <a class="pl-0 ml-0 text-center" href="../../index.php"><img src="../../assets/img/LogoSchoolCare.png" alt="logo"></a>
    </div>

    <!-- Navigation -->
    <ul class="accordion ms-main-aside fs-14" id="side-nav-accordion">
        <!-- Dashboard -->
        <li class="menu-item">
            <a href="#" class="has-chevron" data-toggle="collapse" data-target="#dashboard" aria-expanded="false" aria-controls="dashboard">
               <span><i class="material-icons fs-16">dashboard</i>Dashboard</span>
             </a>
            <ul id="dashboard" class="collapse" aria-labelledby="dashboard" data-parent="#side-nav-accordion">
              <li><a href="../../index.php">SchoolCare</a></li>
            </ul>
        </li>
        
        <!--Proessors Start-->
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
        
         <!--Courses Start-->
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
        
        <!--Student Start-->
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
        
        <!--tutor Start-->
        <li class="menu-item">
            <a href="#" class="has-chevron" data-toggle="collapse" data-target="#staff" aria-expanded="false" aria-controls="dashboard">
               <span><i class="fa fa-user-circle fs-16"></i>Tutores</span>
             </a>
            <ul id="staff" class="collapse" aria-labelledby="staff" data-parent="#side-nav-accordion">
              <li><a href="../tutor/addtutor.php">Añadir Tutor</a></li>
              <li><a href="../tutor/tutorprofile.php">Tabla de Tutores</a></li>
            </ul>
        </li>
        
        <!--Fees Start-->
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
        
        <!--Holiday Start-->
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
        <a class="pl-0 ml-0 text-center navbar-brand mr-0" href="../../index.php"><img src="../../assets/img/LogoSchoolCare.png" alt="logo"></a>
      </div>

      <ul class="ms-nav-list ms-inline mb-0" id="ms-nav-options">
        <li class="ms-nav-item ms-search-form pb-0 py-0">
          <form class="ms-form" method="post"></form>
        </li>
        <li class="ms-nav-item ms-nav-user dropdown">
          <a href="#" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <img class="ms-user-img ms-img-round float-right" src="../../assets/img/we-educate/new-student-5.jpg" alt="people">
          </a>
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
              <a class="media fs-14 p-2" href="../php/logout.php"><span><i class="flaticon-shut-down mr-2"></i> Logout</span></a>
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
      <div class="row">

        <div class="col-md-12">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb pl-0">
              <li class="breadcrumb-item"><a href="../../index.php"><i class="material-icons">home</i> Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Profesor</li>
              <li class="breadcrumb-item active" aria-current="page">Añadir profesor</li>
            </ol>
          </nav>
        </div>
          
        <div class="col-lg-12">
          <div class="ms-panel">
            <div class="ms-panel-header">
              <h6>Añadir Profesor</h6>
            </div>

            <div class="ms-panel-body">
              <form id="registerForm" action="../php/registrar_profesor.php" method="POST" enctype="multipart/form-data" novalidate>
                <div class="row">

                  <!-- Nombre -->
                  <div class="col-lg-6 mb-3">
                    <label for="nombre" class="form-label fw-semibold">Nombre:</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre/s" required>
                    <div class="invalid-feedback">Sólo letras y espacios, mínimo 2 caracteres.</div>
                  </div>

                  <!-- Apellidos -->
                  <div class="col-lg-6 mb-3">
                    <label for="apellido" class="form-label fw-semibold">Apellidos:</label>
                    <input type="text" class="form-control" id="apellido" name="apellido" placeholder="Apellido/s" required>
                    <div class="invalid-feedback">Sólo letras y espacios, mínimo 2 caracteres.</div>
                  </div>

                  <!-- Correo -->
                  <div class="col-lg-6 mb-3">
                    <label for="email" class="form-label fw-semibold">Correo:</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Nombre@ejemplo.com" required>
                    <div class="invalid-feedback">Debe ser un correo electrónico válido.</div>
                  </div>

                  <!-- Contraseña -->
                  <div class="col-lg-6 mb-3">
                    <label for="contraseña" class="form-label fw-semibold">Contraseña:</label>
                    <input type="password" class="form-control" id="contraseña" name="contraseña" placeholder="Contraseña" required>
                    <div class="invalid-feedback">Mínimo 8 caracteres, al menos una mayúscula y un número.</div>
                  </div>

                  <!-- Confirmar contraseña -->
                  <div class="col-lg-6 mb-3">
                    <label for="confirmar_contraseña" class="form-label fw-semibold">Confirmar contraseña:</label>
                    <input type="password" class="form-control" id="confirmar_contraseña" name="confirmar_contraseña" placeholder="Repetir contraseña" required>
                    <div class="invalid-feedback">Las contraseñas no coinciden.</div>
                  </div>

                  <!-- Rol (oculto) -->
                  <div class="col-lg-6 mb-3">
                    <label for="rol" class="form-label fw-semibold">Rol:</label>
                    <select class="form-control" id="rol" name="rol" disabled required>
                      <option value="1" selected>Docente</option>
                    </select>
                    <input type="hidden" name="rol" value="1">
                  </div>

                  <!-- Puesto -->
                  <div class="col-lg-6 mb-3">
                    <label for="puesto" class="form-label fw-semibold">Puesto:</label>
                    <select class="form-control" id="puesto" name="puesto" required>
                      <option value="">Seleccione un puesto</option>
                      <option value="profesor">Profesor</option>
                      <option value="coordinador">Coordinador</option>
                    </select>
                    <div class="invalid-feedback">Debe seleccionar un puesto.</div>
                  </div>

                  <!-- Género -->
                  <div class="col-lg-6 mb-3">
                    <label for="genero" class="form-label fw-semibold">Género:</label>
                    <select class="form-control" id="genero" name="genero" required>
                      <option value="">Seleccione género</option>
                      <option value="masculino">Masculino</option>
                      <option value="femenino">Femenino</option>
                      <option value="otro">Otro</option>
                    </select>
                    <div class="invalid-feedback">Debe seleccionar un género.</div>
                  </div>

                  <!-- Teléfono -->
                  <div class="col-lg-6 mb-3">
                    <label for="telefono" class="form-label fw-semibold">Número de teléfono:</label>
                    <input type="text" class="form-control" id="telefono" name="telefono" placeholder="#" required>
                    <div class="invalid-feedback">Sólo dígitos, entre 7 y 15 caracteres.</div>
                  </div>

                  <!-- Nacimiento -->
                  <div class="col-lg-6 mb-3">
                    <label for="nacimiento" class="form-label fw-semibold">Nacimiento:</label>
                    <input type="date" class="form-control" id="nacimiento" name="nacimiento" required>
                    <div class="invalid-feedback">Fecha inválida.</div>
                  </div>

                  <!-- Salario -->
                  <div class="col-lg-6 mb-3">
                    <label for="salario" class="form-label fw-semibold">Salario:</label>
                    <input type="text" class="form-control" id="salario" name="salario" placeholder="$" required>
                    <div class="invalid-feedback">Debe ser un número válido mayor que 0.</div>
                  </div>

                  <!-- Dirección -->
                  <div class="col-lg-12 mb-3">
                    <label for="direccion" class="form-label fw-semibold">Dirección:</label>
                    <textarea class="form-control" id="direccion" name="direccion" rows="5" required></textarea>
                    <div class="invalid-feedback">Mínimo 5 caracteres.</div>
                  </div>

                  <!-- Imagen (centrada con preview y limpiar) -->
                  <div class="col-12 d-flex justify-content-center">
                    <div class="file-center">
                      <label class="form-label fw-semibold d-block text-center" for="imagen">Seleccione la imagen:</label>
                      <div class="card shadow-sm border-0">
                        <div class="card-body py-3">
                          <div class="input-group">
                            <div class="custom-file">
                              <input type="file"
                                     class="custom-file-input"
                                     id="imagen"
                                     name="imagen"
                                     accept="image/png,image/jpeg,image/webp,image/gif"
                                     required>
                              <label class="custom-file-label" for="imagen">Seleccionar imagen…</label>
                              <!-- feedback pegado al input -->
                              <div class="invalid-feedback">
                                Debe subir un archivo de imagen (jpg, png, webp o gif) menor a 2 MB.
                              </div>
                            </div>
                            <div class="input-group-append">
                              <button class="btn btn-outline-secondary" type="button" id="btnLimpiarImagen">Limpiar</button>
                            </div>
                          </div>
                          <small class="form-text text-muted mt-2 text-center">
                            Formatos: JPG, PNG, WEBP o GIF. Tamaño máx. 2 MB.
                          </small>
                          <div class="mt-3 d-flex justify-content-center align-items-center">
                            <img id="previewImagen" class="img-thumbnail mr-3 d-none" style="max-height:120px;" alt="Vista previa">
                            <div id="infoImagen" class="text-muted small"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Botones (centrados) -->
                  <div class="col-12 text-center my-4">
                    <button type="submit" class="btn btn-warning">Crear</button>
                    <button type="button" class="btn btn-outline-warning ml-3" id="btnCancelar">Cancelar</button>
                  </div>

                </div>
              </form>
            </div>
          </div>
        </div>

      </div>
    </div>

  </main>
  <!-- Quick bar -->
  <aside id="ms-quick-bar" class="ms-quick-bar fixed ms-d-block-lg"></aside>

  <!-- SCRIPTS -->
  <script src="../../assets/js/jquery-3.3.1.min.js"></script>
  <script src="../../assets/js/popper.min.js"></script>
  <script src="../../assets/js/bootstrap.min.js"></script>
  <script src="../../assets/js/perfect-scrollbar.js"></script>
  <script src="../../assets/js/jquery-ui.min.js"></script>

  <script src="../../assets/js/slick.min.js"></script>
  <script src="../../assets/js/moment.js"></script>
  <script src="../../assets/js/jquery.webticker.min.js"></script>
  <script src="../../assets/js/Chart.bundle.min.js"></script>
  <script src="../../assets/js/Chart.Financial.js"></script>

  <script src="../../assets/js/framework.js"></script>
  <script src="../../assets/js/settings.js"></script>
  <script src="../scripts/animacionAgregarProfesor.js"></script>

  <!-- JS para preview/limpiar/validación del selector de imagen -->
  <script src="../scripts/limpiarPreviewAddprofessor.js"></script>

</body>
</html>
