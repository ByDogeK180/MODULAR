function cargarEstudiantes(materiaId = null, claseId = null) {
  const isDocente = typeof userRol !== 'undefined' && userRol == 1;

  let url = '../php/estudiantes-docSeparado.php';
  if (isDocente && materiaId && claseId) {
    url += `?materia_id=${materiaId}&clase_id=${claseId}`;
  }

  $.ajax({
    url: url,
    method: "GET",
    dataType: "json",
    success: data => {
      console.log("ESTUDIANTES RECIBIDOS:", data);

      let tbody = '';
      data.forEach(est => {
        if (String(est.activo) === "1") {
          tbody += `
            <tr>
              <td>${est.estudiante_id}</td>
              <td>${est.tutor_id}</td>
              <td>${est.tutor_nombre || 'Sin tutor'}</td>
              <td>${est.nombre}</td>
              <td>${est.apellido}</td>
              <td>${est.fecha_nacimiento}</td>
              <td>${est.clase}</td> <!-- 👈 muestra la clase (1-A, 2-B, etc.) -->
              <td>${est.materia}</td>
              <td>${est.activo == 1 ? 'Sí' : 'No'}</td>
              <td>${est.creado_en}</td>
              <td>${est.actualizado_en}</td>
            </tr>
          `;
        }
      });

      const $tabla = $('#tabla-estudiantes');
      if ($.fn.DataTable.isDataTable($tabla)) {
        $tabla.DataTable().clear().destroy();
      }

      $('#student-body').html(tbody);

      $tabla.DataTable({
        responsive: true,
        pageLength: 10,
        language: {
          search: "Buscar:",
          lengthMenu: "Mostrar _MENU_ registros por página",
          zeroRecords: "No se encontraron resultados",
          info: "Mostrando página _PAGE_ de _PAGES_",
          infoEmpty: "No hay registros disponibles",
          infoFiltered: "(filtrado de _MAX_ registros totales)",
          paginate: { next: "Siguiente", previous: "Anterior" }
        },
        drawCallback: function () {
          const api = this.api();
          const visibles = api.rows({ page: 'current' }).count();
          document.getElementById("total-estudiantes").textContent = visibles;
        }
      });
    },
    error: (xhr, status, err) => {
      console.error("Error al cargar estudiantes:", err);
      $('#student-body').html('<tr><td colspan="12">Error al cargar estudiantes</td></tr>');
      document.getElementById("total-estudiantes").textContent = 0;
    }
  });
}

function cargarMateriasDocente() {
  fetch('../php/materias-docente.php')
    .then(r => r.json())
    .then(materias => {
      let opciones = '<option value="">Seleccione una materia</option>';
      materias.forEach(m => {
        opciones += `<option value="${m.materia_id}" data-clase="${m.clase_id}">
          ${m.materia} - ${m.ciclo} - Grupo ${m.grupo}
        </option>`;
      });
      $('#materia-select').html(opciones);
    })
    .catch(err => console.error('Error al cargar materias del docente:', err));
}

$(document).ready(function () {
  const isDocente = typeof userRol !== 'undefined' && userRol == 1;

  if (isDocente) {
    cargarMateriasDocente();
    $('#materia-select').on('change', function () {
      const materiaId = $(this).val();
      const claseId = $(this).find(':selected').data('clase');
      if (materiaId && claseId) {
        cargarEstudiantes(materiaId, claseId);
      } else {
        $('#student-body').html('');
        $('#tabla-estudiantes').DataTable().clear().draw();
        document.getElementById("total-estudiantes").textContent = 0;
      }
    });
  } else {
    $('#materia-select').closest('.form-group').hide();
    cargarEstudiantes();
  }
});
