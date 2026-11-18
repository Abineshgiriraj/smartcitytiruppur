@extends('layouts/contentNavbarLayout')

@section('title', 'Tables - Basic Tables')

<style>
    /* btn style */
    .btn-right {
        float: right;
    }
</style>

@php
$get_firm_type = get_company();
@endphp

@section('content')

<div class="row mb-3 page-hed">
    <div class="col-sm-6">

        <h4 class="">
            <span class="text-muted fw-light">Approvals / </span>Firms/Agent
        </h4>
    </div>
    <div class="col-sm-6">
        <!-- <button class="btn btn-primary btn-right" onclick="window.location.href='{{ route('rate_master_add')}}'"
            type="button">
            <i class="i-Add"></i> Add New -->
        </button>
    </div>
</div>

<!-- Responsive Datatable -->
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-2">
                <label class="form-label">From Date</label>
                <input type="date" class="form-control" id="from_date">
            </div>
            <div class="col-2">
                <label class="form-label">To Date</label>
                <input type="date" class="form-control" id="to_date">
            </div>
            <div class="col-2">
                <label for="nationalitySelect" class="form-label">Type of Firms /
                    Agent</label>


                <select class="form-select" id="firm_type" name="firm_type">
                    <option value="" selected>Select Firms</option>
                    @foreach ($get_firm_type as $firm_type )
                    <option value="{{$firm_type->unique_id}}">{{$firm_type->company_name}}</option> @endforeach
                    <!-- <option value="Stevedore">Stevedore
                    </option>
                    <option value="Ship Agent">Ship Agent</option>
                    <option value="Custom Broker">Custom Broker</option>
                    <option value="CFS">CFS (Container Freight Station)</option> -->
                </select>
            </div>

            <div class="col-2">
                <label class="form-label">Company Name</label>
                <input type="text" class="form-control" value="" id="name">
            </div>

            <div class="col-2">
                <label for="example-select" class="form-label">Status Filter</label>
                <select name="status" id="approval" class="select2 form-control" required>
                    <option value="0">Pending</option>
                    <option value="1">Approved</option>
                    <option value="2">Reject</option>
                </select>
            </div>
            <div class="col-2 mt-4">
                <button type="button" class="btn btn-primary" onclick="get_datatable()">Go</button>
            </div>


        </div>
        <div class="card-body">

            <div class="card-datatable table-responsive">
                <table id="firmauth_datatable" class="table table-bordered dt-responsive nowrap">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Firm ID</th>
                            <th>Types of Agent</th>
                            <th>Reg No</th>
                            <th>Reg Date</th>
                            <th>Company Name</th>
                            <th>Phone/Mobile No</th>
                            <th>GST IN</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
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
    <script>
        $(document).ready(function() {
            get_datatable();
        });
        // $(document).ready(function () {
        function get_datatable() {
            if ($.fn.DataTable.isDataTable('#firmauth_datatable')) {
                $('#firmauth_datatable').DataTable().clear().destroy(); // Clear and destroy the existing instance
                $('#firmauth_datatable tbody').empty(); // Clear the table body to avoid appending new rows
            }

            // Reinitialize the DataTable
            var table = $('#firmauth_datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('firmauth.data') }}", // Route to fetch filtered data
                    data: function(d) {
                        // Pass filter values to the server
                        d.from_date = $('#from_date').val();
                        d.to_date = $('#to_date').val();
                        d.firm_type = $('#firm_type').val();
                        d.name = $('#name').val();
                        d.status = $('#approval').val();
                    }

                },

                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    }, // S.No column
                    {
                        data: 'firm_id',
                        name: 'firm_id'
                    },
                    {
                        data: 'company_name',
                        name: 'company_name'
                    },
                    {
                        data: 'company_reg_no',
                        name: 'company_reg_no'
                    },
                    {
                        data: 'entry_date',
                        name: 'entry_date'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'mobile',
                        name: 'mobile'
                    },
                    {
                        data: 'gstin',
                        name: 'gstin'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data, type, row) {
                            if (data == 1) {
                                return '<span class="text-success">Approve</span>';
                            } else if (data == 2) {
                                return '<span class="text-danger">Rejected</span>';
                            } else {
                                return '<span class="text-primary">Pending</span>';
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

                responsive: true,
                destroy: true,
                initComplete: function(settings, json) {
                    // Log the data here after initialization
                    console.log('Data sent to the server:', json);
                } // Ensure proper destruction of previous instances
            });
        }

        // });
    </script>

    @endsection