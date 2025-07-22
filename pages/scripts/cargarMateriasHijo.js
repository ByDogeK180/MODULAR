document.addEventListener('DOMContentLoaded', () => {
  const accordion = document.getElementById('accordionHijos');
  const inputBusqueda = document.getElementById('buscar-materia');
  const selectNivel = document.getElementById('filtrar-nivel');

  let materiasOriginales = [];

  fetch('../php/obtener_materias_hijo.php')
    .then(res => res.json())
    .then(json => {
      if (!json || !json.data) return;

      materiasOriginales = json.data;
      renderMaterias(materiasOriginales);
    })
    .catch(err => console.error('Error cargando materias:', err));

  inputBusqueda.addEventListener('input', aplicarFiltros);
  selectNivel.addEventListener('change', aplicarFiltros);

  function aplicarFiltros() {
    const texto = inputBusqueda.value.toLowerCase();
    const nivel = selectNivel.value.toLowerCase();

    const filtradas = materiasOriginales.filter(m => {
      const nombreMateria = (m.nombre_materia || '').toLowerCase();
      const nombreHijo = `${m.nombre_estudiante || m.nombre || ''} ${m.apellido_estudiante || m.apellido || ''}`.toLowerCase();
      const gradoGrupo = `${m.grado || ''} ${m.grupo || ''}`.toLowerCase();
      const coincideTexto = nombreMateria.includes(texto) || nombreHijo.includes(texto) || gradoGrupo.includes(texto);
      const coincideNivel = nivel === '' || (m.nivel_grado && m.nivel_grado.toLowerCase() === nivel);
      return coincideTexto && coincideNivel;
    });

    renderMaterias(filtradas);
  }

  function renderMaterias(materiasFiltradas) {
    accordion.innerHTML = '';
    const hijosAgrupados = {};

    materiasFiltradas.forEach(m => {
      const id = m.estudiante_id;
      const nombre = m.nombre_estudiante || m.nombre || 'Desconocido';
      const apellido = m.apellido_estudiante || m.apellido || '';

      if (!hijosAgrupados[id]) {
        hijosAgrupados[id] = {
          nombre: `${nombre} ${apellido}`,
          materias: []
        };
      }

      hijosAgrupados[id].materias.push(m);
    });

    let index = 0;
    for (const [id, hijo] of Object.entries(hijosAgrupados)) {
      const collapseId = `collapseHijo${index}`;

      const materiasHtml = hijo.materias.map(m => `
        <div class="card shadow-sm mb-3">
          <div class="card-body">
            <h6 class="card-title mb-1 text-success">
              <i class="fas fa-book"></i> ${m.nombre_materia}
            </h6>
            <p class="card-text mb-1"><i class="fas fa-layer-group"></i> Nivel: ${m.nivel_grado || '-'}</p>
            <p class="card-text mb-2"><i class="fas fa-chalkboard"></i> Clase: ${m.grado || '-'}° ${m.grupo || '-'}</p>
            <a href="materias-informacion.php?materia_id=${m.materia_id}" class="btn btn-outline-primary btn-sm">
              <i class="fa fa-eye"></i> Ver más
            </a>
          </div>
        </div>
      `).join('');

        const card = `
        <div class="card mb-3 hijo-card">
          <div class="card-header p-0" id="heading${index}">
            <h5 class="mb-0">
              <button class="btn btn-link w-100 text-center p-3 d-flex justify-content-center align-items-center" 
                      data-toggle="collapse" 
                      data-target="#${collapseId}" 
                      aria-expanded="${index === 0}" 
                      aria-controls="${collapseId}">
                <span class="d-flex align-items-center info-hijo">
                  <i class="fas fa-child"></i>
                  <span class="nombre-hijo">${hijo.nombre}</span>
                </span>
              </button>
            </h5>
          </div>

          <div id="${collapseId}" class="collapse ${index === 0 ? 'show' : ''}" data-parent="#accordionHijos">
            <div class="card-body">
              ${materiasHtml}
            </div>
          </div>
        </div>
      `;

      accordion.innerHTML += card;
      index++;
    }

    if (index === 0) {
      accordion.innerHTML = `<div class="alert alert-warning">No se encontraron materias o hijos que coincidan.</div>`;
    }
  }
});