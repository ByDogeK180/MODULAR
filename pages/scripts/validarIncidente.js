document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('formIncidente');
  const mensaje = document.getElementById('mensaje');

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    const estudiante = document.getElementById('estudiante').value;
    const tipo = document.getElementById('tipo').value;
    const descripcion = document.getElementById('descripcion').value.trim();
    const fecha = document.getElementById('fecha').value;

    if (!estudiante || !tipo || !descripcion || !fecha) {
      mensaje.innerHTML = `<div class="alert alert-warning">Por favor llena todos los campos antes de enviar.</div>`;
      setTimeout(() => mensaje.innerHTML = '', 5000);
      return;
    }

    const datos = new URLSearchParams(new FormData(form));

    fetch('../php/registrar_incidente.php', {
      method: 'POST',
      body: datos
    })
      .then(res => res.json())
      .then(resp => {
        if (resp.status === 'success') {
          mensaje.innerHTML = `<div class="alert alert-success">Incidente guardado correctamente.</div>`;
          form.reset();
        } else {
          mensaje.innerHTML = `<div class="alert alert-danger">Error: ${resp.msg}</div>`;
        }

        // Limpiar mensaje después de 5 segundos
        setTimeout(() => mensaje.innerHTML = '', 5000);
      })
      .catch(err => {
        console.error('Error al enviar:', err);
        mensaje.innerHTML = `<div class="alert alert-danger">Error al comunicarse con el servidor.</div>`;
        setTimeout(() => mensaje.innerHTML = '', 5000);
      });
  });
});
