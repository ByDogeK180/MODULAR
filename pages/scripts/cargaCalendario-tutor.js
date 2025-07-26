document.addEventListener('DOMContentLoaded', function () {
  const calendarEl = document.getElementById('calendario-festivos');
  const modal = new bootstrap.Modal(document.getElementById('notaModal'));
  const inputFecha = document.getElementById('modalFecha');
  const inputTexto = document.getElementById('notaTexto');
  const btnGuardar = document.getElementById('guardarNota');
  const btnEliminar = document.getElementById('eliminarNota');
  let notas = [];

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    locale: 'es',
    height: 'auto',
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,listWeek'
    },
    views: {
      listWeek: { buttonText: 'Lista semanal' },
      timeGridWeek: { buttonText: 'Semana' },
      dayGridMonth: { buttonText: 'Mes' }
    },
    events: function (info, successCallback, failureCallback) {
      fetch('../php/obtener_notas.php')
        .then(res => res.json())
        .then(data => {
          notas = data.map(nota => ({
            ...nota,
            title: `📌 ${nota.title}`,
            color: '#007bff',
            textColor: 'white',
            tipo: 'nota'
          }));
          successCallback(notas);
        })
        .catch(failureCallback);
    },
    dateClick: function (info) {
      const fecha = info.dateStr;
      const notaExistente = notas.find(n => n.start === fecha);

      inputFecha.value = fecha;
      inputTexto.value = notaExistente?.title?.replace(/^📌\s*/, '') || '';
      btnEliminar.style.display = notaExistente ? 'inline-block' : 'none';

      modal.show();
    },
    eventClick: function (info) {
      const evento = info.event;

      if (evento.extendedProps.tipo !== 'nota') return;

      inputFecha.value = evento.startStr;
      inputTexto.value = evento.title.replace(/^📌\s*/, '');
      btnEliminar.style.display = 'inline-block';

      modal.show();
    },
    eventDidMount: function (info) {
      if (info.event.title.length > 15) {
        new bootstrap.Tooltip(info.el, {
          title: info.event.title,
          placement: 'top',
          trigger: 'hover',
          container: 'body'
        });
      }
    }
  });

  // Agregar fechas importantes desde JSON (no editables)
  fetch('../holidays/dias_festivos.json')
    .then(res => res.json())
    .then(festivos => {
      const eventosFestivos = festivos.map(f => ({
        title: `📅 ${f.motivo}`,
        start: f.fecha,
        color: '#dc3545',
        textColor: 'white',
        display: 'block',
        tipo: 'festivo'
      }));
      calendar.addEventSource(eventosFestivos);
    });

  // Guardar nota
  btnGuardar.addEventListener('click', () => {
    const fecha = inputFecha.value;
    const texto = inputTexto.value.trim();

    if (!fecha) return;

    fetch('../php/guardar_nota.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: `fecha=${encodeURIComponent(fecha)}&contenido=${encodeURIComponent(texto)}`
    })
      .then(res => res.text())
      .then(() => {
        modal.hide();
        calendar.refetchEvents();
      });
  });

  // Eliminar nota
  btnEliminar.addEventListener('click', () => {
    const fecha = inputFecha.value;

    fetch('../php/guardar_nota.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: `fecha=${encodeURIComponent(fecha)}&contenido=`
    })
      .then(res => res.text())
      .then(() => {
        modal.hide();
        calendar.refetchEvents();
      });
  });

  calendar.render();
});
