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
            <span class="text-muted fw-light">Entry Form /</span> Taluk Creation
        </h4>
    </div>
    <div class="col-sm-6">
        <button class="btn btn-primary btn-right" onclick="window.location.href='{{ route('taluk_creation.create') }}'"
            type="button">
            <i class="i-Add"></i> Add New
        </button>
    </div>
</div>

<!-- Responsive Datatable -->
<div class="card">
    <div class="card-body">
        <div class="card-datatable table-responsive">
            <table class="dt-responsive table border-top taluk_datatable">
                <thead>
                    <tr>
                        <th>S NO</th>
                        <th>Zone</th>
                        <th>District</th>
                        <th>Taluk</th>
                        <!-- <th>Status</th> -->
                        <th>Remarks</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
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
<script src="{{ asset('public/assets/js/common.js') }}"></script>

<script type="text/javascript" class="init">
var table = $('.taluk_datatable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: "{{ route('taluk_creation.index') }}",
        dataSrc: function(json) {
            console.log(json); // Log the response for debugging
            return json.data;
        }
    },
    columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false
        },
        {
            data: 'zone_name',
            name: 'zone_name'
        },
        {
            data: 'district_name',
            name: 'district_name'
        }, // Check if 'district' exists in response
        {
            data: 'taluk_name',
            name: 'taluk_name'
        },
        {
            data: 'remark',
            name: 'remark'
        },
        {
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false
        }
    ]
});

$(document).on('click', '.delete', function(e) {
    e.preventDefault();
    var form = $(this).closest('form');
    var actionUrl = form.attr('action'); // Get the form's action URL

    Swal.fire({
        title: 'Are you sure?',
        text: "Want to delete this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: actionUrl,
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    sweetalert('success_delete');
                    window.location.href = "{{ route('taluk_creation.index') }}";
                    // Swal.fire({
                    //     title: 'Taluk deleted successfully!',
                    //     text: response
                    //         .success, // Use the success message from the server
                    //     icon: 'success',
                    //     confirmButtonText: 'OK',
                    //     timer: 1000,
                    //     showConfirmButton: false
                    // }).then(() => {
                    //     // Optionally, reload the DataTable or page
                    //     location.reload(); // Reload the page after success
                    // });
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Error!',
                        text: xhr.responseJSON.error || 'Something went wrong!',
                        icon: 'error',
                    });
                }
            });
        }
    });
});
</script>

@endsection