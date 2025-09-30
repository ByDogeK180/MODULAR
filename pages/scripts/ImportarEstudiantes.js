document.addEventListener('DOMContentLoaded', function () {
  var input = document.getElementById('csvEstudiantes');
  if (!input) return;

  input.addEventListener('change', function () {
    var fileName = this.files && this.files.length ? this.files[0].name : 'Ningún archivo seleccionado';
    var label = this.nextElementSibling; // .custom-file-label
    if (label) {
      label.classList.add('selected');
      label.textContent = fileName;
    }
  });

  // Drag & drop suave sobre el label (opcional)
  var wrapper = input.closest('.custom-file');
  if (wrapper) {
    ['dragover','dragenter'].forEach(evt =>
      wrapper.addEventListener(evt, e => { e.preventDefault(); wrapper.classList.add('border-warning'); })
    );
    ['dragleave','drop'].forEach(evt =>
      wrapper.addEventListener(evt, e => { e.preventDefault(); wrapper.classList.remove('border-warning'); })
    );
    wrapper.addEventListener('drop', function (e) {
      if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0]) {
        input.files = e.dataTransfer.files;
        input.dispatchEvent(new Event('change'));
      }
    });
  }
});
