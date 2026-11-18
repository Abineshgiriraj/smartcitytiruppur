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
            <span class="text-muted fw-light">Port OPeration System /</span>Cargo Import/Export Registration
        </h4>
    </div>
    <div class="col-sm-6">
        <button class="btn btn-primary btn-right" onclick="window.location.href='{{ url('/new_cargo_add')}}'"
            type="button">
            <i class="i-Add"></i> Add New
        </button>
    </div>
</div>

<!-- Responsive Datatable -->
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-2 mt-3"> <label class="form-label me-2" for="transaction_type">Chit No</label></div>
            <div class="col-sm-2 mt-3">
                <input type="text" class="form-control me-4" id="category_type" name="category_type" />
            </div>
            <div class="col-sm-2 mt-3"> <label class="form-label me-2" for="transaction_type">Application No</label>
            </div>
            <div class="col-sm-2 mt-3">
                <input type="text" class="form-control me-4" id="category_type" name="category_type" />
            </div>
            <div class="col-sm-2 mt-3"> <label class="form-label me-2" for="transaction_type">Cargo Code/Cargo</label>
            </div>
            <div class="col-sm-2 mt-3">
                <input type="text" class="form-control me-4" id="category_type" name="category_type" />
            </div>
            <div class="col-sm-2 mt-3"> <label class="form-label me-2" for="transaction_type">Vehicle No</label></div>
            <div class="col-sm-2 mt-3">
                <input type="text" class="form-control me-4" id="category_type" name="category_type" />
            </div>
            <div class="col-sm-2 mt-3">
                <button class="btn btn-primary btn-right md-2" type="button">
                    <i class="i-Add"></i> GO
                </button>
            </div>
        </div>
        <div class=" card-body">

            <div class="card-datatable table-responsive">
                <table class="dt-responsive table border-top data-table" id="cargo_datatable">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Chit No</th>
                            <th>Application No</th>
                            <th>Vehicle No</th>
                            <th>Cargo code</th>
                            <th>Cargo</th>
                            <th>Cargo Type</th>
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

    $(document).ready(function () {
            // get_datatable();
        });
        // $(document).ready(function () {
        function get_datatable() {
            // Check if the DataTable is already initialized and destroy it
            if ($.fn.DataTable.isDataTable('#cargo_datatable')) {
                $('#cargo_datatable').DataTable().clear().destroy();  // Clear and destroy the existing instance
                $('#cargo_datatable tbody').empty();  // Clear the table body to avoid appending new rows
            }

            // Reinitialize the DataTable
            var table = $('#cargo_datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('cargo_ei.data') }}", // Route to fetch filtered data
                    data: function (d) {
                        // Pass filter values to the server
                        d.from_date = $('#from_date').val();
                        d.to_date = $('#to_date').val();
                        d.firm_type = $('#firm_type').val();
                        d.name = $('#name').val();
                        d.reg_no = $('#reg_no').val();
                    }
                },
                columns: [
                    {
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
                        data: 'firms',
                        name: 'firms'
                    },
                    {
                        data: 'company_reg_no',
                        name: 'company_reg_no'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
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
                responsive: true,
                destroy: true,  // Ensure proper destruction of previous instances
            });
        }

        // });
    </script>

    @endsection