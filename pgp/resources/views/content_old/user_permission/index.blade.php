@extends('layouts/contentNavbarLayout')

@section('title', 'Tables - User Type Creation')

@section('content')

    <div class="row mb-3 page-hed">
        <div class="col-sm-6">
            <h4 class="">
                <span class="text-muted fw-light">Admin /</span> User Permission
            </h4>
        </div>
        <div class="col-sm-6">
            <button class="btn btn-primary btn-right" onclick="window.location.href='{{ route('userpermission.create') }}'"
                type="button">
                <i class="i-Add"></i> Add New
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table id="userPermissionTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>User Type</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
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
        $(document).ready(function () {
            $('#userPermissionTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('user_permission.index') }}",
                    type: 'GET'
                },
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                }, // S.No column
                {
                    data: 'user_type',
                    name: 'user_type',
                    title: 'User Type'
                },
                {
                    data: 'action',
                    name: 'action',
                    title: 'Action',
                    orderable: false,
                    searchable: false

                }

                ]
            });
        });
    </script>

@endsection