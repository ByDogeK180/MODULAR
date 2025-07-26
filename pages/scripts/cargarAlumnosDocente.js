document.addEventListener('DOMContentLoaded', () => {
  const select = document.getElementById('estudiante');
  if (!select) return; // Si no existe el select, salimos

  fetch('../php/obtener_estudiantes_docente.php')
    .then(res => res.json())
    .then(data => {
      // Limpiar y poner opción por defecto
      select.innerHTML = '<option value="">Seleccione estudiante</option>';

      if (!Array.isArray(data) || data.length === 0) {
        select.innerHTML += '<option disabled>No hay estudiantes disponibles</option>';
        return;
      }

      data.forEach(e => {
        const opt = document.createElement('option');
        opt.value = e.estudiante_id;
        opt.textContent = `${e.estudiante_nombre} ${e.estudiante_apellido} - ${e.grado}°${e.grupo} - ${e.materia_nombre} (${e.ciclo || 'Sin ciclo'})`;
        select.appendChild(opt);
      });
    })
    .catch(err => console.error('Error cargando estudiantes:', err));
});
