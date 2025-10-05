// pages/scripts/pago-hijo.js
document.addEventListener("DOMContentLoaded", () => {
  const $table = $("#tabla-pagos-hijo");
  const currencyMXN = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' });
  const pagarBtnSelector = '.pagar-btn';
  let dt; // DataTable instance

  // 1) Cargar datos
  fetch("../php/obtener_pagos_hijo.php", { credentials: 'same-origin' })
    .then(res => {
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      return res.json();
    })
    .then(data => {
      // 2) Inicializar DataTable
      dt = $table.DataTable({
        data,
        autoWidth: false,
        // ⬇️ Oculta el buscador global (quita la "f")
        // l = length, r = processing, t = table, i = info, p = pagination
        dom: 'lrtip',

        columns: [
          { data: "pago_id", width: "60px" },
          { data: "nombre_estudiante" },
          { data: "grado", width: "80px" },
          { data: "grupo", width: "80px" },
          {
            data: "monto",
            render: monto => currencyMXN.format(parseFloat(monto || 0)),
            className: "text-right",
            width: "110px"
          },
          { data: "fecha_pago", defaultContent: "", width: "120px" },
          { data: "fecha_vencimiento", width: "130px" },
          {
            data: "estado",
            render: estado => {
              const st = (estado || '').toLowerCase();
              const clase = st === "pagado" ? "badge-success" : (st === "pendiente" ? "badge-warning" : "badge-secondary");
              const texto = st || '—';
              return `<span class="badge ${clase} text-uppercase">${texto}</span>`;
            },
            width: "170px",
            className: "text-nowrap",
            orderDataType: "dom-text",
          },
          { data: "creado_en", visible: false },
          { data: "actualizado_en", visible: false },
          {
            data: null,
            orderable: false,
            searchable: false,
            width: "120px",
            render: row => {
              const pendiente = String(row.estado || '').toLowerCase() === 'pendiente';
              return pendiente
                ? `<button class="btn btn-outline-primary btn-sm pagar-btn" data-id="${row.pago_id}">
                     <i class="fas fa-credit-card"></i> Pagar
                   </button>`
                : `<span class="text-muted">—</span>`;
            }
          }
        ],
        responsive: {
          details: { type: 'inline', target: 'tr' }
        },
        orderCellsTop: true,
        stateSave: true,
        pageLength: 10,
        language: { url: '../../assets/i18n/es-ES.json' }
      });

      // Ajuste visual del select del filtro en la cabecera (columna 8 = Estado)
      $table.find('thead tr.filters th:nth-child(8) select').css('min-width', '170px');

      // 3) Filtros por columna (segunda fila del thead)
      const thead = $table.find('thead');
      thead.on('keyup change', '.filters input, .filters select', function () {
        const $input = $(this);
        const colIndex = $input.closest('th')[0].cellIndex;
        const val = $input.val();
        dt.column(colIndex).search(val || '', true, false).draw();
      });

      // 4) Abrir modal de confirmación
      $table.on('click', pagarBtnSelector, function () {
        const id = this.getAttribute('data-id');
        $('#pago-id-confirmar').val(id);
        $('#modalConfirmarPago').modal('show');
      });

      // 5) Confirmar pago
      const $btnConfirmar = $('#btnConfirmarPago');
      $btnConfirmar.on('click', async () => {
        const id = $('#pago-id-confirmar').val();
        if (!id) return;

        $btnConfirmar.prop('disabled', true).text('Procesando...');
        try {
          const res = await fetch('../php/procesar_pago.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({ pago_id: Number(id) })
          });

          const rawText = await res.text();
          console.log("Respuesta cruda del servidor:", rawText);

          let resp;
          try { resp = JSON.parse(rawText); } catch (e) {
            throw new Error(`Respuesta inválida del servidor: ${rawText}`);
          }

          if (resp.status === 'success') {
            $('#modalConfirmarPago').modal('hide');
            window.open(`../php/generar_recibo.php?pago_id=${id}`, '_blank');

            const rowIdx = dt.rows().eq(0).filter((idx) => dt.cell(idx, 0).data() == id);
            if (rowIdx.length) {
              const current = dt.row(rowIdx[0]).data();
              const hoy = new Date().toISOString().split('T')[0];
              dt.row(rowIdx[0]).data({
                ...current,
                estado: 'pagado',
                fecha_pago: hoy,
                actualizado_en: hoy
              }).draw(false);
            } else {
              dt.ajax?.reload?.(null, false);
            }
          } else {
            alert(resp.message || 'Error: no se pudo completar el pago');
          }
        } catch (err) {
          console.error(err);
          alert(err.message || 'Error de conexión con el servidor');
        } finally {
          $btnConfirmar.prop('disabled', false).text('Confirmar');
        }
      });
    })
    .catch(err => {
      console.error("Error cargando pagos del tutor:", err);
      alert('No se pudieron cargar los pagos. Intenta de nuevo.');
    });
});
