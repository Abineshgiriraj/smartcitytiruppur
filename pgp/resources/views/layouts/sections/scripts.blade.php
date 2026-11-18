<script src="{{ asset((str_ends_with(rtrim(request()->getBaseUrl(), '/'), '/public') ? '' : 'public/') . 'assets/onlinecdn/jquery.min.js') }}"></script>
@yield('vendor-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('#layout-menu .menu-toggle').forEach(function (link) {
    link.addEventListener('click', function (event) {
      event.preventDefault();
      const submenu = link.nextElementSibling;
      if (submenu && submenu.classList.contains('menu-sub')) {
        const wasOpen = submenu.style.display === 'block';
        submenu.style.display = wasOpen ? 'none' : 'block';
        link.setAttribute('aria-expanded', String(!wasOpen));
      }
    });
  });
  document.querySelectorAll('.layout-menu-toggle a, .layout-overlay').forEach(function (toggle) {
    toggle.addEventListener('click', function (event) {
      event.preventDefault();
      document.body.classList.toggle('tcmc-menu-open');
    });
  });
});
</script>
@stack('pricing-script')
@yield('page-script')
