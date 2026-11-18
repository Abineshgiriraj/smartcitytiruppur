@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')

<div class="row mb-3 page-hed">
    <div class="col-sm-6">
        <h4 class="">
            <span class="text-muted fw-light">Entry Form /</span> Designation
        </h4>
    </div>
    <div class="col-sm-6">
        <button class="btn btn-primary btn-right" onclick="window.location.href='{{ route('designation.add') }}'"
            type="button">
            <i class="i-Add"></i> Add New
        </button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table id="designationTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>S.No</th> <!-- Added S.No column -->
                    <th>Designation Name</th>
                    <th>Remarks</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
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
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>

<script>
$(document).ready(function() {
    var table = $('#designationTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('designation.data') }}",
        columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            }, // S.No column
            {
                data: 'designation',
                name: 'designation'
            },
            {
                data: 'remarks',
                name: 'remarks'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
        ],
        order: [
            [1, 'asc']
        ], // Order by ID column
    });

    $('#designationTable').on('click', '.delete-btn', function() {
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
                    url: '{{ route("designation.destroy", ":id") }}'.replace(":id", id),


                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        sweetalert('success_delete');
                        window.location.href = "{{  url('/designation') }}";
                        // Swal.fire(
                        //     'Deleted!',
                        //     'Designation has been deleted.',
                        //     'success'
                        // )
                        // table.ajax.reload();
                    }
                });
            }
        });
    });
});
</script>

@endsection