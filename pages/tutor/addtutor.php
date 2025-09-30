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
  <div class="ms-aside-overlay ms-overlay-right ms-toggler" data-target="#ms-recent-activity" data-toggle="slideRight"></div>

  <!-- Sidebar Navigation Left -->
  <aside id="ms-side-nav" class="side-nav fixed ms-aside-scrollable ms-aside-left">

    <!-- Logo -->
    <div class="logo-sn ms-d-block-lg">
      <a class="pl-0 ml-0 text-center" href="../../index.php"><img src="../../assets/img/LogoSchoolCare.png" alt="logo">  </a>
    </div>

    <!-- Navigation -->
    <ul class="accordion ms-main-aside fs-14" id="side-nav-accordion">
        <!-- Dashboard -->
        <li class="menu-item">
            <a href="#" class="has-chevron" data-toggle="collapse" data-target="#dashboard" aria-expanded="false" aria-controls="dashboard">
               <span><i class="material-icons fs-16">dashboard</i>Dashboard </span>
             </a>
            <ul id="dashboard" class="collapse" aria-labelledby="dashboard" data-parent="#side-nav-accordion">
              <li> <a href="../../index.php">SchoolCare</a> </li>
              
            </ul>
        </li>
        <!-- /Dashboard -->
        
        <!--Proessors Start-->
        <li class="menu-item">
            <a href="#" class="has-chevron" data-toggle="collapse" data-target="#professor" aria-expanded="false" aria-controls="dashboard">
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
            <a href="#" class="has-chevron" data-toggle="collapse" data-target="#courses" aria-expanded="false" aria-controls="dashboard">
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
            <a href="#" class="has-chevron" data-toggle="collapse" data-target="#student" aria-expanded="false" aria-controls="dashboard">
               <span><i class="fa fa-users fs-16"></i>Estudiantes</span>
             </a>
            <ul id="student" class="collapse" aria-labelledby="courses" data-parent="#side-nav-accordion">
              <li> <a href="../student/studentadd.php">Añadir Estudiante</a> </li>
               <li> <a href="../student/studenttable.php">Tabla de Estudiantes</a> </li>
                  </ul>
        </li>
        <!-- /Student End--->
        
        
        <!--tutor Start-->
        <li class="menu-item">
            <a href="#" class="has-chevron" data-toggle="collapse" data-target="#tutor" aria-expanded="false" aria-controls="dashboard">
               <span><i class="fa fa-user-circle fs-16"></i>Tutores</span>
             </a>
            <ul id="tutor" class="collapse" aria-labelledby="tutor" data-parent="#side-nav-accordion">
              <li> <a href="../tutor/addtutor.php">Añadir Tutor</a> </li>
               <li> <a href="../tutor/tutorprofile.php">Tabla de Tutores</a></li>
                 
            </ul>
        </li>
        <!-- /tutor End--->
        
        
        
        <!--Fees Start-->
        <li class="menu-item">
            <a href="#" class="has-chevron" data-toggle="collapse" data-target="#fees" aria-expanded="false" aria-controls="dashboard">
                <span><i class="fas fa-dollar-sign"></i>Orden de Pago</span>
             </a>
            <ul id="fees" class="collapse" aria-labelledby="tutor" data-parent="#side-nav-accordion">
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

    <div class="ms-aside-header">
      <ul class="nav nav-tabs tabs-bordered d-flex nav-justified mb-3" role="tablist">
        <li role="presentation" class="fs-12"><a href="#activityLog" aria-controls="activityLog" class="active" role="tab" data-toggle="tab"> Activity Log</a></li>
        <li role="presentation" class="fs-12"><a href="#recentPosts" aria-controls="recentPosts" role="tab" data-toggle="tab"> Settings </a></li>
        <li><button type="button" class="close ms-toggler text-center" data-target="#ms-recent-activity" data-toggle="slideRight"><span aria-hidden="true">&times;</span></button></li>
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
               <p class="fs-14">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque scelerisque diam non nisi semper, ula in sodales vehicula....</p>
             </li>
             <li>
               <div class="ms-btn-icon btn-pill icon btn-success">
                 <i class="flaticon-tick-inside-circle"></i>
               </div>
               <h6>Profile Updated</h6>
               <span> <i class="material-icons">event</i>4 March, 2018</span>
               <p class="fs-14">Curabitur purus sem, malesuada eu luctus eget, suscipit sed turpis. Nam pellentesque felis vitae justo accumsan, sed semper nisi sollicitudin...</p>
             </li>
             <li>
               <div class="ms-btn-icon btn-pill icon btn-warning">
                 <i class="flaticon-alert-1"></i>
               </div>
               <h6>Your payment is due</h6>
               <span> <i class="material-icons">event</i>1 January, 2021</span>
               <p class="fs-14">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque scelerisque diam non nisi semper, ula in sodales vehicula....</p>
             </li>
             <li>
               <div class="ms-btn-icon btn-pill icon btn-danger">
                 <i class="flaticon-alert"></i>
               </div>
               <h6>Database Error</h6>
               <span> <i class="material-icons">event</i>4 March, 2018</span>
               <p class="fs-14">Curabitur purus sem, malesuada eu luctus eget, suscipit sed turpis. Nam pellentesque felis vitae justo accumsan, sed semper nisi sollicitudin...</p>
             </li>
             <li>
               <div class="ms-btn-icon btn-pill icon btn-info">
                 <i class="flaticon-information"></i>
               </div>
               <h6>Checkout what's Trending</h6>
               <span> <i class="material-icons">event</i>1 January, 2021</span>
               <p class="fs-14">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque scelerisque diam non nisi semper, ula in sodales vehicula....</p>
             </li>
             <li>
               <div class="ms-btn-icon btn-pill icon btn-secondary">
                 <i class="flaticon-diamond"></i>
               </div>
               <h6>Your Dashboard is ready</h6>
               <span> <i class="material-icons">event</i>4 March, 2018</span>
               <p class="fs-14">Curabitur purus sem, malesuada eu luctus eget, suscipit sed turpis. Nam pellentesque felis vitae justo accumsan, sed semper nisi sollicitudin...</p>
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
        <a class="pl-0 ml-0 text-center navbar-brand mr-0" href="../../index.php"><img src="../../assets/img/LogoSchoolCare.png" alt="logo"> </a>
      </div>

      <ul class="ms-nav-list ms-inline mb-0" id="ms-nav-options">
        <li class="ms-nav-item ms-search-form pb-0 py-0">
          <form class="ms-form" method="post">
          </form>
        </li>
        <li class="ms-nav-item ms-nav-user dropdown">
          <a href="#"  id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <img class="ms-user-img ms-img-round float-right" src="../../assets/img/we-educate/new-student-5.jpg" alt="people"> </a>
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
              <a class="media fs-14 p-2" href="../php/logout.php"> <span><i class="flaticon-shut-down mr-2"></i>Cerrar Sesión</span> </a>
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
              <li class="breadcrumb-item active" aria-current="page">Tutores</li>
                <li class="breadcrumb-item active" aria-current="page">Añadir Tutor</li>
            </ol>
          </nav>
        </div>
        </div>
    

<div class="col-lg-12">
  <div class="ms-panel">

    <div class="ms-panel-header">
      <h6>Añadir Nuevo Tutor</h6>
    </div>

    <div class="ms-panel-body">
      
      <form id="registerForm" novalidate>
        <div class="row">
          <!-- NOMBRE -->
          <div class="col-lg-6 mb-3">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Nombre/s" required>
          </div>
          <!-- APELLIDO -->
          <div class="col-lg-6 mb-3">
            <label for="apellido">Apellido:</label>
            <input type="text" id="apellido" name="apellido" class="form-control" placeholder="Apellido/s" required>
          </div>
          <!-- TELÉFONO -->
          <div class="col-lg-6 mb-3">
            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" class="form-control" placeholder="Ej. 555-000-0000" required>
          </div>
          <!-- CORREO -->
          <div class="col-lg-6 mb-3">
            <label for="correo">Correo:</label>
            <input type="email" id="correo" name="correo" class="form-control" placeholder="nombre@ejemplo.com" required>
          </div>
          <!-- ESTUDIANTE -->
          <div class="col-lg-12 mb-3">
            <label for="estudiante_id">Estudiante:</label>
            <select id="estudiante_id" name="estudiante_id" class="form-control" required>
              <option value="" disabled selected>Seleccionar estudiante</option>
            </select>
          </div>
          <!-- DIRECCIÓN -->
          <div class="col-lg-12 mb-3">
            <label for="direccion">Dirección:</label>
            <textarea id="direccion" name="direccion" class="form-control" rows="3" required></textarea>
          </div>
          <!-- CONTRASEÑAS -->
          <div class="col-lg-6 mb-3">
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" class="form-control" required>
          </div>
          <div class="col-lg-6 mb-3">
            <label for="password2">Confirmar contraseña:</label>
            <input type="password" id="password2" name="password2" class="form-control" required>
          </div>
          <!-- BOTONES -->
          <div class="col-12 text-center">
            <button type="submit" class="btn btn-warning mr-2">Crear</button>
            <button type="reset" class="btn btn-outline-warning">Cancelar</button>
          </div>
        </div>
      </form>
    </div> <!-- /.ms-panel-body -->

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

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="../scripts/registrar-tutor.js"></script>

  <!-- Page Specific Scripts Start -->
  <script src="../../assets/js/slick.min.js"> </script>
  <script src="../../assets/js/moment.js"> </script>
  <script src="../../assets/js/jquery.webticker.min.js"> </script>
 

  <!-- Weeducate core JavaScript -->
  <script src="../../assets/js/framework.js"></script>

  <!-- Settings -->
  <script src="../../assets/js/settings.js"></script>

  <script>
    // tras cargar lista de estudiantes (igual que antes)…

    document.addEventListener('DOMContentLoaded', () => {
      // Carga dinámica de estudiantes
      fetch('../php/opciones_estudiantes.php')
        .then(r => r.ok ? r.text() : Promise.reject(r.status))
        .then(html => document.getElementById('estudiante_id').insertAdjacentHTML('beforeend', html))
        .catch(err => console.error('No se pudieron cargar estudiantes:', err));

      // Validación + envío AJAX
      const form = document.getElementById('registerForm');
      const regexName = /^[A-Za-zÁÉÍÓÚÑáéíóúñ ]{2,}$/;
      const regexPhone = /^[0-9\-\+\s]{7,}$/;

      form.addEventListener('submit', e => {
        e.preventDefault();

        // 1) Validaciones cliente
        const f = new FormData(form);
        let err = null;
        if (!regexName.test(f.get('nombre').trim()))         err = 'Nombre inválido';
        else if (!regexName.test(f.get('apellido').trim()))  err = 'Apellido inválido';
        else if (!regexPhone.test(f.get('telefono').trim()))err = 'Teléfono inválido';
        else if (!f.get('correo').includes('@'))             err = 'Correo inválido';
        else if (!f.get('estudiante_id'))                    err = 'Debes seleccionar un estudiante';
        else if (f.get('direccion').trim().length < 5)       err = 'Dirección muy corta';
        else if (f.get('password').length < 6)               err = 'La contraseña debe tener al menos 6 caracteres';
        else if (f.get('password') !== f.get('password2'))   err = 'Las contraseñas no coinciden';

        if (err) {
          Swal.fire({ icon: 'error', title: err, toast: true, position: 'top-end', timer: 2000, showConfirmButton: false });
          return;
        }

        // 2) Envío AJAX
        fetch('../php/registrar_tutor.php', {
          method: 'POST',
          body: f
        })
        .then(r => r.json())
        .then(json => {
          if (json.success) {
            Swal.fire({
              icon: 'success',
              title: json.message || 'Tutor creado',
              toast: true,
              position: 'top-end',
              timer: 2000,
              showConfirmButton: false
            }).then(() => form.reset());
          } else {
            Swal.fire({ icon: 'error', title: json.message, toast: true, position: 'top-end', timer: 2000, showConfirmButton: false });
          }
        })
        .catch(() => {
          Swal.fire({ icon: 'error', title: 'Error de conexión', toast: true, position: 'top-end', timer: 2000, showConfirmButton: false });
        });
      });
    });
    </script>

</body>

</html>
