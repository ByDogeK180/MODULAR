(function ($) {
  'use strict';
  // Delegado: funciona aunque el input esté dentro de un modal
  $(document).on('change', '.custom-file-input', function () {
    const fileName = this.files && this.files.length
      ? this.files[0].name
      : 'Ningún archivo seleccionado';
    $(this).next('.custom-file-label').addClass('selected').text(fileName);
  });
})(jQuery);