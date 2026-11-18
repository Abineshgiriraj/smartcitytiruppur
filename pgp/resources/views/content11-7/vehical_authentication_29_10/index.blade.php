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
    margin-left: 0.5rem;
    /* Add space between icon and text */
    white-space: nowrap;
    /* Prevent text from wrapping */
    font-size: 10px;
}
</style>
<div class="row mb-3 page-hed">
    <div class="col-sm-6">
        <h4 class="">
            <span class="text-muted fw-light">Approvals /</span> Vehicle/Equipments
        </h4>
    </div>

</div>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-2">
                <label class="form-label">From Date</label>
                <input type="date" class="form-control" name="from_date" id="from_date">
            </div>
            <div class="col-2">
                <label class="form-label">To Date</label>
                <input type="date" class="form-control" id="to_date" name="to_date">
            </div>
            <div class="col-2">
                <label class="form-label">Reg No</label>
                <input type="text" class="form-control" id="reg_no" name="reg_no">
            </div>
            <div class="col-2">
                <label class="form-label">Vehicle No</label>
                <input type="text" class="form-control" name="vehicle_no" id="vehicle_no">
            </div>


            <div class="col-2">
                <label for="example-select" class="form-label">Status Filter</label>
                <select name="status_filter" id="status_filter" class="select2 form-control" required>
                    <option value="">Status Filter</option>
                    <option value="0">Pending</option>
                    <option value="1">Approved</option>
                    <option value="2">Reject</option>
                </select>
            </div>

            <div class="col-md-2 align-self-center mt-3">
                <div class="page-title-right">
                    <button class="btn btn-primary" id="filterBtn">GO</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="card-datatable table-responsive">

            <table id="vehicle_authentication" class="table table-bordered">
                <thead>
                    <tr>
                        <th>S.No</th> <!-- Added S.No column -->
                        <th>Vehicle No.</th>
                        <th>Type of Vehicles</th>
                        <th>Reg Date</th>
                        <th>Reg No</th>
                        <th>Owner Name</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>



                    </tr>

                </tbody>
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
<script type="text/javascript" class="init">
$(document).ready(function() {

    var table = $('#vehicle_authentication').DataTable({
        Processing: true,
        Serverside: true,
        ajax: "{{route('vehicle_authentication')}}",
        columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            }, {
                data: 'vech_reg_no',
                name: 'vech_reg_no'
            },
            {
                data: 'status',
                name: 'status'
            },
            {
                data: 'entry_date',
                name: 'entry_date'
            },
            {
                data: 'vehicle_serial_no',
                name: 'vehicle_serial_no'
            },
            {
                data: 'owner_name',
                name: 'owner_name'
            },
            {
                data: 'approval_status',
                name: 'approval_status'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
        ]
    });

    $('#filterBtn').click(function() {


        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();
        var reg_no = $('#reg_no').val();
        var vehicle_no = $('#vehicle_no').val();
        var status_filter = $('#status_filter').val();

        table.ajax.url("{{ route('vehicle_authentication') }}?from_date=" + from_date + "&to_date=" +
            to_date + "&reg_no=" + reg_no + "&vehicle_no=" + vehicle_no + "&status_filter=" +
            status_filter).load();

    });


});
</script>

@endsection