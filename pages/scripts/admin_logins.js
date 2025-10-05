// File: pages/scripts/admin_logins.js
$(function () {
  const $month = $('#loginMonth');
  const $rol   = $('#loginRol');
  const $sumTb = $('#tablaLoginSummary');
  const $detTb = $('#tablaLoginDetalle');
  let chart;

  function initDT($table) {
    if ($.fn.dataTable.isDataTable($table)) $table.DataTable().destroy();
    $table.find('tbody').empty();
  }

  function dtEsOpts(extra={}) {
    return Object.assign({
      language: { url:'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json' },
      pageLength: 10,
      autoWidth: false
    }, extra);
  }

  function cargarResumen() {
    initDT($sumTb);

    const qs = new URLSearchParams({ month: $month.val(), rol: $rol.val() });
    fetch(`pages/php/login_stats_summary.php?${qs.toString()}`)
      .then(r => r.json())
      .then(json => {
        const rows = (json.users || []);
        const $b = $sumTb.find('tbody');

        rows.forEach(u => {
          $b.append(`
            <tr data-user="${u.usuario_id}" data-rol="${u.rol}">
              <td>${u.nombre}</td>
              <td>${u.rol}</td>
              <td class="text-right">${u.total}</td>
              <td><button class="btn btn-sm btn-outline-primary btn-ver">Ver</button></td>
            </tr>
          `);
        });

        $sumTb.DataTable(dtEsOpts());
        if (rows.length) cargarDetalle(rows[0].usuario_id, rows[0].rol);
        else limpiarDetalle();
      })
      .catch(console.error);
  }

  function limpiarDetalle() {
    initDT($detTb);
    $('#loginUserName').text('—');
    $('#loginUserRol').text('—');
    $('#loginUserTotal').text('0');
    if (chart) { chart.destroy(); chart = null; }
  }

  function cargarDetalle(userId, rol) {
    initDT($detTb);

    const qs = new URLSearchParams({ month: $month.val(), user_id: userId, rol });
    fetch(`pages/php/login_stats_user.php?${qs.toString()}`)
      .then(r => r.json())
      .then(json => {
        $('#loginUserName').text(json.user?.nombre || '—');
        $('#loginUserRol').text(json.user?.rol || '—');
        $('#loginUserTotal').text(json.total || 0);

        const $b = $detTb.find('tbody');
        (json.logins || []).forEach(l => {
          $b.append(`<tr>
            <td>${l.ts}</td>
            <td>${l.ip || ''}</td>
            <td style="max-width:360px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${l.ua || ''}</td>
          </tr>`);
        });
        $detTb.DataTable(dtEsOpts());

        const labels = (json.daily || []).map(d => d.day);
        const data   = (json.daily || []).map(d => d.count);

        const ctx = document.getElementById('loginChart');
        if (chart) chart.destroy();
        chart = new Chart(ctx, {
          type: 'bar',
          data: { labels, datasets: [{ label: 'Inicios por día', data }] },
          options: {
            responsive: true,
            scales: { y: { beginAtZero: true, ticks: { precision:0 } } },
            plugins: { legend: { display:false } }
          }
        });
      })
      .catch(console.error);
  }

  // Eventos
  $month.on('change', cargarResumen);
  $rol.on('change', cargarResumen);
  $sumTb.on('click', '.btn-ver', function () {
    const $tr = $(this).closest('tr');
    cargarDetalle($tr.data('user'), $tr.data('rol'));
  });

  // Init
  cargarResumen();
});
