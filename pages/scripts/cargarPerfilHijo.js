// pages/scripts/cargarPerfilHijo.js
document.addEventListener('DOMContentLoaded', () => {
  const $selHijo   = document.getElementById('selectHijo');
  const el = (id)  => document.getElementById(id);

  function qparam(name){
    const p = new URLSearchParams(location.search);
    return p.get(name);
  }

  async function fetchPerfil(estudianteId = '') {
    const url = `../php/get_perfil_hijo.php${estudianteId ? `?estudiante_id=${estudianteId}` : ''}`;
    const res = await fetch(url, { credentials: 'include' });
    return res.json();
  }

  function renderPerfil(data){
    const a = data.alumno || {};
    // Header
    if (el('perfilNombre'))     el('perfilNombre').textContent = `${a.nombre || ''} ${a.apellido || ''}`.trim();
    if (el('perfilEstado'))     el('perfilEstado').innerHTML   = `<span class="text-${a.activo ? 'success':'danger'}"><i class="fas fa-circle mr-1"></i>${a.activo ? 'Activo':'Inactivo'}</span>`;
    if (el('perfilRegistrado')) el('perfilRegistrado').textContent = (a.creado_en || '').replace('T',' ');
    if (el('perfilID'))         el('perfilID').textContent = data.estudiante_id;

    // Info básica
    if (el('perfilFechaNacimiento')) el('perfilFechaNacimiento').textContent = a.fecha_nacimiento || '—';
    if (el('perfilGradoGrupo'))      el('perfilGradoGrupo').textContent      = `${a.grado || '—'} ${a.grupo || ''}`.trim();
    if (el('perfilTutor'))           el('perfilTutor').textContent           = a.tutor_nombre || '—';

    // Métrica Promedio
    const prom = data.promedio_general;
    const promTxt = prom !== null ? Number(prom).toFixed(2) : '—';
    if (el('perfilPromedioValor')) el('perfilPromedioValor').textContent = promTxt;
    if (el('perfilPromedioBadge')) {
      const ok = prom !== null && Number(prom) >= 6;
      el('perfilPromedioBadge').className = `badge ${prom === null ? 'bg-secondary' : ok ? 'bg-success' : 'bg-danger'}`;
      el('perfilPromedioBadge').textContent = prom === null ? '—' : ok ? 'APROBADO' : 'REPROBADO';
    }

    // Materias
    const box = el('perfilMaterias');
    if (box) {
      const mats = data.materias || [];
      if (mats.length === 0) {
        box.innerHTML = `<div class="col-12"><div class="alert alert-light border">No hay materias asignadas.</div></div>`;
      } else {
        box.innerHTML = mats.map(m => `
          <div class="col-sm-6 col-lg-3 mb-4">
            <div class="subject-card h-100 d-flex flex-column">
              <img class="subject-cover" src="${m.foto_url || '../../assets/img/placeholder-materia.jpg'}" alt="${m.materia || ''}">
              <div class="subject-body d-flex flex-column flex-grow-1">
                <span class="status-chip mb-2">En curso</span>
                <div class="subject-title">${m.materia || ''}</div>
                <div class="mini text-muted"><i class="far fa-user mr-1"></i> Docente asignado</div>
                <div class="mt-auto">
                  <div class="divider"></div>
                  <a class="btn btn-sm btn-primary" href="../perfil-hijo/calificaciones-hijo.php?estudiante_id=${data.estudiante_id}">Ver detalles</a>
                </div>
              </div>
            </div>
          </div>
        `).join('');
      }
    }

    // Selector de hijos (si existe)
    if ($selHijo) {
      $selHijo.innerHTML = `<option value="">Selecciona un hijo</option>` +
        (data.hijos || []).map(h => 
          `<option value="${h.estudiante_id}" ${Number(h.estudiante_id)===Number(data.estudiante_id)?'selected':''}>
             ${h.nombre} — Grado ${h.grado}${h.grupo ? ' Grupo '+h.grupo : ''}
           </option>`).join('');
    }
  }

  // Cambiar de hijo desde el selector
  if ($selHijo) {
    $selHijo.addEventListener('change', async function(){
      if (!this.value) return;
      const data = await fetchPerfil(this.value);
      if (data.ok) renderPerfil(data);
    });
  }

  // Carga inicial
  (async () => {
    const id = qparam('estudiante_id') || '';
    const data = await fetchPerfil(id);
    if (data.ok) {
      renderPerfil(data);
    } else {
      // pinta error mínimo
      const cont = document.querySelector('.container-fluid') || document.body;
      const div = document.createElement('div');
      div.className = 'alert alert-danger';
      div.textContent = data.error || 'Error cargando perfil.';
      cont.prepend(div);
    }
  })();
});
