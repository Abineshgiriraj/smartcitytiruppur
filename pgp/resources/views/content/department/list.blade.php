    @extends('layouts/contentNavbarLayout')

    @section('title', 'Tables - Basic Tables')

    <style>
    .btn-right {
        float: right;
    }

    #department_table {
        border-collapse: collapse; /* Ensures that table borders collapse into a single line */
    }

    #department_table th, #department_table td {
        border: 1px solid #dee2e6; /* Add thin border for grid lines */
        padding: 8px; /* Optional: adds padding inside the cells */
    }

    #department_table th {
        background-color: #f8f9fa; /* Optional: adds a background color to the header */
    }

    #department_table td {
        text-align: left;
        vertical-align: middle;
    }

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
                <span class="text-muted fw-light">Settings /</span> Department  
            </h4>
        </div>
        <div class="col-sm-6">
            <button class="btn btn-primary btn-right" onclick="window.location.href='{{ url('/department_add') }}'" type="button">
                <i class="i-Add"></i> Add New
            </button>
        </div>
    </div>

    <!-- Responsive Datatable -->
    <div class="card">
        <div class="card-body">
        <div class="card-datatable table-responsive">
            <table class="dt-responsive table border-top" id="department_table">
                <thead>
                    <tr>
                        <th style="text-align: center">S.No</th>
                        <th style="text-align: center">Department Name</th>
                        <th style="text-align: center">Description</th>
                        <th style="text-align: center">Status</th>
                        <th style="text-align: center">Action</th>
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
            $('#department_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("department") }}',
                columns: [
                    {
                        data: "DT_RowIndex",
                        name: "DT_RowIndex",
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'department',
                        name: 'department'
                    },
                
                    { 
                        data: 'description', 
                        name: 'description' 
                    },
                    { 
                        data: 'status', 
                        name: 'status',
                        render: function(data, type, row) {
                            // Add CSS class based on status value
                            var statusClass = data === 'Active' ? 'status-active' : 'status-inactive';
                            return '<span class="' + statusClass + '">' + data + '</span>';
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                    }
                ],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                order: [[0, 'asc']],
                createdRow: function(row, data, dataIndex) {
    // Left align the specific columns
    $('td:eq(1)', row).css('text-align', 'left'); // Department Name
    $('td:eq(2)', row).css('text-align', 'left'); // Description
    // Center align other columns
    $('td', row).not(':eq(1), :eq(2)').css('text-align', 'center');
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
                        url: '{{ route("department.delete", ":id") }}'.replace(':id', id),
                        type: 'POST',
                        data: {
                            _method: 'DELETE',
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            $('#department_table').DataTable().ajax.reload();
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