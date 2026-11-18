@extends('layouts/contentNavbarLayout')

@section('title', 'Tables - User Type Creation')

@section('content')

<div class="row mb-3 page-hed">
    <div class="col-sm-6">
        <h4 class="">
            <span class="text-muted fw-light">Admin /</span> User Type
        </h4>
    </div>
    <div class="col-sm-6">
        <button class="btn btn-primary btn-right" onclick="window.location.href='{{ route('usertype.create') }}'"
            type="button">
            <i class="i-Add"></i> Add New
        </button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="card-datatable table-responsive">
            <table id="usertypeTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>S.No</th> <!-- Added S.No column -->
                        <th>User Type</th>
                        <th>Status</th>
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

<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('public/assets/js/dataTables.js') }}"></script>
<script src="{{ asset('public/assets/js/dataTables.bootstrap5.js') }}"></script>
<script src="{{ asset('public/assets/js/dataTables.responsive.js') }}"></script>
<script src="{{ asset('public/assets/js/responsive.bootstrap5.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function () {
        
        var table = $('#usertypeTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('usertype.data') }}",
            columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            }, // S.No column
            {
                data: 'user_type',
                name: 'user_type'
            },
            {
                data: 'is_active',
                name: 'is_active',
                render: function (data, type, row) {
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
            responsive: true,
            // order: [[1, 'asc']], // Order by User Type column
        });

        $('#usertypeTable').on('click', '.delete-btn', function () {
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
                        url: '{{ route("usertype.destroy", ":id") }}'.replace(':id',
                            id),

                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            Swal.fire(
                                'Deleted!',
                                'User Type has been deleted.',
                                'success'
                            )
                            table.ajax.reload();
                        }
                    });
                }
            });
        });
    });
</script>

@endsection