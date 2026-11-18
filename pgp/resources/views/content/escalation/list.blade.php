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
        <h4 class=""><span class="text-muted fw-light">Settings/</span>Escalation</h4>
        <a href="{{ route('escalation_add') }}" class="btn btn-primary btn-right mb-1">Add New</a>
    </div>
</div>
<div class="row g-2 align-items-end mb-3">


    <!-- Zone -->
    <div class="col-md-2">
        <label class="form-label">Department</label>
        <select id="zone_filter" class="form-select">
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
        <table class="dt-responsive table border-top" id="escalation_datatable" >
            <thead>
                <tr>
                    <!-- <th></th> -->
                    <th style="text-align : Center">S.No</th>
                    <th style="text-align : Center">Department</th>
                    <th style="text-align : Center">Stage Count</th>
                    <th style="text-align : Center">Status</th>
                    <th style="text-align : Center">Action</th>
                </tr>
            </thead>
            <tbody>
               
            </tbody>
        </table>
    </div>
</div>
</div>

<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
    <script src="{{ asset('public/assets/js/common.js') }}"></script>
    <script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>

    <script>

        $(document).ready(function () {
            $('#escalation_datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("escalation") }}',

                columns: [
                    {
                        data: "DT_RowIndex",
                        name: "DT_RowIndex",
                        orderable: false,
                        searchable: false,
                        className: "text-end"
                    },
                    {
                        data: 'department_name',
                        name: 'department_name'
                    },
                     { 
                        data: 'stage_count', 
                        name: 'stage_count' 
                    },
                     { 
                        data: 'is_active', 
                        name: 'is_active' 
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
                        url: '{{ route("escalation.delete", ":id") }}'.replace(':id', id),
                        type: 'POST',
                        data: {
                            _method: 'DELETE',
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            $('#escalation_datatable').DataTable().ajax.reload();
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