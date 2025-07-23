document.addEventListener('DOMContentLoaded', () => {
  const select = document.getElementById('estudiante');

  fetch('../php/obtener_estudiantes_docente.php')
    .then(res => res.json())
    .then(data => {
      data.forEach(e => {
        const opt = document.createElement('option');
        opt.value = e.estudiante_id;
        opt.textContent = `${e.estudiante_nombre} ${e.estudiante_apellido} - ${e.grado}°${e.grupo} - ${e.materia_nombre} (${e.ciclo || 'Sin ciclo'})`;
        select.appendChild(opt);
      });
    })
    .catch(err => console.error('Error cargando estudiantes:', err));
});
