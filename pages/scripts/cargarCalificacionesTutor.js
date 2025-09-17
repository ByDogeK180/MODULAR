// scripts/cargarCalificacionesTutor.js
document.addEventListener('DOMContentLoaded', function () {
  const selectHijo = document.getElementById('selectHijo');
  const selectPeriodo = document.getElementById('selectPeriodo'); 
  const tabla = document.querySelector('#tablaCalificaciones tbody');
  const btnGuardar = document.querySelector('#btnGuardarCalificaciones');

  if (btnGuardar) btnGuardar.style.display = 'none'; 

  function showError(msg) {
    tabla.innerHTML = `<tr><td colspan="3" class="text-danger">${msg}</td></tr>`;
  }

  function cargaHijos() {
    fetch('../php/get_hijos.php', {
      method: 'GET',
      credentials: 'include'   // 👈 muy importante
    })
    .then(r => r.json())
    .then(data => {
      if (data.error) return showError(data.error);
      selectHijo.innerHTML = '<option value="">Selecciona un hijo</option>';
      data.hijos.forEach(h => {
        const opt = document.createElement('option');
        opt.value = h.estudiante_id;
        opt.textContent = `${h.nombre} — Grado ${h.grado}${h.grupo? (' Grupo ' + h.grupo):''}`;
        selectHijo.appendChild(opt);
      });
    })
    .catch(e => showError('Error cargando hijos.'));
  }

  function cargarPeriodos() {
    // opcional
  }

  async function cargaCalificaciones(estudianteId) {
    tabla.innerHTML = '<tr><td colspan="3">Cargando...</td></tr>';
    const periodo = selectPeriodo ? selectPeriodo.value : '';
    let url = `../php/get_calificaciones_hijo.php?estudiante_id=${estudianteId}`;
    if (periodo) url += `&periodo_id=${periodo}`;
    try {
      const res = await fetch(url, {
        method: 'GET',
        credentials: 'include'   // 👈 también aquí
      });
      const json = await res.json();
      if (json.error) return showError(json.error);

      const materias = json.materias || [];
      if (materias.length === 0) {
        tabla.innerHTML = '<tr><td colspan="3">No hay calificaciones registradas.</td></tr>';
        return;
      }

      tabla.innerHTML = '';
      materias.forEach(m => {
        const tr = document.createElement('tr');

        const tdMateria = document.createElement('td');
        tdMateria.classList.add('text-start');
        let imgHtml = '';
        if (m.foto_url) {
            const ruta = m.foto_url.replace('assets/img/uploads/', '../../assets/img/materias/');
          imgHtml = `<img src="${ruta}" alt="${m.materia}" style="width:36px;height:36px;object-fit:cover;border-radius:6px;margin-right:8px;">`;
        } else {
          imgHtml = `<i class="flaticon-book" style="font-size:20px;margin-right:8px;"></i>`;
        }
        tdMateria.innerHTML = `<div class="d-flex align-items-center">${imgHtml}<div><strong>${m.materia}</strong><div class="text-muted fs-12">Materia</div></div></div>`;

        const tdPromedio = document.createElement('td');
        let prom = (m.promedio !== null && m.promedio !== undefined) ? m.promedio.toFixed(2) : '--';
        let badgeClass = 'badge bg-secondary';
        if (m.promedio !== null) {
          if (m.promedio >= 8) badgeClass = 'badge bg-success';
          else if (m.promedio >= 6) badgeClass = 'badge bg-warning';
          else badgeClass = 'badge bg-danger';
        }
        tdPromedio.innerHTML = `<span class="${badgeClass}" style="font-size:1rem;padding:.45rem .6rem">${prom}</span>`;

        const tdAcciones = document.createElement('td');
        tdAcciones.innerHTML = `<button class="btn btn-sm btn-info ver-detalle" data-calid="${m.calificacion_id || ''}" ${(!m.detalles || m.detalles.length===0)?'disabled':''}><i class="flaticon-list"></i> Ver detalle</button>`;

        tr.appendChild(tdMateria);
        tr.appendChild(tdPromedio);
        tr.appendChild(tdAcciones);

        tabla.appendChild(tr);
      });

      document.querySelectorAll('.ver-detalle').forEach(btn => {
        btn.addEventListener('click', function () {
          const calid = this.dataset.calid;
          const mat = materias.find(x => String(x.calificacion_id) === String(calid));
          if (!mat) return;
          const modalBody = document.querySelector('#modalDetalle .modal-body');
          if (!modalBody) return;
          modalBody.innerHTML = '';
          if (!mat.detalles || mat.detalles.length === 0) {
            modalBody.innerHTML = '<p>No hay detalles de calificaciones.</p>';
          } else {
            const ul = document.createElement('ul');
            ul.className = 'list-group';
            mat.detalles.forEach(d => {
              const li = document.createElement('li');
              li.className = 'list-group-item d-flex justify-content-between align-items-center';
              li.textContent = `Evaluación ${d.numero}`;
              const span = document.createElement('span');
              span.textContent = d.valor.toFixed(2);
              li.appendChild(span);
              ul.appendChild(li);
            });
            modalBody.appendChild(ul);
          }
          // abrir modal (bootstrap)
          $('#modalDetalle').modal('show');
        });
      });

    } catch (err) {
      showError('Error al cargar calificaciones');
    }
  }

  // eventos
  selectHijo.addEventListener('change', function () {
    if (this.value) cargaCalificaciones(this.value);
    else tabla.innerHTML = '<tr><td colspan="3">Selecciona un hijo</td></tr>';
  });

  cargaHijos();
  cargarPeriodos();
});
