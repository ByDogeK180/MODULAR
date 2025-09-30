(function() {
      const form = document.getElementById('formEstudiante');
      const nameRegex = /^[A-Za-zÁÉÍÓÚÑáéíóúñ ]{2,}$/;

      form.addEventListener('submit', function(e) {
        let valid = true;

        const campo = (selector) => form.elements[selector];

        // Nombre
        if (!nameRegex.test(campo('nombre').value.trim())) {
          valid = false;
          campo('nombre').classList.add('is-invalid');
        } else {
          campo('nombre').classList.remove('is-invalid');
        }

        // Apellido
        if (!nameRegex.test(campo('apellido').value.trim())) {
          valid = false;
          campo('apellido').classList.add('is-invalid');
        } else {
          campo('apellido').classList.remove('is-invalid');
        }

        // Fecha de Nacimiento
        if (!campo('fecha_nacimiento').value) {
          valid = false;
          campo('fecha_nacimiento').classList.add('is-invalid');
        } else {
          campo('fecha_nacimiento').classList.remove('is-invalid');
        }

        // Grado
        const gradoVal = parseInt(campo('grado').value, 10);
        if (isNaN(gradoVal) || gradoVal < 1 || gradoVal > 12) {
          valid = false;
          campo('grado').classList.add('is-invalid');
        } else {
          campo('grado').classList.remove('is-invalid');
        }

        // Grupo
        if (!campo('grupo').value) {
          valid = false;
          campo('grupo').classList.add('is-invalid');
        } else {
          campo('grupo').classList.remove('is-invalid');
        }

        // Tutor
        if (!campo('tutor_id').value) {
          valid = false;
          campo('tutor_id').classList.add('is-invalid');
        } else {
          campo('tutor_id').classList.remove('is-invalid');
        }

        if (!valid) {
          e.preventDefault();
          e.stopPropagation();
          Swal.fire({
            icon: 'error',
            title: 'Corrige los campos marcados',
            timer: 2000,
            showConfirmButton: false
          });
        }
      });
    })();