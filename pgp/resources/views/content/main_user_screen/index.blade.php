@extends('layouts/contentNavbarLayout')

@section('title', 'Tables - Main User Screen')

@section('content')
<style>
.btn-right {
    float: right;
}
</style>

<div class="row mb-3 page-hed">
    <div class="col-sm-6">
        <h4 class=""><span class="text-muted fw-light">Admin /</span>Main User Screen</h4>
    </div>
    <div class="col-sm-6">
        <button class="btn btn-primary btn-right" onclick="window.location.href='{{ route('mainuserscreen.create') }}'"
            type="button">
            <i class="i-Add"></i> Add New
        </button>
    </div>
</div>

<!-- Responsive Datatable -->
<div class="card">
    <div class="card-body">
        <div class="card-datatable table-responsive">
            <table class="table table-bordered dt-responsive nowrap" id="mainuser_screen_table">
                <thead>
                    <tr>
                        <th><b>S.No</b></th>
                        <th><b>Screen Name</b></th>
                        <th><b>Screen Type</b></th>
                        <th><b>Order No</b></th>
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

<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('public/assets/js/dataTables.js') }}"></script>
<script src="{{ asset('public/assets/js/dataTables.bootstrap5.js') }}"></script>
<script src="{{ asset('public/assets/js/dataTables.responsive.js') }}"></script>
<script src="{{ asset('public/assets/js/responsive.bootstrap5.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    $('#mainuser_screen_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("mainuserscreen.data") }}',

            type: 'GET',
            error: function(xhr, error, code) {
                console.log(xhr.responseText);
            }
        },
        columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
            {
                data: 'screen_name',
                name: 'screen_name'
            },
            {
                data: 'screen_type',
                name: 'screen_type'
            },
            {
                data: 'order_no',
                name: 'order_no'
            },
            {
                data: 'is_active',
                name: 'is_active',
                render: function(data, type, row) {
                    if (data == 1) {
                        return '<span class="text-success">Active</span>';
                    } else {
                        return '<span class="text-danger">Inactive</span>';
                    }
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
                    url: '{{ route("mainuserscreen.destroy", ":id") }}'.replace(':id',
                        id),

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
                        // table.ajax.reload();

                        // Refresh DataTable
                        $('#mainuser_screen_table').DataTable().ajax.reload();
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