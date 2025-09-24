document.addEventListener("DOMContentLoaded", () => {
  const materiaSelect = document.getElementById("materia-select");
  const fechaSelect = document.getElementById("fecha-select");
  const nuevaFechaInput = document.getElementById("nueva-fecha");
  const nuevaFechaContainer = document.getElementById("nueva-fecha-container");
  const tbody = document.getElementById("asistencias-body");
  const form = document.getElementById("form-asistencias");

  // 1. Cargar materias del docente
  fetch("../php/materias-docente.php")
    .then(res => res.json())
    .then(materias => {
      materias.forEach(mat => {
        const option = document.createElement("option");
        option.value = mat.materia_id;
        option.textContent = `${mat.materia} - ${mat.ciclo} - Grupo ${mat.grupo}`;
        materiaSelect.appendChild(option);
      });
    });

  // 2. Cargar fechas disponibles de la materia
  function cargarFechas(materiaId) {
    fechaSelect.innerHTML = `<option value="">-- Selecciona una fecha --</option>`;

    fetch(`../php/fechas-asistencias.php?materia_id=${materiaId}`)
      .then(res => res.json())
      .then(fechas => {
        if (Array.isArray(fechas) && fechas.length > 0) {
          fechas.forEach(fecha => {
            const opt = document.createElement("option");
            opt.value = fecha;
            opt.textContent = fecha;
            fechaSelect.appendChild(opt);
          });
        }

        // opción fija para nueva fecha
        const optNueva = document.createElement("option");
        optNueva.value = "__nueva__";
        optNueva.textContent = "➕ Nueva fecha...";
        fechaSelect.appendChild(optNueva);
      });
  }

  // 3. Cargar asistencias de un día existente
  function cargarAsistencias(materiaId, fecha) {
    tbody.innerHTML = "";

    fetch(`../php/estudiantes-materia.php?materia_id=${materiaId}&fecha=${fecha}`)
      .then(res => res.json())
      .then(respuesta => {
        if (!respuesta.success) {
          console.error("Error en backend:", respuesta);
          return;
        }

        const estudiantes = respuesta.data;

        if (!Array.isArray(estudiantes) || estudiantes.length === 0) {
          tbody.innerHTML = `
            <tr>
              <td colspan="3" class="text-center">No hay estudiantes inscritos</td>
            </tr>`;
          return;
        }

        estudiantes.forEach(est => {
          const tr = document.createElement("tr");
          tr.innerHTML = `
            <td>${est.nombre}</td>
            <td>${est.apellido}</td>
            <td>
              <select class="form-control asistencia-select" data-id="${est.estudiante_id}">
                <option value="presente" ${est.estado === "presente" ? "selected" : ""}>Presente</option>
                <option value="ausente" ${est.estado === "ausente" ? "selected" : ""}>Ausente</option>
              </select>
            </td>
          `;
          tbody.appendChild(tr);
        });

        nuevaFechaContainer.style.display = "none";
        fechaSelect.dataset.overrideFecha = "";
      })
      .catch(err => {
        console.error("Error al cargar estudiantes:", err);
      });
  }

  // 4. Eventos
  materiaSelect.addEventListener("change", () => {
    const materiaId = materiaSelect.value;
    if (materiaId) {
      cargarFechas(materiaId);
      tbody.innerHTML = "";
    }
  });

  fechaSelect.addEventListener("change", () => {
    const materiaId = materiaSelect.value;
    const fecha = fechaSelect.value;

    if (fecha === "__nueva__") {
      // mostrar input para nueva fecha
      nuevaFechaContainer.style.display = "block";
      tbody.innerHTML = "";

      // cargar estudiantes inscritos (estado por defecto = ausente)
      fetch(`../php/estudiantes-materia.php?materia_id=${materiaId}&fecha=0000-00-00`)
        .then(res => res.json())
        .then(respuesta => {
          if (!respuesta.success) {
            console.error("Error en backend:", respuesta);
            return;
          }

          const estudiantes = respuesta.data;

          if (!Array.isArray(estudiantes) || estudiantes.length === 0) {
            tbody.innerHTML = `
              <tr>
                <td colspan="3" class="text-center">No hay estudiantes inscritos</td>
              </tr>`;
            return;
          }

          estudiantes.forEach(est => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
              <td>${est.nombre}</td>
              <td>${est.apellido}</td>
              <td>
                <select class="form-control asistencia-select" data-id="${est.estudiante_id}">
                  <option value="presente">Presente</option>
                  <option value="ausente" selected>Ausente</option>
                </select>
              </td>
            `;
            tbody.appendChild(tr);
          });
        });
    } else if (materiaId && fecha) {
      cargarAsistencias(materiaId, fecha);
    }
  });

  // 5. Guardar asistencias (nueva o existente)
  form.addEventListener("submit", e => {
    e.preventDefault();

    const materiaId = materiaSelect.value;
    let fecha = fechaSelect.value;

    if (fecha === "__nueva__") {
      fecha = nuevaFechaInput.value;
    }

    if (!fecha) {
      Swal.fire("Error", "Debes seleccionar o crear una fecha válida", "error");
      return;
    }

    const asistencias = [...document.querySelectorAll(".asistencia-select")].map(sel => ({
      estudiante_id: sel.dataset.id,
      estado: sel.value
    }));

    const payload = { materia_id: materiaId, fecha: fecha, asistencias: asistencias };

    fetch("../php/guardar_asistencias.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload)
    })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          Swal.fire("Éxito", "Asistencias guardadas", "success");

          // 🔄 Limpiar interfaz después de guardar
          tbody.innerHTML = "";
          materiaSelect.value = "";
          fechaSelect.innerHTML = `<option value="">-- Selecciona una fecha --</option>`;
          nuevaFechaContainer.style.display = "none";
          nuevaFechaInput.value = "";
          fechaSelect.dataset.overrideFecha = "";

        } else {
          Swal.fire("Error", data.error, "error");
        }
      })
      .catch(err => {
        console.error("Error en fetch:", err);
        Swal.fire("Error", "No se pudo guardar la asistencia", "error");
      });
  });
});
