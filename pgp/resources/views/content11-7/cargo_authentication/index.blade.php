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
    <h4 class=""><span class="text-muted fw-light">Approvals /</span>Cargo/Container </h4>
</div>

<div class="card">
    <div class="card-body">
        <div class="row">

            <div class="col-sm-2 mt-3"> <label class="form-label me-2" for="transaction_type">Application No</label>
            </div>
            <!-- <div class="col-sm-2 mt-3">
                <input type="text" class="form-control me-4" id="category_type" name="category_type" />
            </div>
            <div class="col-sm-2 mt-3"> <label class="form-label me-2" for="transaction_type">Cargo Code/Cargo</label>
            </div> -->
            <div class="col-sm-2 mt-3">
                <input type="text" class="form-control me-4" id="category_type" name="category_type" />
            </div>
            <div class="col-sm-2 mt-3"> <label class="form-label me-2" for="transaction_type">Vehicle No</label></div>
            <div class="col-sm-2 mt-3">
                <input type="text" class="form-control me-4" id="category_type" name="category_type" />
            </div>
            <!-- <div class="col-sm-2 mt-3">
                <button class="btn btn-primary btn-right md-2" type="button">
                    <i class="i-Add"></i> GO
                </button>
            </div> -->
            <div class="col-md-2 align-self-center mt-3">
                <div class="page-title-right"><button class="btn btn-primary" id="filterBtn">GO</button></div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="card-datatable table-responsive">
            <table id="cargo_datatabl" class="table table-bordered dt-responsive nowrap">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Transaction Type</th>
                        <th>Export/Import No</th>
                        <th>Export/Import Name</th>
                        <th>Cargo Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Import</td>
                        <td>demo</td>
                        <td>demo</td>
                        <td>demo</td>
                        <td>

                            <button class="btn btn-primary btn-right btn-small"
                                onclick="window.location.href='{{ route('cargo_authentication_add', ['id' => 1]) }}'">
                                Approval
                            </button>
                        </td>

                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Export</td>
                        <td>test</td>
                        <td>test</td>
                        <td>test</td>
                        <td>

                            <button class="btn btn-primary btn-right btn-small"
                                onclick="window.location.href='{{ route('cargo_authentication_add', ['id' => 2]) }}'">
                                Approval
                            </button>
                        </td>
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

<!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script> -->



<script type="text/javascript" class="init">
$(document).ready(function() {
    get_datatable();

});
// $(document).ready(function () {
function get_datatable() {
    // Check if the DataTable is already initialized and destroy it
    if ($.fn.DataTable.isDataTable('#cargo_datatable')) {
        $('#cargo_datatable').DataTable().clear().destroy(); // Clear and destroy the existing instance
        $('#cargo_datatable tbody').empty(); // Clear the table body to avoid appending new rows
    }

    // Reinitialize the DataTable
    var table = $('#cargo_datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{route('cargo_ei.data')}}", // Route to fetch filtered data
            data: function(d) {
                // Pass filter values to the server
                d.from_date = $('#from_date').val();
                d.to_date = $('#to_date').val();
                d.firm_type = $('#firm_type').val();
                d.name = $('#name').val();
                d.reg_no = $('#reg_no').val();
            }
        },
        columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            }, // S.No column
            {
                data: 'transaction_type',
                name: 'transaction_type',
                render: function(data, type, row) {
                    if (data == 1) {
                        return 'Export';
                    } else if (data == 2) {
                        return 'Import';
                    }
                }
            },
            {
                data: 'exp_imp_no',
                name: 'exp_imp_no'
            },
            {
                data: 'exp_imp_name',
                name: 'exp_imp_name'
            },
            {
                data: 'cargo_name',
                name: 'cargo_name'
            },

            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
        ],
        responsive: true,
        destroy: true, // Ensure proper destruction of previous instances
    });
}
</script>
@endsection