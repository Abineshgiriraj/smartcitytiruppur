{{-- Minimal, local Smart City Tiruppur UI assets. Legacy compiled vendor theme is not present in repository. --}}
@php
  $pgpAssetPrefix = str_ends_with(rtrim(request()->getBaseUrl(), '/'), '/public') ? '' : 'public/';
@endphp
<link rel="stylesheet" href="{{ asset($pgpAssetPrefix . 'assets/onlinecdn/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset($pgpAssetPrefix . 'assets/fontawesome-free-6.6.0-web/css/all.min.css') }}">
<link rel="stylesheet" href="{{ asset($pgpAssetPrefix . 'assets/css/tiruppur-admin.css') }}">
@yield('vendor-style')
@yield('page-style')
