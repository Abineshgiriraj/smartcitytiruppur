@extends('layouts/contentNavbarLayout')

@section('title', 'Tables - User Type Creation')

@section('content')

<div class="row mb-3 page-hed">
    <div class="col-sm-6">
        <h4 class="">
            <span class="text-muted fw-light">Settings /</span> Complaint Type
        </h4>
    </div>
    <div class="col-sm-6">
        <button class="btn btn-primary btn-right" onclick="window.location.href='{{ route('complaint_type.create') }}'"
            type="button">
            <i class="i-Add"></i> Add New
        </button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="card-datatable table-responsive">
            <table id="complainttypeTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>S.No</th> <!-- Added S.No column -->
                        <th>Complaint Type</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- <script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script> -->
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
<!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->

<script>
var table = $('#complainttypeTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: "{{ route('complaint_type.data') }}",
        type: 'POST', // Ensure this is set to POST
        data: function(d) {
            d._token = "{{ csrf_token() }}"; // Include CSRF token if needed
        }
    },
    columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false
        }, // S.No column
        {
            data: 'complaint_type',
            name: 'complaint_type'
        },
        {
            data: 'is_active',
            name: 'is_active',
            render: function(data, type, row) {
                return data == 1 ?
                    '<span class="text-success">Active</span>' :
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
    responsive: true,
});

$('#complainttypeTable').on('click', '.delete-btn', function() {
    var id = $(this).data('id');

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
                url: '{{ route("complaint_type.destroy", ":id") }}'.replace(':id',
                    id),

                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire(
                        'Deleted!',
                        'Complaint Type has been deleted.',
                        'success'
                    )
                    table.ajax.reload();
                }
            });
        }
    });
});
// });
</script>

@endsection