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
            <span class="text-muted fw-light">Port OPeration System /</span>Rate Master
        </h4>
    </div>
    <div class="col-sm-6">
        <button class="btn btn-primary btn-right" onclick="window.location.href='{{ route('rate_master_add')}}'"
            type="button">
            <i class="i-Add"></i> Add New
        </button>
    </div>
</div>

<!-- Responsive Datatable -->
<div class="card">
    <div class="card-body">
        <div class="card-datatable table-responsive">
            <table class="dt-responsive table border-top data-table" id="rate_master">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Access Gate</th>
                        <th>Allowed Type</th>
                        <th>Vehicle/Person Type</th>
                        <th>Validity</th>
                        <th>Rate</th>

                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>

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
    var table = $('#rate_master').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{route("rate_master")}}',

        columns: [{
                data: "DT_RowIndex",
                name: "DT_RowIndex",
                orderable: false,
                searchable: false
            },
            {
                data: 'access_gate',
                name: 'access_gate'
            },
            {
                data: 'allowed_type',
                name: 'allowed_type'
            },
            {
                data: 'vehicle_person',
                name: 'vehicle_person',

            },

            {
                data: 'validity',
                name: 'validity'
            }, {
                data: 'rate',
                name: 'rate'
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

    $('#rate_master').on('click', '.delete-btn', function() {
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
                    url: '{{ route("rate_master.delete", ":id") }}'
                        .replace(
                            ":id", id),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        table.ajax.reload();
                        Swal.fire('Deleted!',
                            'Rate Master has been deleted.',
                            'success');
                    },
                    error: function(xhr) {
                        Swal.fire('Error!',
                            'There was an error deleting the category.',
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