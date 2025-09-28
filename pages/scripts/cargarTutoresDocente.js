document.addEventListener('DOMContentLoaded', () => {
  const selectGrupo = document.getElementById('filtroGrupo');
  const tablaHijos = document.getElementById('tabla-hijos');

  // 1. Cargar grupos del docente
  function cargarGrupos() {
    fetch('../php/obtener_grupos_docente.php')
      .then(res => res.json())
      .then(grupos => {
        if (!selectGrupo) return;
        selectGrupo.innerHTML = '<option value="">Todos los grupos</option>';
        grupos.forEach(g => {
          const opt = document.createElement('option');
          opt.value = g.materia_id; // usamos materia_id como valor
          opt.textContent = ` ${g.ciclo} - Grado ${g.grado}${g.grupo}`;
          selectGrupo.appendChild(opt);
        });
      })
      .catch(err => console.error('❌ Error al cargar grupos:', err));
  }

  // 2. Cargar tutores (con o sin filtro de materia_id)
  function cargarTutores(filtro = '') {
    const url = filtro
      ? `../php/obtener_tutores_docente.php${filtro}`
      : '../php/obtener_tutores_docente.php';

    fetch(url)
      .then(res => res.json())
      .then(data => {
        if (!Array.isArray(data)) {
          console.warn("⚠️ Respuesta inesperada:", data);
          return;
        }

        if ($.fn.DataTable.isDataTable('#tablaTutores')) {
          const table = $('#tablaTutores').DataTable();
          table.clear().rows.add(data).draw();
        } else {
          $('#tablaTutores').DataTable({
            data: data,
            columns: [
              { data: 'nombre' },
              { data: 'apellido' },
              { data: 'telefono' },
              { data: 'correo' },
              { data: 'direccion' },
              {
                data: 'tutor_id',
                render: function (data) {
                  return `
                    <button class="btn btn-warning btn-sm ver-hijos" data-id="${data}">
                      Ver Hijos
                    </button>`;
                }
              }
            ],
            language: {
              search: "Buscar:",
              paginate: { next: "Siguiente", previous: "Anterior" },
              lengthMenu: "Mostrar _MENU_ registros",
              info: "Mostrando _START_ a _END_ de _TOTAL_ tutores",
              emptyTable: "No se encontraron tutores."
            }
          });

          // Evento delegado para abrir modal de hijos
          $('#tablaTutores tbody').on('click', '.ver-hijos', function () {
            const tutorId = $(this).data('id');

            fetch(`../php/obtener_hijos.php?tutor_id=${tutorId}`)
              .then(res => res.json())
              .then(hijos => {
                tablaHijos.innerHTML = "";
                if (hijos.length === 0) {
                  tablaHijos.innerHTML = `<tr><td colspan="4" class="text-center">No tiene hijos registrados</td></tr>`;
                } else {
                  hijos.forEach(h => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                      <td>${h.nombre}</td>
                      <td>${h.apellido}</td>
                      <td>${h.grado}</td>
                      <td>${h.grupo}</td>
                    `;
                    tablaHijos.appendChild(tr);
                  });
                }
                const modal = new bootstrap.Modal(document.getElementById('modalHijos'));
                modal.show();
              })
              .catch(err => console.error("❌ Error al obtener hijos:", err));
          });
        }
      })
      .catch(err => console.error("❌ Error al cargar tutores:", err));
  }

  // 3. Evento para aplicar filtro
  if (selectGrupo) {
    selectGrupo.addEventListener('change', () => {
      const materiaId = selectGrupo.value;
      if (materiaId) {
        cargarTutores(`?materia_id=${materiaId}`);
      } else {
        cargarTutores();
      }
    });
  }

  // 4. Inicializar
  cargarGrupos();
  cargarTutores();
});
