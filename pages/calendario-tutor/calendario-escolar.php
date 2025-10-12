<?php
require_once __DIR__ . '/../php/auth.php'; // ✅ Solo auth en la vista (sin conecta())
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

  <!-- Iconos / fuentes -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="../../vendors/iconic-fonts/font-awesome/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../vendors/iconic-fonts/flat-icons/flaticon.css">

  <!-- Core CSS -->
  <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="../../assets/css/jquery-ui.min.css" rel="stylesheet">
  <link href="../../assets/css/slick.css" rel="stylesheet">
  <link href="../../assets/css/style.css" rel="stylesheet">
  <link rel="icon" type="image/png" sizes="32x32" href="../../assets/img/LogoSchoolCare.png">

  <!-- FullCalendar -->
  <link href="../../assets/css/calendarioadmin.css" rel="stylesheet">

  <style>
    /* Tarjeta del calendario */
    #calendario-tutor{
      background: linear-gradient(180deg,#fff 0%,#f9fbff 100%);
      border-radius: 14px; padding:12px;
      box-shadow: 0 8px 28px rgba(12,61,223,.08);
      transition: transform .2s ease, box-shadow .2s ease;
    }
    #calendario-tutor:hover{ transform: translateY(-2px); box-shadow: 0 10px 32px rgba(12,61,223,.12); }

    /* Botones y hoy */
    .fc .fc-button{ border:0; transition:transform .15s ease, box-shadow .15s ease; }
    .fc .fc-button:hover{ transform: translateY(-1px); box-shadow:0 8px 18px rgba(0,0,0,.08); }
    .fc .fc-daygrid-day.fc-day-today{ background: rgba(13,110,253,.08); }

    /* Eventos */
    .fc .fc-daygrid-event{ border-radius:10px; font-weight:600; box-shadow:0 2px 10px rgba(30,38,82,.08); }
    .evt-incidente{ background:#dc3545; color:#fff; }
    .evt-festivo{ background:#0dcaf0; color:#00323a; }
    .evt-periodo{ background:#0d6efd; }
    .evt-ciclo{ background:#6f42c1; }

    /* Background events */
    .fc-event-background.evt-periodo{ opacity:.25; }
    .fc-event-background.evt-ciclo{ opacity:.18; }
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

  <!-- Sidebar -->
  <aside id="ms-side-nav" class="side-nav fixed ms-aside-scrollable ms-aside-left">
    <div class="logo-sn ms-d-block-lg">
      <a class="pl-0 ml-0 text-center" href="../../Tutor.php"><img src="../../assets/img/LogoSchoolCare.png" alt="logo"></a>
    </div>

    <ul class="accordion ms-main-aside fs-14" id="side-nav-accordion">
      <li class="menu-item">
        <a href="#" class="has-chevron" data-toggle="collapse" data-target="#dashboard" aria-expanded="false">
          <span><i class="material-icons fs-16">dashboard</i>Home</span>
        </a>
        <ul id="dashboard" class="collapse" data-parent="#side-nav-accordion">
          <li><a href="../../Tutor.php">SchoolCare</a></li>
        </ul>
      </li>

      <li class="menu-item">
        <a href="#" class="has-chevron" data-toggle="collapse" data-target="#student"><span><i class="fa fa-users fs-16"></i>Mi hijo</span></a>
        <ul id="student" class="collapse" data-parent="#side-nav-accordion">
          <li><a href="../perfil-hijo/perfil.php">Perfil</a></li>
          <li><a href="../perfil-hijo/calificaciones-hijo.php">Calificaciones</a></li>
        </ul>
      </li>

      <li class="menu-item">
        <a href="#" class="has-chevron" data-toggle="collapse" data-target="#courses"><span><i class="fa fa-graduation-cap fs-16"></i>Materias</span></a>
        <ul id="courses" class="collapse" data-parent="#side-nav-accordion">
          <li><a href="../cursos-tutor/materias-hijo.php">Todas mis materias</a></li>
        </ul>
      </li>

      <li class="menu-item">
        <a href="../calendario-tutor/calendario-escolar.php"><span><i class="fa fa-calendar fs-16"></i>Calendario Escolar</span></a>
      </li>

      <li class="menu-item">
        <a href="#" class="has-chevron" data-toggle="collapse" data-target="#fees"><span><i class="fas fa-dollar-sign"></i>Pagos Escolares</span></a>
        <ul id="fees" class="collapse" data-parent="#side-nav-accordion">
          <li><a href="../pagos-tutor/pagos-hijo.php">Orden de pago</a></li>
        </ul>
      </li>
    </ul>
  </aside>

  <!-- Main -->
  <main class="body-content">
    <nav class="navbar ms-navbar">
      <div class="ms-aside-toggler ms-toggler pl-0" data-target="#ms-side-nav" data-toggle="slideLeft">
        <span class="ms-toggler-bar bg-primary"></span><span class="ms-toggler-bar bg-primary"></span><span class="ms-toggler-bar bg-primary"></span>
      </div>
      <div class="logo-sn logo-sm ms-d-block-sm">
        <a class="pl-0 ml-0 text-center navbar-brand mr-0" href="../../Tutor.php"><img src="../../assets/img/logo/weeducate-4.png" alt="logo"></a>
      </div>

      <ul class="ms-nav-list ms-inline mb-0" id="ms-nav-options">
        <li class="ms-nav-item ms-nav-user dropdown">
          <a href="#" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <img class="ms-user-img ms-img-round float-right" src="../../assets/img/we-educate/new-student-5.jpg" alt="people">
          </a>
          <ul class="dropdown-menu dropdown-menu-right user-dropdown" aria-labelledby="userDropdown">
            <li class="dropdown-menu-header">
              <h6 class="dropdown-header ms-inline m-0">
                <span class="text-disabled">Bienvenido, <?= h((($_SESSION['nombre'] ?? '') . ' ' . ($_SESSION['apellido'] ?? ''))) ?></span>
              </h6>
            </li>
            <li class="dropdown-divider"></li>
            <li class="dropdown-menu-footer">
              <a class="media fs-14 p-2 logout-link" href="../php/logout.php"><span><i class="flaticon-shut-down mr-2"></i> Cerrar Sesión</span></a>
            </li>
          </ul>
        </li>
      </ul>

      <div class="ms-toggler ms-d-block-sm pr-0 ms-nav-toggler" data-toggle="slideDown" data-target="#ms-nav-options">
        <span class="ms-toggler-bar bg-primary"></span><span class="ms-toggler-bar bg-primary"></span><span class="ms-toggler-bar bg-primary"></span>
      </div>
    </nav>

    <div class="ms-content-wrapper">
      <div class="row">
        <div class="col-md-12">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb pl-0">
              <li class="breadcrumb-item"><a href="../../Tutor.php"><i class="material-icons">home</i> Lobby</a></li>
              <li class="breadcrumb-item active" aria-current="page">Calendario Escolar</li>
            </ol>
          </nav>
        </div>
      </div>

      <div class="col-xl-12">
        <div class="ms-panel">
          <div class="ms-panel-header d-flex align-items-center justify-content-between">
            <h6 class="m-0">Avisos de tus hijos y periodos</h6>
          </div>
          <div class="ms-panel-body">
            <div id="calendario-tutor"></div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Modal de avisos del día -->
  <div class="modal fade" id="modalAvisosDia" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header border-0">
          <h5 class="modal-title"><i class="fa fa-bell mr-2"></i> Avisos del <span id="lblFecha"></span></h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body"><div id="contenedorAvisos"></div></div>
        <div class="modal-footer border-0">
          <button class="btn btn-primary" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts base -->
  <script src="../../assets/js/jquery-3.3.1.min.js"></script>
  <script src="../../assets/js/popper.min.js"></script>
  <script src="../../assets/js/bootstrap.min.js"></script>
  <script src="../../assets/js/perfect-scrollbar.js"></script>
  <script src="../../assets/js/jquery-ui.min.js"></script>
  <script src="../../assets/js/slick.min.js"></script>
  <script src="../../assets/js/moment.js"></script>
  <script src="../../assets/js/jquery.webticker.min.js"></script>
  <script src="../../assets/js/framework.js"></script>
  <script src="../../assets/js/settings.js"></script>

  <!-- FullCalendar -->
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/locales/es.global.min.js"></script>

  <script>
  document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('calendario-tutor');

    const calendar = new FullCalendar.Calendar(el, {
      initialView: 'dayGridMonth',
      locale: 'es',
      height: 670,
      dayMaxEvents: true,
      headerToolbar: { left:'prev today next', center:'title', right:'dayGridMonth,timeGridWeek,listWeek' },

      eventSources: [
        // Festivos desde JSON
        {
          events: function(fetchInfo, success){
            fetch('../holidays/dias_festivos.json')
              .then(r => r.json())
              .then(data => {
                const evts = data.map(f => {
                  const rango = (f.fecha || '').split(' al ');
                  const end = rango[1]
                    ? new Date(new Date(rango[1]).getTime() + 86400000).toISOString().split('T')[0] : undefined;
                  return {
                    title: 'Festivo: ' + f.motivo,
                    start: rango[0], end, allDay: true,
                    extendedProps: { kind:'festivo', body:`<div><b>Motivo:</b> ${f.motivo}</div>` }
                  };
                });
                success(evts);
              })
              .catch(() => success([]));
          },
          className: 'evt-festivo'
        },
        // Ciclos/Periodos (background) + Incidentes de los hijos del tutor
        {
          url: '../php/api_calendar_tutor.php',
          method: 'GET',
          failure: () => console.warn('No se pudieron cargar los eventos del tutor')
        }
      ],

      eventClassNames: function(info){
        const k = info.event.extendedProps.kind;
        if (info.event.display === 'background') {
          return k === 'periodo' ? ['evt-periodo'] : ['evt-ciclo'];
        }
        if (k === 'incidente') return ['evt-incidente'];
        if (k === 'festivo')   return ['evt-festivo'];
        return [];
      },

      eventClick: (info) => abrirModalDia(info.event.start),
      dateClick:  (info) => abrirModalDia(info.date),
    });

    calendar.render();

    function abrirModalDia(dateObj){
      const y = dateObj.getFullYear(), m = ('0'+(dateObj.getMonth()+1)).slice(-2), d = ('0'+dateObj.getDate()).slice(-2);
      const ymd = `${y}-${m}-${d}`;
      document.getElementById('lblFecha').textContent = dateObj.toLocaleDateString('es-MX');

      const eventos = calendar.getEvents().filter(ev => {
        if (ev.display === 'background') return false;
        const s = (ev.startStr || '').split('T')[0];
        const e = (ev.endStr || '').split('T')[0];
        return e ? (ymd >= s && ymd < e) : (ymd === s);
      });

      const html = eventos.length
        ? '<ul class="list-group">' + eventos.map(ev => {
            const p = ev.extendedProps || {};
            return `<li class="list-group-item">
                      <div class="fw-semibold">${ev.title || 'Aviso'}</div>
                      ${p.estudiante ? `<small class="text-muted mr-2">Hijo(a): ${p.estudiante}</small>` : ''}
                      ${p.materia ? `<small class="text-muted mr-2">Materia: ${p.materia}</small>` : ''}
                      ${p.tipo ? `<small class="text-muted mr-2">Tipo: ${p.tipo}</small>` : ''}
                      ${(p.descripcion ? `<div class="small mt-1">${p.descripcion}</div>` : (p.body||''))}
                    </li>`;
          }).join('') + '</ul>'
        : '<div class="text-muted">Sin avisos en esta fecha.</div>';

      document.getElementById('contenedorAvisos').innerHTML = html;
      $('#modalAvisosDia').modal('show');
    }
  });
  </script>
</body>
</html>
