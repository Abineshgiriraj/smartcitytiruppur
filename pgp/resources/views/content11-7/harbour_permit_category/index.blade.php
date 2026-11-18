@extends('layouts/contentNavbarLayout')

@section('title', 'Tables - User Type Creation')

@section('content')

<div class="row mb-3 page-hed">
    <div class="col-sm-6">
        <h4 class="">
            <span class="text-muted fw-light">Settings /</span> Port Category
        </h4>
    </div>
    <div class="col-sm-6">
        <button class="btn btn-primary btn-right"
            onclick="window.location.href='{{ route('harbour_permit_category_add')}}'" type="button">
            <i class="i-Add"></i> Add New
        </button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="card-datatable table-responsive">
            <table id="category" class="table table-bordered dt-responsive nowrap">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Category</th>
                        <th>Sub Category</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- CSS Links -->
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
        var table = $('#category').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{route("harbour_permit_category")}}',

            columns: [{
                    data: "DT_RowIndex",
                    name: "DT_RowIndex",
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'category',
                    name: 'category'
                },
                {
                    data: 'sub_category',
                    name: 'sub_category'
                },
                {
                    data: 'description',
                    name: 'description'
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

        $('#category').on('click', '.delete-btn', function() {
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
                        url: '{{ route("harbour_permit_category.delete", ":id") }}'
                            .replace(
                                ":id", id),
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            table.ajax.reload();
                            Swal.fire('Deleted!',
                                'Designation has been deleted.',
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