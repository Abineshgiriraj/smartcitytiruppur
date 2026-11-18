@extends('layouts/contentNavbarLayout')

@section('title', 'Tables - Basic Tables')

<style>
/* btn style */
.btn-right {
    float: right;
}
</style>
<?php
$get_dept_name = get_dept_name();
?>
@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

 <div class="row mb-3 page-hed">
        <div class="col-12">
            <h4 class=""><span class="text-muted fw-light">Settings/</span>Department Allocation</h4>
            <a href="{{ route('department_allocation_add') }}" class="btn btn-primary btn-right mb-1">Add New</a>
        </div>
    </div>
<div class="row g-2 align-items-end mb-3">
    

    <!-- Zone -->
    <div class="col-md-2">
        <label class="form-label">Department</label>
       <select class="form-select" id="department" name="department">
                                <option value="">Department name</option>
                                @foreach ($get_dept_name as $get_department)
                                <option value="{{ $get_department->unique_id }}"
                                    {{ (isset($escalation) && $escalation->department == $get_department->unique_id) ? 'selected' : '' }}>
                                    {{ formatToCapitalizedWords($get_department->department) }}
                                </option>
                                @endforeach
                            </select>
    </div>

    <!-- Go Button -->
 <div class="col-md-1">
    <button id="filter_btn" type="button" class="btn btn-primary w-100">
        <i class="bi bi-search"></i> Go
    </button>
</div>



</div>



<!-- Responsive Datatable -->
<div class="card">
    <div class="card-body">
        <div class="card-datatable table-responsive">
            <table class="dt-responsive table border-top" id="department_allocation">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Staff</th>
                         <th>Mobile No</th>
                        <th>Designation</th>
                        <th>Department</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>

$(document).ready(function () {
    $('#department_allocation').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("department_allocation") }}',   // ✅ Ensure route returns JSON in DataTables format
            type: 'GET'                   // or 'POST' if your route expects POST
        },
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
                data: 'designation',
                name: 'designation'
            },
            {
                data: 'department',
                name: 'department'
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
                        url: '{{ route("department_allocation.delete", ":id") }}'.replace(':id', id),
                        type: 'POST',
                        data: {
                            _method: 'DELETE',
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            $('#zone_table').DataTable().ajax.reload();
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