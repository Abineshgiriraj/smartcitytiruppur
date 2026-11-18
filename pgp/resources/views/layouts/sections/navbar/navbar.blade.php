@php
$containerNav = $containerNav ?? 'container-fluid';
$navbarDetached = ($navbarDetached ?? '');

@endphp

<!-- Navbar -->
@if(isset($navbarDetached) && $navbarDetached == 'navbar-detached')
<nav class="layout-navbar {{$containerNav}} navbar navbar-expand-xl {{$navbarDetached}} align-items-center bg-navbar-theme"
    id="layout-navbar">
    @endif
    @if(isset($navbarDetached) && $navbarDetached == '')
    <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
        <div class="{{$containerNav}}">
            @endif

            <!--  Brand demo (display only for navbar-full and hide on below xl) -->
            @if(isset($navbarFull))
            <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
                <a href="{{url('/')}}" class="app-brand-link gap-2">
                    <span class="app-brand-logo demo">@include('_partials.macros', [
                        "width" => 25,
                        "withbg" =>
                        'var(--bs-primary)'
                        ])</span>
                    <span class="app-brand-text demo menu-text fw-bold">{{config('variables.templateName')}}</span>
                </a>
            </div>
            @endif

            <!-- ! Not required for layout-without-menu -->
            @if(!isset($navbarHideToggle))
            <div
                class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0{{ isset($menuHorizontal) ? ' d-xl-none ' : '' }} {{ isset($contentNavbar) ? ' d-xl-none ' : '' }}">
                <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                    <i class="bx bx-menu bx-sm"></i>
                </a>
            </div>
            @endif

            <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                <!-- Search -->
                <!-- <div class="navbar-nav align-items-center">
                    <div class="nav-item d-flex align-items-center">
                        <i class="bx bx-search fs-4 lh-0"></i>
                        <input type="text" class="form-control border-0 shadow-none ps-1 ps-sm-2"
                            placeholder="Search..." aria-label="Search...">
                    </div>
                </div> -->
                <!-- /Search -->
                <ul class="navbar-nav flex-row align-items-center ms-auto">

                    @if(session('user_type') == '670f5205cc28b44056')
                    <!-- Place this tag where you want the button to render. -->
                    <li class="nav-item lh-1 me-3">
                        <a>Issue Center : {{session('centerName')}}</a>

                    </li>

                    <li class="nav-item lh-1 me-3">
                    <a>Staff Name : {{session('staff_name')}}</a>

                    </li>

                    <li class="nav-item lh-1 me-3">
                        <a>A/C Year: {{session('acc_year')}}</a>
                    </li>
                    @elseif(session('user_type') == '670f52142fb6532995')
                    <li class="nav-item lh-1 me-3">
                        <a>Issue Center : {{session('access_area')}}</a>
                        <!-- - {{session('access_gate')}} -->
                    </li>

                    <li class="nav-item lh-1 me-3">
                    <a>Staff Name : {{session('staff_name')}}</a>

                    </li>

                    <li class="nav-item lh-1 me-3">
                        <a>A/C Year: {{session('acc_year')}}</a>
                    </li>
                    @endif
                    <li class="nav-item lh-1 me-3 d-none d-md-block">
                        <div class="tcmc-staff-details">
                            <strong>{{ session('staff_name') ?: 'Municipal Staff' }}</strong>
                            <small>{{ session('UserTypeName') ?: 'Staff Portal' }}</small>
                        </div>
                    </li>
                    <!-- User -->
                    <li class="nav-item navbar-dropdown dropdown-user dropdown">
                        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                            data-bs-toggle="dropdown" id="dropdownProfileToggle">
                            <div class="avatar avatar-online">
                                <span class="tcmc-avatar" aria-label="Staff profile">{{ strtoupper(substr(trim(session('staff_name') ?: 'S'), 0, 1)) }}</span>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" id="profileDropdown"
                            style="display: none; right: 0px;">
                            <li>
                                <a class="dropdown-item" href="javascript:void(0);">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar avatar-online">
                                                <span class="tcmc-avatar" aria-label="Staff profile">{{ strtoupper(substr(trim(session('staff_name') ?: 'S'), 0, 1)) }}</span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <span class="fw-medium d-block mb-3">{{session('staff_name')}}</span>
                                            <small class="text-muted">{{session('UserTypeName')}}</small>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <div class="dropdown-divider"></div>
                            </li>
                            <li>
                                <a class="dropdown-item" href="javascript:void(0);">
                                    <i class="bx bx-user me-2"></i>
                                    <span class="align-middle">My Profile</span>
                                </a>
                            </li>
                            <!-- <li>
                                <a class="dropdown-item" href="javascript:void(0);">
                                    <i class='bx bx-cog me-2'></i>
                                    <span class="align-middle">Settings</span>
                                </a>
                            </li> -->
                            <!-- <li>
                                <a class="dropdown-item" href="javascript:void(0);">
                                    <span class="d-flex align-items-center align-middle">
                                        <i class="flex-shrink-0 bx bx-credit-card me-2 pe-1"></i>
                                        <span class="flex-grow-1 align-middle">Billing</span>
                                        <span
                                            class="flex-shrink-0 badge badge-center rounded-pill bg-danger w-px-20 h-px-20">4</span>
                                    </span>
                                </a>
                            </li> -->
                            <li>
                                <div class="dropdown-divider"></div>
                            </li>
                            <li>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                </form>
                                <a class="dropdown-item" href="javascript:void(0);"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class='bx bx-power-off me-2'></i>
                                    <span class="align-middle">Log Out</span>
                                </a>

                                <!-- <a class="dropdown-item" href="javascript:void(0);">
                                    <i class='bx bx-power-off me-2'></i>
                                    <span class="align-middle">Log Out</span>
                                </a> -->
                            </li>
                        </ul>
                    </li>
                    <!--/ User -->
                </ul>
            </div>

            @if(!isset($navbarDetached))
        </div>
        @endif
    </nav>
    <!-- / Navbar -->

    <script>
    document.getElementById('dropdownProfileToggle').addEventListener('click', function() {
        var dropdownMenu = document.getElementById('profileDropdown');

        // Toggle the display of the dropdown
        if (dropdownMenu.style.display === 'none' || dropdownMenu.style.display === '') {
            dropdownMenu.style.display = 'block';
        } else {
            dropdownMenu.style.display = 'none';
        }
    });

    // Optional: Close the dropdown if clicked outside
    document.addEventListener('click', function(event) {
        var isClickInside = document.getElementById('dropdownProfileToggle').contains(event.target);
        var dropdownMenu = document.getElementById('profileDropdown');

        if (!isClickInside && dropdownMenu.style.display === 'block') {
            dropdownMenu.style.display = 'none';
        }
    });
    </script>