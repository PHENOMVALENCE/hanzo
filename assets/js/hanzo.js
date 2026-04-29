(function () {
  'use strict';
  document.addEventListener('DOMContentLoaded', function () {
    var sp = document.getElementById('spinner');
    if (sp) {
      sp.classList.remove('show');
      setTimeout(function () { sp.style.display = 'none'; }, 200);
    }
  });
})();
