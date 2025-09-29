// pages/scripts/login.js
$(document).ready(function () {
  // Diagnóstico: ¿está Bootstrap Toast disponible?
  if (!window.bootstrap || !bootstrap.Toast) {
    console.error("❌ Bootstrap Toast no está disponible. Revisa que el <script> de Bootstrap JS esté antes de este archivo.");
  }

  // Utilidad: crear y mostrar toasts
  function showToast({ title = "Aviso", message = "", type = "info", delay = 4500 } = {}) {
    const area = document.getElementById("toastArea");
    if (!area) {
      console.error("❌ No existe #toastArea en el DOM. Asegúrate de poner el contenedor de toasts antes de </body>.");
      return;
    }

    const bg = {
      success: "bg-success text-white",
      danger:  "bg-danger text-white",
      warning: "bg-warning text-dark",
      info:    "bg-primary text-white"
    }[type] || "bg-primary text-white";

    const id = "t_" + Date.now();
    const html = `
      <div id="${id}" class="toast align-items-center fade" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="${delay}">
        <div class="toast-header ${bg}">
          <strong class="me-auto">${title}</strong>
          <small>ahora</small>
          <button type="button" class="btn-close ${bg.includes('text-white') ? 'btn-close-white' : ''} ms-2 mb-1" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">${message}</div>
      </div>`;

    area.insertAdjacentHTML("beforeend", html);
    const toastEl = document.getElementById(id);

    try {
      const t = new bootstrap.Toast(toastEl);
      t.show();
      toastEl.addEventListener("hidden.bs.toast", () => toastEl.remove());
    } catch (e) {
      console.error("❌ No se pudo crear el Toast. ¿bootstrap.Toast existe?", e);
    }
  }

  // Exponer pruebas a la consola
  window.SCL_testToast = (msg = "Toast de prueba") => showToast({ title: "Prueba", message: msg, type: "info" });

  // Mostrar aviso si viene de ?mensaje=no-autorizado
  const params = new URLSearchParams(window.location.search);
  if (params.get("mensaje") === "no-autorizado") {
    showToast({
      title: "Acceso requerido",
      message: "⚠️ No autorizado. Inicia sesión con tu cuenta.",
      type: "warning"
    });
  }

  // Envío del login
  $("#loginForm").on("submit", function (event) {
    event.preventDefault();

    const email = $("#email").val().trim();
    const password = $("#password").val().trim();

    if (!email || !password) {
      showToast({
        title: "Campos incompletos",
        message: "⚠️ Completa correo y contraseña.",
        type: "warning"
      });
      return;
    }

    $.ajax({
      url: "../php/login.php",
      type: "POST",
      data: { email, password },
      dataType: "json",
      success: function (response) {
        if (response && response.status === "success") {
          showToast({
            title: "Bienvenido",
            message: "✅ Acceso concedido. Redirigiendo...",
            type: "success",
            delay: 2500
          });
          setTimeout(() => { window.location.href = response.redirect; }, 600);
        } else {
          showToast({
            title: "Credenciales inválidas",
            message: (response && response.message) ? "❌ " + response.message : "❌ Verifica tu correo y contraseña.",
            type: "danger"
          });
        }
      },
      error: function (xhr) {
        showToast({
          title: "Error de conexión",
          message: `❌ No se pudo contactar al servidor${xhr?.status ? " (HTTP " + xhr.status + ")" : ""}.`,
          type: "danger"
        });
      }
    });
  });

  // Forgot password
  $("#forgotPasswordForm").on("submit", function (event) {
    event.preventDefault();

    const email = $("#forgotEmail").val().trim();

    if (!email) {
      showToast({
        title: "Falta el correo",
        message: "⚠️ Ingresa tu correo electrónico.",
        type: "warning"
      });
      return;
    }

    $.ajax({
      url: "../php/forgot_password.php",
      type: "POST",
      data: { email },
      dataType: "json",
      success: function (response) {
        if (response && response.status === "success") {
          showToast({
            title: "Correo enviado",
            message: "✅ Revisa tu bandeja. Te enviamos un enlace para restablecer la contraseña.",
            type: "success"
          });
          $("#forgotEmail").val("");
        } else {
          showToast({
            title: "No se pudo enviar",
            message: (response && response.message) ? "❌ " + response.message : "❌ Inténtalo de nuevo en unos minutos.",
            type: "danger"
          });
        }
      },
      error: function (xhr) {
        showToast({
          title: "Error de conexión",
          message: `❌ No se pudo contactar al servidor${xhr?.status ? " (HTTP " + xhr.status + ")" : ""}.`,
          type: "danger"
        });
      }
    });
  });

  // ===== OJITO: mostrar / ocultar contraseña =====
  $(document).on('click', '.toggle-password', function () {
    const $input = $('#password');
    const isText = $input.attr('type') === 'text';
    $input.attr('type', isText ? 'password' : 'text');

    const $icon = $(this).find('i');
    $icon.toggleClass('bi-eye bi-eye-slash');

    $(this)
      .attr('aria-label', isText ? 'Mostrar contraseña' : 'Ocultar contraseña')
      .attr('aria-pressed', isText ? 'false' : 'true');
  });
});
