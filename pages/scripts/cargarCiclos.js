// File: cargarCiclos.js
$(document).ready(() => {
  const $periodosContainer = $('#periodosContainer');
  const tpl = $('#periodoTemplate').html();

  // ============ 1) Mini-resumen de periodos ============
  function renderSummary() {
    const $sum = $('#periodosSummary').empty();
    $periodosContainer.find('.periodo-row').each((i, el) => {
      const $el  = $(el);
      const nom  = $el.find('.periodo-nombre').val() || '[sin nombre]';
      const ini  = $el.find('.periodo-inicio').val();
      const fin  = $el.find('.periodo-fin').val();
      $sum.append(`
        <li class="list-group-item px-2 py-1">
          <strong>${i+1}. ${nom}</strong> — ${ini} → ${fin}
        </li>`);
    });
  }

  // ============ 2) Agregar periodo ============
  $('#addPeriodoBtn').click(() => {
    $periodosContainer.append(tpl);
    renderSummary();
  });

  // ============ 3) Quitar periodo ============
  $periodosContainer.on('click', '.btn-remove-periodo', function(){
    $(this).closest('.periodo-row').remove();
    renderSummary();
  });

  // ============ 4) Cambios en campos de periodo ============
  $periodosContainer.on('change', '.periodo-nombre, .periodo-inicio, .periodo-fin', renderSummary);

  // ============ 5) Nuevo Ciclo ============
  $('#newCicloBtn').click(() => {
    $('#cicloForm')[0].reset();
    $('#cicloForm').removeData('id');
    $periodosContainer.empty();
    $('#periodosSummary').empty();
    $('#cicloModalLabel').text('Nuevo Ciclo Escolar');
    $('#cicloModal').modal('show');
  });

  // ============ 6) Envío del formulario (crear/actualizar) ============
  $('#cicloForm').submit(function(e){
    e.preventDefault();

    // 6.1 Validación básica de fechas del ciclo
    let errores = [];
    const iniC = $('#fechaInicioCiclo').val();
    const finC = $('#fechaFinCiclo').val();
    $('#fechaInicioCiclo, #fechaFinCiclo').removeClass('is-invalid');
    if (!iniC || !finC || iniC >= finC) {
      errores.push('Rango del ciclo inválido (inicio ≥ fin).');
      $('#fechaInicioCiclo, #fechaFinCiclo').addClass('is-invalid');
    }

    // 6.2 Recolectar y validar periodos
    const periodos = [];
    $periodosContainer.find('.periodo-row').each((_, row) => {
      const $r   = $(row);
      const nom  = ($r.find('.periodo-nombre').val() || '').trim();
      const ini  = $r.find('.periodo-inicio').val();
      const fin  = $r.find('.periodo-fin').val();
      $r.find('.form-control').removeClass('is-invalid');

      if (!nom) {
        errores.push('Nombre de algún periodo vacío.');
        $r.find('.periodo-nombre').addClass('is-invalid');
      }
      if (!ini || !fin || ini >= fin) {
        errores.push(`Periodo “${nom||'?'}” con rango inválido.`);
        $r.find('.periodo-inicio, .periodo-fin').addClass('is-invalid');
      }
      if (ini < iniC || fin > finC) {
        errores.push(`Periodo “${nom||'?'}” fuera del rango del ciclo.`);
        $r.find('.periodo-inicio, .periodo-fin').addClass('is-invalid');
      }
      periodos.push({ ini, fin, row: $r });
    });

    // 6.3 Solapamientos
    periodos.sort((a,b) => a.ini.localeCompare(b.ini));
    for (let i = 1; i < periodos.length; i++) {
      if (periodos[i].ini < periodos[i-1].fin) {
        errores.push('Al menos dos periodos se solapan.');
        periodos[i].row.find('.periodo-inicio, .periodo-fin').addClass('is-invalid');
      }
    }

    if (errores.length) {
      return alert(errores.join('\n'));
    }

    // 6.4 Payload
    const payload = {
      ciclo_id:     $(this).data('id') || null,
      nombre:       $('#nombreCiclo').val().trim(),
      fecha_inicio: iniC,
      fecha_fin:    finC,
      estado:       $('#estadoCiclo').val(),
      observaciones: $('#observacionesCiclo').val().trim(),
      periodos:     []
    };
    $periodosContainer.find('.periodo-row').each((_, row) => {
      const $r = $(row);
      payload.periodos.push({
        periodo_id:   $r.find('.periodo-id').val() || null,
        nombre:       ($r.find('.periodo-nombre').val() || '').trim(),
        fecha_inicio: $r.find('.periodo-inicio').val(),
        fecha_fin:    $r.find('.periodo-fin').val()
      });
    });

    // 6.5 Enviar a crear o actualizar
    const url = payload.ciclo_id
      ? '../php/actualizar_ciclo.php'
      : '../php/crear_ciclo.php';

    fetch(url, {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify(payload)
    })
    .then(res => {
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      return res.json();
    })
    .then(json => {
      if (json.success) {
        $('#cicloModal').modal('hide');
        cargarCiclos();
      } else {
        alert(json.message || 'Error al guardar ciclo');
      }
    })
    .catch(err => {
      console.error('Formulario error:', err);
      alert('No se pudo procesar el ciclo. Revisa la consola.');
    });
  });

  // ============ 7) Editar ciclo ============
  $('#tabla-ciclos').on('click', '.btn-editar', function(){
    const id = $(this).data('id');
    fetch(`../php/obtener_ciclo.php?id=${id}`)
      .then(res => res.json())
      .then(json => {
        const c = json.ciclo;
        $('#cicloForm').data('id', id);
        $('#nombreCiclo').val(c.nombre);
        $('#fechaInicioCiclo').val(c.fecha_inicio);
        $('#fechaFinCiclo').val(c.fecha_fin);
        $('#estadoCiclo').val(c.estado);
        $('#observacionesCiclo').val(c.observaciones);
        $periodosContainer.empty();
        json.periodos.forEach(p => {
          const $row = $(tpl);
          $row.find('.periodo-id').val(p.periodo_id);
          $row.find('.periodo-nombre').val(p.nombre);
          $row.find('.periodo-inicio').val(p.fecha_inicio);
          $row.find('.periodo-fin').val(p.fecha_fin);
          $periodosContainer.append($row);
        });
        renderSummary();
        $('#cicloModalLabel').text('Editar Ciclo Escolar');
        $('#cicloModal').modal('show');
      })
      .catch(err => console.error('Carga ciclo error:', err));
  });

  // ============ 8) Cerrar/Abrir ciclo ============
  $('#tabla-ciclos').on('click', '.btn-toggle-estado', function(){
    const $btn    = $(this);
    const id      = $btn.data('id');
    const estado  = String($btn.data('estado') || '').toLowerCase();
    const $row    = $btn.closest('tr');

    const nextEstado = (estado === 'activo') ? 'cerrado' : 'activo';
    const accionTxt  = (nextEstado === 'cerrado') ? 'Cerrar' : 'Abrir';
    const msgTxt     = (nextEstado === 'cerrado')
        ? 'El estado pasará a "Cerrado".'
        : 'El estado pasará a "Activo".';

    Swal.fire({
      title: `¿${accionTxt} ciclo?`,
      text: msgTxt,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: `Sí, ${accionTxt.toLowerCase()}`,
      cancelButtonText: 'Cancelar'
    }).then(result => {
      if (!result.isConfirmed) return;

      const body = new URLSearchParams();
      body.set('id', id);
      body.set('estado', nextEstado);

      fetch('../php/toggle_estado_ciclo.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded;charset=UTF-8'},
        body: body.toString()
      })
      .then(async res => {
        const json = await res.json().catch(() => ({}));
        if (!res.ok || !json.ok) {
          throw new Error(json && json.msg ? json.msg : 'Error al actualizar estado');
        }
        return json;
      })
      .then(json => {
        const $badge = $row.find('.badge-estado');
        if (json.estado === 'cerrado') {
          $badge.text('Cerrado').removeClass('badge-success').addClass('badge-secondary');
          $btn.attr('title', 'Abrir ciclo')
              .data('estado', 'cerrado')
              .find('i').removeClass('fa-lock-open').addClass('fa-lock');
        } else {
          $badge.text('Activo').removeClass('badge-secondary').addClass('badge-success');
          $btn.attr('title', 'Cerrar ciclo')
              .data('estado', 'activo')
              .find('i').removeClass('fa-lock').addClass('fa-lock-open');
        }
        Swal.fire('Listo', `El ciclo ahora está ${json.estado}.`, 'success');
      })
      .catch(err => {
        console.error('Toggle estado error:', err);
        Swal.fire('Error', err.message || 'No se pudo cambiar el estado.', 'error');
      });
    });
  });

  // ============ 9) Duplicar ciclo ============
  $('#tabla-ciclos').on('click', '.btn-duplicar', function(){
    const id = $(this).data('id');
    fetch(`../php/obtener_ciclo.php?id=${id}`)
      .then(res => res.json())
      .then(json => {
        const c = json.ciclo;
        $('#cicloForm')[0].reset();
        $('#cicloForm').removeData('id');
        $('#nombreCiclo').val(c.nombre + ' (Copia)');
        $('#fechaInicioCiclo').val(c.fecha_inicio);
        $('#fechaFinCiclo').val(c.fecha_fin);
        $('#estadoCiclo').val('activo');
        $('#observacionesCiclo').val(c.observaciones);
        $periodosContainer.empty();
        json.periodos.forEach(p => {
          const $row = $(tpl);
          $row.find('.periodo-nombre').val(p.nombre);
          $row.find('.periodo-inicio').val(p.fecha_inicio);
          $row.find('.periodo-fin').val(p.fecha_fin);
          $periodosContainer.append($row);
        });
        renderSummary();
        $('#cicloModalLabel').text('Duplicar Ciclo Escolar');
        $('#cicloModal').modal('show');
      })
      .catch(err => console.error('Duplicar ciclo error:', err));
  });

  // ============ 10) Listar ciclos ============
  function cargarCiclos() {
    fetch('../php/obtener_ciclos.php')
      .then(res => res.json())
      .then(data => {
        if ($.fn.dataTable && $.fn.dataTable.isDataTable('#tabla-ciclos')) {
          $('#tabla-ciclos').DataTable().destroy();
        }
        const $tbody = $('#tabla-ciclos tbody').empty();

        (Array.isArray(data) ? data : []).forEach(c => {
          const isActivo  = c.estado === 'activo';
          const badgeCls  = isActivo ? 'badge-success' : 'badge-secondary';
          const iconHtml  = isActivo ? '<i class="fa fa-lock-open"></i>' : '<i class="fa fa-lock"></i>';
          const titleTxt  = isActivo ? 'Cerrar ciclo' : 'Abrir ciclo';

          $tbody.append(`
            <tr data-id="${c.ciclo_id}">
              <td>${c.nombre}</td>
              <td>${c.fecha_inicio}</td>
              <td>${c.fecha_fin}</td>
              <td>
                <span class="badge badge-estado ${badgeCls}">
                  ${isActivo ? 'Activo' : 'Cerrado'}
                </span>
              </td>
              <td>${c.observaciones || ''}</td>
              <td class="text-nowrap">
                <button class="btn btn-sm btn-warning btn-editar" data-id="${c.ciclo_id}" title="Editar">
                  <i class="fa fa-edit"></i>
                </button>

                <button class="btn btn-sm btn-primary btn-toggle-estado"
                        data-id="${c.ciclo_id}"
                        data-estado="${c.estado}"
                        title="${titleTxt}">
                  ${iconHtml}
                </button>

                <button class="btn btn-sm btn-info btn-duplicar" data-id="${c.ciclo_id}" title="Duplicar">
                  <i class="fa fa-clone"></i>
                </button>
              </td>
            </tr>`);
        });

        // ✅ DataTables en español
        $('#tabla-ciclos').DataTable({
          language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
          },
          pageLength: 10,
          autoWidth: false,
          lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']]
        });
      })
      .catch(err => {
        console.error('Cerrar ciclo error:', err);
        Swal.fire('Error', err.message || 'No se pudo cambiar el estado.', 'error');
      });
  }

  // ============ 11) Carga inicial ============
  cargarCiclos();
});
