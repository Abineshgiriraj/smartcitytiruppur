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
            <span class="text-muted fw-light">Entry Form /</span> Category Creation
        </h4>
    </div>
    <div class="col-sm-6">
        <button class="btn btn-primary btn-right" onclick="window.location.href='{{ url('/category_add')}}'"
            type="button">
            <i class="i-Add"></i> Add New
        </button>
    </div>
</div>

<!-- Responsive Datatable -->
<div class="card">
    <div class="card-body">
        <div class="card-datatable table-responsive">
            <table class="dt-responsive table border-top data-table" id="example">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Category Type</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="categoryTableBody">

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
new DataTable('#example', {
    responsive: true
});





var table = $('.data-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('category_list') }}",
    columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false
        },

        {
            data: 'category_type',
            name: 'category_type'
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
        },
    ]
});

// function deleteCategory(id) {
// $(document).on('click', '.delete', function(e) {
//     e.preventDefault();
//     var form = $(this).closest('form');
//     var actionUrl = form.attr('action'); // Get the form's action URL

//     Swal.fire({
//         title: 'Are you sure?',
//         text: "You won't be able to revert this!",
//         icon: 'warning',
//         showCancelButton: true,
//         confirmButtonColor: '#3085d6',
//         cancelButtonColor: '#d33',
//         confirmButtonText: 'Yes, delete it!'
//     }).then((result) => {
//         if (result.isConfirmed) {
//             $.ajax({
//                 url: '{{ url("/category/delete") }}/' + id,
//                 type: 'DELETE',
//                 data: {
//                     _token: '{{ csrf_token() }}'
//                 },
//                 success: function(response) {
//                     // SweetAlert2 success message with OK button
//                     Swal.fire({
//                         title: 'Deleted!',
//                         text: response.success || 'Category has been deleted.',
//                         icon: 'success',
//                         confirmButtonText: 'OK'
//                     }).then(() => {
//                         // Reload the page or remove the category from the DOM
//                         $('#category_' + id).remove();
//                         window.location.reload(); // Optional: Reload the page
//                     });
//                 },
//                 error: function(xhr) {
//                     // SweetAlert2 error message
//                     Swal.fire({
//                         title: 'Error!',
//                         text: 'An error occurred. Please try again.',
//                         icon: 'error',
//                         confirmButtonText: 'OK'
//                     });
//                 }
//             });
//         }
//     });
// });

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
                    window.location.href = "{{  url('/category_list') }}";
                    // Swal.fire({
                    //     title: 'The Category has been deleted!',
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

<!-- </div> -->

@endsection