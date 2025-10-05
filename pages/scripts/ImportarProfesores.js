// fileLabelUpdate.js (genérico)
document.addEventListener('change', (e) => {
  const input = e.target.closest('.custom-file-input');
  if (!input) return;
  const label = input.nextElementSibling;
  if (label && label.classList.contains('custom-file-label')) {
    const name = input.files && input.files[0] ? input.files[0].name : 'Ningún archivo seleccionado';
    label.textContent = name;
    label.classList.add('selected');
  }
});
