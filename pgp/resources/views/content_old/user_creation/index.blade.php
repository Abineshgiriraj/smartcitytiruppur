@extends('layouts/contentNavbarLayout')

@section('title', 'Tables - User Type Creation')

@section('content')
    <style>
        .btn-right {
            float: right;
        }
    </style>

    <div class="row mb-3 page-hed">
        <div class="col-sm-6">
            <h4 class="">
                <span class="text-muted fw-light">Admin /</span> User Creation
            </h4>
        </div>
        <div class="col-sm-6">
            <button class="btn btn-primary btn-right" onclick="window.location.href='{{ route("usercreation.create") }}'"
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
                            <th><b>Staff Name</b></th>
                            <th><b>User Name</b></th>
                            <th><b>Mobile No</b></th>
                            <th><b>User Type</b></th>
                            <th><b>Status</b></th>
                            <th><b>Actions</b></th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('page-script')
    <script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
    <script src="{{ asset('public/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/dataTables.js') }}"></script>
    <script src="{{ asset('public/assets/js/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('public/assets/js/dataTables.responsive.js') }}"></script>
    <script src="{{ asset('public/assets/js/responsive.bootstrap5.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            $('#usertype-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("usercreation.data") }}',
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'staff_name',
                    name: 'staff_name'
                },
                {
                    data: 'user_name',
                    name: 'user_name'
                },
                {
                    data: 'mobile_number',
                    name: 'mobile_number'
                },
                {
                    data: 'user_type',
                    name: 'user_type'
                },
                {
                    data: 'status',
                    name: 'status',
                    render: function (data) {
                        return data ? 'Active' : 'Inactive';
                    }
                },
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false
                }
                ]
            });

            //handle edit button
            $(document).on('click', '.edit', function () {
                var url = $(this).data('url');
                window.location.href = url;
            });

            $(document).on('click', '.delete-btn', function () {
                var id = $(this).data('id');

                console.log('ID to delete:', id); // Add this for debugging
                if (!id) {
                    console.error('No ID found for the delete operation.');
                    return;
                }

                // Get the record ID from the data-id attribute

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
                            url: '{{ route("usercreation.destroy", ":id") }}'.replace(':id',
                                id),
                            type: 'POST', // Use POST since Laravel 8+ handles DELETE through POST with a _method field
                            data: {
                                _method: 'DELETE', // Indicate the DELETE method
                                _token: '{{ csrf_token() }}' // Include CSRF token
                            },
                            success: function (response) {
                                // Show success message with SweetAlert2
                                Swal.fire(
                                    'Deleted!',
                                    'Record has been deleted.',
                                    'success'
                                );

                                // Refresh DataTable
                                $('#usertype-table').DataTable().ajax.reload();
                            },
                            error: function (xhr) {
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

        // Handle delete button click
        // $(document).on('click', '.delete-btn', function() {
        // let deleteUrl = $(this).data('url');
        // if (confirm('Are you sure you want to delete this user?')) {
        //     $.ajax({
        //         url: deleteUrl,
        //         type: 'DELETE',
        //         data: {
        //             _token: '{{ csrf_token() }}'
        //         },
        //         success: function(response) {
        //             $('#usertype-table').DataTable().ajax.reload();
        //             alert('User deleted successfully');
        //         },
        //         error: function(xhr) {
        //             alert('Failed to delete user');
        //         }
        //     });
        // }
        // });
        // });
    </script>
@endsection