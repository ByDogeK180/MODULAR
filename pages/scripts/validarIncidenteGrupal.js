document.addEventListener('DOMContentLoaded', () => {
  const formGrupal = document.getElementById('formIncidenteGrupal');
  const mensajeGrupal = document.getElementById('mensajeGrupal');

  if (!formGrupal) return;

  formGrupal.addEventListener('submit', (e) => {
    e.preventDefault();

    const clase_id = document.getElementById('selectClaseGrupal').value;
    const materia_id = document.getElementById('selectMateriaGrupal').value;
    const tipo = document.getElementById('tipoGrupal').value;
    const descripcion = document.getElementById('descripcionGrupal').value.trim();
    const fecha = document.getElementById('fechaGrupal').value;

    if (!clase_id || !materia_id || !tipo || !descripcion || !fecha) {
      mensajeGrupal.innerHTML = `<div class="alert alert-warning">Por favor llena todos los campos.</div>`;
      setTimeout(() => mensajeGrupal.innerHTML = '', 5000);
      return;
    }

    const formData = new FormData();
    formData.append('clase_id', clase_id);
    formData.append('materia_id', materia_id);
    formData.append('tipo', tipo);
    formData.append('descripcion', descripcion);
    formData.append('fecha', fecha);

    fetch('../php/registrar_incidente_grupal.php', {
      method: 'POST',
      body: formData
    })
      .then(res => res.json())
      .then(res => {
        if (res.status === 'success') {
          mensajeGrupal.innerHTML = `<div class="alert alert-success">Incidente grupal registrado correctamente.</div>`;
          formGrupal.reset();
        } else {
          mensajeGrupal.innerHTML = `<div class="alert alert-danger">Error: ${res.msg}</div>`;
        }
        setTimeout(() => mensajeGrupal.innerHTML = '', 5000);
      })
      .catch(err => {
        console.error('Error al enviar incidente grupal:', err);
        mensajeGrupal.innerHTML = `<div class="alert alert-danger">Error de conexión con el servidor.</div>`;
        setTimeout(() => mensajeGrupal.innerHTML = '', 5000);
      });
  });
});
