(function () {
      var input     = document.getElementById('imagen');
      var label     = document.querySelector('label.custom-file-label[for="imagen"]');
      var preview   = document.getElementById('previewImagen');
      var info      = document.getElementById('infoImagen');
      var btnClear  = document.getElementById('btnLimpiarImagen');
      var btnCancel = document.getElementById('btnCancelar');
      var MAX_MB    = 2;

      function bytesToMB(bytes){ return (bytes / (1024*1024)).toFixed(2); }

      function resetImagen() {
        input.value = '';
        input.classList.remove('is-invalid');
        if (label) label.textContent = 'Seleccionar imagen…';
        preview.src = '';
        preview.classList.add('d-none');
        info.textContent = '';
      }

      if (input) {
        input.addEventListener('change', function () {
          if (!input.files || !input.files.length) { resetImagen(); return; }
          var file = input.files[0];

          var okType = /image\/(jpeg|png|webp|gif)/i.test(file.type);
          var okSize = file.size <= MAX_MB * 1024 * 1024;

          if (!okType || !okSize) {
            input.classList.add('is-invalid');  // muestra el invalid-feedback
            if (!okType) alert('Formato no permitido. Usa JPG, PNG, WEBP o GIF.');
            else alert('La imagen supera ' + MAX_MB + ' MB (' + bytesToMB(file.size) + ' MB).');
            resetImagen();
            return;
          }

          input.classList.remove('is-invalid');
          if (label) label.textContent = file.name;
          info.textContent = file.type.toUpperCase() + ' · ' + bytesToMB(file.size) + ' MB';

          var reader = new FileReader();
          reader.onload = function (e) {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
          };
          reader.readAsDataURL(file);
        });
      }

      if (btnClear) btnClear.addEventListener('click', resetImagen);

      if (btnCancel) btnCancel.addEventListener('click', function(){
        var f = document.getElementById('registerForm');
        if (f) f.reset();
        resetImagen();
      });
    })();