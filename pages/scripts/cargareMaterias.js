document.addEventListener("DOMContentLoaded", () => {
  const contenedor = document.getElementById("materias-container");
  const buscarInput = document.getElementById("buscar-materia");
  const filtroSelect = document.getElementById("filtrar-nivel");
  let materias = [];
  let rolUsuario = null;

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

    const rutaDetalle = `../courses/detalle-materia.php?id=${m.materia_id}`;

    wrapper.innerHTML = `
      <div class="card h-100 shadow-sm border-0">
        ${
          m.foto_url
            ? `<img src="../../${m.foto_url}" class="card-img-top" alt="${m.nombre}">`
            : `<div class="text-center py-5 bg-light">
                 <i class="material-icons text-warning" style="font-size:48px;">menu_book</i>
               </div>`
        }
        <div class="card-body d-flex flex-column text-center">
          <h5 class="fw-bold">${m.nombre}</h5>
          <span class="badge bg-warning text-dark mb-2">${m.nivel_grado}</span>
          <p class="text-muted flex-grow-1">${m.descripcion || "Sin descripción"}</p>
          <a href="${rutaDetalle}" class="btn btn-outline-warning mt-auto">
            Ver detalle
          </a>
        </div>
      </div>
    `;
    contenedor.appendChild(wrapper);
  });
}


  buscarInput.addEventListener("input", renderMaterias);
  filtroSelect.addEventListener("change", renderMaterias);

  fetch("../php/obtener_materias.php")
    .then(res => res.json())
    .then(data => {
      materias = data.materias;
      rolUsuario = data.rol;
      renderMaterias();
    })
    .catch(err => console.error("Error al cargar materias:", err));
});