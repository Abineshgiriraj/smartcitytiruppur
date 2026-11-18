<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme collapsible-sidebar">

    <div class="app-brand demo logo-container">
        <a href="{{ url('/dashboard') }}" class="app-brand-link d-flex align-items-center">
            <!-- Small icon logo -->
            <span class="logo-icon tcmc-logo-symbol" aria-hidden="true">T</span>

            <!-- Full text logo -->
            <span class="logo-full tcmc-brand-name">Tiruppur <small>City Corporation</small></span>
        </a>

       
    </div>

    <div class="menu-inner-shadow"></div>

    @php
        $user_type_name = session('user_type');
        $session_main_screens = session('main_screens', []);
        $screens = session('screens', []);
        $main_screens = user_main_screen();
    @endphp

    

    <ul class="menu-inner">
        {{-- Static Dashboard Link --}}
        <li class="menu-item {{ request()->is('dashboard') ? 'active' : '' }}">
            <a href="{{ url('/dashboard') }}" class="menu-link menu_main" title="Dashboard">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div>Dashboard</div>
            </a>
        </li>

        {{-- Dynamic Menu Items --}}
        @foreach ($main_screens as $main_value)
            @if (in_array($main_value->unique_id, $session_main_screens))
                @php
                    $section_unique_id = $main_value->unique_id;
                    $menu_main_name = $main_value->screen_name;
                    $menu_main_icon = $main_value->icon;
                    $screen_sections = user_screen_names($section_unique_id);
                    $has_submenu = !empty($screen_sections);
                @endphp

                <li class="menu-item">
                    <a href="javascript:void(0);" class="menu-link {{ $has_submenu ? 'menu-toggle' : '' }} menu_main"
                       title="{{ __($menu_main_name) }}">
                        @if (!empty($menu_main_icon))
                            <i class="menu-icon {{ $menu_main_icon }}"></i>
                        @endif
                        <div>{{ __($menu_main_name) }}</div>
                    </a>

                    @if ($has_submenu)
                        <ul class="menu-sub" style="display: none;">
                            @foreach ($screen_sections as $submenu)
                                @if (in_array($submenu->unique_id, $screens))
                                    <li class="menu-item">
                                        <a href="{{ url($submenu->folder_name ?? '#') }}" class="menu-link"
                                           title="{{ __($submenu->screen_name) }}">
                                            <div>{{ __($submenu->screen_name) }}</div>
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endif
        @endforeach
    </ul>
</aside>

<script>
    // Submenu toggle
    document.querySelectorAll('.menu-toggle').forEach(toggle => {
        toggle.addEventListener('click', function () {
            const submenu = this.nextElementSibling;
            if (submenu && submenu.classList.contains('menu-sub')) {
                submenu.style.display = submenu.style.display === 'none' || submenu.style.display === '' ? 'block' : 'none';
            }
        });
    });

    // Sidebar toggle button
    const sidebar = document.getElementById('layout-menu');
    const toggleBtn = document.getElementById('sidebar-toggle');

    // Toggle collapse/expand
    if (toggleBtn) toggleBtn.addEventListener('click', function () {
        sidebar.classList.toggle('collapsed');
        if (sidebar.classList.contains('collapsed')) {
            localStorage.setItem('sidebarState', 'collapsed');
        } else {
            localStorage.setItem('sidebarState', 'expanded');
        }
    });

    // Restore state on page load
    if (localStorage.getItem('sidebarState') === 'collapsed') {
        sidebar.classList.add('collapsed');
    }
</script>
