(function () {
  'use strict';
  var body = document.body;
  var toggle = document.getElementById('adminMenuToggle');
  var overlay = document.getElementById('adminOverlay');
  function closeNav() {
    body.classList.remove('admin-nav-open');
    if (toggle) toggle.setAttribute('aria-expanded', 'false');
  }
  if (toggle) {
    toggle.addEventListener('click', function () {
      var open = body.classList.toggle('admin-nav-open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }
  if (overlay) overlay.addEventListener('click', closeNav);
  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') closeNav();
  });
})();
