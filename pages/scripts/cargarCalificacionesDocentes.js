// File: cargarCalificacionesDocentes.js

document.addEventListener('DOMContentLoaded', () => {
  const selClase = document.getElementById('selectClase');
  const selPeriodo = document.getElementById('selectPeriodo');
  const tbody = document.querySelector('#tablaCalificaciones tbody');
  const form = document.getElementById('formCalificaciones');
  const headerFila = document.getElementById('headerFila');

  let materias = [];

  // Cargar clases disponibles para el docente logueado
  fetch('../php/obtener_clases_docenteda.php')
    .then(res => res.json())
    .then(data => {
      data.forEach(c => {
        const option = document.createElement('option');
        option.value = c.clase_id;
        option.textContent = `${c.ciclo} - Grado ${c.grado}${c.grupo}`;
        option.dataset.ciclo = c.ciclo_id;
        selClase.appendChild(option);
      });
    });

  // Al cambiar clase, cargar periodos
  selClase.addEventListener('change', () => {
    selPeriodo.innerHTML = '<option value="">Selecciona periodo</option>';
    const cicloId = selClase.selectedOptions[0]?.dataset?.ciclo;
    if (!cicloId) return;

    fetch(`../php/obtener_periodos.php?ciclo_id=${cicloId}`)
      .then(res => res.json())
      .then(data => {
        data.forEach(p => {
          const opt = document.createElement('option');
          opt.value = p.periodo_id;
          opt.textContent = p.nombre;
          selPeriodo.appendChild(opt);
        });
      });
  });

  // Al seleccionar clase y periodo, cargar materias, estudiantes y calificaciones
  selPeriodo.addEventListener('change', () => {
    const claseId = selClase.value;
    const periodoId = selPeriodo.value;
    if (!claseId || !periodoId) return;

    Promise.all([
      fetch(`../php/obtener_materias_por_clase.php?clase_id=${claseId}`).then(r => r.json()),
      fetch(`../php/obtener_estudiantes_por_clase.php?clase_id=${claseId}`).then(r => r.json()),
      fetch(`../php/obtener_calificaciones_docente.php?clase_id=${claseId}&periodo_id=${periodoId}`).then(r => r.json())
    ])
      .then(([mats, estudiantes, califs]) => {
        materias = mats;
        renderEncabezado(mats);
        renderTabla(estudiantes, califs);
      })
      .catch(err => console.error('Error al cargar:', err));
  });

  function renderEncabezado(materias) {
    headerFila.innerHTML = '<th>Estudiante</th>';
    materias.forEach(m => {
      const th = document.createElement('th');
      th.textContent = m.nombre;
      headerFila.appendChild(th);
    });
    headerFila.innerHTML += '<th>Promedio</th>';
  }

  function renderTabla(estudiantes, calificaciones) {
    tbody.innerHTML = '';
    estudiantes.forEach(est => {
      const tr = document.createElement('tr');
      tr.dataset.estudianteId = est.estudiante_id;

      const tdNombre = document.createElement('td');
      tdNombre.textContent = `${est.nombre} ${est.apellido}`;
      tr.appendChild(tdNombre);

      let suma = 0;
      let cuenta = 0;

      materias.forEach(mat => {
        const td = document.createElement('td');
        const input = document.createElement('input');
        input.type = 'number';
        input.className = 'form-control form-control-sm';
        input.min = 0;
        input.max = 10;
        input.step = 0.1;
        input.dataset.materiaId = mat.materia_id;

        const valor = calificaciones.find(c =>
          c.estudiante_id == est.estudiante_id && c.materia_id == mat.materia_id)?.valor;

        if (valor !== undefined) {
          input.value = valor;
          suma += parseFloat(valor);
          cuenta++;
        }

        input.addEventListener('input', () => {
          const rowInputs = tr.querySelectorAll('input');
          const valores = [...rowInputs].map(i => parseFloat(i.value)).filter(v => !isNaN(v));
          const promedio = valores.reduce((a, b) => a + b, 0) / valores.length;
          tr.querySelector('.promedio').textContent = isNaN(promedio)
            ? '-'
            : (Number.isInteger(promedio) ? promedio : promedio.toFixed(1));
        });

        td.appendChild(input);
        tr.appendChild(td);
      });

      const tdProm = document.createElement('td');
      tdProm.className = 'promedio';
      const promedioFinal = suma / cuenta;
      tdProm.textContent = cuenta
        ? Number.isInteger(promedioFinal)
          ? promedioFinal
          : promedioFinal.toFixed(1)
        : '-';
      tr.appendChild(tdProm);

      tbody.appendChild(tr);
    });
  }

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    const claseId = selClase.value;
    const periodoId = selPeriodo.value;
    if (!claseId || !periodoId) return alert('Selecciona clase y periodo');

    const payload = [];
    tbody.querySelectorAll('tr').forEach(tr => {
      const estudianteId = tr.dataset.estudianteId;
      const calificaciones = [];

      tr.querySelectorAll('input').forEach(input => {
        const materiaId = input.dataset.materiaId;
        const valor = parseFloat(input.value);
        if (!isNaN(valor)) {
          calificaciones.push({ materia_id: materiaId, valor });
        }
      });

      payload.push({ estudiante_id: estudianteId, calificaciones });
    });

    fetch('../php/guardar_calificacion_docenteda.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ clase_id: claseId, periodo_id: periodoId, calificaciones: payload })
    })
      .then(r => r.json())
      .then(resp => {
        if (resp.success) alert('Calificaciones guardadas correctamente.');
        else alert('Error al guardar calificaciones.');
      })
      .catch(err => {
        console.error('Error al guardar:', err);
        alert('Error al guardar calificaciones.');
      });
  });
});
