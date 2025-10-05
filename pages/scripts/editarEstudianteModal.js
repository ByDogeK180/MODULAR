// ../scripts/editarEstudianteModal.js
(() => {
  if (window.__editarEstudianteModalLoaded) return;
  window.__editarEstudianteModalLoaded = true;

  /* ========== Toast con SweetAlert2 ========== */
  function toastSuccess(title = 'Cambios guardados') {
    if (!window.Swal) return;
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title,
      showConfirmButton: false,
      timer: 1800,
      timerProgressBar: true,
      background: '#1e293b',
      color: '#fff',
      customClass: { popup: 'swal2-toast border-0 shadow-lg' },
      didOpen: (el) => {
        el.addEventListener('mouseenter', Swal.stopTimer);
        el.addEventListener('mouseleave', Swal.resumeTimer);
      }
    });
  }

  /* ========== CSS para animación de la fila ========== */
  (function ensureRowFlashCSS() {
    const id = 'row-flash-css';
    if (document.getElementById(id)) return;
    const s = document.createElement('style');
    s.id = id;
    s.textContent = `
      @keyframes rowFlash {
        0%   { background: #fff7cc; }
        100% { background: transparent; }
      }
      #data-table-4 tbody tr.flash-update {
        animation: rowFlash 1200ms ease-out 1;
        will-change: background;
      }
    `;
    document.head.appendChild(s);
  })();

  let currentEditBtn = null;

  /* ========== Abrir modal y rellenar ========== */
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.btn-edit');
    if (!btn) return;

    currentEditBtn = btn;

    const form = document.getElementById('formEditarEstudiante');
    form.reset();
    form.elements['estudiante_id'].value = btn.dataset.id || '';
    document.getElementById('ed_nombre').value    = btn.dataset.nombre   || '';
    document.getElementById('ed_apellido').value  = btn.dataset.apellido || '';
    document.getElementById('ed_fecha_nac').value = btn.dataset.fecha    || '';
    document.getElementById('ed_grado').value     = btn.dataset.grado    || '';
    document.getElementById('ed_grupo').value     = btn.dataset.grupo    || '';

    // Cargar tutores (una sola vez)
    const sel = document.getElementById('ed_tutor_id');
    if (!sel.dataset.loaded) {
      try {
        const res = await fetch('../php/tutores_opciones.php', { credentials: 'same-origin' });
        const tutores = await res.json();
        sel.innerHTML =
          `<option value="">— Sin tutor —</option>` +
          tutores.map(t => `<option value="${t.tutor_id}">${t.nombre} ${t.apellido}</option>`).join('');
        sel.dataset.loaded = '1';
      } catch {
        sel.innerHTML = `<option value="">(no disponible)</option>`;
      }
    }
    sel.value = btn.dataset.tutorId || '';

    if (window.$) $('#modalEditarEstudiante').modal('show');
  });

  /* ========== Enviar edición ========== */
  const form = document.getElementById('formEditarEstudiante');
  if (form && !form.dataset.bound) {
    form.dataset.bound = '1';
    form.addEventListener('submit', onSubmitEdit);
  }

  async function onSubmitEdit(e) {
    e.preventDefault();
    const submitBtn = e.currentTarget.querySelector('button[type="submit"]');
    submitBtn.disabled = true;

    try {
      const fd = new FormData(e.currentTarget);
      const r  = await fetch('../php/editar_estudiante.php', {
        method: 'POST',
        body: fd,
        credentials: 'same-origin'
      });

      const raw = await r.text();
      let json;
      try { json = JSON.parse(raw); } 
      catch { throw new Error('Respuesta no es JSON: ' + raw); }

      const ok = !!(json && (json.ok === true || json.success === true));
      if (!r.ok || !ok) throw new Error(json?.msg || json?.message || 'Fallo en la actualización');
    } catch (err) {
      alert('No se pudo actualizar: ' + (err?.message || 'Error de red/JSON'));
      submitBtn.disabled = false;
      return;
    }

    // Cerrar modal
    if (window.$) $('#modalEditarEstudiante').modal('hide');

    // === Actualizar fila y animar (sin recargar, sin redraw de DT) ===
    const idEdit   = form.elements['estudiante_id'].value;
    const nombre   = document.getElementById('ed_nombre').value.trim();
    const apellido = document.getElementById('ed_apellido').value.trim();
    const fecha    = document.getElementById('ed_fecha_nac').value;
    const grado    = document.getElementById('ed_grado').value;
    const grupo    = document.getElementById('ed_grupo').value;
    const selTutor = document.getElementById('ed_tutor_id');
    const tutorId  = selTutor.value || '';
    const tutorNom = (selTutor.selectedOptions[0]?.text || '— Sin tutor —').trim();

    // 1) intentar con DataTables (si está presente)
    let rowNode = null;
    try {
      if (window.$ && $.fn && $.fn.dataTable && $('#data-table-4').length) {
        const dt = $('#data-table-4').DataTable();
        const rowIdx = dt.rows().eq(0).filter(idx => String(dt.cell(idx, 0).data()).trim() === String(idEdit));
        if (rowIdx.length) {
          rowNode = dt.row(rowIdx[0]).node();
          // Actualiza directamente el DOM de la fila (sin draw)
          const c = rowNode.children;
          if (c[1]) c[1].textContent = tutorId || '—';   // Tutor ID
          if (c[2]) c[2].textContent = tutorNom;         // Nombre Tutor
          if (c[3]) c[3].textContent = nombre;           // Nombre
          if (c[4]) c[4].textContent = apellido;         // Apellido
          if (c[5]) c[5].textContent = fecha;            // Fecha
          if (c[6]) c[6].textContent = grado;            // Grado
          if (c[7]) c[7].textContent = grupo;            // Grupo
        }
      }
    } catch (e) {
      console.warn('No se pudo actualizar vía DataTables', e);
    }

    // 2) fallback: buscar por DOM si DT no está o no encontró
    if (!rowNode) {
      rowNode = [...document.querySelectorAll('#data-table-4 tbody tr')]
        .find(tr => tr.cells[0] && tr.cells[0].textContent.trim() === String(idEdit));
      if (rowNode) {
        const c = rowNode.children;
        if (c[1]) c[1].textContent = tutorId || '—';
        if (c[2]) c[2].textContent = tutorNom;
        if (c[3]) c[3].textContent = nombre;
        if (c[4]) c[4].textContent = apellido;
        if (c[5]) c[5].textContent = fecha;
        if (c[6]) c[6].textContent = grado;
        if (c[7]) c[7].textContent = grupo;
      }
    }

    // 3) actualizar datasets del botón "Editar" dentro de la fila
    try {
      const btnInRow = rowNode?.querySelector('.btn-edit');
      if (btnInRow) {
        btnInRow.dataset.nombre   = nombre;
        btnInRow.dataset.apellido = apellido;
        btnInRow.dataset.fecha    = fecha;
        btnInRow.dataset.grado    = grado;
        btnInRow.dataset.grupo    = grupo;
        btnInRow.dataset.tutorId  = tutorId;
      }
    } catch {}

    // 4) ANIMAR (si encontramos la fila)
    if (rowNode) {
      rowNode.classList.remove('flash-update'); // por si acaso
      // Forzar reflow para reiniciar la animación si se repite rápido
      // eslint-disable-next-line no-unused-expressions
      rowNode.offsetHeight;
      rowNode.classList.add('flash-update');
      rowNode.addEventListener('animationend', () => {
        rowNode.classList.remove('flash-update');
      }, { once: true });
    }

    toastSuccess('Cambios guardados');
    submitBtn.disabled = false;
  }
})();
