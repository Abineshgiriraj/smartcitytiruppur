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
            <span class="text-muted fw-light">Entry Form /</span> Student Creation
        </h4>
    </div>
    <div class="col-sm-6">
        <button class="btn btn-primary btn-right" onclick="window.location.href='{{ url('/student_add') }}'" type="button">
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
                    <!-- <th></th> -->
                    <th>Student name</th>
                    <th>Father name</th>
                    <th>D.O.B</th>
                    <th>Standard</th>
                    <th>District</th>
                    <th>Zone</th>
                    <th>School</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <!-- <th></th> -->
                    <td>Dinesh</td>
                    <td>Tamil</td>
                    <td>12-12-2013</td>
                    <td>6</td>
                    <td>Chennai</td>
                    <td>Chennai</td>
                    <td>Government boys school</td>
                </tr>
                <tr>
                    <td>Jaya</td>
                    <td>balan</td>
                    <td>01-11-2012</td>
                    <td>7</td>
                    <td>Chennai</td>
                    <td>Chennai</td>
                    <td>Government girls school</td>
                </tr>
                <tr>
                    <td>Anusha</td>
                    <td>Jayakumar</td>
                    <td>10-12-2010</td>
                    <td>9</td>
                    <td>Chennai</td>
                    <td>Chennai</td>
                    <td>Government boys school</td>
                </tr>
                <tr>
                    <td>karthick</td>
                    <td>dinesh</td>
                    <td>12-12-2010</td>
                    <td>9</td>
                    <td>Chennai</td>
                    <td>Chennai</td>
                    <td>Government boys school</td>
                </tr>
                <tr>
                  <td>deepak</td>
                    <td>Thana</td>
                    <td>12-12-2011</td>
                    <td>8</td>
                    <td>Chennai</td>
                    <td>Chennai</td>
                    <td>Government boys school</td>
                </tr>
                <tr>
                   <td>Divya</td>
                    <td>Annamalai</td>
                    <td>12-12-2010</td>
                    <td>9</td>
                    <td>Chennai</td>
                    <td>Chennai</td>
                    <td>Government boys school</td>
                </tr>
                <tr>
                    <td>Prabu</td>
                    <td>Sivaraj</td>
                    <td>12-12-2011</td>
                    <td>8</td>
                    <td>Chennai</td>
                    <td>Chennai</td>
                    <td>Government boys school</td>
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