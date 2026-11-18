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
        <button class="btn btn-primary btn-right" onclick="window.location='{{ route('student.create') }}'"
            type="button">
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
                        <th>Student name</th>
                        <th>Father name</th>
                        <th>D.O.B</th>
                        <th>Standard</th>
                        <th>District</th>
                        <th>Taluk</th>
                        <th>School</th>
                    </tr>
                </thead>
                <tbody>

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
// new DataTable('#example', {
//     responsive: true
// });


$(document).ready(function() {

    var table = $('#example').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('student.index') }}",
        columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'student_name',
                name: 'student_name'
            },
            {
                data: 'father_name',
                name: 'father_name'
            },
            {
                data: 'stu_dob',
                name: 'stu_dob'
            },
            {
                data: 'cur_std',
                name: 'cur_std'
            },
            {
                data: 'schl_district',
                name: 'schl_district'
            },
            {
                data: 'schl_block',
                name: 'schl_block'
            },
            {
                data: 'schl_name',
                name: 'schl_name',

                orderable: false,
                searchable: false
            },
        ]
    });
});
</script>

<!-- </div> -->

@endsection