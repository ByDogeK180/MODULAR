document.addEventListener("DOMContentLoaded", () => {
  fetch("../../pages/php/obtener_pagos_hijo.php")
    .then(res => res.json())
    .then(data => {
      const tabla = $("#tabla-pagos-hijo").DataTable({
        data,
        columns: [
          { data: "pago_id" },
          { data: "nombre_estudiante" },
          { data: "grado" },
          { data: "grupo" },
          {
            data: "monto",
            render: monto => `$${parseFloat(monto).toFixed(2)}`
          },
          { data: "fecha_pago" },
          { data: "fecha_vencimiento" },
          {
            data: "estado",
            render: estado => {
              const clase = estado === "pagado" ? "badge-success" : "badge-warning";
              return `<span class="badge ${clase} text-uppercase">${estado}</span>`;
            }
          },
          { data: "creado_en" },
          { data: "actualizado_en" },
          {
            data: null,
            render: row => `
              ${row.estado === 'pendiente' ? `
              <button class="btn btn-outline-primary btn-sm pagar-btn" data-id="${row.pago_id}">
                <i class="fas fa-credit-card"></i> Pagar
              </button>` : `<span class="text-muted">-</span>`}
            `
          }
        ],
        responsive: true,
        language: {
          url: '../../assets/i18n/es-ES.json'
        }
      });

      // Evento para botón "Pagar"
      $('#tabla-pagos-hijo tbody').on('click', '.pagar-btn', function () {
        const id = this.getAttribute('data-id');
        $('#pago-id-confirmar').val(id);
        $('#modalConfirmarPago').modal('show');
      });

      // Confirmar pago
      document.getElementById('btnConfirmarPago').addEventListener('click', () => {
        const id = $('#pago-id-confirmar').val();

        fetch('../../pages/php/procesar_pago.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ pago_id: id })
        })
          .then(res => res.text())
          .then(texto => {
            console.log("Respuesta cruda del servidor:", texto);
            try {
              const resp = JSON.parse(texto);
              if (resp.status === 'success') {
                $('#modalConfirmarPago').modal('hide');

                // Abrir recibo PDF
                window.open(`../../pages/php/generar_recibo.php?pago_id=${id}`, '_blank');

                // Actualizar la tabla con nuevo estado
                tabla.clear().rows.add(data.map(p => {
                  if (p.pago_id == id) {
                    p.estado = 'pagado';
                    p.fecha_pago = new Date().toISOString().split('T')[0];
                  }
                  return p;
                })).draw();
              } else {
                alert('Error: no se pudo completar el pago');
              }
            } catch (e) {
              console.error("Error al parsear JSON:", e);
              alert("Respuesta inválida del servidor:\n" + texto);
            }
          })
          .catch(err => {
            console.error('Error en la solicitud de pago:', err);
            alert('Error de conexión con el servidor');
          });
      });
    })
    .catch(err => {
      console.error("Error cargando pagos del tutor:", err);
    });
});
