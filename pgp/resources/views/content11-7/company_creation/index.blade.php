@extends('layouts/contentNavbarLayout')

@section('title', 'Company Creation')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-sm-6">
        <h4><span class="text-muted fw-light">Settings / </span>Company Type Creation</h4>
    </div>
    <div class="col-sm-6">
        <button class="btn btn-primary btn-right" onclick="window.location.href='{{ route('company_creation_add') }}'" type="button">
            <i class="i-Add"></i> Add New
        </button>
    </div>
</div>

<!-- Responsive Datatable -->
<div class="card">
    <div class="card-body">
        <div class="card-datatable table-responsive">
            <table class="table border-top data-table" id="company">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Company Name</th>
                        <th>Description</th>
                        <th>Status</th>
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



$(document).ready(function() {

    var table = $('#company').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{route("company_creation.index")}}',

        columns: [{
                data: "DT_RowIndex",
                name: "DT_RowIndex",
                orderable: false,
                searchable: false
            },
            {
                data: 'company_name',
                name: 'company_name'
            },

            {
                data: 'description',
                name: 'description'
            },
            
            {
                data: 'status',
                name: 'status'
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

    // $('#company').on('click', '.delete-btn', function(e) {
    // e.preventDefault(); // Prevent the form from submitting
    // var id = $(this).data('id');
    // Swal.fire({
    //     title: 'Are you sure?',
    //     text: "You won't be able to revert this!",
    //     icon: 'warning',
    //     showCancelButton: true,
    //     confirmButtonColor: '#3085d6',
    //     cancelButtonColor: '#d33',
    //     confirmButtonText: 'Yes, delete it!'
    // }).then((result) => {
    //     if (result.isConfirmed) {
    //         $.ajax({
    //             url: '{{ route("company_creation.delete", ":id") }}'.replace(":id", id),
    //             type: 'DELETE',
    //             data: {
    //                 _token: '{{ csrf_token() }}'
    //             },
    //             success: function(response) {
    //                 table.ajax.reload();
    //                 Swal.fire('Deleted!', 'Company has been deleted.', 'success');
    //             },
    //             error: function(xhr) {
    //                 Swal.fire('Error!', 'There was an error deleting the Company.', 'error');
    //             }
    //         });
    //     }
    // });
// });


$('#company').on('click', '.delete-btn', function() {
        var id = $(this).data('id');
        // alert(id);
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
                    url: '{{ route("company_creation.delete", ":id") }}'
                        .replace(
                            ":id", id),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        table.ajax.reload();
                        Swal.fire('Deleted!',
                            'company has been deleted.',
                            'success');
                    },
                    error: function(xhr) {
                        Swal.fire('Error!',
                            'There was an error deleting the company.',
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
