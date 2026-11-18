@extends('layouts/contentNavbarLayout')

@section('title', 'Tables - vehicle registration')

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
<div class="row mb-3 page-hed">
    <div class="col-sm-6">
        <h4 class="">
            <span class="text-muted fw-light">User Firms /</span>Port Vehicle Registration
        </h4>
    </div>
    <div class="col-sm-6">
        <button class="btn btn-primary btn-right "
            onclick="window.location.href='{{ route('port_vehicle_registration_add') }}'" type="button">
            <i class="i-Add"></i> Add New
        </button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row">

            <div class="col-2">
                <label class="form-label">From Date</label>
                <input type="date" name="from_date" class="form-control">
            </div>
            <div class="col-2">
                <label class="form-label">To Date</label>
                <input type="date" name="to_date" class="form-control">
            </div>
            <div class="col-3">
                <label class="form-label" style="width: 250px;">Vehicle NO</label>
                <input type="text" name="vehicle_no" class="form-control">
            </div>
            <div class="col-3 mt-4">
                <button type="button" class="btn btn-primary">GO</button>
            </div>

        </div>
    </div>
    <div class="card-body">
        <div class="card-datatable table-responsive">
            <table id="port_vehicle_registration" class="table table-bordered">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Vehicle Reg. No</th>
                        <th>Vehicle Type</th>
                        <th>Fitness Valid Upto</th>
                        <th>Insurance Valid Upto</th>
                        <th>PUC Valid Upto</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be populated by DataTables via AJAX -->
                </tbody>
            </table>

        </div>
    </div>
</div>

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


<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>
<script>
    $(document).ready(function () {
        var table = $('#port_vehicle_registration').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('port_vehicle_registration.index') }}",
                data: function (d) {
                    d.from_date = $('input[name="from_date"]').val();
                    d.to_date = $('input[name="to_date"]').val();
                    d.vehicle_no = $('input[name="vehicle_no"]').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'vech_reg_no', name: 'vech_reg_no' },
                { data: 'vehicle_type', name: 'vehicle_type' },
                { data: 'fitness', name: 'fitness', defaultContent: 'N/A' },
                { data: 'insurance', name: 'insurance', defaultContent: 'N/A' },
                { data: 'puc', name: 'puc', defaultContent: 'N/A' },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        // Filter function
        $('.btn-primary').on('click', function () {
            table.draw();
        });
    });

    $(document).on('click', '.delete-btn', function () {
        var vehicleId = $(this).data('id'); // Get the vehicle ID from the data attribute

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
                    url: '{{ route('port_vehicle_registration.destroy', ':id') }}'.replace(':id', vehicleId), // Replace with the vehicle ID
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
                            window.location.href = "{{ route('port_vehicle_registration.index')}}";
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