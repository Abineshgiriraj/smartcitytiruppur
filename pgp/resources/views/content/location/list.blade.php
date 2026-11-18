@extends('layouts/contentNavbarLayout') 

@section('title', 'Tables - Basic Tables')

<style>
/* btn style */
.btn-right { float: right; }

/* force table borders */
#location_datatable { border-collapse: collapse !important; }
#location_datatable th, 
#location_datatable td {
    border: 1px solid #dee2e6 !important;
    vertical-align: middle;
}

/* status colors */
.status-active   { color:#28a745; font-weight:600; }
.status-inactive { color:#dc3545; font-weight:600; }
</style>

@section('content')

<div class="row mb-3 page-hed">
    <div class="col-sm-6">
        <h4>
            <span class="text-muted fw-light">Settings /</span> Location  
        </h4>
    </div>
    <div class="col-sm-6">
        <button class="btn btn-primary btn-right" onclick="window.location.href='{{ url('/location_add') }}'" type="button">
            <i class="i-Add"></i> Add New
        </button>
    </div>
</div>

<!-- Responsive Datatable -->
<div class="card">
    <div class="card-body">
        <div class="card-datatable table-responsive">
            <table class="table table-bordered dt-responsive border-top w-100" id="location_datatable">
                <thead>
                    <tr>
                        <th class="text-center">S.No</th>
                        <th class="text-center">Zone Name</th>
                        <th class="text-center">Ward Name</th>
                        <th class="text-center">Location</th>
                        <th class="text-center">Description</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
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
        $('#location_datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("location") }}',  

            columns: [
                {
                    data: "DT_RowIndex",
                    name: "DT_RowIndex",
                    orderable: false,
                    searchable: false,
                    className: "text-center"   // ✅ S.No centered
                },
                {
                    data: 'zone_name',
                    name: 'zone_name',
                    className: "text-start"      // ✅ right align Zone
                },
                {
                    data: 'ward_name',
                    name: 'ward_name',
                    className: "text-start"      // ✅ right align Ward
                },
                {
                    data: 'location_name',
                    name: 'location_name',
                    className: "text-start"      // ✅ right align Location
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
                    render: function (data) {
                        return (String(data) === '1') 
                          ? '<span class="status-active">Active</span>' 
                          : '<span class="status-inactive">Inactive</span>';
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
            text: "This will delete the location.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("location.delete", ":id") }}'.replace(':id', id),
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function () {
                        $('#location_datatable').DataTable().ajax.reload();
                        Swal.fire('Deleted!', 'Location deleted successfully.', 'success');
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
