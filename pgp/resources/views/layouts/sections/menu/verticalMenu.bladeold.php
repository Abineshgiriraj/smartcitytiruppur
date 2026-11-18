<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <!-- ! Hide app brand if navbar-full -->
    <div class="app-brand demo">
        <a href="{{ url('/dashboard') }}" class="app-brand-link">
            <!--<span class="app-brand-logo demo">-->
            <!--  @include('_partials.macros', ["width" => 25, "withbg" => 'var(--bs-primary)'])-->
            <!--</span>-->
            <img src="{{ asset('public/assets/img/favicon/logo.jpeg') }}" class="hed-logo">
            <span class="app-brand-text demo menu-text ms-2 hed-logo-tex">VOC HEP</span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>
    <div class="menu-inner-shadow"></div>
    <ul class="menu-inner py-1">
        @foreach ($menuData[0]->menu as $menu)
        {{-- menu headers --}}
        @if (isset($menu->menuHeader))
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">{{ __($menu->menuHeader) }}</span>
        </li>
        @else
        {{-- active menu method --}}
        @php
        $activeClass = '';
        $currentRouteName = Route::currentRouteName();

        if ($currentRouteName === $menu->slug) {
        $activeClass = 'active';
        } elseif (isset($menu->submenu)) {
        if (is_array($menu->slug)) {
        foreach ($menu->slug as $slug) {
        if (str_starts_with($currentRouteName, $slug)) {
        $activeClass = 'active open';
        break;
        }
        }
        } else {
        if (str_starts_with($currentRouteName, $menu->slug)) {
        $activeClass = 'active open';
        }
        }
        }
        @endphp

        {{-- main menu --}}
        <li class="menu-item {{ $activeClass }}">
            <a href="{{ isset($menu->url) ? url($menu->url) : 'javascript:void(0);' }}"
                class="{{ isset($menu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }} menu_main"
                @if(isset($menu->target) && !empty($menu->target)) target="_blank" @endif>

                @isset($menu->icon)
                <i class="{{ $menu->icon }}"></i>
                @endisset

                <div>{{ isset($menu->name) ? __($menu->name) : '' }}</div>

                @isset($menu->badge)
                <div class="badge bg-{{ $menu->badge[0] }} rounded-pill ms-auto">{{ $menu->badge[1] }}</div>
                @endisset

            </a>
            {{-- submenu --}}
            @isset($menu->submenu)
            @include('layouts.sections.menu.submenu', ['menu' => $menu->submenu])
            @endisset
        </li>

        @endif
        @endforeach
    </ul>
</aside>