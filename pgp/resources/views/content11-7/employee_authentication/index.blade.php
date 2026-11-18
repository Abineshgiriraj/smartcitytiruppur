@extends('layouts/contentNavbarLayout')

@section('title', 'Tables - Academic Year Creation')

@section('content')

<style>
    /* btn style */
    .btn-right {
        float: right;
    }

    .btn-medium-custom {
        padding: 6px 12px;
        font-size: 14px;
    }

    .btn-small {
        /* width: auto; */
        white-space: nowrap;
        padding: 0;
        /* Remove padding */
        margin: 0;
        display: inline-flex;
        /* Align content properly */
        align-items: center;
        width: 55px;
        /* Center icons and text vertically */
        /* Remove margin */
    }

    .lab_add {
        white-space: nowrap;
        font-size: 10px;
    }
</style>

@php

    $desgination_list = get_designation();

@endphp


<div class="row mb-3 page-hed">
    <div class="col-sm-6">
        <h4 class="">
            <span class="text-muted fw-light">Approvals/</span> Port Employee
        </h4>
    </div>
   
</div>
<div class="card">
    <div class="card-body">
        <div class="row">

            <div class="col-2">
                <label class="form-label">From Date</label>
                <input type="date" id="from_date" class="form-control">
            </div>
            <div class="col-2">
                <label class="form-label">To Date</label>
                <input type="date" id="to_date" class="form-control">
            </div>
            <div class="col-2">
                <label class="form-label">Reg No</label>
                <input type="text" class="form-control" id="reg_no">
            </div>
            <div class="col-2">
                <label class="form-label">Name</label>
                <input type="text" class="form-control" id="name">
            </div>

            <div class="col-2"><label for="example-select" class="form-label">Status Filter</label>
                <select name="status" id="status" class="select2 form-control" required>
                    <option value="0">Pending</option>
                    <option value="1">Approved</option>
                    <option value="2">Reject</option>
                </select>
            </div>
            <div class="col-md-2 align-self-center mt-3">
                <div class="page-title-right"><button class="btn btn-primary" id="filterBtn">GO</button></div>
            </div>
        </div>
    </div>
</div>
<!-- Responsive Datatable -->
<div class="card">
    <div class="card-body">
        <div class="card-datatable table-responsive">
            <table class="table table-bordered dt-responsive nowrap" id="employee_designation">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Register Number</th>
                        <th>Register Date</th>
                        <th>Employee Id</th>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Aadhaar No</th>
                        <th>Status</th>
                        <th>Actions</th>

                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
<!--/ Responsive Datatable -->

<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>


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


<script>
$(document).ready(function () {
    var table = $('#employee_designation').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('employee_authentication') }}",
            data: function (d) {
                d.from_date = $('#from_date').val(); // Get value from From Date input
                d.to_date = $('#to_date').val(); // Get value from To Date input
                d.reg_no = $('#reg_no').val(); // Get value from Reg No input
                d.name = $('#name').val(); // Get value from Name input
                d.status = $('#status').val(); // Get value from Status select
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'register_no', name: 'register_no' },
            { data: 'reg_date', name: 'reg_date' },
            { data: 'employee_id', name: 'employee_id' },
            { data: 'fullname', name: 'fullname' },
            { data: 'designations', name: 'designations' },
            { data: 'aadhar_no', name: 'aadhar_no' },
            {
                data: 'status', // Use the status column
                name: 'status',
                render: function(data, type, row) {
                    return data; // Return the HTML for the status directly
                }
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
        ]
    });

    // Filter function
    $('#filterBtn').on('click', function () {
        table.draw(); // Trigger DataTable to redraw with new filter values
    });
});
</script>
<script>
// $(document).ready(function () {
//     var table = $('#employee_designation').DataTable({
//         processing: true,
//         serverSide: true,
//         ajax: {
//             url: "{{ route('employee_authentication') }}",
//             data: function (d) {
//                 d.designation = $('#designation').val();
//                 d.id_no = $('#id_no').val();
//             }
//         },
//         columns: [
//             { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
//             { data: 'register_no', name: 'register_no' },
//             { data: 'reg_date', name: 'reg_date' },
//             { data: 'employee_id', name: 'employee_id' },
//             { data: 'fullname', name: 'fullname' },
//             { data: 'designations', name: 'designations' },
//             { data: 'aadhar_no', name: 'aadhar_no' },
//             {
//                 data: 'status', // Use the status column
//                 name: 'status',
//                 render: function(data, type, row) {
//                     return data; // Return the HTML for the status directly
//                 }
//             },
//             {
//                 data: 'action',
//                 name: 'action',
//                 orderable: false,
//                 searchable: false
//             }
//         ]
//     });

//     // Filter function
//     $('#openTableButton').on('click', function () {
//         table.draw(); // Trigger DataTable to redraw with new filter values
//     });
// });

// $(document).ready(function () {
//     var table = $('#employee_designation').DataTable({
//         processing: true,
//         serverSide: true,
//         ajax: {
//             url: "{{ route('employee_authentication') }}",
//             data: function (d) {
//                 // Update to correctly get designation value
//                 d.designation = $('#designation').val(); // Use select ID
//                 d.id_no = $('#id_no').val(); // Get value from ID input
//             }
//         },
//         columns: [
//             { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
//             { data: 'register_no', name: 'register_no' },
//             { data: 'employee_id', name: 'employee_id' },
//             { data: 'fullname', name: 'fullname' },
//             { data: 'designations', name: 'designations' },
//             { data: 'aadhar_no', name: 'aadhar_no' },
//             { data: 'status', name: 'status' },
//             {
//                 data: 'action',
//                 name: 'action',
//                 orderable: false,
//                 searchable: false
//             }
//         ]
//     });

//     // Filter function
//     $('#openTableButton').on('click', function () {
//         table.draw(); // Trigger DataTable to redraw with new filter values
//     });
// });

</script>


@endsection