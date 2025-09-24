// scripts/cargarCalificacionesTutor.js
document.addEventListener('DOMContentLoaded', function () {
  const selectHijo = document.getElementById('selectHijo');
  const selectPeriodo = document.getElementById('selectPeriodo');
  const tbody = document.querySelector('#tablaCalificaciones tbody');
  const tfoot = document.querySelector('#tablaCalificaciones tfoot');
  const btnGuardar = document.getElementById('btnGuardarCalificaciones');

  let graficoInstancia = null;

  if (btnGuardar) btnGuardar.style.display = 'none';
  if (selectPeriodo) selectPeriodo.disabled = true;

  function showError(msg) {
    tbody.innerHTML = `<tr><td colspan="3" class="text-danger">${msg}</td></tr>`;
    if (tfoot) tfoot.innerHTML = '';
  }

  function cargaHijos() {
    fetch('../php/get_hijos.php', { method: 'GET', credentials: 'include' })
      .then(r => r.json())
      .then(data => {
        if (data.error) return showError(data.error);
        selectHijo.innerHTML = '<option value="">Selecciona un hijo</option>';
        data.hijos.forEach(h => {
          const opt = document.createElement('option');
          opt.value = h.estudiante_id;
          opt.textContent = `${h.nombre} — Grado ${h.grado}${h.grupo ? (' Grupo ' + h.grupo) : ''}`;
          selectHijo.appendChild(opt);
        });
      })
      .catch(() => showError('Error cargando hijos.'));
  }

  async function cargaCalificaciones(estudianteId) {
    tbody.innerHTML = '<tr><td colspan="3">Cargando...</td></tr>';
    if (tfoot) tfoot.innerHTML = '';

    const periodo = (selectPeriodo && selectPeriodo.value) ? parseInt(selectPeriodo.value) : '';
    let url = `../php/get_calificaciones_hijo.php?estudiante_id=${estudianteId}`;
    if (periodo) url += `&periodo_id=${periodo}`;

    try {
      const res = await fetch(url, { method: 'GET', credentials: 'include' });
      const json = await res.json();
      if (json.error) return showError(json.error);

      const materias = json.materias || [];
      window.__materiasCache = materias;

      if (materias.length === 0) {
        tbody.innerHTML = '<tr><td colspan="3">No hay calificaciones registradas.</td></tr>';
        return;
      }

      // Ordenar por nombre
      materias.sort((a, b) => (a.materia || '').localeCompare(b.materia || ''));

      tbody.innerHTML = '';
      materias.forEach(m => {
        const tr = document.createElement('tr');

        // Materia
        const tdMateria = document.createElement('td');
        tdMateria.classList.add('text-start');
        const imgHtml = m.foto_url
          ? `<img src="${m.foto_url}" alt="${m.materia}" style="width:36px;height:36px;object-fit:cover;border-radius:6px;margin-right:8px;">`
          : `<i class="flaticon-book" style="font-size:20px;margin-right:8px;"></i>`;
        tdMateria.innerHTML =
          `<div class="d-flex align-items-center">
            ${imgHtml}
            <div>
              <strong>${m.materia}</strong>
              <div class="text-muted fs-12">Materia</div>
            </div>
          </div>`;

        // Calificación (por materia)
        const tdCalificacion = document.createElement('td');
        const calif = (m.calificacion ?? m.promedio ?? null);
        const califNum = (calif !== null) ? Number(calif) : null;
        const califTxt = (califNum !== null) ? califNum.toFixed(2) : '--';
        let badgeMateria = 'badge bg-secondary';
        if (califNum !== null) {
          if (califNum >= 8) badgeMateria = 'badge bg-success';
          else if (califNum >= 6) badgeMateria = 'badge bg-warning';
          else badgeMateria = 'badge bg-danger';
        }
        tdCalificacion.innerHTML =
          `<span class="${badgeMateria}" style="font-size:1rem;padding:.45rem .6rem">${califTxt}</span>`;

        // Acciones
        const tdAcciones = document.createElement('td');
        tdAcciones.innerHTML =
          `<button type="button" class="btn btn-sm btn-info ver-detalle" 
                   data-calid="${m.calificacion_id || ''}" 
                   ${(!m.detalles || m.detalles.length === 0) ? 'disabled' : ''}>
             <i class="flaticon-list"></i> Ver detalle
           </button>`;

        tr.appendChild(tdMateria);
        tr.appendChild(tdCalificacion);
        tr.appendChild(tdAcciones);
        tbody.appendChild(tr);
      });

      // Footer resumen
      const promGeneral = (json.promedio_general ?? null);
      const minAprob = (json.min_aprobatoria ?? 6);
      const aprobado = json.aprobado;

      if (tfoot) {
        const badgeGen = (promGeneral !== null)
          ? (promGeneral >= minAprob ? 'badge bg-success' : 'badge bg-danger')
          : 'badge bg-secondary';

        const valorGen = (promGeneral !== null) ? promGeneral.toFixed(2) : '--';
        const estadoTxt = (aprobado === null) ? '' : (aprobado ? 'APROBADO' : 'REPROBADO');
        const estadoCls = (aprobado === null) ? '' : (aprobado ? 'aprobado' : 'reprobado');

        tfoot.innerHTML = `
          <tr class="summary-row">
            <td colspan="3">
              <div class="summary-wrap">
                <div class="summary-kpi">
                  <span class="summary-label">Promedio General (mín. ${minAprob})</span>
                  <span class="summary-value">
                    <span class="${badgeGen}">${valorGen}</span>
                  </span>
                </div>
                <span class="summary-status ${estadoCls}">${estadoTxt}</span>
              </div>
            </td>
          </tr>`;
      }

    } catch (err) {
      showError('Error al cargar calificaciones');
    }
  }

  // Cargar Chart.js si hace falta
  function ensureChartJs() {
    return new Promise((resolve, reject) => {
      if (typeof Chart !== 'undefined') return resolve();
      const s = document.createElement('script');
      s.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js';
      s.onload = () => resolve();
      s.onerror = () => reject(new Error('No se pudo cargar Chart.js'));
      document.head.appendChild(s);
    });
  }

  // Delegación de eventos para "Ver detalle"
  tbody.addEventListener('click', async function (e) {
    const btn = e.target.closest('.ver-detalle');
    if (!btn) return;

    e.preventDefault();
    e.stopPropagation();

    const calid = btn.dataset.calid;
    const materias = window.__materiasCache || [];
    const mat = materias.find(x => String(x.calificacion_id) === String(calid));
    if (!mat) return;

    // Modal refs
    const modalEl = document.getElementById('modalDetalle');
    if (!modalEl) return;
    const modalTitle = modalEl.querySelector('.modal-title');
    const modalBody  = modalEl.querySelector('.modal-body');

    if (modalTitle) modalTitle.textContent = `Detalle de ${mat.materia || 'Materia'}`;

    // Estructura del modal (lista + canvas)
    if (modalBody) {
      modalBody.innerHTML = `
        <ul class="list-group mb-3" id="listaEvaluaciones"></ul>
        <canvas id="graficoEvaluaciones" height="140"></canvas>
      `;
    }

    // ---------- Lista + gráfico considerando periodo (con fallbacks robustos) ----------
    const ul = document.getElementById('listaEvaluaciones');
    if (ul) ul.innerHTML = '';

    const periodoSeleccionado = (selectPeriodo && selectPeriodo.value) ? String(selectPeriodo.value) : '';
    const labels = [];
    const valores = [];

    const detalles = Array.isArray(mat.detalles) ? mat.detalles : [];
    // Hay info de periodo si periodo_id NO es null/undefined, o si 'periodo' viene con texto
    const tieneInfoPeriodo = detalles.some(d => (d && (d.periodo_id != null || (d.periodo && String(d.periodo).trim() !== ''))));

    if (periodoSeleccionado === '') {
      // "Todos"
      if (tieneInfoPeriodo) {
        // Agrupar por periodo y promediar
        const porPeriodo = new Map();
        detalles.forEach(d => {
          const etiqueta = (d.periodo && String(d.periodo).trim()) ? d.periodo : `Periodo ${d.periodo_id ?? ''}`;
          const val = Number(d.valor);
          if (!porPeriodo.has(etiqueta)) porPeriodo.set(etiqueta, []);
          porPeriodo.get(etiqueta).push(val);
        });
        porPeriodo.forEach((arr, etiqueta) => {
          const prom = arr.reduce((a, b) => a + b, 0) / arr.length;
          if (ul) {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between';
            li.innerHTML = `${etiqueta} <span class="fw-bold">${prom.toFixed(2)}</span>`;
            ul.appendChild(li);
          }
          labels.push(etiqueta);
          valores.push(Number(prom.toFixed(2)));
        });
      } else {
        // Sin info de periodo → lista simple
        detalles.forEach((d, i) => {
          if (ul) {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between';
            li.innerHTML = `Evaluación ${i + 1} <span class="fw-bold">${Number(d.valor).toFixed(2)}</span>`;
            ul.appendChild(li);
          }
          labels.push(`Eval ${i + 1}`);
          valores.push(Number(d.valor));
        });
        if (detalles.length === 0 && ul) {
          ul.innerHTML = '<li class="list-group-item">No hay detalles de calificaciones.</li>';
        }
      }

    } else {
      // Periodo específico seleccionado
      if (tieneInfoPeriodo) {
        const filtrados = detalles.filter(d => Number(d.periodo_id) === Number(periodoSeleccionado));
        if (filtrados.length > 0) {
          filtrados.forEach((d, i) => {
            if (ul) {
              const li = document.createElement('li');
              li.className = 'list-group-item d-flex justify-content-between';
              li.innerHTML = `Evaluación ${i + 1} <span class="fw-bold">${Number(d.valor).toFixed(2)}</span>`;
              ul.appendChild(li);
            }
            labels.push(`Eval ${i + 1}`);
            valores.push(Number(d.valor));
          });
        } else if (detalles.length > 0) {
          // Fallback: el backend ya filtró y no mandó periodo_id → mostramos las que hay
          detalles.forEach((d, i) => {
            if (ul) {
              const li = document.createElement('li');
              li.className = 'list-group-item d-flex justify-content-between';
              li.innerHTML = `Evaluación ${i + 1} <span class="fw-bold">${Number(d.valor).toFixed(2)}</span>`;
              ul.appendChild(li);
            }
            labels.push(`Eval ${i + 1}`);
            valores.push(Number(d.valor));
          });
        } else if (ul) {
          ul.innerHTML = '<li class="list-group-item">No hay calificaciones para el periodo seleccionado.</li>';
        }
      } else {
        // Sin info de periodo → mostramos las que hay (probablemente el backend ya filtró)
        detalles.forEach((d, i) => {
          if (ul) {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between';
            li.innerHTML = `Evaluación ${i + 1} <span class="fw-bold">${Number(d.valor).toFixed(2)}</span>`;
            ul.appendChild(li);
          }
          labels.push(`Eval ${i + 1}`);
          valores.push(Number(d.valor));
        });
        if (detalles.length === 0 && ul) {
          ul.innerHTML = '<li class="list-group-item">No hay calificaciones para el periodo seleccionado.</li>';
        }
      }
    }

    // Graficar
    try {
      await ensureChartJs();
      const canvas = document.getElementById('graficoEvaluaciones');
      if (canvas) {
        if (graficoInstancia) { graficoInstancia.destroy(); graficoInstancia = null; }
        graficoInstancia = new Chart(canvas, {
          type: 'bar',
          data: {
            labels,
            datasets: [{
              label: (periodoSeleccionado === '' && tieneInfoPeriodo) ? 'Promedio por periodo' : 'Calificaciones',
              data: valores,
              backgroundColor: '#4e73df'
            }]
          },
          options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { suggestedMin: 0, suggestedMax: 10, ticks: { stepSize: 1 } } }
          }
        });
      }
    } catch (err) {
      console.error(err);
    }

    // Mostrar modal
    if (window.$ && typeof $().modal === 'function') {
      $('#modalDetalle').modal('show');
    } else if (window.bootstrap?.Modal) {
      new bootstrap.Modal(modalEl).show();
    }
  });

  // eventos
  selectHijo.addEventListener('change', function () {
    if (this.value) {
      if (selectPeriodo) {
        selectPeriodo.disabled = false;
        selectPeriodo.value = "";
      }
      cargaCalificaciones(this.value);
    } else {
      tbody.innerHTML = '<tr><td colspan="3">Selecciona un hijo</td></tr>';
      if (tfoot) tfoot.innerHTML = '';
      if (selectPeriodo) {
        selectPeriodo.disabled = true;
        selectPeriodo.value = "";
      }
    }
  });

  function cargarPeriodos() { /* opcional */ }

  cargaHijos();
  cargarPeriodos();
});
