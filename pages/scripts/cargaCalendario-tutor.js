// pages/scripts/cargaCalendario-tutor.js (CON FILTRO 'limpias')
document.addEventListener('DOMContentLoaded', function () {
  const calendarEl   = document.getElementById('calendario-festivos');
  const inputFecha   = document.getElementById('modalFecha');
  const inputTexto   = document.getElementById('notaTexto');
  const btnGuardar   = document.getElementById('guardarNota');
  const btnEliminar  = document.getElementById('eliminarNota');

  // Estado en memoria
  let notas = [];
  let selectedEvent = null; // referencia al evento clickeado

  // Helpers para Bootstrap 4
  const showModal = () => $('#notaModal').modal('show');
  const hideModal = () => $('#notaModal').modal('hide');

const calendar = new FullCalendar.Calendar(calendarEl, {
  initialView: 'dayGridMonth',
  locale: 'es',
  height: 'auto',
  headerToolbar: {
    left: 'prev,next today',
    center: 'title',
    right: 'dayGridMonth,timeGridWeek,listWeek'
  },
  buttonText: {
    today: 'Hoy'
  },
  views: {
    listWeek:     { buttonText: 'Lista semanal' },
    timeGridWeek: { buttonText: 'Semana' },
    dayGridMonth: { buttonText: 'Mes' }
  },

    // Carga de NOTAS desde backend
    events: function (info, successCallback, failureCallback) {
      fetch('../php/obtener_notas.php')
        .then(res => res.json())
        .then(data => {
          // ✅ Filtrar notas vacías (ni title ni contenido)
          const limpias = (data || []).filter(n => ((n.title ?? n.contenido ?? '').trim() !== ''));

          // Normaliza campos para FullCalendar
          notas = limpias.map(nota => {
            const id     = nota.id ?? null;
            const start  = nota.start || nota.fecha;      // permite 'start' o 'fecha'
            const raw    = nota.title ?? nota.contenido ?? '';
            const title  = `📌 ${raw}`;

            return {
              id, start, title,
              color: '#007bff',
              textColor: 'white',
              tipo: 'nota'
            };
          });

          successCallback(notas);
        })
        .catch(err => {
          console.error('Error al cargar notas:', err);
          failureCallback(err);
        });
    },

    // Carga de FESTIVOS desde JSON (solo visual)
    eventSources: [
      {
        events: function (info, successCallback, failureCallback) {
          fetch('../holidays/dias_festivos.json')
            .then(res => res.json())
            .then(festivos => {
              const eventos = festivos.map(f => ({
                title: `📅 ${f.motivo}`,
                start: f.fecha,
                color: '#dc3545',
                textColor: 'white',
                display: 'block',
                tipo: 'festivo'
              }));
              successCallback(eventos);
            })
            .catch(failureCallback);
        }
      }
    ],

    dateClick: function (info) {
      const fecha = info.dateStr;
      selectedEvent = null; // no hay evento seleccionado aún

      // ¿Ya hay una nota en esa fecha?
      const notaExistente = notas.find(n => n.start === fecha);

      inputFecha.value = fecha;
      inputTexto.value = notaExistente ? (notaExistente.title || '').replace(/^📌\s*/, '') : '';
      btnEliminar.style.display = notaExistente ? 'inline-block' : 'none';

      showModal();
    },

    eventClick: function (info) {
      // Solo notas (ignora festivos)
      if (info.event.extendedProps.tipo !== 'nota') return;

      selectedEvent = info.event;

      inputFecha.value = info.event.startStr;
      inputTexto.value = (info.event.title || '').replace(/^📌\s*/, '');
      btnEliminar.style.display = 'inline-block';

      showModal();
    },

    eventDidMount: function (info) {
      // Tooltip con Bootstrap 4
      if ((info.event.title || '').length > 15) {
        $(info.el).tooltip({
          title: info.event.title,
          placement: 'top',
          trigger: 'hover',
          container: 'body'
        });
      }
    }
  });

  // GUARDAR nota (crear o actualizar)
  btnGuardar.addEventListener('click', () => {
    const fecha = inputFecha.value;
    const texto = inputTexto.value.trim();

    if (!fecha) return;

    // ¿Actualizar nota seleccionada?
    if (selectedEvent && selectedEvent.extendedProps.tipo === 'nota') {
      const payload = new URLSearchParams();
      if (selectedEvent.id) payload.append('id', selectedEvent.id);
      payload.append('fecha', fecha);
      payload.append('contenido', texto);

      fetch('../php/guardar_nota.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: payload.toString()
      })
        .then(res => res.json().catch(() => ({})))
        .then(() => {
          selectedEvent.setProp('title', texto ? `📌 ${texto}` : '📌 (sin nota)');
          hideModal();
          calendar.refetchEvents();
        })
        .catch(err => console.error('Error guardando nota:', err));

      return;
    }

    // CREAR nota nueva
    const payload = new URLSearchParams();
    payload.append('fecha', fecha);
    payload.append('contenido', texto);

    fetch('../php/guardar_nota.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: payload.toString()
    })
      .then(res => res.json().catch(() => ({})))
      .then(() => {
        hideModal();
        calendar.refetchEvents();
      })
      .catch(err => console.error('Error creando nota:', err));
  });

  // ELIMINAR nota
  btnEliminar.addEventListener('click', () => {
    const fecha = inputFecha.value;

    // Caso 1: tenemos evento seleccionado con ID -> eliminar por ID
    if (selectedEvent && selectedEvent.extendedProps.tipo === 'nota' && selectedEvent.id) {
      const payload = new URLSearchParams();
      payload.append('id', selectedEvent.id);

      fetch('../php/eliminar_nota.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: payload.toString()
      })
        .then(res => res.json().catch(() => ({})))
        .then(() => {
          selectedEvent.remove();
          selectedEvent = null;
          hideModal();
          // calendar.refetchEvents(); // si prefieres siempre recargar del server
        })
        .catch(err => console.error('Error eliminando nota:', err));

      return;
    }

    // Caso 2 (fallback): eliminar por FECHA enviando contenido vacío
    const payload = new URLSearchParams();
    payload.append('fecha', fecha);
    payload.append('contenido', '');

    fetch('../php/guardar_nota.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: payload.toString()
    })
      .then(res => res.json().catch(() => ({})))
      .then(() => {
        hideModal();
        calendar.refetchEvents();
      })
      .catch(err => console.error('Error eliminando (fallback):', err));
  });

  calendar.render();
});
