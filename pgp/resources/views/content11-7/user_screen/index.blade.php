@extends('layouts/contentNavbarLayout')

@section('title', 'Tables - User Screen Index')

@section('content')
<style>
/* btn style */
.btn-right {
    float: right;
}
</style>

<div class="row mb-3 page-hed">
    <div class="col-sm-6">
        <h4 class="">
            <span class="text-muted fw-light">Admin /</span>User Screen
        </h4>
    </div>
    <div class="col-sm-6">
        <button class="btn btn-primary btn-right" onclick="window.location.href='{{ route('userscreen.create') }}'"
            type="button">
            <i class="i-Add"></i> Add New
        </button>
    </div>
</div>

<!-- Responsive Datatable -->
<div class="card">
    <div class="card-body">
        <div class="card-datatable table-responsive">
            <table class="table table-bordered dt-responsive nowrap" id="usertype-table">
                <thead>
                    <tr>
                        <th><b>S.No</b></th>
                        <th><b>Main Screen</b></th>
                        <th><b>Screen Name</b></th>
                        <th><b>Order</b></th>
                        <th><b>Status</b></th>
                        <th><b>Actions</b></th>
                    </tr>
                </thead>
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


<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert2.js')}}"></script>
<!-- <script>
    $(document).ready(function () {
        $('#usertype-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('userscreen.data') }}',
                type: 'GET',
                error: function (xhr, error, code) {
                    console.log(xhr.responseText);
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'screen_name', name: 'screen_name' },
                { data: 'sub_screen_name', name: 'sub_screen_name' },
                { data: 'order_no', name: 'order_no' },
                {
                    data: 'is_active',
                    name: 'is_active',
                    render: function (data, type, row) {
                        return data == 1 ? '<span class="text-success">Active</span>' : '<span class="text-danger">Inactive</span>';
                    }
                },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            responsive: true
        });


        $(document).on('click', '.delete-btn', function () {
            var id = $(this).data('id'); // Get the record ID from the data-id attribute
            console.log("Deleting ID:", id); // Debugging: Check the ID value

            // Use SweetAlert2 for confirmation dialog
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
                    $.ajax({
                        url: '{{ route('userscreen.destroy', ':id') }}'.replace(':id', id), // Update URL dynamically
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' }, // Include CSRF token
                        success: function (response) {
                            console.log("Response:", response); // Debugging: Check the response
                            Swal.fire(
                                'Deleted!',
                                'Record has been deleted.',
                                'success'
                            );

                            // Refresh DataTable
                            $('#usertype-table').DataTable().ajax.reload();
                        },
                        error: function (xhr) {
                            console.log("Error:", xhr.responseText); // Debugging: Check the error response
                            Swal.fire(
                                'Failed!',
                                'Failed to delete the record.',
                                'error'
                            );
                        }
                    });
                }
            });
        });

    });

</script> -->


<script>
$(document).ready(function() {
    $('#usertype-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("userscreen.data") }}',

            type: 'GET',
            error: function(xhr, error, code) {
                console.log(xhr.responseText); // Check for any errors here
            }
        },
        columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },{
                data: 'main_screen_name',
                name: 'main_screen_name'
            },
            {
                data: 'screen_name',
                name: 'screen_name'
            },
            // { data: 'sub_screen_name', name: 'sub_screen_name' },
            {
                data: 'order_no',
                name: 'order_no'
            },
            {
                data: 'is_active',
                name: 'is_active',
                render: function(data, type, row) {
                    return data == 1 ? '<span class="text-success">Active</span>' :
                        '<span class="text-danger">Inactive</span>';
                }
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
        ],
        responsive: true
    });

    $(document).on('click', '.delete-btn', function() {
        var id = $(this).data('id'); // Get the record ID from the data-id attribute

        // Use SweetAlert2 for confirmation dialog
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
                $.ajax({
                    url: '{{ route("userscreen.destroy", ":id") }}'.replace(':id', id),

                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    }, // Include CSRF token
                    success: function(response) {
                        // Show success message with SweetAlert2
                        Swal.fire(
                            'Deleted!',
                            'Record has been deleted.',
                            'success'
                        );

                        // Refresh DataTable
                        $('#usertype-table').DataTable().ajax.reload();
                    },
                    error: function(xhr) {
                        // Show error message with SweetAlert2
                        Swal.fire(
                            'Failed!',
                            'Failed to delete the record.',
                            'error'
                        );
                    }
                });
            }
        });
    });
});
</script>


@endsection