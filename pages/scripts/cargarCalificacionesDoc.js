// File: cargarCalificacionesDoc.js
document.addEventListener('DOMContentLoaded', () => {
  const selClase   = document.getElementById('selectClase');
  const selPeriodo = document.getElementById('selectPeriodo');
  const tbody      = document.querySelector('#tablaCalificaciones tbody');
  const headerFila = document.getElementById('headerFila');

  // Si no estamos en esta página, salir sin romper
  if (!selClase || !selPeriodo || !tbody || !headerFila) {
    console.warn('[calificaciones] Elementos no encontrados. ¿Estás en la página correcta?');
    return;
  }

  let materias = [];

  // ========== Cargar clases ==========
  fetch('../php/obtener_clases_docente.php')
    .then(res => res.json())
    .then(data => {
      (data || []).forEach(c => {
        const option = document.createElement('option');
        option.value = c.clase_id;
        option.textContent = `${c.ciclo} - Grado ${c.grado}${c.grupo}`;
        option.dataset.ciclo = c.ciclo_id;
        selClase.appendChild(option);
      });
    })
    .catch(err => console.error('Error clases:', err));

  // ========== Cambia clase ⇒ cargar periodos ==========
  selClase.addEventListener('change', () => {
    selPeriodo.innerHTML = '<option value="">Selecciona el periodo</option>';
    const cicloId = selClase.selectedOptions[0]?.dataset?.ciclo;
    if (!cicloId) return;

    fetch(`../php/obtener_periodos.php?ciclo_id=${encodeURIComponent(cicloId)}`)
      .then(res => res.json())
      .then(data => {
        (data || []).forEach(p => {
          const opt = document.createElement('option');
          opt.value = p.periodo_id;
          opt.textContent = p.nombre;
          selPeriodo.appendChild(opt);
        });
      })
      .catch(err => console.error('Error periodos:', err));
  });

  // ========== Cambia periodo ⇒ cargar materias/estudiantes/calificaciones ==========
  selPeriodo.addEventListener('change', () => {
    const claseId   = selClase.value;
    const periodoId = selPeriodo.value;
    if (!claseId || !periodoId) return;

    Promise.all([
      fetch(`../php/obtener_materias_por_clase.php?clase_id=${encodeURIComponent(claseId)}`).then(r => r.json()),
      fetch(`../php/obtener_estudiantes_por_clase.php?clase_id=${encodeURIComponent(claseId)}`).then(r => r.json()),
      fetch(`../php/obtener_calificaciones_docente.php?clase_id=${encodeURIComponent(claseId)}&periodo_id=${encodeURIComponent(periodoId)}`).then(r => r.json())
    ])
      .then(([mats, estudiantes, califs]) => {
        materias = mats || [];
        renderEncabezado(materias);
        renderTabla(estudiantes || [], califs || []);
      })
      .catch(err => console.error('Error al cargar datos:', err));
  });

  function renderEncabezado(mats) {
    headerFila.innerHTML = '<th>Estudiante</th>';
    mats.forEach(m => {
      const th = document.createElement('th');
      th.textContent = m.nombre;
      headerFila.appendChild(th);
    });
    headerFila.insertAdjacentHTML('beforeend', '<th>Promedio</th>');
  }

  function renderTabla(estudiantes, calificaciones) {
    tbody.innerHTML = '';
    estudiantes.forEach(est => {
      const tr = document.createElement('tr');
      tr.dataset.estudianteId = est.estudiante_id;

      // Nombre
      const tdNombre = document.createElement('td');
      tdNombre.textContent = `${est.nombre} ${est.apellido}`;
      tr.appendChild(tdNombre);

      let suma = 0, cuenta = 0;

      // Materias
      materias.forEach(mat => {
        const td = document.createElement('td');
        const input = document.createElement('input');
        input.type  = 'number';
        input.className = 'form-control form-control-sm';
        input.min = 0; input.max = 10; input.step = 0.1;
        input.dataset.materiaId = mat.materia_id;

        const existente = (calificaciones || []).find(c =>
          String(c.estudiante_id) === String(est.estudiante_id) &&
          String(c.materia_id)    === String(mat.materia_id)
        );
        if (existente && existente.valor != null && existente.valor !== '') {
          input.value = existente.valor;
          const num = parseFloat(existente.valor);
          if (!isNaN(num)) { suma += num; cuenta++; }
        }

        input.addEventListener('input', () => {
          const valores = [...tr.querySelectorAll('input')]
            .map(i => parseFloat(i.value))
            .filter(v => !isNaN(v));
          const prom = valores.length ? (valores.reduce((a,b)=>a+b,0) / valores.length) : NaN;
          tr.querySelector('.promedio').textContent =
            isNaN(prom) ? '-' : (Number.isInteger(prom) ? prom : prom.toFixed(1));
        });

        td.appendChild(input);
        tr.appendChild(td);
      });

      // Promedio
      const tdProm = document.createElement('td');
      tdProm.className = 'promedio';
      const promFinal = cuenta ? (suma / cuenta) : NaN;
      tdProm.textContent = isNaN(promFinal) ? '-' : (Number.isInteger(promFinal) ? promFinal : promFinal.toFixed(1));
      tr.appendChild(tdProm);

      tbody.appendChild(tr);
    });
  }

  // ========== Submit con DELEGACIÓN (no rompe si el form no existe aún) ==========
  document.addEventListener('submit', (e) => {
    const form = e.target.closest('#formCalificaciones');
    if (!form) return;            // ignorar otros formularios
    e.preventDefault();

    const claseId   = selClase.value;
    const periodoId = selPeriodo.value;
    if (!claseId || !periodoId) { alert('Selecciona clase y periodo'); return; }

    const payload = [];
    tbody.querySelectorAll('tr').forEach(tr => {
      const estudianteId = tr.dataset.estudianteId;
      const calificaciones = [];
      tr.querySelectorAll('input').forEach(input => {
        const materiaId = input.dataset.materiaId;
        const valor = parseFloat(input.value);
        if (!isNaN(valor)) calificaciones.push({ materia_id: materiaId, valor });
      });
      payload.push({ estudiante_id: estudianteId, calificaciones });
    });

    fetch('../php/guardar_calificacion_docente.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ clase_id: claseId, periodo_id: periodoId, calificaciones: payload })
    })
      .then(r => r.json())
      .then(resp => {
        if (resp && resp.success) {
          alert('Calificaciones guardadas correctamente.');
        } else {
          alert('Error al guardar calificaciones.');
        }
      })
      .catch(err => {
        console.error('Error al guardar:', err);
        alert('Error al guardar calificaciones.');
      });
  });
});
