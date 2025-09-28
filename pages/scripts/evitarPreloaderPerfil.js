(function () {
      function hidePreloader() {
        var p = document.getElementById('preloader-wrap');
        if (p) p.style.display = 'none';
      }
      window.addEventListener('load', hidePreloader);
      setTimeout(hidePreloader, 2000);
    })();