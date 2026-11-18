@extends('layouts/contentNavbarLayout')

@section('title', 'Port Issue Centers - List')

<style> 
.btn-right {
    float: right;
}

/* Default cell styling */
#zone_table td {
    vertical-align: middle;
}

/* Column specific alignment */
.text-center {
    text-align: center !important;
}
.text-left {
    text-align: left !important; /* Ensure left alignment */
}
.text-right {
    text-align: right !important;
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
        <div class="col-12">
            <h4 class=""><span class="text-muted fw-light">Settings /</span> Zone</h4>
            <a href="{{ route('zone_add') }}" class="btn btn-primary btn-right mb-1">Add New</a>
        </div>
    </div>

    <!-- Table to display zones -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered dt-responsive nowrap" id="zone_table">
                    <thead>
                        <tr>
                            <th class="text-center">S.No</th>
                            <th class="text-center">Zone Name</th> <!-- Updated to left-align -->
                            <th class="text-center">Description</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Action</th>
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
        $('#zone_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("zone") }}',

            columns: [
                {
                    data: "DT_RowIndex",
                    name: "DT_RowIndex",
                    orderable: false,
                    searchable: false,
                    className: "text-center"   // ✅ center align S.No
                },
                {
                    data: 'zone',
                    name: 'zone',
                    className: "text-left"    // ✅ right align Zone Name
                },
                {
                    data: 'description',
                    name: 'description',
                    className: "text-center"
                },
                {
                    data: 'is_active',
                    name: 'is_active',
                    className: "text-center",
                    render: function(data, type, row) {
                        if (data == 1) {
                            return '<span class="status-active">Active</span>';   // ✅ Green Active
                        } else {
                            return '<span class="status-inactive">Inactive</span>'; // ✅ Red Inactive
                        }
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
                    url: '{{ route("zone.delete", ":id") }}'.replace(':id', id),
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
