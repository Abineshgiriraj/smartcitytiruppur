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
            <span class="text-muted fw-light">Approvals /</span>Port Vehicle
        </h4>
    </div>
    <div class="col-sm-6">
        <!-- Uncomment the button if needed -->
        <!-- <button class="btn btn-primary btn-right" onclick="window.location.href='{{ route('rate_master_add') }}'" type="button">
            <i class="i-Add"></i> Add New
        </button> -->
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-2">
                <label class="form-label">From Date</label>
                <input type="date" name="from_date" class="form-control" id="from_date">
            </div>
            <div class="col-2">
                <label class="form-label">To Date</label>
                <input type="date" name="to_date" class="form-control" id="to_date">
            </div>
            <div class="col-3">
                <label class="form-label">Vehicle No</label>
                <input type="text" name="vehicle_no" class="form-control" id="vech_reg_no" value="">
            </div>
            <div class="col-2">
                <label for="status" class="form-label">Status Filter</label>
                <select name="status" id="status" class="select2 form-control" required>
                    <option value="0">Pending</option>
                    <option value="1">Approved</option>
                    <option value="2">Reject</option>
                </select>
            </div>
            <div class="col-2 mt-4">
                <button class="btn btn-primary" id="filterBtn">GO</button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="card-datatable table-responsive">
            <table id="port_vehicle_datatable" class="table table-bordered dt-responsive nowrap">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Vehicle No</th>
                        <th>Type Of Vehicles</th>
                        <th>Reg Date</th>
                        <th>Reg. No</th>
                        <th>Contact No</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be populated by DataTables via AJAX -->
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript" class="init">
        $(document).ready(function () {
            var table = $('#port_vehicle_datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('port_vehicle_authentication') }}",
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'vech_reg_no',
                    name: 'vech_reg_no'
                },
                {
                    data: 'vehicle_type',
                    name: 'vehicle_type'
                },
                {
                    data: 'entry_date',
                    name: 'entry_date'
                },
                {
                    data: 'vehicle_id',
                    name: 'vehicle_id'
                },

                {
                    data: 'contact_number',
                    name: 'contact_number'
                },

                {
                    data: 'status',
                    name: 'status',
                    render: function (data, type, row) {
                        if (data == 1) {
                            return '<span class="text-success">Approve</span>';
                        } else if (data == 2) {
                            return '<span class="text-danger">Rejected</span>';
                        } else {
                            return '<span>Pending</span>';
                        }
                    }
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
                ],
            });

            $('#filterBtn').click(function () {
                var fromDate = $('#from_date').val();
                var toDate = $('#to_date').val();
                var vech_reg_no = $('#vech_reg_no').val();
                var status = $('#status').val();

                var url = "{{ route('port_vehicle_authentication') }}?from_date=" + fromDate + "&to_date=" +
                    toDate + "&vech_reg_no=" + vech_reg_no + "&status=" + status;

                console.log(url); // Debugging log

                // Reload the table with filter parameters
                table.ajax.url(url).load();
            });
        });
    </script>
    @endsection