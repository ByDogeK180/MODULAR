// File: cargarTutores.js
document.addEventListener('DOMContentLoaded', () => {
  let tabla; 

  // Función para formatear fecha (si la tuvieras en tutorprofile.html)
  const formatDate = d => {
    if (!d) return '';
    const dt = new Date(d);
    return `${dt.getFullYear()}-${String(dt.getMonth()+1).padStart(2,'0')}-${String(dt.getDate()).padStart(2,'0')}`;
  };

  function cargarTutores() {
    fetch('../php/obtener_tutores.php')
      .then(res => res.json())
      .then(data => {
        const tbody = document.getElementById('tutores-lista');
        tbody.innerHTML = '';
        data.forEach(t => {
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${t.nombre}</td>
            <td>${t.apellido}</td>
            <td>${t.telefono}</td>
            <td>${t.correo}</td>
            <td>${t.direccion}</td>
            <td>${t.activo==1 || t.activo===true ? 'Sí':'No'}</td>
            <td>
              <button class="btn btn-sm btn-outline-warning btn-editar" data-id="${t.tutor_id}">
                <i class="fa fa-edit"></i>
              </button>
              <button class="btn btn-sm btn-outline-danger btn-eliminar" data-id="${t.tutor_id}">
                <i class="fa fa-trash"></i>
              </button>
            </td>`;
          tbody.appendChild(tr);
        });

        // Inicializa DataTable una sola vez
        if (!tabla) {
          tabla = $('#tablaTutores').DataTable({
            dom: 'Bfrtip',
            buttons: [
              {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel"></i> Excel',
                titleAttr: 'Exportar a Excel'
              },
              {
                extend: 'csvHtml5',
                text: '<i class="fa fa-file-csv"></i> CSV',
                titleAttr: 'Exportar a CSV'
              }
            ],
            language: {
              search: "Buscar:",
              paginate: { next: "Siguiente", previous: "Anterior" },
              lengthMenu: "Mostrar _MENU_ registros",
              info: "Mostrando _START_ a _END_ de _TOTAL_ tutores"
            }
          });
        } else {
          tabla.clear().rows.add($('#tablaTutores tbody tr')).draw();
        }

        // Eventos editar / eliminar
        document.querySelectorAll('.btn-editar').forEach(btn =>
          btn.addEventListener('click', () => editarTutor(btn.dataset.id))
        );
        document.querySelectorAll('.btn-eliminar').forEach(btn =>
          btn.addEventListener('click', () => eliminarTutor(btn.dataset.id))
        );
      })
      .catch(console.error);
  }

  // Función para editar tutor
  function editarTutor(id) {
    console.log("Editar tutor con id:", id);

    fetch(`../php/obtener_tutor.php?tutor_id=${id}`)
      .then(res => res.json())
      .then(data => {
        const t = data.tutor || data; // acepta { tutor: {...} } o { ... }

        if (!t) {
          alert("No se pudo cargar la información del tutor");
          return;
        }

        // Rellena tu formulario del modal (asegúrate de tener estos IDs en tu HTML)
        document.getElementById('tutorNombre').value    = t.nombre   || '';
        document.getElementById('tutorApellido').value  = t.apellido || '';
        document.getElementById('tutorTelefono').value  = t.telefono || '';
        document.getElementById('tutorCorreo').value    = t.correo   || '';
        document.getElementById('tutorDireccion').value = t.direccion|| '';

        // Guardar el id en el form
        document.getElementById('tutorForm').dataset.id = id;

        // Mostrar modal
        $('#tutorModal').modal('show');
      })
      .catch(err => {
        console.error(err);
        alert("Error al obtener datos del tutor");
      });
  }

  // Función para eliminar tutor
  function eliminarTutor(id) {
    if (!confirm("¿Seguro que quieres eliminar este tutor?")) return;

    fetch("../php/eliminar_tutor.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ tutor_id: id })
    })
      .then(res => res.json())
      .then(json => {
        if (json.success) {
          alert("Tutor eliminado correctamente");
          cargarTutores(); // recargar la tabla
        } else {
          alert(json.message || "Error al eliminar tutor");
        }
      })
      .catch(err => {
        console.error(err);
        alert("No se pudo eliminar el tutor");
      });
  }


    // Guardar cambios de tutor
  document.getElementById('tutorForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const id = this.dataset.id;

    const payload = {
      tutor_id: id,
      nombre: document.getElementById('tutorNombre').value,
      apellido: document.getElementById('tutorApellido').value,
      telefono: document.getElementById('tutorTelefono').value,
      correo: document.getElementById('tutorCorreo').value,
      direccion: document.getElementById('tutorDireccion').value,
      activo: document.getElementById('tutorActivo').value
    };

    fetch('../php/actualizar_tutor.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    })
      .then(res => res.json())
      .then(json => {
        if (json.success) {
          alert("Tutor actualizado correctamente");
          $('#tutorModal').modal('hide');
          cargarTutores(); // refrescar tabla
        } else {
          alert(json.message || "Error al actualizar tutor");
        }
      })
      .catch(err => {
        console.error(err);
        alert("Error en la petición");
      });
  });


  // Botón Exportar genérico
  document.getElementById('btnExportarTutores').addEventListener('click', () => {
    tabla.button('.buttons-excel').trigger();
  });

  // Carga inicial
  cargarTutores();
});

