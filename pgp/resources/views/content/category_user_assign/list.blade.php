@extends('layouts/contentNavbarLayout')

@section('title', 'Tables - Basic Tables')

<style>
/* btn style */
.btn-right {
    float: right;
}

</style>

@section('content')

<div class="row mb-3 page-hed">
    <div class="col-sm-6">
        <h4 class="">
            <span class="text-muted fw-light">Entry Form /</span>Category User Assign </h4>
    </div>
    <div class="col-sm-6">
        <button class="btn btn-primary btn-right" onclick="window.location.href='{{ url('/category_user_assign_add') }}'" type="button">
            <i class="i-Add"></i> Add New
        </button>
    </div>
</div>

<!-- Responsive Datatable -->
<div class="card">
    <div class="card-body">
        <div class="card-datatable table-responsive">
            <table class="dt-responsive table border-top" id="example">
                <thead>
                    <tr>
                        <th>School ID</th>
                        <th>Academic Year</th>
                        <th>Zone</th>
                        <th>District</th>
                        <th>Taluk</th>
                        <th>School</th>
                        <th>Category Type</th>
                        <th>Staff Name</th>
                        <th>Username</th>
                        <th>Password</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
    <td>SI-098</td>
    <td>2024-2025</td>
    <td>Zone 1</td>
    <td>Erode</td>
    <td>Gobi</td>
    <td>Govt Boys School, Erode</td>
    <td>Break Fast</td>
    <td>Surya</td>
    <td>username1</td>
    <td>!vnj@ED98df</td>
</tr>
<tr>
    <td>SI-123</td>
    <td>2024-2025</td>
    <td>Zone 5</td>
    <td>Chennai</td>
    <td>Chengalpattu</td>
    <td>Govt Boys School, Chennai</td>
    <td>Lunch</td>
    <td>Balu</td>
    <td>username2</td>
    <td>dfd@#sd#rfw3e</td>
</tr>
<tr>
    <td>SI-343</td>
    <td>2024-2025</td>
    <td>Zone 4</td>
    <td>Salem</td>
    <td>Salem-West</td>
    <td>Govt Boys School, Salem</td>
    <td>Check IN</td>
    <td>Siva</td>
    <td>username3</td>
    <td>!dw$ywe$54</td>
</tr>

                </tbody>
            </table>
        </div>
    </div>
</div>
<!--/ Responsive Datatable -->

<link rel="stylesheet" href="{{ asset('public/assets/css/bootstrap.min.css') }}" />
<link rel="stylesheet" href="{{ asset('public/assets/css/dataTables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('public/assets/css/responsive.bootstrap.min.css') }}" />
<link rel="stylesheet" href="{{ asset('public/assets/css/core.css') }}" />

<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('public/assets/js/dataTables.js') }}"></script>
<script src="{{ asset('public/assets/js/dataTables.bootstrap5.js') }}"></script>
<script src="{{ asset('public/assets/js/dataTables.responsive.js') }}"></script>
<script src="{{ asset('public/assets/js/responsive.bootstrap5.js') }}"></script>

<script type="text/javascript" class="init">
new DataTable('#example', {
    responsive: true
});
</script>

@endsection
