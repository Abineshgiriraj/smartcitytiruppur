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
        <h4>
            <span class="text-muted fw-light">Entry Form /</span> Staff Creation
        </h4>
    </div>
    <div class="col-sm-6">
        <button class="btn btn-primary btn-right" onclick="window.location.href='{{ route('staff.create') }}'"
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
                        <th>S NO</th>
                        <th>Emp name</th>
                        <th>Emp id</th>
                        <th>Designation</th>
                        <th>District</th>
                        <th>Zone</th>
                        <th>School</th>
                        <th>Date of Join</th>
                        <th>Mobile number</th>
                        <th>Actions</th> <!-- Add Actions Column -->
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!--/ Responsive Datatable -->

<!-- Include necessary CSS files -->
<link rel="stylesheet" href="{{ asset('public/assets/css/bootstrap.min.css') }}" />
<link rel="stylesheet" href="{{ asset('public/assets/css/dataTables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('public/assets/css/responsive.bootstrap.min.css') }}" />
<link rel="stylesheet" href="{{ asset('public/assets/css/core.css') }}" />

<!-- Include necessary JS files -->
<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('public/assets/js/dataTables.js') }}"></script>
<script src="{{ asset('public/assets/js/dataTables.bootstrap5.js') }}"></script>
<script src="{{ asset('public/assets/js/dataTables.responsive.js') }}"></script>
<script src="{{ asset('public/assets/js/responsive.bootstrap5.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>

<script type="text/javascript">
$(document).ready(function() {
    $('#example').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('staff.index') }}",
        columns: [{

                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
            {
                data: 'emp_name',
                name: 'emp_name'
            },
            {
                data: 'emp_id',
                name: 'emp_id'
            },
            {
                data: 'designation',
                name: 'designation',

            },
            {
                data: 'district',
                name: 'district',

            },
            {
                data: 'zone',
                name: 'zone',
                // Default to data if no match
            },

            {
                data: 'school',
                name: 'school',

            },
            // { data: 'school', name: 'school' },
            {
                data: 'doj',
                name: 'doj'
            },
            {
                data: 'emp_contact',
                name: 'emp_contact'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
        ]
    });
});

$(document).ready(function() {
    // Handle delete button click
    $(document).on('click', '.delete_btn', function() {
        var url = $(this).data('url');

        // Use SweetAlert to confirm deletion
        Swal.fire({
            title: 'Are you sure?',
            text: 'You won’t be able to revert this!',
            icon: 'warning',
            // showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}' // Ensure CSRF token is included
                    },
                    success: function(response) {

                        Swal.fire({
                            title: 'The staff has been deleted!',
                            text: response
                                .success, // Use the success message from the server
                            icon: 'success',
                            confirmButtonText: 'OK',
                            timer: 1000,
                            showConfirmButton: false
                        }).then(() => {
                            // Optionally, reload the DataTable or page
                            location
                                .reload(); // Reload the page after success
                        });
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText); // Log error for debugging
                        Swal.fire(
                            'Error!',
                            xhr.responseJSON && xhr.responseJSON.error ? xhr
                            .responseJSON.error : 'Something went wrong!',
                            'error'
                        );
                    }
                });
            }
        });
    });
});

// Handle delete button click
</script>

@endsection