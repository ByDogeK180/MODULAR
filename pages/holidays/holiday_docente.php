<?php 
include '../php/auth.php';
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
  <title>SchoolCare</title>
  <!-- Iconic Fonts -->
  <link href=" https://fullcalendar.io/">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="../../vendors/iconic-fonts/font-awesome/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../vendors/iconic-fonts/flat-icons/flaticon.css">
  <!-- Bootstrap core CSS -->
  <link rel="stylesheet" href="../../assets/css/datatables.min.css">
  <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
  <!-- jQuery UI -->
  <link href="../../assets/css/jquery-ui.min.css" rel="stylesheet">
  <!-- Page Specific CSS (Slick Slider.css) -->
  <link href="../../assets/css/slick.css" rel="stylesheet">
  <link rel="icon" type="image/png" sizes="32x32" href="../../assets/img/LogoSchoolCare.png">
  <!-- Weeducate styles -->
  <link href="../../assets/css/style.css" rel="stylesheet">
  <link href="../../assets/css/calendarioadmin.css" rel="stylesheet">
  <!-- FullCalendar CSS -->
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">

  <!-- Estilos mínimos para colores y botones del calendario -->
  <style>
    .fc .fc-event { border:0; border-radius:10px; font-weight:600; }
    .fc .fc-event-incidente { background:#dc3545; color:#fff; }     /* avisos/incidentes */
    .fc .fc-event-festivo   { background:#0dcaf0; color:#00323a; }  /* festivos JSON */
    .fc .fc-event-periodo   { background:#0d6efd; opacity:.25; }    /* periodos fondo */
    .fc .fc-event-ciclo     { background:#6f42c1; opacity:.18; }    /* ciclos fondo */
    /* darle look a los botones (Bootstrap-ish) */
    .fc .fc-button { border:0; transition:transform .15s ease, box-shadow .15s ease; }
    .fc .fc-button:hover { transform: translateY(-1px); box-shadow: 0 8px 18px rgba(0,0,0,.08); }
    .fc .fc-daygrid-day.fc-day-today { background: rgba(13,110,253,.08); }
  </style>
</head>

<body class="ms-body ms-aside-left-open ms-primary-theme ms-has-quickbar">

  <!-- Preloader -->
  <div id="preloader-wrap">
    <div class="spinner spinner-8">
      <div class="ms-circle1 ms-child"></div><div class="ms-circle2 ms-child"></div><div class="ms-circle3 ms-child"></div>
      <div class="ms-circle4 ms-child"></div><div class="ms-circle5 ms-child"></div><div class="ms-circle6 ms-child"></div>
      <div class="ms-circle7 ms-child"></div><div class="ms-circle8 ms-child"></div><div class="ms-circle9 ms-child"></div>
      <div class="ms-circle10 ms-child"></div><div class="ms-circle11 ms-child"></div><div class="ms-circle12 ms-child"></div>
    </div>
  </div>

  <!-- Overlays -->
  <div class="ms-aside-overlay ms-overlay-left ms-toggler" data-target="#ms-side-nav" data-toggle="slideLeft"></div>
  <div class="ms-aside-overlay ms-overlay-right ms-toggler" data-target="#ms-recent-activity" data-toggle="slideRight"></div>

  <!-- Sidebar Navigation Left -->
  <aside id="ms-side-nav" class="side-nav fixed ms-aside-scrollable ms-aside-left">
    <!-- Logo -->
    <div class="logo-sn ms-d-block-lg">
      <a class="pl-0 ml-0 text-center" href="../../index.php"> <img src="../../assets/img/LogoSchoolCare.png" alt="logo"> </a>
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
              <li> <a href="../../pages/students-doc/studendoc.php">Todos los Estudiantes</a> </li>
               <li> <a href="../../pages/students-doc/asistencias.php">Asistencias</a> </li>
                 <li> <a href="../../pages/students-doc/scoredoc.php">Acerca de los Estudiantes</a> </li>
                  <li> <a href="../../pages/students-doc/formulario_incidentes.php">Recordatorios  </a> </li>
                  <li> <a href="../../pages/students-doc/recordatorios_docente.php">Todos los Recordatorios  </a> </li>
            </ul>
        </li>
        <!-- /Proessors End--->
        
         <!--Courses Start-->
        <li class="menu-item">
            <a href="#" class="has-chevron" data-toggle="collapse" data-target="#courses" aria-expanded="false" aria-controls="dashboard">
               <span><i class="fa fa-graduation-cap fs-16"></i>Materias</span>
             </a>
            <ul id="courses" class="collapse" aria-labelledby="courses" data-parent="#side-nav-accordion">
              <li> <a href="../../pages/students-doc/allcoursesDoc.php">Todas las Materias</a> </li>
            </ul>
        </li>
        <!-- /Courses End--->
          <!--tutor Start-->
        <li class="menu-item">
            <a href="#" class="has-chevron" data-toggle="collapse" data-target="#staff" aria-expanded="false" aria-controls="dashboard">
               <span><i class="fa fa-user-circle fs-16"></i>Tutores</span>
             </a>
            <ul id="staff" class="collapse" aria-labelledby="staff" data-parent="#side-nav-accordion">
               <li> <a href="../../pages/students-doc/tutoresDoc.php">Tabla de Tutores</a> </li>
            </ul>
        </li>
        <!-- /tutor End--->
        


       <!--Holiday Start-->
        <li class="menu-item">
          <a href="holiday_docente.php">
            <span><i class="fa fa-calendar fs-16"></i>Holidays</span>
          </a>
        </li>
        <!-- /Holiday End--->

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
        <a class="pl-0 ml-0 text-center navbar-brand mr-0" href="../../index.php"><img src="../../assets/img/LogoSchoolCare.png" alt="logo"> </a>
      </div>
      <ul class="ms-nav-list ms-inline mb-0" id="ms-nav-options">
        <li class="ms-nav-item ms-search-form pb-0 py-0"><form class="ms-form" method="post"></form></li>
        <li class="ms-nav-item"></li>
        <li class="ms-nav-item ms-nav-user dropdown">
          <a href="#" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <img class="ms-user-img ms-img-round float-right" src="../../assets/img/we-educate/new-student-5.jpg" alt="people">
          </a>
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
      <div class="row">
        <div class="col-md-12">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb pl-0">
              <li class="breadcrumb-item"><a href="../../index.php"><i class="material-icons">home</i> Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Calendario</li>
            </ol>
          </nav>
        </div>
      </div>

      <div class="col-xl-12">
        <div class="ms-panel">
          <div class="ms-panel-header">
            <h6>Calendario Anual Escolar</h6>
          </div>
          <div class="ms-panel-body">
            <div id="calendario-festivos"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- calendar modal -->
    <div id="modal-view-event" class="modal modal-top fade calendar-modal">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-body">
            <h4 class="modal-title"><span class="event-icon"></span><span class="event-title"></span></h4>
            <div class="event-body"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <div></div>
  </main>

  <!-- Quick bar -->
  <aside id="ms-quick-bar" class="ms-quick-bar fixed ms-d-block-lg"></aside>

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
  <script src="../../assets/js/datatables.min.js"> </script>
  <script src="../../assets/js/data-tables.js"> </script>
  <!-- Weeducate core JavaScript -->
  <script src="../../assets/js/framework.js"></script>
  <!-- Settings -->
  <script src="../../assets/js/settings.js"></script>

  <!-- FullCalendar -->
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/locales/es.global.min.js"></script>

  <!-- Calendario mejorado -->
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const calendarEl = document.getElementById('calendario-festivos');

      const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        height: 600,
        dayMaxEvents: true,
        headerToolbar: {
          left: 'prev today next',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,listWeek'
        },

        /* Fuentes de eventos */
        eventSources: [
          // Festivos desde tu JSON
          {
            events: function(fetchInfo, success){
              fetch('../holidays/dias_festivos.json')
                .then(r => r.json())
                .then(data => {
                  const evts = data.map(f => {
                    const rango = (f.fecha || '').split(' al ');
                    return {
                      title: 'Festivo: ' + f.motivo,
                      start: rango[0],
                      end: rango[1]
                        ? new Date(new Date(rango[1]).getTime() + 86400000).toISOString().split('T')[0]
                        : undefined,
                      allDay: true,
                      classNames: ['fc-event-festivo'],
                      kind: 'festivo',
                      body: `<div><b>Motivo:</b> ${f.motivo}</div>`
                    };
                  });
                  success(evts);
                })
                .catch(() => success([]));
            }
          },
          // Ciclos/Periodos (background) + Avisos SOLO del profesor (endpoint en ../php/)
          {
            url: '../php/api_calendar_profesor.php',
            method: 'GET',
            failure: () => console.warn('No fue posible cargar eventos de la BD')
          }
        ],

        /* Click: si hay varios avisos en ese día, mostrar TODOS en el modal */
        eventClick: function (info) {
          const clicked = info.event;
          const ymd = (clicked.startStr || '').split('T')[0];

          // Juntar todos los eventos NO background que caen en esa fecha
          const sameDay = calendar.getEvents().filter(ev => {
            if (ev.display === 'background') return false; // ignora ciclos/periodos
            const s = (ev.startStr || '').split('T')[0];
            const e = (ev.endStr   || '').split('T')[0];
            if (e) { // rangos (festivos varios días)
              return ymd >= s && ymd < e;
            } else {
              return ymd === s;
            }
          });

          // Construir HTML del modal
          let html = '';
          if (sameDay.length === 0) {
            html = `<div class="text-muted">Sin avisos para este día.</div>`;
          } else {
            html = `<ul class="list-group">` +
              sameDay.map(ev => {
                const body = ev.extendedProps?.body || '';
                const alumno = ev.extendedProps?.estudiante || '';
                const materia = ev.extendedProps?.materia || '';
                const tipo = ev.extendedProps?.tipo || '';
                const desc = ev.extendedProps?.descripcion || '';
                const titulo = ev.title || 'Aviso';
                return `<li class="list-group-item">
                          <div class="fw-semibold mb-1">${titulo}</div>
                          ${alumno ? `<small class="text-muted mr-2">Alumno: ${alumno}</small>` : ''}
                          ${materia ? `<small class="text-muted mr-2">Materia: ${materia}</small>` : ''}
                          ${tipo ? `<small class="text-muted mr-2">Tipo: ${tipo}</small>` : ''}
                          ${body || (desc ? `<div class="small mt-1">${desc}</div>` : '')}
                        </li>`;
              }).join('') +
              `</ul>`;
          }

          // Título y cuerpo del modal
          document.querySelector('#modal-view-event .event-title').textContent =
            `Avisos del ${new Date(clicked.start).toLocaleDateString('es-MX')}`;
          document.querySelector('#modal-view-event .event-body').innerHTML = html;

          // Mostrar modal (Bootstrap 4)
          $('#modal-view-event').modal('show');
        },

        /* Dar look a los botones según tu tema */
        datesSet: function () {
          setTimeout(() => {
            document.querySelectorAll('.fc .fc-button').forEach(btn => {
              btn.classList.add('btn','btn-sm','btn-primary','rounded-pill','shadow-sm');
            });
          }, 0);
        }
      });

      calendar.render();
    });
  </script>
</body>
</html>
