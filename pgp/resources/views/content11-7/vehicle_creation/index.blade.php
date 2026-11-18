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
            <span class="text-muted fw-light">Port OPeration System /</span>Vehicle Creation
        </h4>
    </div>
    <div class="col-sm-6">
        <button class="btn btn-primary btn-right" onclick="window.location.href='{{ url('/vehicle_creation_add')}}'"
            type="button">
            <i class="i-Add"></i> Add New
        </button>
    </div>
</div>

<!-- Responsive Datatable -->
<div class="card">
    <div class="card-body">
        <div class="card-datatable table-responsive">
            <table class="dt-responsive table border-top data-table" id="vehicle_creation">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Vehicle Type</th>

                        <th>Vehicle Category</th>
                        <th>Action</th>
                    </tr>
                </thead>


            </table>
        </div>
    </div>
</div>

<link rel="stylesheet" href="{{ asset('public/assets/css/bootstrap.min.css') }}" />
<link rel="stylesheet" href="{{ asset('public/assets/css/dataTables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('public/assets/css/responsive.bootstrap.min.css') }}" />
<link rel="stylesheet" href="{{ asset('public/assets/css/core.css') }}" />

<!-- JavaScript Links -->
<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('public/assets/js/dataTables.js') }}"></script>
<script src="{{ asset('public/assets/js/dataTables.bootstrap5.js') }}"></script>
<script src="{{ asset('public/assets/js/dataTables.responsive.js') }}"></script>
<script src="{{ asset('public/assets/js/responsive.bootstrap5.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert2.js') }}"></script>
<script>
$(document).ready(function() {
    var table = $('#vehicle_creation').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{route("vehicle_creation")}}',

        columns: [{
                data: "DT_RowIndex",
                name: "DT_RowIndex",
                orderable: false,
                searchable: false
            },
            {
                data: 'cargo',
                name: 'cargo'
            },
            {
                data: 'vehicle_type',
                name: 'vehicle_type'
            },

            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
        ],
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50],
        order: [
            [0, 'asc']
        ]
    });

    $('#vehicle_creation').on('click', '.delete-btn', function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            timer: 5000,
            timerProgressBar: true,
            // willOpen: () => {
            //     Swal.showLoading(); // Optionally show loading animation
            // },
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{route("vehicle_creation.delete", ":id") }}'
                        .replace(":id", id),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        const url = "{{route('vehicle_creation')}}";

                        table.ajax.reload();
                        Swal.fire('Deleted!',
                            'Vehicle Creation has been deleted.',
                            'success');
                    },
                    error: function(xhr) {
                        Swal.fire('Error!',
                            'There was an error deleting the vehicle.',
                            'error'
                        );
                    }
                });
            }
        });
    });
});
</script>@endsection