document.addEventListener('DOMContentLoaded', () => {
  const contenedor = document.getElementById('contenedor-recordatorios');
  const buscador = document.getElementById('buscador');
  const paginacion = document.getElementById('paginacion');
  let dataGlobal = [];
  let paginaActual = 1;
  const tarjetasPorPagina = 6;

  fetch('../php/obtener_incidentes_docente.php')
    .then(res => res.json())
    .then(data => {
      dataGlobal = data;
      renderizar();
    })
    .catch(err => {
      console.error('Error al cargar incidentes:', err);
    });

  function renderizar() {
    const texto = buscador.value.toLowerCase();

    const filtrados = dataGlobal.filter(inc =>
      (inc.estudiante_nombre + ' ' + inc.estudiante_apellido).toLowerCase().includes(texto) ||
      (inc.materia_nombre || '').toLowerCase().includes(texto) ||
      inc.descripcion.toLowerCase().includes(texto)
    );

    const inicio = (paginaActual - 1) * tarjetasPorPagina;
    const paginados = filtrados.slice(inicio, inicio + tarjetasPorPagina);

    contenedor.innerHTML = '';

    if (!filtrados.length) {
      contenedor.innerHTML = `<div class="col-12"><div class="alert alert-info">No se encontraron resultados.</div></div>`;
      paginacion.innerHTML = '';
      return;
    }

    paginados.forEach(inc => {
      const card = document.createElement('div');
      card.className = 'col-md-6 col-lg-4 mb-4';
      card.innerHTML = `
        <div class="card h-100 shadow-sm border-left-warning">
          <div class="card-body">
            <h5 class="card-title text-danger d-flex justify-content-between">
              <span><i class="fas fa-exclamation-triangle me-2"></i> ${inc.tipo.toUpperCase()}</span>
              <span class="badge ${inc.alcance === 'general' ? 'bg-primary' : 'bg-info'} text-uppercase">${inc.alcance}</span>
            </h5>
            ${inc.alcance === 'general' 
              ? `<p class="card-text mb-1"><strong>Clase:</strong> ${inc.clase_nombre}</p>` 
              : `<p class="card-text mb-1"><strong>Estudiante:</strong> ${inc.estudiante_nombre} ${inc.estudiante_apellido}</p>`}
            <p class="card-text mb-1"><strong>Materia:</strong> ${inc.materia_nombre}</p>
            <p class="card-text"><strong>Descripción:</strong> ${inc.descripcion}</p>
            <p class="card-text mb-1"><strong>Ciclo:</strong> ${inc.ciclo_nombre}</p>
            <p class="card-text mb-1"><strong>Periodo:</strong> ${inc.periodo_nombre}</p>
          </div>
          <div class="card-footer text-muted small">
            ${new Date(inc.fecha).toLocaleDateString()}
          </div>
        </div>
      `;
      contenedor.appendChild(card);
    });

    renderizarPaginacion(filtrados.length);
  }

  function renderizarPaginacion(total) {
    const totalPaginas = Math.ceil(total / tarjetasPorPagina);
    paginacion.innerHTML = '';

    for (let i = 1; i <= totalPaginas; i++) {
      const li = document.createElement('li');
      li.className = `page-item ${i === paginaActual ? 'active' : ''}`;
      li.innerHTML = `<button class="page-link">${i}</button>`;
      li.addEventListener('click', () => {
        paginaActual = i;
        renderizar();
      });
      paginacion.appendChild(li);
    }
  }

  buscador.addEventListener('input', () => {
    paginaActual = 1;
    renderizar();
  });
});
