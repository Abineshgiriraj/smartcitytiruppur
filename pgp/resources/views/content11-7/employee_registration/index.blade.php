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
            <span class="text-muted fw-light">Port Employees/</span> Employee Registration
        </h4>
    </div>
    <div class="col-sm-6">
        <button class="btn btn-primary btn-right" onclick="window.location='{{ route('employee_registration_add') }}'"
            type="button">
            <i class="i-Add"></i> Add New
        </button>
    </div>
</div>

<!-- Responsive Datatable -->
<div class="card">
    <div class="card-body">
        <div class="row mb-3">
        <div class="col-2">
                <label class="form-label">From Date</label>
                <input type="date" class="form-control" id="from_date" name="from_date">
            </div>
            <div class="col-2">
                <label class="form-label">To Date</label>
                <input type="date" class="form-control" id="to_date" name="to_date">
            </div>
            <div class="col-sm-2">
            <label class="form-label">Designation</label>
                <select class="form-select" name="designation" id="designation">
                    <option value="">Select Designation</option>
                    @foreach ($desgination_list as $designation)
                        <option value="{{$designation->unique_id}}">{{$designation->designation}}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2">
            <label class="form-label">Emp. ID</label>
                <input type="text" class="form-control" id="id_no" name="id_no" placeholder="EMP. ID" />
            </div>
            <div class="col-sm-2">
            <label class="form-label">Emp. Name</label>
                <input type="text" class="form-control" id="emp_name" name="emp_name" placeholder="EMP. Name" />
            </div>
            <div class="col-sm-2 mt-4">
                <button id="openTableButton" class="btn btn-primary">GO</button>

            </div>
        </div>
        <div class="card-datatable table-responsive">
            <table class="table table-bordered dt-responsive nowrap" id="employee_designation">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Reg.No</th>
                        <th>Employee Id</th>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Aadhaar No</th>
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
                url: "{{ route('employee_registration') }}",
                data: function (d) {
                    // Update to correctly get designation value
                    d.from_date = $('#from_date').val(); // Use select ID
                    // alert(d.from_date);
                    d.to_date = $('#to_date').val(); // Use select ID
                    // alert(d.to_date);
                    d.designation = $('#designation').val(); // Use select ID
                    d.id_no = $('#id_no').val(); // Get value from ID input
                    d.emp_name = $('#emp_name').val(); // Get value from name input
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'register_no', name: 'register_no' },
                { data: 'employee_id', name: 'employee_id' },
                { data: 'fullname', name: 'fullname' },
                { data: 'designations', name: 'designations' },
                { data: 'aadhar_no', name: 'aadhar_no' },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        // Filter function
        $('#openTableButton').on('click', function () {
            table.draw(); // Trigger DataTable to redraw with new filter values
        });
    });

    // $(document).ready(function () {
    //     var table = $('#employee_designation').DataTable({
    //         processing: true,
    //         serverSide: true,
    //         // ajax: "{{ route('employee_registration') }}", // Correct AJAX URL
    //         ajax: {
    //             url: "{{ route('employee_registration') }}",
    //             data: function (d) {
    //                 d.designation = $('input[name="designation"]').val();
    //                 d.id_no = $('input[name="id_no"]').val();
    //             }
    //         },
    //         columns: [
    //             { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
    //             { data: 'register_no', name: 'register_no' },
    //             { data: 'employee_id', name: 'employee_id' },
    //             { data: 'fullname', name: 'fullname' },
    //             { data: 'designations', name: 'designations' },
    //             { data: 'aadhar_no', name: 'aadhar_no' },
    //             {
    //                 data: 'action',
    //                 name: 'action',
    //                 orderable: false,
    //                 searchable: false
    //             }
    //         ]
    //     });

    //     //filter function
    //     $('.btn-primary').on('click', function () {
    //         table.draw();
    //     });

    // });

    $(document).on('click', '.delete-btn', function () {
        var employeeRegId = $(this).data('id'); // Get the vehicle ID from the data attribute

        // Show SweetAlert confirmation dialog
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                var csrfToken = $('meta[name="csrf-token"]').attr('content'); // Get CSRF token

                // Perform AJAX request
                $.ajax({
                    url: '{{ route('employee_registration.destroy', ':id') }}'.replace(':id', employeeRegId), // Replace with the vehicle ID
                    method: 'DELETE', // Use DELETE method
                    data: {
                        _token: csrfToken // Include CSRF token
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire(
                                'Deleted!',
                                'The vehicle has been deleted successfully.',
                                'success'
                            );
                            window.location.href = "{{ route('employee_registration')}}";
                            // Reload the DataTable or remove the row from the DataTable
                            // $('#vehicle-table').DataTable().ajax.reload(); // Reload the DataTable to reflect changes
                        } else {
                            Swal.fire(
                                'Error!',
                                response.message || 'Failed to delete the vehicle.',
                                'error'
                            );
                        }
                    },
                    error: function (xhr) {
                        Swal.fire(
                            'Error!',
                            'An error occurred while deleting the vehicle: ' + xhr.responseText,
                            'error'
                        );
                    }
                });
            }
        });
    });

</script>


@endsection