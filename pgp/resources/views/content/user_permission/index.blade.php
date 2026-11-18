@extends('layouts/contentNavbarLayout')

@section('title', 'Tables - User Type Creation')

@section('content')

<div class="row mb-3 page-hed">
    <div class="col-sm-6">
        <h4>
            <span class="text-muted fw-light">Admin /</span> User Permission
        </h4>
    </div>
    <div class="col-sm-6">
        <button class="btn btn-primary btn-right"
            onclick="window.location.href='{{ route('userpermission.create') }}'" type="button">
            <i class="i-Add"></i> Add New
        </button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table id="userPermissionTable" class="table table-bordered table-striped">
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

{{-- CSS --}}
<link rel="stylesheet" href="{{ asset('public/assets/css/bootstrap.min.css') }}" />
<link rel="stylesheet" href="{{ asset('public/assets/css/dataTables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('public/assets/css/responsive.bootstrap.min.css') }}" />
<link rel="stylesheet" href="{{ asset('public/assets/css/core.css') }}" />

{{-- JS --}}
<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('public/assets/js/dataTables.js') }}"></script>
<script src="{{ asset('public/assets/js/dataTables.bootstrap5.js') }}"></script>
<script src="{{ asset('public/assets/js/dataTables.responsive.js') }}"></script>
<script src="{{ asset('public/assets/js/responsive.bootstrap5.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>

<script>
$(document).ready(function() {
    $('#userPermissionTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('user_permission.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false }, // Serial No
            { data: 'user_type', name: 'user_type' }, // User Type
            { data: 'action', name: 'action', orderable: false, searchable: false } // Action buttons
        ]
    });
});
</script>

@endsection
