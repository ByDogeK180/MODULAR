document.addEventListener('DOMContentLoaded', () => {
  const formIndividual = document.getElementById('formIncidenteIndividual');
  const formGrupal = document.getElementById('formIncidenteGrupal');

  const mensajeIndividual = document.getElementById('mensajeIndividual');
  const mensajeGrupal = document.getElementById('mensajeGrupal');

  // Validación para el formulario individual
  if (formIndividual) {
    formIndividual.addEventListener('submit', function (e) {
      e.preventDefault();

      const estudiante = document.getElementById('estudiante').value;
      const tipo = document.getElementById('tipoIndividual').value;
      const descripcion = document.getElementById('descripcionIndividual').value.trim();
      const fecha = document.getElementById('fechaIndividual').value;

      if (!estudiante || !tipo || !descripcion || !fecha) {
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

  // Validación para el formulario grupal
  if (formGrupal) {
    formGrupal.addEventListener('submit', function (e) {
      e.preventDefault();

      const clase = document.getElementById('selectClaseGrupal').value;
      const materia = document.getElementById('selectMateriaGrupal').value;
      const tipo = document.getElementById('tipoGrupal').value;
      const descripcion = document.getElementById('descripcionGrupal').value.trim();
      const fecha = document.getElementById('fechaGrupal').value;

      if (!clase || !materia || !tipo || !descripcion || !fecha) {
        mensajeGrupal.innerHTML = `<div class="alert alert-warning">Por favor llena todos los campos.</div>`;
        setTimeout(() => mensajeGrupal.innerHTML = '', 5000);
        return;
      }

      const datos = new URLSearchParams(new FormData(formGrupal));

      fetch('../php/registrar_incidente_grupal.php', {
        method: 'POST',
        body: datos
      })
        .then(res => res.json())
        .then(resp => {
          if (resp.status === 'success') {
            mensajeGrupal.innerHTML = `<div class="alert alert-success">Incidente grupal guardado.</div>`;
            formGrupal.reset();
          } else {
            mensajeGrupal.innerHTML = `<div class="alert alert-danger">Error: ${resp.msg}</div>`;
          }
          setTimeout(() => mensajeGrupal.innerHTML = '', 5000);
        })
        .catch(err => {
          console.error('Error al enviar:', err);
          mensajeGrupal.innerHTML = `<div class="alert alert-danger">Error de conexión.</div>`;
          setTimeout(() => mensajeGrupal.innerHTML = '', 5000);
        });
    });
  }
});
