document.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('accordionProfesores');
  const inputBusqueda = document.getElementById('busquedaProfesor');
  let datosOriginales = [];

  fetch('../php/obtener_profesores_hijos.php')
    .then(res => res.json())
    .then(json => {
      if (!json.data) return;
      datosOriginales = json.data;
      renderizar(datosOriginales);
    });

  inputBusqueda.addEventListener('input', () => {
    const termino = inputBusqueda.value.toLowerCase();
    const filtrados = datosOriginales.filter(p =>
      `${p.nombre_docente} ${p.apellido_docente}`.toLowerCase().includes(termino) ||
      p.materia.toLowerCase().includes(termino) ||
      (p.grado || '').toLowerCase().includes(termino) ||
      String(p.docente_id).includes(termino)
    );
    renderizar(filtrados);
  });

  function renderizar(data) {
    const agrupado = {};

    data.forEach(p => {
      const id = p.estudiante_id;
      const nombre = `${p.nombre_estudiante} ${p.apellido_estudiante}`;
      if (!agrupado[id]) agrupado[id] = { nombre, profesores: [] };
      agrupado[id].profesores.push(p);
    });

    container.innerHTML = '';

    let index = 0;
    for (const [id, hijo] of Object.entries(agrupado)) {
      const collapseId = `collapseHijo${index}`;
      const card = document.createElement('div');
      card.className = 'card mb-3';

      const docentesHtml = hijo.profesores.map(p => `
        <div class="media border-bottom py-3 d-flex align-items-center">
            <img src="../../${p.foto_url}" class="mr-3 rounded-circle" width="60" height="60">
            <div class="media-body">
            <h6 class="mt-0 mb-1">
                <i class="fa fa-user text-primary"></i> ${p.nombre_docente} ${p.apellido_docente}
            </h6>
            <p class="mb-0"><i class="fa fa-envelope"></i> ${p.correo}</p>
            <p class="mb-0"><i class="fa fa-book"></i> Materia: ${p.materia}</p>
            <a href="mailto:${p.correo}" class="btn btn-sm btn-outline-primary mt-2">
                <i class="fa fa-paper-plane"></i> Contactar
            </a>
            </div>
        </div>
        `).join('');

      card.innerHTML = `
        <div class="card-header" id="heading${index}">
          <h5 class="mb-0">
            <button class="btn btn-link d-flex justify-content-between w-100" data-toggle="collapse" data-target="#${collapseId}" aria-expanded="true">
              <span><i class="fa fa-child"></i> ${hijo.nombre}</span>
              <i class="fa fa-chevron-down"></i>
            </button>
          </h5>
        </div>
        <div id="${collapseId}" class="collapse ${index === 0 ? 'show' : ''}" data-parent="#accordionProfesores">
          <div class="card-body">${docentesHtml}</div>
        </div>
      `;
      container.appendChild(card);
      index++;
    }

    if (Object.keys(agrupado).length === 0) {
      container.innerHTML = '<p class="text-center text-muted">No se encontraron resultados.</p>';
    }
  }
});
