// 1) Textos en español para TODAS las DataTables de esta página
  $.extend(true, $.fn.dataTable.defaults, {
    language: {
      search: "Buscar:",
      searchPlaceholder: "Buscar profesor...",   // <-- placeholder
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "No se encontraron resultados",
      info: "Mostrando _START_ a _END_ de _TOTAL_",
      infoEmpty: "Sin registros",
      infoFiltered: "(filtrado de _MAX_)",
      paginate: {
        first: "Primero",
        last: "Último",
        next: "Siguiente",
        previous: "Anterior"
      }
    }
  });

  // 2) (Por si tu inicialización del tema no respeta searchPlaceholder)
  //    Ajusta el placeholder cuando la tabla ya está creada
  $(document).on('init.dt', function (e, settings) {
    const $wrapper = $(settings.nTableWrapper);
    $wrapper.find('div.dataTables_filter input[type=search]')
      .attr('placeholder', 'Buscar profesor...');
  });