// animacionAgregarProfesor.js
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('registerForm');
  if (!form) return;

  const submitBtn = form.querySelector('button[type="submit"]');
  let sending = false;

  function marcarInvalido(el, msg) {
    if (!el) return;
    el.classList.add('is-invalid');
    const fb = el.nextElementSibling;
    if (fb && fb.classList.contains('invalid-feedback')) fb.textContent = msg;
  }

  function limpiarInvalidos() {
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
  }

  function validar() {
    limpiarInvalidos();

    // Ajusta estos IDs a los que uses realmente en tu form:
    const email = form.querySelector('#correo');
    const pwd   = form.querySelector('#contraseña');
    const pwd2  = form.querySelector('#confirmar_contraseña');
    const nombre = form.querySelector('#nombre');     // opcional
    const apellido = form.querySelector('#apellido'); // opcional

    let ok = true;

    if (nombre && !nombre.value.trim()) { marcarInvalido(nombre, 'Obligatorio'); ok = false; }
    if (apellido && !apellido.value.trim()) { marcarInvalido(apellido, 'Obligatorio'); ok = false; }

    if (email) {
      const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!email.value.trim() || !re.test(email.value)) { marcarInvalido(email, 'Correo inválido'); ok = false; }
    }

    if (pwd) {
      if (!pwd.value) { marcarInvalido(pwd, 'Obligatoria'); ok = false; }
      else if (pwd.value.length < 8) { marcarInvalido(pwd, 'Mínimo 8 caracteres'); ok = false; }
    }

    if (pwd2) {
      if (!pwd2.value) { marcarInvalido(pwd2, 'Confirma la contraseña'); ok = false; }
      else if (pwd && pwd.value !== pwd2.value) { marcarInvalido(pwd2, 'No coinciden'); ok = false; }
    }

    if (!ok) {
      Swal.fire({ icon: 'error', title: 'Corrige los campos marcados', timer: 2000, showConfirmButton: false });
    }
    return ok;
  }

  // quitar rojo al escribir confirmación
  form.querySelector('#confirmar_contraseña')?.addEventListener('input', e => e.target.classList.remove('is-invalid'));

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    if (sending) return;
    if (!validar()) return;

    sending = true;
    if (submitBtn) submitBtn.disabled = true;

    const fd = new FormData(form);
    fetch(form.action, { method: 'POST', body: fd })
      .then(r => {
        // Ideal: tu PHP debe responder JSON {success, message}
        const ct = r.headers.get('content-type') || '';
        return ct.includes('application/json') ? r.json() : r.text().then(t => ({ success: !/error/i.test(t), message: t }));
      })
      .then(({ success, message }) => {
        Swal.fire({ toast: true, position: 'top-end', icon: success ? 'success' : 'error', title: message, showConfirmButton: false, timer: 2500 });
        if (success) form.reset();
      })
      .catch(() => {
        Swal.fire({ icon: 'error', title: 'No se pudo conectar', timer: 2000, showConfirmButton: false });
      })
      .finally(() => {
        sending = false;
        if (submitBtn) submitBtn.disabled = false;
      });
  });
});
