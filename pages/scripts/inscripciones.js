// File: inscripciones.js

$(function() {
  const $selClase = $('#selectClase');
  const $tblIns   = $('#tablaInscritos');
  const $tblOut   = $('#tablaNoInscritos');
  const $titulo   = $('#tituloClase');
  const $btnDer   = $('#btnPasarADerecha');
  const $btnIzq   = $('#btnPasarAIzquierda');
  const $form     = $('#formInscripciones');

  let todos       = [];
  let inscritos   = [];
  let noInscritos = [];

  // 1️⃣ Cargar clases con ciclo_id visible
  function cargarClases() {
    fetch('../php/obtener_clase.php')
      .then(r => r.json())
      .then(data => {
        $selClase
          .empty()
          .append('<option value="">Seleccione clase</option>');
        data.forEach(c => {
          $selClase.append(
            `<option value="${c.clase_id}" data-ciclo="${c.ciclo_id}">
               ${c.ciclo} • Grado ${c.grado}${c.grupo}
             </option>`
          );
        });
      })
      .catch(err => console.error('Error cargando clases:', err));
  }

  // 2️⃣ Cargar estudiantes, inscripciones por clase y por ciclo
  function cargarDatos(claseId) {
    if (!claseId) {
      $titulo.text('');
      inscritos = [];
      noInscritos = [];
      renderTablas();
      return;
    }

    const selectedOption = $selClase.find(':selected');
    const cicloId = selectedOption.data('ciclo');

    Promise.all([
      fetch('../php/obtener_estudiantes.php').then(r => r.json()),
      fetch(`../php/obtener_inscripciones.php?clase_id=${claseId}`).then(r => r.json()),
      fetch(`../php/obtener_inscripciones.php?ciclo_id=${cicloId}`).then(r => r.json())
    ])
    .then(([allEst, insClase, insCiclo]) => {
      todos = allEst.filter(e => e.activo == 1);
      const setClase = new Set(insClase.map(i => +i.estudiante_id));
      const setCiclo = new Set(insCiclo.map(i => +i.estudiante_id));

      inscritos = todos.filter(e => setClase.has(+e.estudiante_id));
      noInscritos = todos.filter(e => !setCiclo.has(+e.estudiante_id));

      $titulo.text($selClase.find(':selected').text());
      renderTablas();
    })
    .catch(err => console.error('Error cargando datos:', err));
  }

  // 3️⃣ Pintar las dos tablas
  function renderTablas() {
    const $inB  = $tblIns.find('tbody').empty();
    const $outB = $tblOut.find('tbody').empty();

    inscritos.forEach(e => {
      $inB.append(`
        <tr data-id="${e.estudiante_id}">
          <td><input type="checkbox" class="chkIns" data-id="${e.estudiante_id}"></td>
          <td>${e.nombre} ${e.apellido}</td>
        </tr>
      `);
    });

    noInscritos.forEach(e => {
      $outB.append(`
        <tr data-id="${e.estudiante_id}">
          <td><input type="checkbox" class="chkOut" data-id="${e.estudiante_id}"></td>
          <td>${e.nombre} ${e.apellido}</td>
        </tr>
      `);
    });
  }

  // 4️⃣ Pasar de no inscritos → inscritos
  $btnDer.on('click', () => {
    $tblOut.find('tbody .chkOut:checked').each(function() {
      const id = +$(this).data('id');
      const idx = noInscritos.findIndex(e => +e.estudiante_id === id);
      if (idx > -1) {
        inscritos.push(noInscritos[idx]);
        noInscritos.splice(idx, 1);
      }
    });
    renderTablas();
  });

  // 5️⃣ Pasar de inscritos → no inscritos
  $btnIzq.on('click', () => {
    $tblIns.find('tbody .chkIns:checked').each(function() {
      const id = +$(this).data('id');
      const idx = inscritos.findIndex(e => +e.estudiante_id === id);
      if (idx > -1) {
        noInscritos.push(inscritos[idx]);
        inscritos.splice(idx, 1);
      }
    });
    renderTablas();
  });

  // 6️⃣ Guardar inscripciones (enviando clase y ciclo)
  $form.on('submit', function(e) {
    e.preventDefault();
    const claseId = +$selClase.val();
    const cicloId = +$selClase.find(':selected').data('ciclo');

    if (!claseId || !cicloId) {
      return alert('Seleccione una clase válida.');
    }

    fetch('../php/guardar_inscripciones.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({
        clase_id: claseId,
        ciclo_id: cicloId,
        estudiantes: inscritos.map(e => e.estudiante_id)
      })
    })
    .then(r => r.json())
    .then(resp => {
      if (resp.success) {
        alert('Inscripciones guardadas correctamente.');
      } else {
        alert(resp.message || 'Error al guardar inscripciones.');
      }
    })
    .catch(err => {
      console.error('Error guardando inscripciones:', err);
      alert('Ocurrió un error al guardar.');
    });
  });

  // 7️⃣ Inicializar
  cargarClases();
  $selClase.on('change', () => cargarDatos($selClase.val()));
});
