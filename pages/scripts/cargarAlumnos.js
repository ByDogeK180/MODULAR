// ../scripts/cargarAlumnos.js
function cargarEstudiantes(materiaId = null) {
  const isDocente = typeof userRol !== 'undefined' && userRol == 1;

  let url = '../php/estudiantes-doc.php';
  if (isDocente && materiaId) {
    url += `?materia_id=${encodeURIComponent(materiaId)}`;
  }

  $.ajax({
    url,
    method: 'GET',
    dataType: 'json',
    success: (data) => {
      console.log('ESTUDIANTES RECIBIDOS:', data);

      let tbody = '';
      data.forEach((est) => {
        if (String(est.activo) === '1') {
          tbody += `
            <tr>
              <td>${est.estudiante_id}</td>
              <td>${est.tutor_id}</td>
              <td>${est.tutor_nombre || 'Sin tutor'}</td>
              <td>${est.nombre}</td>
              <td>${est.apellido}</td>
              <td>${est.fecha_nacimiento}</td>
              <td>${est.grado}</td>
              <td>${est.grupo}</td>
              <td>${est.activo == 1 ? 'Sí' : 'No'}</td>
              <td>${est.creado_en}</td>
              <td>${est.actualizado_en}</td>
              <td>
                <button class="btn btn-sm btn-outline-warning btn-editar" data-id="${est.estudiante_id}">Editar</button>
                <button class="btn btn-sm btn-outline-danger btn-eliminar" data-id="${est.estudiante_id}">Eliminar</button>
              </td>
            </tr>
          `;
        }
      });

      // Usa el ID REAL de la tabla en tu HTML
      const $tabla = $('#data-table-4');

      // Si ya estaba inicializada, destruye antes de reinyectar
      if ($.fn.DataTable.isDataTable($tabla)) {
        $tabla.DataTable().clear().destroy();
      }

      $('#student-body').html(tbody);

      // Inicializa DataTable y actualiza contador visible (si existe el span)
      $tabla.DataTable({
        responsive: true,
        pageLength: 10,
        language: {
          search: 'Buscar:',
          lengthMenu: 'Mostrar _MENU_ registros por página',
          zeroRecords: 'No se encontraron resultados',
          info: 'Mostrando página _PAGE_ de _PAGES_',
          infoEmpty: 'No hay registros disponibles',
          infoFiltered: '(filtrado de _MAX_ registros totales)',
          paginate: { next: 'Siguiente', previous: 'Anterior' }
        },
        drawCallback: function () {
          const api = this.api();
          const visibles = api.rows({ page: 'current' }).count();
          const counter = document.getElementById('total-estudiantes');
          if (counter) counter.textContent = visibles;
        }
      });
    },
    error: (xhr, status, err) => {
      console.error('Error al cargar estudiantes:', err);
      $('#student-body').html('<tr><td colspan="12">Error al cargar estudiantes</td></tr>');
      const counter = document.getElementById('total-estudiantes');
      if (counter) counter.textContent = 0;
    }
  });
}

// Carga opciones del SELECT de tutores en el modal (usa #ed_tutor_id del HTML)
function cargarTutores(selectedId = null) {
  return fetch('../php/tutores_opciones.php')
    .then((res) => {
      if (!res.ok) throw new Error('HTTP ' + res.status);
      return res.json();
    })
    .then((tutores) => {
      let options = '<option value="" disabled>Seleccione un tutor</option>';
      tutores.forEach((tutor) => {
        const sel = String(tutor.tutor_id) === String(selectedId) ? ' selected' : '';
        options += `<option value="${tutor.tutor_id}"${sel}>${tutor.nombre} ${tutor.apellido}</option>`;
      });
      $('#ed_tutor_id').html(options);
    })
    .catch((err) => {
      console.error('Error cargando tutores:', err);
      $('#ed_tutor_id').html('<option value="">Error al cargar tutores</option>');
    });
}

function cargarMateriasDocente() {
  fetch('../php/materias-docente.php')
    .then((r) => r.json())
    .then((materias) => {
      let opciones = '<option value="">Seleccione una materia</option>';
      materias.forEach((m) => {
        opciones += `<option value="${m.materia_id}">${m.materia} - ${m.ciclo} - Grupo ${m.grupo}</option>`;
      });
      $('#materia-select').html(opciones);
    })
    .catch((err) => console.error('Error al cargar materias del docente:', err));
}

$(document).ready(function () {
  const isDocente = typeof userRol !== 'undefined' && userRol == 1;

  if (isDocente) {
    cargarMateriasDocente();
    $('#materia-select').on('change', function () {
      const materiaId = $(this).val();
      if (materiaId) {
        cargarEstudiantes(materiaId);
      } else {
        $('#student-body').html('');
        if ($.fn.DataTable.isDataTable('#data-table-4')) {
          $('#data-table-4').DataTable().clear().draw();
        }
        const counter = document.getElementById('total-estudiantes');
        if (counter) counter.textContent = 0;
      }
    });
  } else {
    $('#materia-select').closest('.form-group').hide();
    cargarEstudiantes();
  }

  // Eliminar
  $(document).on('click', '.btn-eliminar', function () {
    const id = $(this).data('id');
    if (!confirm('¿Deseas eliminar este estudiante?')) return;

    fetch('../php/eliminar_estudiante.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id })
    })
      .then((r) => r.json())
      .then((resp) => {
        if (resp.success) {
          Swal.fire({ icon: 'success', title: 'Eliminado', timer: 1500, showConfirmButton: false });
          const materiaId = $('#materia-select').val();
          cargarEstudiantes(materiaId);
        } else {
          alert('Error al eliminar estudiante.');
        }
      })
      .catch((e) => {
        console.error('Eliminar error:', e);
        alert('Error de red al eliminar estudiante.');
      });
  });

  // Editar → llenar modal (IDs iguales a tu HTML)
  $(document).on('click', '.btn-editar', function () {
    const id = $(this).data('id');

    fetch(`../php/obtener_estudiante.php?id=${encodeURIComponent(id)}`)
      .then((r) => r.json())
      .then((data) => {
        // Campos del modal (coinciden con tu HTML)
        $('#ed_id').val(data.estudiante_id);
        $('#ed_nombre').val(data.nombre);
        $('#ed_apellido').val(data.apellido);
        $('#ed_fecha_nac').val(data.fecha_nacimiento); // formato YYYY-MM-DD
        $('#ed_grado').val(data.grado);
        $('#ed_grupo').val(data.grupo);

        // Cargar tutores y seleccionar el actual
        cargarTutores(data.tutor_id).then(() => {
          $('#modalEditarEstudiante').modal('show');
        });
      })
      .catch((err) => {
        console.error('Error al obtener datos del estudiante:', err);
        alert('No se pudo cargar la información del estudiante.');
      });
  });

  // Guardar edición (tu form id coincide con HTML)
  $('#formEditarEstudiante').on('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('../php/editar_estudiante.php', {
      method: 'POST',
      body: formData
    })
      .then((r) => r.json())
      .then((data) => {
        if (data.success) {
          $('#modalEditarEstudiante').modal('hide');
          Swal.fire({ icon: 'success', title: '¡Estudiante actualizado!', timer: 1500, showConfirmButton: false });

          const materiaId = $('#materia-select').val();
          cargarEstudiantes(materiaId);
        } else {
          alert(`No se pudo actualizar: ${data.message || 'Error desconocido'}`);
        }
      })
      .catch((err) => {
        console.error('Fetch error al actualizar estudiante:', err);
        alert('Error de red al actualizar estudiante.');
      });
  });
});

// Para poder llamar manualmente desde otros scripts si lo necesitas
window.cargarEstudiantes = cargarEstudiantes;
