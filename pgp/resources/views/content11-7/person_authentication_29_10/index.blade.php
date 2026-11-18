@extends('layouts/contentNavbarLayout')

@section('title', 'Tables - User Type Creation')

@section('content')
<style>
.btn-small {
    /* width: auto; */

    padding: 0;
    /* Remove padding */
    margin: 0;
    display: inline-flex;
    /* Align content properly */
    align-items: center;
    width: 80px;
    /* Center icons and text vertically */
    /* Remove margin */
}

.lab_add {
    font-size: 10px;
}
h4 {
    font-size: 15px !important;
}
</style>
<div class="col-sm-6">
    <h4 class=""><span class="text-muted fw-light">Approvals /</span>Person/Driver </h4>
</div>

<div class="card">
    <div class="card-body">
        <div class="row">

            <div class="col-2">
                <label class="form-label">From Date</label>
                <input type="date" id="from_date" class="form-control">
            </div>
            <div class="col-2">
                <label class="form-label">To Date</label>
                <input type="date" id="to_date" class="form-control">
            </div>
            <div class="col-2">
                <label class="form-label">Reg No</label>
                <input type="text" class="form-control" id="reg_no">
            </div>
            <div class="col-2">
                <label class="form-label">Name</label>
                <input type="text" class="form-control" id="name">
            </div>

            <div class="col-2"><label for="example-select" class="form-label">Status Filter</label>
                <select name="status" id="status" class="select2 form-control" required>
                    <option value="0">Pending</option>
                    <option value="1">Approved</option>
                    <option value="2">Reject</option>
                </select>
            </div>
            <div class="col-md-2 align-self-center mt-3">
                <div class="page-title-right"><button class="btn btn-primary" id="filterBtn">GO</button></div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="card-datatable table-responsive">
            <table id="person_authentication" class="table table-bordered dt-responsive nowrap">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>REG NO</th>
                        <th>REG Date</th>
                        <th>Name</th>
                        <th>Company Name</th>
                        <th>Designation</th>
                        <th>Aadhaar/ Driving License No</th>
                        <th>Mobile No</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

            </table>
        </div>
    </div>
</div>

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


<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>

<!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script> -->



<script type="text/javascript" class="init">
$(document).ready(function() {
    var table = $('#person_authentication').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('person_authentication') }}",
        columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex'
            },
            {
                data: 'register_no',
                name: 'register_no'
            },
            {
                data: 'register_date',
                name: 'register_date'
            },
            {
                data: 'full_name',
                name: 'full_name'
            },
            {
                data: 'company_name',
                name: 'company_name'
            },
            {
                data: 'employee_designation',
                name: 'employee_designation'
            },
            {
                data: 'aadhaar_no',
                name: 'aadhaar_no'
            },
            {
                data: 'mobile',
                name: 'mobile'
            },
            {
                data: 'status',
                name: 'status',
                orderable: false, // Disable ordering on this column
                searchable: false // Disable searching on this column
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
        ],
        "columnDefs": [{
            "targets": 8, // Adjust this index based on the actual column position for 'status'
            "render": function(data, type, row) {
                return data; // This will render the formatted status
            }
        }]
    });

    $('#filterBtn').click(function() {
        var fromDate = $('#from_date').val();
        var toDate = $('#to_date').val();
        var regNo = $('#reg_no').val();
        var name = $('#name').val();
        var status = $('#status').val();

        // Reload the table with filter parameters
        table.ajax.url("{{ route('person_authentication') }}?from_date=" + fromDate + "&to_date=" +
            toDate + "&reg_no=" + regNo + "&name=" + name + "&status=" + status).load();
    });
});
</script>@endsection