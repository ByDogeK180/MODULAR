// pages/scripts/perfil-hijo.js
(function () {
  function hidePreloader() {
    var p = document.getElementById('preloader-wrap');
    if (p) p.style.display = 'none';
  }
  document.addEventListener('DOMContentLoaded', hidePreloader);
  window.addEventListener('load', hidePreloader);
  setTimeout(hidePreloader, 2000);
})();
