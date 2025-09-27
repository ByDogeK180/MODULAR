// pages/scripts/cargarMateriasHijo.js
document.addEventListener('DOMContentLoaded', () => {
  const accordion = document.getElementById('accordionHijos');
  const inputBusqueda = document.getElementById('buscar-materia');
  const selectNivel   = document.getElementById('filtrar-nivel');

  let materiasOriginales = [];

  // ------------------------- Utils -------------------------
  function ensureModal() {
    if (document.getElementById('modalMateria')) return;

    const modal = document.createElement('div');
    modal.innerHTML = `
    <div class="modal fade" id="modalMateria" tabindex="-1" role="dialog" aria-labelledby="modalMateriaLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="modalMateriaLabel">Resumen de la materia</h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <div class="modal-body">
            <div id="mm-loading" class="py-3">Cargando…</div>
            <div id="mm-content" class="d-none">
              <div class="d-flex align-items-center mb-3">
                <img id="mm-docente-foto" src="" alt="Docente" class="rounded mr-3" style="width:56px;height:56px;object-fit:cover;display:none;">
                <div>
                  <h4 id="mm-materia-nombre" class="mb-1"></h4>
                  <div class="text-muted" id="mm-materia-sub"></div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-4 mb-3">
                  <div class="card h-100"><div class="card-body">
                    <h6 class="text-uppercase text-muted">Docente</h6>
                    <div id="mm-docente-nombre" class="font-weight-bold"></div>
                    <div id="mm-docente-correo" class="small text-muted"></div>
                  </div></div>
                </div>

                <div class="col-md-4 mb-3">
                  <div class="card h-100"><div class="card-body">
                    <h6 class="text-uppercase text-muted">Avance (promedio)</h6>
                    <div id="mm-promedio" class="display-4 mb-0">—</div>
                    <div class="small text-muted">Últimos periodos</div>
                  </div></div>
                </div>

                <div class="col-md-4 mb-3">
                  <div class="card h-100"><div class="card-body">
                    <h6 class="text-uppercase text-muted">Asistencia</h6>
                    <div id="mm-asistencia" class="display-4 mb-0">—%</div>
                    <div class="small text-muted" id="mm-asistencia-det">—</div>
                  </div></div>
                </div>
              </div>

              <div id="mm-descripcion" class="mt-2"></div>
            </div>
          </div>

          <div class="modal-footer">
            <button class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>`;
    document.body.appendChild(modal.firstElementChild);
  }

  async function abrirModalMateria(materiaId, estudianteId) {
    ensureModal();
    $('#mm-loading').removeClass('d-none');
    $('#mm-content').addClass('d-none');
    $('#modalMateria').modal('show');

    try {
      const qs = new URLSearchParams({ materia_id: materiaId, estudiante_id: estudianteId }).toString();
      const res = await fetch(`../php/obtener_materia_resumen.php?${qs}`, { credentials: 'same-origin' });
      const raw = await res.text();
      let data;
      try { data = JSON.parse(raw); } catch { throw new Error('Respuesta inválida del servidor: ' + raw); }
      if (!res.ok || data.error) throw new Error(data.error || 'No se pudo cargar el resumen');

      const m = data.materia || {};
      const s = data.stats || {};
      const a = s.asistencia || {};

      document.getElementById('mm-materia-nombre').textContent = m.nombre || 'Materia';
      document.getElementById('mm-materia-sub').textContent = `Nivel: ${m.nivel || '-'} · Ciclo: ${m.ciclo || '-'}`;

      document.getElementById('mm-docente-nombre').textContent = (m.docente && m.docente.nombre) || '—';
      document.getElementById('mm-docente-correo').textContent = (m.docente && m.docente.correo) || '';

      const foto = document.getElementById('mm-docente-foto');
      if (m.docente && m.docente.foto_url) { foto.src = m.docente.foto_url; foto.style.display = 'block'; }
      else { foto.style.display = 'none'; }

      document.getElementById('mm-promedio').textContent = s.promedio != null ? s.promedio : '—';
      document.getElementById('mm-asistencia').textContent = a.porcentaje != null ? `${a.porcentaje}%` : '—%';
      document.getElementById('mm-asistencia-det').textContent =
        a.total ? `${a.presentes}/${a.total} presentes` : 'Sin registros';

      document.getElementById('mm-descripcion').textContent = m.descripcion || '';

      $('#mm-loading').addClass('d-none');
      $('#mm-content').removeClass('d-none');
    } catch (err) {
      console.error(err);
      $('#mm-loading').addClass('d-none');
      $('#mm-content').removeClass('d-none').innerHTML = `<div class="alert alert-danger">${err.message}</div>`;
    }
  }

  // ------------------------- Carga inicial -------------------------
  fetch('../php/obtener_materias_hijo.php', { credentials: 'same-origin' })
    .then(async res => {
      const txt = await res.text();
      try { return JSON.parse(txt); } catch { throw new Error('Respuesta inválida: ' + txt); }
    })
    .then(json => {
      if (!json || !json.data) return;
      materiasOriginales = json.data;
      renderMaterias(materiasOriginales);
    })
    .catch(err => console.error('Error cargando materias:', err));

  // ------------------------- Filtros -------------------------
  inputBusqueda.addEventListener('input', aplicarFiltros);
  selectNivel.addEventListener('change', aplicarFiltros);

  function aplicarFiltros() {
    const texto = (inputBusqueda.value || '').toLowerCase();
    const nivel = (selectNivel.value || '').toLowerCase();

    const filtradas = materiasOriginales.filter(m => {
      const nombreMateria = (m.nombre_materia || '').toLowerCase();
      const nombreHijo = `${m.nombre_estudiante || m.nombre || ''} ${m.apellido_estudiante || m.apellido || ''}`.toLowerCase();
      const gradoGrupo = `${m.grado || ''} ${m.grupo || ''}`.toLowerCase();
      const coincideTexto = nombreMateria.includes(texto) || nombreHijo.includes(texto) || gradoGrupo.includes(texto);
      const coincideNivel = !nivel || ((m.nivel_grado || '').toLowerCase() === nivel);
      return coincideTexto && coincideNivel;
    });

    renderMaterias(filtradas);
  }

  // ------------------------- Render tarjetas -------------------------
  function renderMaterias(mats) {
    accordion.innerHTML = '';
    const hijosAgrupados = {};

    mats.forEach(m => {
      const id = m.estudiante_id;
      const nombre = m.nombre_estudiante || m.nombre || 'Desconocido';
      const apellido = m.apellido_estudiante || m.apellido || '';

      if (!hijosAgrupados[id]) {
        hijosAgrupados[id] = { nombre: `${nombre} ${apellido}`.trim(), materias: [] };
      }
      hijosAgrupados[id].materias.push(m);
    });

    let index = 0;
    for (const [id, hijo] of Object.entries(hijosAgrupados)) {
      const collapseId = `collapseHijo${index}`;

      const materiasHtml = hijo.materias.map(m => {
        const materiaId = m.materia_id;
        const estudianteId = m.estudiante_id;
        const gradoTxt = `${m.grado || '-'}° ${m.grupo || '-'}`;

        return `
          <div class="card shadow-sm mb-3">
            <div class="card-body">
              <h6 class="card-title mb-1 text-success">
                <i class="fas fa-book"></i> ${m.nombre_materia}
              </h6>
              <p class="card-text mb-1"><i class="fas fa-layer-group"></i> Nivel: ${m.nivel_grado || '-'}</p>
              <p class="card-text mb-2"><i class="fas fa-chalkboard"></i> Clase: ${gradoTxt}</p>

              <button class="btn btn-outline-primary btn-sm btn-ver-mas"
                      data-materia-id="${materiaId}"
                      data-estudiante-id="${estudianteId}">
                <i class="fa fa-eye"></i> Ver más
              </button>
            </div>
          </div>
        `;
      }).join('');

      const card = `
        <div class="card mb-3 hijo-card">
          <div class="card-header p-0" id="heading${index}">
            <h5 class="mb-0">
              <button class="btn btn-link w-100 text-center p-3 d-flex justify-content-center align-items-center" 
                      data-toggle="collapse" 
                      data-target="#${collapseId}" 
                      aria-expanded="${index === 0}" 
                      aria-controls="${collapseId}">
                <span class="d-flex align-items-center info-hijo">
                  <i class="fas fa-child"></i>
                  <span class="nombre-hijo">${hijo.nombre}</span>
                </span>
              </button>
            </h5>
          </div>

          <div id="${collapseId}" class="collapse ${index === 0 ? 'show' : ''}" data-parent="#accordionHijos">
            <div class="card-body">
              ${materiasHtml}
            </div>
          </div>
        </div>
      `;

      accordion.insertAdjacentHTML('beforeend', card);
      index++;
    }

    if (index === 0) {
      accordion.innerHTML = `<div class="alert alert-warning">No se encontraron materias o hijos que coincidan.</div>`;
    }
  }

  // ------------------------- Click "Ver más" (modal) -------------------------
  document.addEventListener('click', (ev) => {
    const btn = ev.target.closest('.btn-ver-mas');
    if (!btn) return;
    ev.preventDefault();

    const materiaId = Number(btn.dataset.materiaId || 0);
    const estudianteId = Number(btn.dataset.estudianteId || 0);
    if (!materiaId || !estudianteId) {
      alert('Faltan datos de la materia o del estudiante.');
      return;
    }
    abrirModalMateria(materiaId, estudianteId);
  });

});
