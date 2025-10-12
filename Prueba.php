<!doctype html>
<html lang="es" data-bs-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MathQuest · Inicio Alumno</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <style>
    /* ========= Estilos rápidos para “cascarón” ========= */
    body { background: #0f172a0a; }
    .brand {
      font-weight: 800; letter-spacing: .5px;
    }
    .app-sidebar {
      width: 280px; background: #0b1220; color: #e2e8f0;
    }
    .app-sidebar .nav-link { color: #cbd5e1; border-radius: .75rem; }
    .app-sidebar .nav-link.active, .app-sidebar .nav-link:hover {
      color: #fff; background: #1e293b;
    }
    .section-title {
      font-weight: 700; letter-spacing: .3px;
    }
    .card {
      border: 1px solid #e5e7eb;
      border-radius: 16px;
    }
    .card-header {
      background: #fff; border-bottom: 1px dashed #e5e7eb;
      border-top-left-radius: 16px; border-top-right-radius: 16px;
    }
    .badge-soft {
      background: #eef2ff; color: #3730a3; border: 1px solid #c7d2fe;
    }
    .reward-pill {
      background: #ecfeff; border: 1px solid #a5f3fc; color: #155e75;
      border-radius: 999px; padding: .35rem .7rem; font-size: .85rem;
    }
    .avatar {
      width: 40px; height: 40px; border-radius: 999px; object-fit: cover;
    }
    .progress.rounded-pill { height: 10px; }
    .kanban-col {
      min-height: 180px; background: #f8fafc; border: 1px dashed #e2e8f0; border-radius: 12px; padding: .75rem;
    }
    .calendar-grid {
      display: grid; grid-template-columns: repeat(7, 1fr); gap: .35rem;
    }
    .calendar-grid .day {
      background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 8px;
      height: 44px; display: grid; place-items: center; font-size: .9rem;
    }
    .calendar-grid .day.has { border-color: #a7f3d0; background: #ecfdf5; font-weight: 600; }
    .sticky-toolbar {
      position: sticky; top: 0; z-index: 1030; background: #fff;
      border-bottom: 1px solid #e5e7eb;
    }
    .shadow-soft { box-shadow: 0 6px 20px rgba(2,6,23,.07); }
  </style>
</head>
<body>

  <!-- ======== Topbar ======== -->
  <nav class="navbar sticky-toolbar navbar-expand-lg">
    <div class="container-fluid px-3">
      <button class="btn btn-outline-secondary d-lg-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar">
        <i class="fa-solid fa-bars"></i>
      </button>

      <a class="navbar-brand brand" href="#">
        <i class="fa-solid fa-hat-wizard text-primary me-2"></i> MathQuest
      </a>

      <form class="d-none d-md-flex ms-3 flex-grow-1" role="search">
        <div class="input-group">
          <span class="input-group-text bg-white"><i class="fa-solid fa-search"></i></span>
          <input class="form-control" type="search" placeholder="Buscar retos, clases o minijuegos...">
        </div>
      </form>

      <ul class="navbar-nav ms-auto align-items-center gap-2">
        <li class="nav-item d-none d-md-block">
          <button class="btn btn-outline-secondary" id="toggle-theme" type="button">
            <i class="fa-solid fa-moon"></i>
          </button>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
            <img class="avatar me-2" src="https://i.pravatar.cc/80?img=5" alt="">
            <span class="fw-semibold">Yoav Pary</span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><h6 class="dropdown-header">Alumno</h6></li>
            <li><a class="dropdown-item" href="#"><i class="fa-regular fa-id-card me-2"></i> Perfil</a></li>
            <li><a class="dropdown-item" href="#"><i class="fa-solid fa-shield-halved me-2"></i> Privacidad</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="#"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Cerrar sesión</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </nav>

  <!-- ======== Layout ======== -->
  <div class="d-flex">
    <!-- Sidebar -->
    <div class="offcanvas-lg offcanvas-start app-sidebar" tabindex="-1" id="sidebar">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title text-white">Menú</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
      </div>
      <div class="offcanvas-body d-flex flex-column">
        <div class="mb-3 px-2">
          <div class="small text-secondary">Bienvenido</div>
          <div class="fw-bold text-white">Yoav Pary</div>
          <div class="text-secondary">Alumno</div>
        </div>
        <hr class="border-secondary">
        <ul class="nav nav-pills flex-column gap-1 px-2">
          <li><a class="nav-link active" href="#"><i class="fa-solid fa-house me-2"></i> Inicio</a></li>
          <li><a class="nav-link" href="#"><i class="fa-solid fa-book-open me-2"></i> Mis Clases</a></li>
          <li><a class="nav-link" href="#"><i class="fa-solid fa-flag-checkered me-2"></i> Retos</a></li>
          <li><a class="nav-link" href="#"><i class="fa-solid fa-gamepad me-2"></i> Minijuegos</a></li>
          <li><a class="nav-link" href="#"><i class="fa-solid fa-trophy me-2"></i> Recompensas</a></li>
          <li><a class="nav-link" href="#"><i class="fa-solid fa-chart-line me-2"></i> Progreso</a></li>
          <li><a class="nav-link" href="#"><i class="fa-regular fa-bell me-2"></i> Notificaciones</a></li>
          <li><a class="nav-link" href="#"><i class="fa-regular fa-message me-2"></i> Mensajes</a></li>
          <li><a class="nav-link" href="#"><i class="fa-solid fa-gear me-2"></i> Configuración</a></li>
        </ul>
        <div class="mt-auto px-2">
          <hr class="border-secondary">
          <div class="d-flex align-items-center gap-2">
            <img class="avatar" src="https://i.pravatar.cc/80?img=5" alt="">
            <div>
              <div class="fw-semibold">Yoav Pary</div>
              <div class="small text-secondary">ID: STU-2391</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main -->
    <main class="flex-grow-1 p-3 p-lg-4">
      <!-- Saludo + chips -->
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
          <h1 class="h4 mb-1">Hola, <span class="fw-bold">Yoav</span> 👋</h1>
          <div class="text-secondary">Este es tu panel. ¡Sigue avanzando para ganar más insignias!</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
          <span class="badge badge-soft rounded-pill"><i class="fa-solid fa-bolt me-1"></i> Racha: 7 días</span>
          <span class="badge badge-soft rounded-pill"><i class="fa-solid fa-medal me-1"></i> Nivel: 4</span>
          <span class="badge badge-soft rounded-pill"><i class="fa-solid fa-coins me-1"></i> Monedas: 340</span>
        </div>
      </div>

      <!-- Resumen superior -->
      <div class="row g-3 mb-3">
        <div class="col-12 col-xl-8">
          <div class="card shadow-soft">
            <div class="card-header d-flex align-items-center justify-content-between">
              <h5 class="m-0 section-title"><i class="fa-solid fa-chart-pie me-2 text-primary"></i> Progreso general</h5>
              <span class="small text-secondary">Últimos 7 días</span>
            </div>
            <div class="card-body">
              <div class="row g-3">
                <div class="col-md-6">
                  <div class="mb-2 d-flex justify-content-between">
                    <span>Retos completados</span><span class="fw-bold">12/18</span>
                  </div>
                  <div class="progress rounded-pill">
                    <div class="progress-bar bg-success" style="width: 66%"></div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-2 d-flex justify-content-between">
                    <span>Promedio de aciertos</span><span class="fw-bold">82%</span>
                  </div>
                  <div class="progress rounded-pill">
                    <div class="progress-bar" style="width: 82%"></div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-2 d-flex justify-content-between">
                    <span>Tiempo de práctica</span><span class="fw-bold">3h 25m</span>
                  </div>
                  <div class="progress rounded-pill">
                    <div class="progress-bar bg-info" style="width: 55%"></div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-2 d-flex justify-content-between">
                    <span>Rendimiento por intentos</span><span class="fw-bold">+12%</span>
                  </div>
                  <div class="progress rounded-pill">
                    <div class="progress-bar bg-warning" style="width: 72%"></div>
                  </div>
                </div>
              </div>

              <hr class="my-4">

              <div class="row g-3">
                <div class="col-md-6">
                  <div class="kanban-col">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                      <strong>Retos en curso</strong>
                      <a href="#" class="small">Ver todos</a>
                    </div>
                    <ul class="list-unstyled m-0">
                      <li class="mb-2">
                        <div class="d-flex justify-content-between">
                          <span><i class="fa-solid fa-flag-checkered me-1 text-primary"></i> Fracciones I</span>
                          <span class="badge text-bg-light">Vence hoy</span>
                        </div>
                        <div class="progress rounded-pill mt-1"><div class="progress-bar" style="width: 45%"></div></div>
                      </li>
                      <li class="mb-2">
                        <div class="d-flex justify-content-between">
                          <span><i class="fa-solid fa-flag-checkered me-1 text-primary"></i> Geometría básica</span>
                          <span class="badge text-bg-light">2 días</span>
                        </div>
                        <div class="progress rounded-pill mt-1"><div class="progress-bar bg-success" style="width: 70%"></div></div>
                      </li>
                      <li>
                        <div class="d-flex justify-content-between">
                          <span><i class="fa-solid fa-flag-checkered me-1 text-primary"></i> Álgebra — ecuaciones</span>
                          <span class="badge text-bg-light">5 días</span>
                        </div>
                        <div class="progress rounded-pill mt-1"><div class="progress-bar bg-info" style="width: 30%"></div></div>
                      </li>
                    </ul>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="kanban-col">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                      <strong>Minijuegos recomendados</strong>
                      <a href="#" class="small">Explorar</a>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                      <span class="reward-pill"><i class="fa-solid fa-gamepad me-1"></i> Operación Ninja</span>
                      <span class="reward-pill"><i class="fa-solid fa-gamepad me-1"></i> Memoria de Ángulos</span>
                      <span class="reward-pill"><i class="fa-solid fa-gamepad me-1"></i> Carrera de Fracciones</span>
                      <span class="reward-pill"><i class="fa-solid fa-gamepad me-1"></i> FactorizaXpress</span>
                    </div>
                  </div>
                </div>
              </div>

            </div><!-- /card-body -->
          </div>
        </div>

        <div class="col-12 col-xl-4">
          <div class="card shadow-soft h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
              <h5 class="m-0 section-title"><i class="fa-regular fa-bell me-2 text-primary"></i> Notificaciones</h5>
              <a href="#" class="small">Marcar leídas</a>
            </div>
            <div class="card-body">
              <div class="list-group list-group-flush">
                <a href="#" class="list-group-item list-group-item-action d-flex gap-3">
                  <i class="fa-solid fa-circle text-success mt-1"></i>
                  <div>
                    <div class="fw-semibold">Insignia ganada: “Velocista”</div>
                    <div class="small text-secondary">Por completar 3 retos en un día.</div>
                  </div>
                </a>
                <a href="#" class="list-group-item list-group-item-action d-flex gap-3">
                  <i class="fa-solid fa-circle text-primary mt-1"></i>
                  <div>
                    <div class="fw-semibold">Nuevo reto: Geometría básica</div>
                    <div class="small text-secondary">La profa. López lo asignó a tu grupo.</div>
                  </div>
                </a>
                <a href="#" class="list-group-item list-group-item-action d-flex gap-3">
                  <i class="fa-solid fa-circle text-warning mt-1"></i>
                  <div>
                    <div class="fw-semibold">Recordatorio</div>
                    <div class="small text-secondary">Tienes pendiente “Fracciones I”.</div>
                  </div>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div><!-- /row resumen -->

      <!-- Clases + Recompensas + Actividad + Leaderboard + Calendario -->
      <div class="row g-3">
        <div class="col-12 col-lg-6 col-xxl-4">
          <div class="card shadow-soft h-100">
            <div class="card-header">
              <h5 class="m-0 section-title"><i class="fa-solid fa-book-open me-2 text-primary"></i> Mis clases</h5>
            </div>
            <div class="card-body">
              <div class="row g-3">
                <div class="col-12">
                  <div class="p-3 rounded-3 border d-flex justify-content-between">
                    <div>
                      <div class="fw-bold">Matemáticas 2B</div>
                      <div class="small text-secondary">Prof. Ramírez · 28 alumnos</div>
                    </div>
                    <a href="#" class="btn btn-sm btn-outline-primary">Entrar</a>
                  </div>
                </div>
                <div class="col-12">
                  <div class="p-3 rounded-3 border d-flex justify-content-between">
                    <div>
                      <div class="fw-bold">Álgebra I</div>
                      <div class="small text-secondary">Profa. López · 31 alumnos</div>
                    </div>
                    <a href="#" class="btn btn-sm btn-outline-primary">Entrar</a>
                  </div>
                </div>
                <div class="col-12">
                  <button class="btn btn-outline-secondary w-100"><i class="fa-solid fa-plus me-2"></i> Unirse con código</button>
                </div>
              </div>
            </div>
          </div>
        </div><!-- /clases -->

        <div class="col-12 col-lg-6 col-xxl-4">
          <div class="card shadow-soft h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
              <h5 class="m-0 section-title"><i class="fa-solid fa-trophy me-2 text-primary"></i> Recompensas & Insignias</h5>
              <a href="#" class="small">Ver todo</a>
            </div>
            <div class="card-body">
              <div class="d-flex flex-wrap gap-2 mb-3">
                <span class="reward-pill"><i class="fa-solid fa-medal me-1"></i> Velocista</span>
                <span class="reward-pill"><i class="fa-solid fa-crown me-1"></i> Maestro de Fracciones</span>
                <span class="reward-pill"><i class="fa-solid fa-star me-1"></i> Constancia</span>
              </div>
              <div class="row g-3">
                <div class="col-6">
                  <div class="border rounded-3 p-3 text-center">
                    <div class="display-6">340</div>
                    <div class="small text-secondary">Monedas</div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="border rounded-3 p-3 text-center">
                    <div class="display-6">12</div>
                    <div class="small text-secondary">Insignias</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div><!-- /recompensas -->

        <div class="col-12 col-xxl-4">
          <div class="card shadow-soft h-100">
            <div class="card-header">
              <h5 class="m-0 section-title"><i class="fa-regular fa-clock me-2 text-primary"></i> Actividad reciente</h5>
            </div>
            <div class="card-body">
              <ul class="list-unstyled m-0">
                <li class="mb-3">
                  <div class="d-flex justify-content-between">
                    <div><strong>Fracciones I</strong> — intento #3</div>
                    <span class="text-success fw-semibold">92%</span>
                  </div>
                  <div class="small text-secondary">hoy · 15:42</div>
                </li>
                <li class="mb-3">
                  <div class="d-flex justify-content-between">
                    <div><strong>Geometría básica</strong> — intento #1</div>
                    <span class="text-warning fw-semibold">70%</span>
                  </div>
                  <div class="small text-secondary">ayer · 19:10</div>
                </li>
                <li>
                  <div class="d-flex justify-content-between">
                    <div><strong>Álgebra — ecuaciones</strong> — intento #2</div>
                    <span class="text-danger fw-semibold">58%</span>
                  </div>
                  <div class="small text-secondary">ayer · 17:50</div>
                </li>
              </ul>
            </div>
          </div>
        </div><!-- /actividad -->
      </div><!-- /row -->

      <div class="row g-3 mt-1">
        <div class="col-12 col-lg-7">
          <div class="card shadow-soft h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
              <h5 class="m-0 section-title"><i class="fa-solid fa-ranking-star me-2 text-primary"></i> Leaderboard (clase Matemáticas 2B)</h5>
              <a href="#" class="small">Ver ranking completo</a>
            </div>
            <div class="card-body">
              <ol class="list-group list-group-numbered">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <span><img class="avatar me-2" src="https://i.pravatar.cc/80?img=12"> Ana Torres</span>
                  <span class="fw-bold">1540 pts</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <span><img class="avatar me-2" src="https://i.pravatar.cc/80?img=18"> Luis Madera</span>
                  <span class="fw-bold">1510 pts</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <span><img class="avatar me-2" src="https://i.pravatar.cc/80?img=5"> Tú</span>
                  <span class="fw-bold">1480 pts</span>
                </li>
              </ol>
            </div>
          </div>
        </div>

        <div class="col-12 col-lg-5">
          <div class="card shadow-soft h-100">
            <div class="card-header">
              <h5 class="m-0 section-title"><i class="fa-regular fa-calendar-days me-2 text-primary"></i> Calendario (octubre)</h5>
            </div>
            <div class="card-body">
              <div class="calendar-grid">
                <!-- Semilla de calendario “relleno estético” -->
                <div class="day"></div><div class="day"></div><div class="day"></div><div class="day">1</div><div class="day has">2</div><div class="day">3</div><div class="day">4</div>
                <div class="day">5</div><div class="day">6</div><div class="day has">7</div><div class="day">8</div><div class="day">9</div><div class="day has">10</div><div class="day">11</div>
                <div class="day">12</div><div class="day">13</div><div class="day">14</div><div class="day has">15</div><div class="day">16</div><div class="day">17</div><div class="day">18</div>
                <div class="day">19</div><div class="day has">20</div><div class="day">21</div><div class="day">22</div><div class="day has">23</div><div class="day">24</div><div class="day">25</div>
                <div class="day">26</div><div class="day">27</div><div class="day">28</div><div class="day">29</div><div class="day has">30</div><div class="day">31</div><div class="day"></div>
              </div>
              <div class="mt-3 small">
                <span class="badge text-bg-light me-2"><span class="me-1" style="display:inline-block;width:.7rem;height:.7rem;background:#ecfdf5;border:1px solid #a7f3dc;border-radius:.25rem;"></span> Entregas</span>
                <span class="badge text-bg-light"><span class="me-1" style="display:inline-block;width:.7rem;height:.7rem;background:#f8fafc;border:1px solid #e5e7eb;border-radius:.25rem;"></span> Vacío</span>
              </div>
            </div>
          </div>
        </div>
      </div><!-- /row -->

      <footer class="text-center small text-secondary mt-4">
        © 2025 MathQuest · Construido con ❤️ para aprender mejor
      </footer>
    </main>
  </div><!-- /layout -->

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Toggle claro/oscuro (simple)
    const btn = document.getElementById('toggle-theme');
    btn?.addEventListener('click', () => {
      const html = document.documentElement;
      const current = html.getAttribute('data-bs-theme') || 'light';
      const next = current === 'light' ? 'dark' : 'light';
      html.setAttribute('data-bs-theme', next);
      btn.innerHTML = next === 'dark'
        ? '<i class="fa-solid fa-sun"></i>'
        : '<i class="fa-solid fa-moon"></i>';
    });
  </script>
</body>
</html>
