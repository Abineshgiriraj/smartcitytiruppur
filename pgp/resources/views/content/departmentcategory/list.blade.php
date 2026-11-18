@extends('layouts/contentNavbarLayout')

@section('title', 'Tables - Basic Tables')

<style> 
    /* btn style */
    .btn-right {
        float: right;
    }

    #department_category_datatable {
        border-collapse: collapse; /* Ensures that table borders collapse into a single line */
    }

    #department_category_datatable th, #department_category_datatable td {
        border: 1px solid #dee2e6; /* Add thin border for grid lines */
        padding: 8px; /* Optional: adds padding inside the cells */
    }

    #department_category_datatable th {
        background-color: #f8f9fa; /* Optional: adds a background color to the header */
    }

    /* Center align all table cells */
    #department_category_datatable td {
        text-align: center;
        vertical-align: middle;
    }

    /* Status styling */
    .status-active {
        color: #28a745;
        font-weight: 500;
    }

    .status-inactive {
        color: #dc3545;
        font-weight: 500;
    }
</style>

@section('content')

<div class="row mb-3 page-hed">
    <div class="col-sm-6">
        <h4 class="">
            <span class="text-muted fw-light">Settings /</span> Department category
        </h4>
    </div>
    <div class="col-sm-6">
        <button class="btn btn-primary btn-right" onclick="window.location.href='{{ url('/departmentcategory_add') }}'" type="button">
            <i class="i-Add"></i> Add New
        </button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="card-datatable table-responsive">
            <table class="dt-responsive table border-top" id="department_category_datatable">
                <thead>
                    <tr>
                        <th style="text-align : Center">S.No</th>
                        <th style="text-align : Center">Department Name</th>
                        <th style="text-align : Center">Department category</th>
                        <th style="text-align : Center">Description</th>
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
    $(document).ready(function() {
    $('#department_category_datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("departmentcategory") }}',
        columns: [
            {
                data: "DT_RowIndex",
                name: "DT_RowIndex",
                orderable: false,
                searchable: false,
                className: "text-end"
            },
            {
                data: 'department',
                name: 'department'
            },
            {
                data: 'department_category',
                name: 'department_category'
            },
            {
                data: 'description',
                name: 'description'
            },
            {
                data: 'status',
                name: 'status',
                render: function(data, type, row) {
                    var statusClass = data === 'Active' ? 'status-active' : 'status-inactive';
                    return '<span class="' + statusClass + '">' + data + '</span>';
                }
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
        order: [
            [0, 'asc']
        ],
        createdRow: function(row, data, dataIndex) {
            $('td:eq(1)', row).css('text-align', 'left'); 
            $('td:eq(2)', row).css('text-align', 'left'); 
            var editUrl = '{{ route("departmentcategory.edit", ":id") }}'.replace(':id', data.dept_id);
            var actionBtn = '<a href="' + editUrl + '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a>';
            var deleteBtn = '<a href="#" class="text-danger" title="Delete" onclick="confirmDelete(\'' + data.dept_id + '\')" style="background:none; border:none; padding:0;"><i class="fas fa-trash-alt"></i></a>';
            $('td', row).eq(5).html(actionBtn + ' &nbsp;' + deleteBtn);  // Set the action buttons
        }
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
                url: '{{ route("departmentcategory.delete", ":id") }}'.replace(':id', id),
                type: 'delete',
                data: {
                    _method: 'POST',
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#department_category_datatable').DataTable().ajax.reload();
                        Swal.fire('Deleted!', 'Category deleted successfully.', 'success');
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'Something went wrong.', 'error');
                }
            });
        }
    });
}


</script>

@endsection