@extends('layouts/contentNavbarLayout')

@section('title', 'Tables - Basic Tables')

<style>
/* btn style */
.btn-right {
    float: right;
}
</style>
<?php
$get_department_name = get_department();
$get_designation_name = get_designation();
?>
@section('content')

<div class="row mb-3 page-hed">
    <div class="col-sm-6">

        <h4 class="">
            <span class="text-muted fw-light">Settings /</span> Staff Registration  
        </h4>
    </div>
    <div class="col-sm-6">
        <button class="btn btn-primary btn-right" onclick="window.location.href='{{ url('/user_registration_add') }}'" type="button">
            <i class="i-Add"></i> Add New
        </button>
    </div>
</div>

<!-- Responsive Datatable -->
<div class="card">
    <div class="card-body">
    <div class="card-datatable table-responsive">
        <table class="dt-responsive table border-top" id="user_registration_datatable">
            <thead>
                <tr>
                    <!-- <th></th> -->
                    <th style="text-align : left">S.No</th>
                    <th style="text-align : left">Name</th>
                     <th style="text-align : left">Mobile NO</th>
                    <th style="text-align : left">Designation</th>
                    <th style="text-align : left">Department</th>
                    <th style="text-align : left">Action</th>
                </tr>
            </thead>
            <tbody>
                                
            </tbody>
        </table>
    </div>
</div>
</div>
<!--/ Responsive Datatable -->


<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
    <script src="{{ asset('public/assets/js/common.js') }}"></script>
    <script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>

    <script>

        $(document).ready(function () {
            $('#user_registration_datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("user_registration") }}',

                columns: [
                    {
                        data: "DT_RowIndex",
                        name: "DT_RowIndex",
                        orderable: false,
                        searchable: false,
                        className: "text-end"
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                     { 
                        data: 'mobile_no', 
                        name: 'mobile_no' 
                    },
                     { 
                        data: 'designations', 
                        name: 'designations' 
                    },
                    {
                        data: 'departments',
                        name: 'departments'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: "text-center"
                    }
                ],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                order: [[0, 'asc']]
            });
        });



        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This will delete the category.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("user_registration.delete", ":id") }}'.replace(':id', id),
                        type: 'POST',
                        data: {
                            _method: 'DELETE',
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            $('#userregistration_datatable').DataTable().ajax.reload();
                            Swal.fire('Deleted!', 'Deleted Successfully.', 'success');
                        },
                        error: function () {
                            Swal.fire('Error!', 'Something went wrong.', 'error');
                        }
                    });
                }
            });
        }


    </script>

@endsection