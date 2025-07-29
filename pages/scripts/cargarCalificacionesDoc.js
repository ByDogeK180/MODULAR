// File: cargarCalificacionesDoc.js

document.addEventListener('DOMContentLoaded', () => {
  const selClase = document.getElementById('selectClase');
  const selPeriodo = document.getElementById('selectPeriodo');
  const tbody = document.querySelector('#tablaCalificaciones tbody');
  const form = document.getElementById('formCalificaciones');

  let estudiantes = [];

  // Cargar clases
  fetch('../php/obtener_clases_docente.php')
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

  // Al cambiar clase, cargar periodos del ciclo
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

  // Al seleccionar clase y periodo, cargar alumnos y calificaciones
  selPeriodo.addEventListener('change', () => {
    const claseId = selClase.value;
    const periodoId = selPeriodo.value;
    if (!claseId || !periodoId) return;

    // 🔁 CAMBIADO aquí el archivo a usar:
    fetch(`../php/obtener_estudiantes_por_clase.php?clase_id=${claseId}`)
      .then(r => r.json())
      .then(data => {
        estudiantes = data;
        return fetch(`../php/obtener_calificaciones_docente.php?clase_id=${claseId}&periodo_id=${periodoId}`);
      })
      .then(r => r.json())
      .then(calif => renderTabla(estudiantes, calif))
      .catch(err => console.error('Error:', err));
  });

  function renderTabla(estudiantes, calificaciones) {
    tbody.innerHTML = '';
    estudiantes.forEach(est => {
      const tr = document.createElement('tr');
      tr.dataset.estudianteId = est.estudiante_id;

      const tdNombre = document.createElement('td');
      tdNombre.textContent = `${est.nombre} ${est.apellido}`;

      const tdCalif = document.createElement('td');
      tdCalif.className = 'inputs-calif';
      const califEst = calificaciones.find(c => c.estudiante_id == est.estudiante_id);
      const valores = califEst?.detalles || [];
      valores.forEach(valor => {
        const input = crearInput(valor.valor);
        tdCalif.appendChild(input);
      });
      if (valores.length === 0) {
        tdCalif.appendChild(crearInput());
      }

      const tdProm = document.createElement('td');
      tdProm.className = 'promedio';
      tdProm.textContent = calcularPromedio(tdCalif);

      const tdBtn = document.createElement('td');
      const btn = document.createElement('button');
      btn.className = 'btn btn-sm btn-outline-success';
      btn.type = 'button';
      btn.innerHTML = '<i class="fas fa-plus"></i>';
      btn.addEventListener('click', () => {
        tdCalif.appendChild(crearInput());
        tdProm.textContent = calcularPromedio(tdCalif);
      });
      tdBtn.appendChild(btn);

      tr.append(tdNombre, tdCalif, tdProm, tdBtn);
      tbody.appendChild(tr);
    });
  }

  function crearInput(valor = '') {
    const input = document.createElement('input');
    input.type = 'number';
    input.min = 0;
    input.max = 10;
    input.step = 0.1;
    input.value = valor;
    input.className = 'form-control form-control-sm d-inline-block';
    input.style.width = '60px';
    input.addEventListener('input', () => {
      const tr = input.closest('tr');
      const tdProm = tr.querySelector('.promedio');
      const cont = tr.querySelector('.inputs-calif');
      tdProm.textContent = calcularPromedio(cont);
    });
    return input;
  }

  function calcularPromedio(container) {
    const inputs = container.querySelectorAll('input');
    const valores = [...inputs].map(i => parseFloat(i.value)).filter(v => !isNaN(v));
    if (valores.length === 0) return '-';
    const suma = valores.reduce((a, b) => a + b, 0);
    return (suma / valores.length).toFixed(2);
  }

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    const claseId = selClase.value;
    const periodoId = selPeriodo.value;
    if (!claseId || !periodoId) return alert('Selecciona clase y periodo');

    const payload = [];
    tbody.querySelectorAll('tr').forEach(tr => {
      const estudianteId = tr.dataset.estudianteId;
      const valores = [...tr.querySelectorAll('input')]
        .map(i => parseFloat(i.value))
        .filter(v => !isNaN(v));
      payload.push({ estudiante_id: estudianteId, valores });
    });

    fetch('../php/guardar_calificacion_docente.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ clase_id: claseId, periodo_id: periodoId, calificaciones: payload })
    })
      .then(r => r.json())
      .then(resp => {
        if (resp.success) alert('Calificaciones guardadas');
        else alert('Error al guardar');
      })
      .catch(err => console.error('Error al guardar:', err));
  });
});
