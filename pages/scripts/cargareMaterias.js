document.addEventListener("DOMContentLoaded", () => {
  const contenedor   = document.getElementById("materias-container");
  const buscarInput  = document.getElementById("buscar-materia");
  const filtroSelect = document.getElementById("filtrar-nivel");
  let materias = [];
  let rolUsuario = null;

  // ---------- Helpers de imagen ----------
  // quita acentos/espacios: "Educación Física" -> "educacionfisica"
  function slugMateria(s = "") {
    return String(s)
      .normalize("NFD").replace(/[\u0300-\u036f]/g, "")
      .toLowerCase()
      .replace(/\s+/g, "");
  }
  // arma la ruta final para mostrar en <img>, con fallback a /assets/img/materias/
  function fotoMateria(obj = {}) {
    const pathFromDB = obj.foto_url || "";

    // si viene de BD: soporta http(s) o rutas relativas
    if (pathFromDB) {
      if (/^https?:\/\//i.test(pathFromDB)) return pathFromDB;
      if (pathFromDB.startsWith("../") || pathFromDB.startsWith("../../")) return pathFromDB;
      return `../../${pathFromDB}`;
    }

    // fallback por nombre
    const k = slugMateria(obj.nombre);
    const overrides = {
      // mapea a tus archivos EXACTOS
      // si renombraste "español.png" a "espanol.png", cambia a 'espanol': 'espanol.png'
      'espanol': 'español.png',
      'ciencias': 'cienciasNaturales.png',
      'geografia': 'geografia.png',
      'historia': 'historia.png',
      'educacionfisica': 'educacionFisica.png',
      'educacioncivica': 'educacionCivica.png',
      // añade más si los necesitas: 'matematicas': 'matematicas.png'
    };
    const file = overrides[k] || 'others/placeholder.png';
    return `../../assets/img/materias/${file}`;
  }

  function renderMaterias() {
    const texto = buscarInput.value.toLowerCase();
    const nivel = filtroSelect.value;
    contenedor.innerHTML = "";

    const materiasFiltradas = materias.filter(m =>
      m.nombre.toLowerCase().includes(texto) &&
      (nivel === "" || m.nivel_grado === nivel)
    );

    if (materiasFiltradas.length === 0) {
      contenedor.innerHTML = `
        <div class="col-12 text-center text-muted py-5">
          <i class="material-icons text-warning" style="font-size:48px;">menu_book</i>
          <p class="mt-2">No se encontraron materias.</p>
        </div>`;
      return;
    }

    materiasFiltradas.forEach(m => {
      const wrapper = document.createElement("div");
      wrapper.className = "col-12 col-sm-6 col-md-4 col-lg-3 mb-4";

      const imgSrc = fotoMateria(m);

      wrapper.innerHTML = `
        <div class="card h-100 shadow-sm border-0">
          <img src="${encodeURI(imgSrc)}" class="card-img-top" alt="${m.nombre}">
          <div class="card-body d-flex flex-column text-center">
            <h5 class="fw-bold">${m.nombre}</h5>
            <span class="badge bg-warning text-dark mb-2">${m.nivel_grado}</span>
            <p class="text-muted flex-grow-1">${m.descripcion || "Sin descripción"}</p>
            <button type="button"
                    class="btn btn-outline-warning mt-auto btn-ver-detalle"
                    data-id="${m.materia_id}">
              Ver detalle
            </button>
          </div>
        </div>
      `;
      contenedor.appendChild(wrapper);
    });
  }

  // Delegación: click en "Ver detalle"
  contenedor.addEventListener('click', (e) => {
    const btn = e.target.closest('.btn-ver-detalle');
    if (!btn) return;

    const id = btn.dataset.id;
    if (window.$) $('#det-tabs a[href="#tab-resumen"]').tab('show');

    fetch('../php/materia_detalle.php?id=' + encodeURIComponent(id))
      .then(r => r.json())
      .then(data => {
        if (data.error) throw new Error(data.error);

        // Campos base
        document.getElementById('det-id').textContent     = data.materia_id || '';
        document.getElementById('det-nombre').textContent = data.nombre || 'Materia';
        document.getElementById('det-nivel').textContent  = data.nivel_grado || '—';

        // imagen del modal (usa el mismo fallback)
        const foto = fotoMateria(data);
        const img  = document.getElementById('det-foto');
        if (img) { img.src = encodeURI(foto); img.alt = data.nombre || 'Materia'; }
        const hero = document.getElementById('det-hero');
        if (hero) hero.style.backgroundImage = `url("${encodeURI(foto)}")`;

        document.getElementById('det-desc').textContent = (data.descripcion || 'Sin descripción.');

        // Temario
        const temario = Array.isArray(data.temario) ? data.temario : [];
        let temHtml = '<div class="text-muted">Sin temario por ahora.</div>';
        if (temario.length) {
          temHtml = '<ul class="pl-3 mb-0">' + temario.map(u => `
            <li><strong>${u.titulo || ''}</strong>
              ${
                Array.isArray(u.subtemas) && u.subtemas.length
                  ? '<ul class="mb-0">' + u.subtemas.map(s => `<li>${s}</li>`).join('') + '</ul>'
                  : ''
              }
            </li>
          `).join('') + '</ul>';
        }
        document.getElementById('det-temario').innerHTML = temHtml;

        // Recursos
        const recursos = Array.isArray(data.recursos) ? data.recursos : [];
        document.getElementById('det-recursos').innerHTML =
          recursos.length
            ? recursos.map(r => `<li><a href="${r.url}" target="_blank" rel="noopener">${r.titulo || r.url}</a></li>`).join('')
            : '<span class="text-muted">Sin recursos.</span>';

        // Docente
        const d = data.docente || {};
        document.getElementById('det-doc-nombre').textContent = d.nombre || 'Sin asignar';
        document.getElementById('det-doc-email').textContent  = d.correo || '';
        document.getElementById('det-doc-foto').src          =
          encodeURI(d.foto_url
            ? (d.foto_url.startsWith('http') || d.foto_url.startsWith('../') || d.foto_url.startsWith('../../')
                ? d.foto_url
                : `../../${d.foto_url}`)
            : '../../assets/img/uploads/profesor-default.png');

        // Opiniones
        const opiniones = Array.isArray(data.opiniones) ? data.opiniones : [];
        document.getElementById('det-opiniones').innerHTML =
          opiniones.length
            ? opiniones.map(o => `
                <div class="mb-3">
                  <strong>${o.autor || 'Anónimo'}</strong> ${o.estrellas || '★★★★★'}<br>
                  ${o.texto || ''}
                </div>
              `).join('')
            : '<div class="text-muted">Aún no hay opiniones.</div>';

        // Mostrar modal
        if (window.$) {
          $('#modalDetalleMateria').modal('show');
        } else {
          document.getElementById('modalDetalleMateria').classList.add('show');
        }
      })
      .catch(err => {
        console.error(err);
        alert('No se pudo cargar el detalle de la materia.');
      });
  });

  buscarInput.addEventListener("input", renderMaterias);
  filtroSelect.addEventListener("change", renderMaterias);

  fetch("../php/obtener_materias.php")
    .then(res => res.json())
    .then(data => {
      materias   = data.materias || [];
      rolUsuario = data.rol;
      renderMaterias();
    })
    .catch(err => console.error("Error al cargar materias:", err));
});
