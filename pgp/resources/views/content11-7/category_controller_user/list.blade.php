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
            <span class="text-muted fw-light">Entry Form /</span>Category Controller User</h4>
    </div>
    <div class="col-sm-6">
        <button class="btn btn-primary btn-right" onclick="window.location.href='{{ url('/category_controller_user_add') }}'" type="button">
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
                        <th>S.No</th>
                        <th>Academic Year</th>
                        <th>Zone</th>
                        <th>District</th>
                        <th>Taluk</th>
                        <th>School</th>
                        <th>Date & Time</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Category Type</th>
                        <th>Username</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>2024-2025</td>
                        <td>Zone 1</td>
                        <td>Chennai</td>
                        <td>Chengalpattu</td>
                        <td>Govt School</td>
                        <td>09/20/2024 & 09:00</td>
                        <td>09:00</td>
                        <td>10:00</td>
                        <td>Break Fast</td>
                        <td>user1</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>2024-2025</td>
                        <td>Zone 2</td>
                        <td>Salem</td>
                        <td>Omalur</td>
                        <td>Private School</td>
                        <td>09/21/2024 & 10:00</td>
                        <td>10:00</td>
                        <td>11:00</td>
                        <td>Check In</td>
                        <td>user2</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>2024-2025</td>
                        <td>Zone 3</td>
                        <td>Erode</td>
                        <td>Perundurai</td>
                        <td>High School</td>
                        <td>09/22/2024 & 11:00</td>
                        <td>12:00</td>
                        <td>01:00</td>
                        <td>Lunch</td>
                        <td>user3</td>
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

<!-- </div> -->

@endsection