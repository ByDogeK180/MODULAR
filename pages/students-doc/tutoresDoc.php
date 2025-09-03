<?php include '../php/auth.php'; ?>
<script>
  const userRol = <?php echo $_SESSION['rol'] ?? 'null'; ?>;
</script>




<!DOCTYPE html>
<html lang="en">

<head>

  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Schoolcare</title>
  <!-- Iconic Fonts -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="../../vendors/iconic-fonts/font-awesome/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../vendors/iconic-fonts/flat-icons/flaticon.css">
  
     <link rel="stylesheet" href="../../assets/css/datatables.min.css">
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
  <div class="ms-aside-overlay ms-overlay-right ms-toggler" data-target="#ms-recent-activity" data-toggle="slideRight"></div>

   <!-- Sidebar Navigation Left -->
  <aside id="ms-side-nav" class="side-nav fixed ms-aside-scrollable ms-aside-left">

    <!-- Logo -->
    <div class="logo-sn ms-d-block-lg">
      <a class="pl-0 ml-0 text-center" href="../../Docentes.php"><img src="../../assets/img/LogoSchoolCare.png" alt="logo">  </a>
    </div>

    <!-- Navigation -->
    <ul class="accordion ms-main-aside fs-14" id="side-nav-accordion">
        <!-- Dashboard -->
        <li class="menu-item">
            <a href="#" class="has-chevron" data-toggle="collapse" data-target="#dashboard" aria-expanded="false" aria-controls="dashboard">
               <span><i class="material-icons fs-16">dashboard</i>Dashboard </span>
             </a>
            <ul id="dashboard" class="collapse" aria-labelledby="dashboard" data-parent="#side-nav-accordion">
              <li> <a href="../../Docentes.php">Schoolcare</a> </li>
              
            </ul>
        </li>
        <!-- /Dashboard -->
        
        <!--Proessors Start-->
        <li class="menu-item">
            <a href="#" class="has-chevron" data-toggle="collapse" data-target="#professor" aria-expanded="false" aria-controls="dashboard">
               <span><i class="fa fa-user fs-16"></i>Estudiantes</span>
             </a>
            <ul id="professor" class="collapse" aria-labelledby="dashboard" data-parent="#side-nav-accordion">
              <li> <a href="studendoc.php">Todos los estudiantes</a> </li>
               <li> <a href="asistencias.php">Asistencias</a> </li>
                 <li> <a href="scoredoc.php">Acerca los estudiantes</a> </li>
                 <li> <a href="formulario_incidentes.php">Recordatorios</a> </li>
                 <li> <a href="recordatorios_docente.php">Todos los recordatorios</a> </li>
            </ul>
        </li>
        <!-- /Proessors End--->
        
         <!--Courses Start-->
        <li class="menu-item">
      <a href="#" class="has-chevron" data-toggle="collapse" data-target="#courses" aria-expanded="false" aria-controls="dashboard">
      <span><i class="fa fa-graduation-cap fs-16"></i>Materias</span>
       </a>
      <ul id="courses" class="collapse" aria-labelledby="courses" data-parent="#side-nav-accordion">
        <li> <a href="allcoursesDoc.php">Acerca de las Materias</a> </li>
      </ul>
        </li>
        <!-- /Courses End--->

        <!--tutor Start-->
        <li class="menu-item">
            <a href="#" class="has-chevron" data-toggle="collapse" data-target="#staff" aria-expanded="false" aria-controls="dashboard">
               <span><i class="fa fa-user-circle fs-16"></i>Tutores</span>
             </a>
            <ul id="staff" class="collapse" aria-labelledby="staff" data-parent="#side-nav-accordion">
              <li> <a href="tutoresDoc.php">Tabla de Tutores</a> </li>

            </ul>
        </li>
        <!-- /tutor End--->
        
        <!--Holiday Start-->
        <li class="menu-item">
          <a href="../holidays/holiday.html">
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
        <a class="pl-0 ml-0 text-center navbar-brand mr-0" href="../../index.php"><img src="../../assets/img/logo/weeducate-4.png" alt="logo"> </a>
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
          <a href="#"  id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <img class="ms-user-img ms-img-round float-right" src="../../assets/img/we-educate/new-student-5.jpg" alt="people"> </a>
          <ul class="dropdown-menu dropdown-menu-right user-dropdown" aria-labelledby="userDropdown">
            <li class="dropdown-menu-header">
              <span class="text-disabled">Welcome, <?php echo $_SESSION['correo']; ?></span>
            </li>
            <li class="dropdown-divider"></li>
            <li class="ms-dropdown-list">
              <a class="media fs-14 p-2" href="pages/prebuilt-pages/user-profile.html"> <span><i class="flaticon-user mr-2"></i> Profile</span> </a>
            </li>
            <li class="dropdown-divider"></li>
            <li class="dropdown-menu-footer">
            </li>
            <li class="dropdown-menu-footer">
                    <a class="media fs-14 p-2" href="../../pages/prebuilt-pages/default-login.html"> <span><i
                    class="flaticon-shut-down mr-2"></i> Logout</span> </a>
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
<!-- Body Content Wrapper -->
<div class="ms-content-wrapper">
  <div class="row">

    <div class="col-md-12">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb pl-0">
          <li class="breadcrumb-item"><a href="../../Docentes.php"><i class="material-icons">home</i> Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">Tutores</li>
          <li class="breadcrumb-item active" aria-current="page">Tabla Tutores</li>
        </ol>
      </nav>
    </div>

<select id="filtroGrupo" class="form-control mb-3" style="width: auto;">
  <option value="">Todos los grupos</option>
</select>



<div class="col-lg-12">
  <div class="ms-panel">
    <!-- Header -->
    <div class="ms-panel-header d-flex justify-content-between align-items-center">
      <h6>Perfiles de Tutores</h6>

      
    </div>

    <!-- Tabla -->
    <div class="ms-panel-body">
      <div class="table-responsive">
        <table id="tablaTutores" class="table">
          <thead>
            <tr>
              <th>Nombre</th>
              <th>Apellido</th>
              <th>Teléfono</th>
              <th>Correo</th>
              <th>Dirección</th>
            </tr>
          </thead>
          <tbody id="tutores-lista">
            <!-- Los tutores se insertan dinámicamente aquí -->
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>


  </main>

  <!-- Quick bar -->
  <aside id="ms-quick-bar" class="ms-quick-bar fixed ms-d-block-lg">

  </aside>

  <!-- MODALS -->

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
  
  <!-- Weeducate core JavaScript -->
  <script src="../../assets/js/framework.js"></script>
  <script src="../../assets/js/datatables.min.js"> </script>
  <script src="../../assets/js/data-tables.js"> </script>
  <!-- Settings -->
  <script src="../../pages/scripts/cargarTutoresDocente.js"> </script>


  <!-- Importar CSV -->
  <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form id="formImportarTutores" action="../php/importar_tutores.php" method="post" enctype="multipart/form-data" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="importModalLabel">Importar Tutores</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="csvTutores" class="form-label">Selecciona archivo CSV</label>
            <input class="form-control" type="file" id="csvTutores" name="file" accept=".csv" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary">Importar</button>
        </div>
      </form>
    </div>
  </div>


</body>

</html>