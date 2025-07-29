document.addEventListener('DOMContentLoaded', () => {
  const formIndividual = document.getElementById('formIncidenteIndividual');
  const formGrupal = document.getElementById('formIncidenteGrupal');

  const mensajeIndividual = document.getElementById('mensajeIndividual');
  const mensajeGrupal = document.getElementById('mensajeGrupal');
  const selectEstudiante = document.getElementById('estudiante');
  const inputMateria = document.getElementById('materia_id');

  // 🔁 Actualizar materia_id cada vez que cambie el estudiante
  if (selectEstudiante && inputMateria) {
    selectEstudiante.addEventListener('change', () => {
      const selectedOption = selectEstudiante.options[selectEstudiante.selectedIndex];
      const materiaId = selectedOption.getAttribute('data-materia-id');
      inputMateria.value = materiaId || '';
    });
  }

  // ✅ Validación y envío para el formulario individual
  if (formIndividual) {
    formIndividual.addEventListener('submit', function (e) {
      e.preventDefault();

      // 🔄 Forzar actualización de materia_id por si el usuario no cambió el select
      if (selectEstudiante && inputMateria) {
        const selectedOption = selectEstudiante.options[selectEstudiante.selectedIndex];
        const materiaId = selectedOption.getAttribute('data-materia-id');
        inputMateria.value = materiaId || '';
      }

      const estudiante = selectEstudiante.value;
      const tipo = document.getElementById('tipoIndividual').value;
      const descripcion = document.getElementById('descripcionIndividual').value.trim();
      const fecha = document.getElementById('fechaIndividual').value;
      const materia_id = inputMateria.value;

      if (!estudiante || !materia_id || !tipo || !descripcion || !fecha) {
        mensajeIndividual.innerHTML = `<div class="alert alert-warning">Por favor llena todos los campos.</div>`;
        setTimeout(() => mensajeIndividual.innerHTML = '', 5000);
        return;
      }

      const datos = new URLSearchParams(new FormData(formIndividual));

      fetch('../php/registrar_incidente.php', {
        method: 'POST',
        body: datos
      })
        .then(res => res.json())
        .then(resp => {
          if (resp.status === 'success') {
            mensajeIndividual.innerHTML = `<div class="alert alert-success">Incidente individual guardado.</div>`;
            formIndividual.reset();
            inputMateria.value = ''; // Limpia el hidden también
          } else {
            mensajeIndividual.innerHTML = `<div class="alert alert-danger">Error: ${resp.msg}</div>`;
          }
          setTimeout(() => mensajeIndividual.innerHTML = '', 5000);
        })
        .catch(err => {
          console.error('Error al enviar:', err);
          mensajeIndividual.innerHTML = `<div class="alert alert-danger">Error de conexión.</div>`;
          setTimeout(() => mensajeIndividual.innerHTML = '', 5000);
        });
    });
  }
});
