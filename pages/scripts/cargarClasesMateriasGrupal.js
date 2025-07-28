document.addEventListener('DOMContentLoaded', () => {
  const claseSelect = document.getElementById('selectClaseGrupal');
  const materiaSelect = document.getElementById('selectMateriaGrupal');

  // Cargar clases
  fetch('../php/obtener_clases_docente.php')
    .then(res => res.json())
    .then(data => {
      claseSelect.innerHTML = '<option value="">Seleccione clase</option>';
      data.forEach(clase => {
        claseSelect.innerHTML += `<option value="${clase.clase_id}">${clase.grado}° ${clase.grupo}</option>`;
      });
    })
    .catch(err => {
      console.error('Error al cargar clases:', err);
    });

  // Cargar materias
  fetch('../php/obtener_materias_docente_form.php')
    .then(res => res.json())
    .then(data => {
      materiaSelect.innerHTML = '<option value="">Seleccione materia</option>';
      data.forEach(materia => {
        materiaSelect.innerHTML += `<option value="${materia.materia_id}">${materia.nombre}</option>`;
      });
    })
    .catch(err => {
      console.error('Error al cargar materias:', err);
    });
});
