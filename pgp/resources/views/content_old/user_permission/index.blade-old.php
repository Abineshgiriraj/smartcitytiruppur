@extends('layouts/contentNavbarLayout')

@section('title', 'Tables - User Permission Creation')

@section('content')
<style>
/* btn style */
.btn-right {
    float: right;
}
</style>

<div class="row mb-3 page-hed">
    <div class="col-sm-6">
        <h4 class="">
            <span class="text-muted fw-light">Admin /</span>User Permission
        </h4>
    </div>
    <div class="col-sm-6">
        <button class="btn btn-primary btn-right" onclick="window.location.href='{{ route('userpermission.create') }}'"
            type="button">
            <i class="i-Add"></i> Add New
        </button>
    </div>
</div>

<!-- Responsive Datatable -->
<div class="card">
    <div class="card-body">
        <div class="card-datatable table-responsive">
            <table class="table table-bordered dt-responsive" id="userPermissionTable">
                <thead>
                    <th>S.No</th>
                    <th>User Type</th>
                    <th>Action</th>
                </thead>
                <tbody></tbody>
        </div>
    </div>
</div>

<!--/ Responsive Datatable -->

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

<script>
$(document).ready(function() {
    $('#userPermissionTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('user_permission.index') }}",
            type: 'GET'
        },
        columns: [{
                data: 'user_type',
                name: 'user_type',
                title: 'User Type'
            },

            {
                data: 'actions',
                name: 'actions',
                title: 'Actions'
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

// Handle delete action
// function deleteUserPermission(id) {
//     if (confirm('Are you sure you want to delete this permission?')) {
//         $.ajax({
//             url: `/userpermission/destroy/${id}`,
//             type: 'DELETE',
//             data: {
//                 "_token": "{{ csrf_token() }}",
//             },
//             success: function(response) {
//                 // Refresh the DataTable after deletion
//                 $('#userPermissionTable').DataTable().ajax.reload();
//             }
//         });
//     }
// }

// $(document).ready(function() {
//     $('#user_permission').DataTable({
//         processing: true,
//         serverSide: true,
//         ajax: '{{ route("user_permission.index") }}',
//         columns: [{
//                 data: 'id',
//                 name: 'id'
//             },
//             {
//                 data: 'user_type',
//                 name: 'user_type'
//             },
//             {
//                 data: 'main_screen',
//                 name: 'main_screen'
//             },
//             {
//                 data: 'sub_screen',
//                 name: 'sub_screen'
//             },
//             {
//                 data: 'actions',
//                 name: 'actions',
//                 orderable: false,
//                 searchable: false
//             },
//             {
//                 data: 'action',
//                 name: 'action',
//                 orderable: false,
//                 searchable: false
//             }
//         ],
//         // columns: [{
//         //         data: 'id',
//         //         name: 'id',
//         //         orderable: false,
//         //         searchable: false,
//         //     },
//         //     {
//         //         data: 'user_type',
//         //         name: 'user_type'
//         //     },
//         //     {
//         //         data: 'action',
//         //         name: 'action',
//         //         orderable: false,
//         //         searchable: false
//         //     }
//         // ],
//         responsive: true
//     });
// });
</script>
@endsection