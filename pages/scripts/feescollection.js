document.addEventListener("DOMContentLoaded", function () {
  fetch("../../pages/php/obtener_pagos.php")
    .then(response => response.json())
    .then(data => {
      const tbody = document.querySelector("#tabla-fees tbody");
      if (!tbody) {
        console.error("No se encontró el tbody de la tabla");
        return;
      }
      tbody.innerHTML = "";

      if (!Array.isArray(data)) {
        console.error("Respuesta no válida:", data);
        return;
      }

      data.forEach(fee => {
        const estadoPlano = (fee.estado || "").toString().trim().toLowerCase(); // "pagado" | "pendiente"

        const fila = document.createElement("tr");
        fila.innerHTML = `
          <td>${fee.pago_id}</td>
          <td>${fee.nombre} ${fee.apellido}</td>
          <td>${fee.grado}</td>
          <td>${fee.grupo}</td>
          <td>$${parseFloat(fee.monto).toFixed(2)}</td>
          <td>${fee.fecha_pago}</td>
          <td>${fee.fecha_vencimiento}</td>

          <!-- 🔑 Aquí forzamos el texto para buscar/ordenar -->
          <td data-search="${estadoPlano}" data-order="${estadoPlano}">
            <span class="badge badge-${estadoPlano === "pagado" ? "success" : "warning"}">
              ${estadoPlano}
            </span>
          </td>

          <td>${fee.creado_en}</td>
          <td>${fee.actualizado_en}</td>
          <td>
            <button class="btn btn-sm btn-outline-primary actualizar-btn" data-id="${fee.pago_id}" data-estado="${estadoPlano}">Cambiar</button>
          </td>
        `;
        tbody.appendChild(fila);
      });

      // Inicializa DataTables
      const tabla = $("#tabla-fees").DataTable({
        pageLength: 50,
        orderCellsTop: true,
        fixedHeader: true,
        responsive: { details: { type: "column", target: "tr" } },
        language: { url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
        // ❗ Ya no necesitamos columnDefs/render hack: DataTables usará data-search/order automáticamente
      });

      // Filtros en la fila .filters
      const api = tabla; // por comodidad
      $("#tabla-fees thead tr.filters th").each(function (colIdx) {
        const $inp = $(this).find("input, select");
        if (!$inp.length) return;

        // Columna ESTADO (índice 7) → regex exacta sobre el texto (gracias a data-search)
        if (colIdx === 7 && $inp.is("select")) {
          $inp.on("change", function () {
            const val = (this.value || "").trim().toLowerCase();
            if (!val) {
              api.column(colIdx).search("", false, false).draw(); // Todos
            } else {
              const rx = "^" + $.fn.dataTable.util.escapeRegex(val) + "$";
              api.column(colIdx).search(rx, true, false).draw();
            }
          });
        } else {
          // Resto de columnas: búsqueda normal
          $inp.on("keyup change", function () {
            api.column(colIdx).search(this.value).draw();
          });
        }
      });
    })
    .catch(error => {
      console.error("Error al cargar fees:", error);
    });

  // Evento para mostrar el modal
  document.addEventListener("click", function (e) {
    if (e.target.classList.contains("actualizar-btn")) {
      const pagoId = e.target.dataset.id;
      const estadoActual = e.target.dataset.estado;

      document.getElementById("modal-pago-id").value = pagoId;
      document.getElementById("modal-estado").value = estadoActual;

      $("#modalActualizarEstado").modal("show");
    }
  });

  // Guardar cambios desde el modal
  const btnGuardar = document.getElementById("btnGuardarEstado");
  if (btnGuardar) {
    btnGuardar.addEventListener("click", () => {
      const pagoId = document.getElementById("modal-pago-id").value;
      const nuevoEstado = document.getElementById("modal-estado").value;

      fetch("../../pages/php/actualizar_estado_pago.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ pago_id: pagoId, estado: nuevoEstado })
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            $("#modalActualizarEstado").modal("hide");
            const alerta = document.createElement("div");
            alerta.className = "alert alert-success text-center mt-3";
            alerta.textContent = "✔️ Estado actualizado exitosamente.";
            const wrapper = document.querySelector(".ms-content-wrapper .card-body") || document.body;
            wrapper.prepend(alerta);
            setTimeout(() => location.reload(), 1200);
          } else {
            Swal.fire("Error", data.message || "No se pudo actualizar.", "error");
          }
        })
        .catch(() => {
          Swal.fire("Error", "Error de red o servidor.", "error");
        });
    });
  }
});
