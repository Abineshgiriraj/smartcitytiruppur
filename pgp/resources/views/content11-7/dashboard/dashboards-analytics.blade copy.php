@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('public/assets/vendor/libs/apex-charts/apex-charts.css') }}">
@endsection

@section('vendor-script')
<script src="{{ asset('public/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
@endsection

@section('page-script')
<script src="{{ asset('public/assets/js/dashboards-analytics.js') }}"></script>
@endsection

@section('content')


@endsection

<!-- <script>
const districtMap = {
    '6b1c3a9f5e7d4028': 'Chennai',
    '5c9a2f7d3b4e1068': 'Salem',
    '0a5c9d4f6b7e8013': 'Erode'
};

const zoneMap = {
    '4e2d6f9b7a1c3058': 'Madhavaram',
    '8b7e1c3f5d9a6042': 'Tondiarpet',
    '2a8c4b6d9e1f3057': 'Royapuram'
};
const talukMap = {
    '9e3d1b5f7a4c6028': 'Taluk 1',
    '1b6f9a4d3c2e7058': 'Taluk 2'
};
</script> -->