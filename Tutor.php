<?php require_once './pages/php/auth.php';?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Weeducate</title>
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
  <link rel="icon" type="image/png" sizes="32x32" href="assets/img/weicon/weicon.ico">

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
      <a class="pl-0 ml-0 text-center" href="./Tutor.php "><img src="assets/img/logo/weeducate-4.png" alt="logo"> </a>
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

  <!-- Sidebar Right -->
  <aside id="ms-recent-activity" class="side-nav fixed ms-aside-right ms-scrollable">

    <div class="ms-aside-header">
      <ul class="nav nav-tabs tabs-bordered d-flex nav-justified mb-3" role="tablist">
        <li role="presentation" class="fs-12"><a href="#activityLog" aria-controls="activityLog" class="active"
            role="tab" data-toggle="tab"> Activity Log</a></li>
        <li role="presentation" class="fs-12"><a href="#recentPosts" aria-controls="recentPosts" role="tab"
            data-toggle="tab"> Settings </a></li>
        <li><button type="button" class="close ms-toggler text-center" data-target="#ms-recent-activity"
            data-toggle="slideRight"><span aria-hidden="true">&times;</span></button></li>
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
              <p class="fs-14">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque scelerisque diam non
                nisi semper, ula in sodales vehicula....</p>
            </li>
            <li>
              <div class="ms-btn-icon btn-pill icon btn-success">
                <i class="flaticon-tick-inside-circle"></i>
              </div>
              <h6>Profile Updated</h6>
              <span> <i class="material-icons">event</i>4 March, 2018</span>
              <p class="fs-14">Curabitur purus sem, malesuada eu luctus eget, suscipit sed turpis. Nam pellentesque
                felis vitae justo accumsan, sed semper nisi sollicitudin...</p>
            </li>
            <li>
              <div class="ms-btn-icon btn-pill icon btn-warning">
                <i class="flaticon-alert-1"></i>
              </div>
              <h6>Your payment is due</h6>
              <span> <i class="material-icons">event</i>1 January, 2021</span>
              <p class="fs-14">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque scelerisque diam non
                nisi semper, ula in sodales vehicula....</p>
            </li>
            <li>
              <div class="ms-btn-icon btn-pill icon btn-danger">
                <i class="flaticon-alert"></i>
              </div>
              <h6>Database Error</h6>
              <span> <i class="material-icons">event</i>4 March, 2018</span>
              <p class="fs-14">Curabitur purus sem, malesuada eu luctus eget, suscipit sed turpis. Nam pellentesque
                felis vitae justo accumsan, sed semper nisi sollicitudin...</p>
            </li>
            <li>
              <div class="ms-btn-icon btn-pill icon btn-info">
                <i class="flaticon-information"></i>
              </div>
              <h6>Checkout what's Trending</h6>
              <span> <i class="material-icons">event</i>1 January, 2021</span>
              <p class="fs-14">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque scelerisque diam non
                nisi semper, ula in sodales vehicula....</p>
            </li>
            <li>
              <div class="ms-btn-icon btn-pill icon btn-secondary">
                <i class="flaticon-diamond"></i>
              </div>
              <h6>Your Dashboard is ready</h6>
              <span> <i class="material-icons">event</i>4 March, 2018</span>
              <p class="fs-14">Curabitur purus sem, malesuada eu luctus eget, suscipit sed turpis. Nam pellentesque
                felis vitae justo accumsan, sed semper nisi sollicitudin...</p>
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
        <a class="pl-0 ml-0 text-center navbar-brand mr-0" href="index.php"><img src="assets/img/logo/weeducate-4.png"
            alt="logo"> </a>
      </div>

      <ul class="ms-nav-list ms-inline mb-0" id="ms-nav-options">
        <li class="ms-nav-item ms-search-form pb-0 py-0">
          <form class="ms-form" method="post">
            <div class="ms-form-group my-0 mb-0 has-icon fs-14">
              <input type="search" class="ms-form-input" name="search" placeholder="Search here..." value="">
              <i class="flaticon-search text-disabled"></i>
            </div>
          </form>
        </li>
          
          <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="mailDropdown">
            <li class="dropdown-menu-header">
              <h6 class="dropdown-header ms-inline m-0"><span class="text-disabled">Mail</span></h6><span
                class="badge badge-pill badge-success">3 New</span>
            </li>
            <li class="dropdown-divider"></li>
            <li class="ms-scrollable ms-dropdown-list">
              <a class="media p-2" href="#">
                <div class="ms-chat-status ms-status-offline ms-chat-img mr-2 align-self-center">
                  <img src="assets/img/we-educate/new-student-4.jpg" class="ms-img-round" alt="people">
                </div>
                <div class="media-body">
                  <span>Hey man, looking forward to your new project.</span>
                  <p class="fs-10 my-1 text-disabled"><i class="material-icons">access_time</i> 30 seconds ago</p>
                </div>
              </a>
              <a class="media p-2" href="#">
                <div class="ms-chat-status ms-status-online ms-chat-img mr-2 align-self-center">
                  <img src="assets/img/we-educate/new-student-6.jpg" class="ms-img-round" alt="people">
                </div>
                <div class="media-body">
                  <span>Dear John, I was told you bought Weeducate! Send me your feedback</span>
                  <p class="fs-10 my-1 text-disabled"><i class="material-icons">access_time</i> 28 minutes ago</p>
                </div>
              </a>
              <a class="media p-2" href="#">
                <div class="ms-chat-status ms-status-offline ms-chat-img mr-2 align-self-center">
                  <img src="assets/img/we-educate/new-student-1.jpg" class="ms-img-round" alt="people">
                </div>
                <div class="media-body">
                  <span>How many people are we inviting to the dashboard?</span>
                  <p class="fs-10 my-1 text-disabled"><i class="material-icons">access_time</i> 6 hours ago</p>
                </div>
              </a>
            </li>
            <li class="dropdown-divider"></li>
            <li class="dropdown-menu-footer text-center">
              <a href="apps/email.html">Go to Inbox</a>
            </li>
          </ul>
        </li>
          
          <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="notificationDropdown">
            <li class="dropdown-menu-header">
              <h6 class="dropdown-header ms-inline m-0"><span class="text-disabled">Notifications</span></h6><span
                class="badge badge-pill badge-info">4 New</span>
            </li>
            <li class="dropdown-divider"></li>
            <li class="ms-scrollable ms-dropdown-list">
              <a class="media p-2" href="#">
                <div class="media-body">
                  <span>12 ways to improve your crypto dashboard</span>
                  <p class="fs-10 my-1 text-disabled"><i class="material-icons">access_time</i> 30 seconds ago</p>
                </div>
              </a>
              <a class="media p-2" href="#">
                <div class="media-body">
                  <span>You have newly registered users</span>
                  <p class="fs-10 my-1 text-disabled"><i class="material-icons">access_time</i> 45 minutes ago</p>
                </div>
              </a>
              <a class="media p-2" href="#">
                <div class="media-body">
                  <span>Your account was logged in from an unauthorized IP</span>
                  <p class="fs-10 my-1 text-disabled"><i class="material-icons">access_time</i> 2 hours ago</p>
                </div>
              </a>
              <a class="media p-2" href="#">
                <div class="media-body">
                  <span>An application form has been submitted</span>
                  <p class="fs-10 my-1 text-disabled"><i class="material-icons">access_time</i> 1 day ago</p>
                </div>
              </a>
            </li>
            <li class="dropdown-divider"></li>
            <li class="dropdown-menu-footer text-center">
              <a href="#">View all Notifications</a>
            </li>
          </ul>
        </li>
          
        </li>

        <li class="ms-nav-item ms-nav-user dropdown">
          <a href="#" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <img
              class="ms-user-img ms-img-round float-right" src="assets/img/we-educate/new-student-5.jpg" alt="people">
          </a>
          <ul class="dropdown-menu dropdown-menu-right user-dropdown" aria-labelledby="userDropdown">

            <li class="dropdown-menu-header">
              <h6 class="dropdown-header ms-inline m-0">
                <span class="text-disabled">Bienvenido, <?php echo $_SESSION['nombre'] . ' ' . $_SESSION['apellido']; ?></span>
              </h6>
            </li>

            <li class="dropdown-divider"></li>
            <li class="ms-dropdown-list">
              <a class="media fs-14 p-2" href="pages/prebuilt-pages/user-profile.html"> <span><i
                    class="flaticon-user mr-2"></i> Perfil</span> </a>

            </li>
            <li class="dropdown-divider"></li>
            <li class="dropdown-menu-footer">

            </li>
            <li class="dropdown-menu-footer">
              <a class="media fs-14 p-2 logout-link" href="pages/php/auth.php?logout=true">
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

    <!-- Body Content Wrapper -->
    <div class="ms-content-wrapper">


      <!-- Icon cards Widget -->
      <div class="row">
        <div class="col-xl-3 col-md-6">
          <div class="ms-card card-gradient-success ms-widget ms-infographics-widget">
            <div class="ms-card-body media">
              <div class="media-body">
                <h6>Total Students</h6>
                <p class="ms-card-change"> <i class="material-icons">arrow_upward</i> 450</p>
                <p class="fs-12">48% increase</p>
              </div>
            </div>
            <i class="fa fa-user"></i>
          </div>
        </div>
        <div class="col-xl-3 col-md-6">
          <div class="ms-card card-gradient-secondary ms-widget ms-infographics-widget">
            <div class="ms-card-body media">
              <div class="media-body">
                <h6>Total Fees</h6>
                <p class="ms-card-change"> $30,950</p>
                <p class="fs-12">22% increase</p>
              </div>
            </div>
            <i class="fas fa-dollar-sign"></i>
          </div>
        </div>
        <div class="col-xl-3 col-md-6">
          <div class="ms-card card-gradient-warning ms-widget ms-infographics-widget">
            <div class="ms-card-body media">
              <div class="media-body">
                <h6>Total Student</h6>
                <p class="ms-card-change"> <i class="material-icons">arrow_upward</i> 4567</p>
                <p class="fs-12">78% increase</p>
              </div>
            </div>
            <i class="fa fa-users "></i>
          </div>
        </div>
        <div class="col-xl-3 col-md-6">
          <div class="ms-card card-gradient-info ms-widget ms-infographics-widget">
            <div class="ms-card-body media">
              <div class="media-body">
                <h6>Total Courses</h6>
                <p class="ms-card-change"> 50</p>
                <p class="fs-12">62% increase</p>
              </div>
            </div>
            <i class="fa fa-graduation-cap "></i>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-xl-6 col-md-12 ">
          <div class="ms-panel ">
            <div class="ms-panel-header">
              <h6> Universties Toppers</h6>
              <a class=" fa fa-chevron-down float-right" data-toggle="collapse" data-target="#topper"
                aria-expanded="false" aria-controls="popups"> </a>
            </div>
            <div class="ms-panel-body" id="topper">
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th scope="col">Name</th>
                      <th scope="col">Rank</th>
                      <th scope="col">Persentage</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="ms-table-f-w"> <img src="assets/img/we-educate/topper-1.jpg" alt="people"> Chihoo Hwang
                      </td>
                      <td>1</td>
                      <td>99.9%</td>
                    </tr>
                    <tr>
                      <td class="ms-table-f-w"> <img src="assets/img/we-educate/topper-2.jpg" alt="people"> Ajay
                        Suryavanash </td>
                      <td>2</td>
                      <td>98%</td>
                    </tr>
                    <tr>
                      <td class="ms-table-f-w"> <img src="assets/img/we-educate/topper-3.jpg" alt="people"> Johnson
                      </td>
                      <td>3</td>
                      <td>97%</td>
                    </tr>
                    <tr>
                      <td class="ms-table-f-w"> <img src="assets/img/we-educate/topper-5.jpg" alt="people"> John Doe
                      </td>
                      <td>5</td>
                      <td>93%</td>
                    </tr>
                    <tr>
                      <td class="ms-table-f-w"> <img src="assets/img/we-educate/topper-6.jpg" alt="people"> John Doe
                      </td>
                      <td>6</td>
                      <td>92%</td>
                    </tr>
                    <tr>
                      <td class="ms-table-f-w"> <img src="assets/img/we-educate/topper-2.jpg" alt="people"> John Doe<br>
                      </td>
                      <td>7</td>
                      <td>91%</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-6 col-md-12">
          <div class="ms-panel pb-8">
            <div class="ms-panel-header">
              <h6>Course Table</h6>
              <a class=" fa fa-chevron-down float-right" data-toggle="collapse" data-target="#course"
                aria-expanded="false" aria-controls="popups"> </a>
            </div>
            <div class="ms-panel-body" id="course">
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>

                      <th scope="col">#</th>
                      <th scope="col">Courses</th>
                      <th scope="col">Professors</th>
                      <th scope="col">Fees</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <th scope="row">1</th>
                      <td>Java</td>
                      <td>John Kery</td>
                      <td>130$</td>

                    </tr>
                    <tr>
                      <th scope="row">2</th>
                      <td>Php</td>
                      <td>Johnson</td>
                      <td>140$</td>

                    </tr>
                    <tr>
                      <th scope="row">3</th>
                      <td>Anguler</td>
                      <td>Kevin Owens</td>
                      <td>120$</td>

                    </tr>
                    <tr>
                      <th scope="row">4</th>
                      <td>React JS</td>
                      <td>Kety Perey</td>
                      <td>170$</td>

                    </tr>
                    <tr>
                      <th scope="row">5</th>
                      <td>.Net</td>
                      <td>Kevin shai</td>
                      <td>110$</td>

                    </tr>
                    <tr>
                      <th scope="row">6</th>
                      <td>Javascript</td>
                      <td>John Kesy</td>
                      <td>90$</td>

                    </tr>
                    <tr>
                      <th scope="row">7</th>
                      <td>Kotline</td>
                      <td>Alura</td>
                      <td>180$</td>

                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>


      <div class="row">
        <div class="col-xl-6 col-md-12 ">

          <div class="ms-panel">

            <div class="ms-panel-header ">
              <h6>University Results</h6>
              <a class=" fa fa-chevron-down float-right" data-toggle="collapse" data-target="#graph"
                aria-expanded="false" aria-controls="popups"> </a>
            </div>

            <div class="ms-panel-body " id="graph">
              <canvas id="bar-chart"></canvas>
            </div>


          </div>

        </div>
        <div class="col-xl-6 col-md-12">
          <div class="ms-panel  height-84 ">
            <div class="ms-panel-header">
              <h6>Excellent Toppers</h6>
            </div>
            <div class="ms-panel-body">
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th scope="col">Name</th>
                      <th scope="col">Rank</th>
                      <th scope="col">Persentage</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="ms-table-f-w"> Chihoo Hwang </td>
                      <td>1</td>
                      <td>99.9%</td>
                    </tr>
                    <tr>
                      <td class="ms-table-f-w"> Ajay Suryavanash </td>
                      <td>2</td>
                      <td>98%</td>
                    </tr>
                    <tr>
                      <td class="ms-table-f-w"> Johnson </td>
                      <td>3</td>
                      <td>97%</td>
                    </tr>

                    <tr>
                      <td class="ms-table-f-w"> Kalvish </td>
                      <td>4</td>
                      <td>96%</td>
                    </tr>


                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>


      <div class="row">
        <div class="col-xl-12 col-md-12 ">
          <div class="ms-panel ">
            <div class="ms-panel-header">
              <h6>New Student List</h6>
              <a class=" fa fa-chevron-down float-right" data-toggle="collapse" data-target="#list"
                aria-expanded="false" aria-controls="popups"> </a>
            </div>
            <div class="ms-panel-body" id="list">
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th scope="col">Name</th>
                      <th scope="col">Head Of Departements</th>
                      <th scope="col">Date of Admit</th>
                      <th scope="col">fees</th>
                      <th scope="col">Branch</th>

                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="ms-table-f-w"> <img src="assets/img/we-educate/topper-1.jpg" alt="people"> Chihoo Hwang
                      </td>
                      <td>Joan</td>
                      <td>27/5/2021</td>
                      <td>paid</td>
                      <td>Meachnical</td>
                    </tr>
                    <tr>
                      <td class="ms-table-f-w"> <img src="assets/img/we-educate/topper-3.jpg" alt="people"> Ajay
                        Suryavanash </td>
                      <td>Moxely</td>
                      <td>11/6/2021</td>
                      <td>Unpaid</td>
                      <td>Electrical</td>
                    </tr>
                    <tr>
                      <td class="ms-table-f-w"> <img src="assets/img/we-educate/topper-5.jpg" alt="people"> Johny martin
                      </td>
                      <td>Rich Flair</td>
                      <td>26/5/2021</td>
                      <td>paid</td>
                      <td>Computer</td>
                    </tr>
                    <tr>
                      <td class="ms-table-f-w"> <img src="assets/img/we-educate/topper-6.jpg" alt="people"> Noxin Lee
                      </td>
                      <td>Tony</td>
                      <td>20/7/2021</td>
                      <td>unpaid</td>
                      <td>Civil</td>
                    </tr>
                    <tr>
                      <td class="ms-table-f-w"> <img src="assets/img/we-educate/topper-2.jpg" alt="people">Jackey</td>
                      <td>Bilgates</td>
                      <td>11/4/2021</td>
                      <td>paid</td>
                      <td>Computer</td>
                    </tr>
                    <tr>
                      <td class="ms-table-f-w"> <img src="assets/img/we-educate/topper-5.jpg" alt="people">Rocky </td>
                      <td>Stephan</td>
                      <td>24/6/2021</td>
                      <td>unpaid</td>
                      <td>Electronics</td>
                    </tr>

                  </tbody>
                </table>
              </div>
            </div>
          </div>
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

</body>

</html>