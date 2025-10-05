(function () {
      var input     = document.getElementById('imagenMateria');
      var label     = document.querySelector('label.custom-file-label[for="imagenMateria"]');
      var preview   = document.getElementById('previewImagen');
      var info      = document.getElementById('infoImagen');
      var btnClear  = document.getElementById('btnLimpiarImagen');
      var MAX_MB    = 2;

      function bytesToMB(bytes){ return (bytes / (1024*1024)).toFixed(2); }

      function resetImagen() {
        input.value = '';
        label.textContent = 'Seleccionar imagen…';
        preview.src = '';
        preview.classList.add('d-none');
        info.textContent = '';
      }

      input.addEventListener('change', function () {
        if (!input.files || !input.files.length) { resetImagen(); return; }
        var file = input.files[0];

        var okType = /image\/(jpeg|png|webp|gif)/i.test(file.type);
        var okSize = file.size <= MAX_MB * 1024 * 1024;

        if (!okType) { resetImagen(); alert('Formato no permitido. Usa JPG, PNG, WEBP o GIF.'); return; }
        if (!okSize) { resetImagen(); alert('La imagen supera ' + MAX_MB + ' MB (' + bytesToMB(file.size) + ' MB).'); return; }

        label.textContent = file.name;
        info.textContent = file.type.toUpperCase() + ' · ' + bytesToMB(file.size) + ' MB';

        var reader = new FileReader();
        reader.onload = function (e) {
          preview.src = e.target.result;
          preview.classList.remove('d-none');
        };
        reader.readAsDataURL(file);
      });

      btnClear.addEventListener('click', resetImagen);

      window.resetForm = function () {
        document.getElementById('formAgregarMateria').reset();
        resetImagen();
      };
    })();