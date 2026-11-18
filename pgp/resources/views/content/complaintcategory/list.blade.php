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
            <span class="text-muted fw-light">Settings /</span> Complaint category  
        </h4>
    </div>
    <div class="col-sm-6">
        <button class="btn btn-primary btn-right" onclick="window.location.href='{{ url('/complaintcategory_add') }}'" type="button">
            <i class="i-Add"></i> Add New
        </button>
    </div>
</div>

<!-- Responsive Datatable -->
<div class="card">
    <div class="card-body">
    <div class="card-datatable table-responsive">
        <table class="dt-responsive table border-top" >
            <thead>
                <tr>
                    <!-- <th></th> -->
                    <th style="text-align : Center">S.No</th>
                    <th style="text-align : Center">Department Name</th>
                    <th style="text-align : Center">Complaint category</th>
                    <th style="text-align : Center">Description</th>
                    <th style="text-align : Center">Status</th>
                    <th style="text-align : Center">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align : Center">
                        1
                    </td>
                    <td style="text-align : Center">
                        Accounts
                    </td>
                    <td style="text-align : Center">
                        Contract Bill related
                    </td>
                    <td style="text-align : Center">
                        Test
                    </td>
                    <td style="text-align : Center; color: #6fbf63;">
                        Active
                    </td>
                    <td style="text-align : Center">
                                <a class="text-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="#" class="text-danger" title="Delete" onclick=""
                                    style="background:none; border:none; padding:0;"><i class="fas fa-trash-alt"></i></a>
                    </td>
                </tr>
                 <tr>
                    <td style="text-align : Center">
                        2
                    </td>
                    <td style="text-align : Center">
                        e-Governance
                    </td>
                    <td style="text-align : Center">
                        Special provident fund
                    </td>
                    <td style="text-align : Center">
                        Test
                    </td>
                    <td style="text-align : Center; color: red;">
                        Inactive
                    </td>
                    <td style="text-align : Center">
                                <a class="text-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                <a class="text-danger" title="Delete" onclick=""
                                    style="background:none; border:none; padding:0;"><i class="fas fa-trash-alt"></i></a>
                    </td>
                </tr>                
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
            $('#ward_datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("ward") }}',

                columns: [
                    {
                        data: "DT_RowIndex",
                        name: "DT_RowIndex",
                        orderable: false,
                        searchable: false,
                        className: "text-end"
                    },
                    {
                        data: 'zone',
                        name: 'zone'
                    },
                   
                    { 
                        data: 'description', 
                        name: 'description' 
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



        // function confirmDelete(id) {
        //     Swal.fire({
        //         title: 'Are you sure?',
        //         text: "This will delete the category.",
        //         icon: 'warning',
        //         showCancelButton: true,
        //         confirmButtonText: 'Yes, delete it!'
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             $.ajax({
        //                 url: '{{ route("zone.delete", ":id") }}'.replace(':id', id),
        //                 type: 'POST',
        //                 data: {
        //                     _method: 'DELETE',
        //                     _token: '{{ csrf_token() }}'
        //                 },
        //                 success: function (response) {
        //                     $('#zone_table').DataTable().ajax.reload();
        //                     Swal.fire('Deleted!', 'Deleted Successfully.', 'success');
        //                 },
        //                 error: function () {
        //                     Swal.fire('Error!', 'Something went wrong.', 'error');
        //                 }
        //             });
        //         }
        //     });
        // }


    </script>

@endsection