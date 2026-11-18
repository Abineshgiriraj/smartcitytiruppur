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
            <span class="text-muted fw-light">Authentication /</span> Vehicle Authentication
        </h4>
    </div>

</div>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-2">
                <label class="form-label">From Date</label>
                <input type="date" class="form-control" value="">
            </div>
            <div class="col-2">
                <label class="form-label">To Date</label>
                <input type="date" class="form-control" value="">
            </div>
            <div class="col-2">
                <label class="form-label">Reg No</label>
                <input type="text" class="form-control" value="">
            </div>
            <div class="col-2">
                <label class="form-label">Vehicle No</label>
                <input type="text" class="form-control" value="">
            </div>


            <div class="col-2">
                <label for="example-select" class="form-label">Status Filter</label>
                <select name="taluk_name" id="taluk_name" class="select2 form-control" required>
                    <option value="">Status Filter</option>
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Reject">Reject</option>
                </select>
            </div>

            <div class="col-md-2 align-self-center mt-3">
                <div class="page-title-right">
                    <button class="btn btn-primary">GO</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="card-datatable table-responsive">

            <table id="usertypeTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>S.No</th> <!-- Added S.No column -->
                        <th>Vehicle No.</th>
                        <th>Type of Vehicles</th>
                        <th>Reg Date</th>
                        <th>Reg No</th>
                        <th>Insurance No</th>
                        <th>PUC No</th>
                        <th>Status</th>

                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td> <!-- Added S.No column -->
                        <td>TN32 js 4090</td>
                        <td>Heavy</td>
                        <td>21-08-2024</td>
                        <td>R345677</td>
                        <td>INS2345</td>
                        <td>90832</td>
                        <td>Pending</td>

                        <td>
                            <button class="btn btn-primary btn-right btn-small"
                                onclick="window.location.href='{{ route('vehical_authentication_add') }}'"
                                type="button">
                                <i class="i-Add"></i> <span class="lab_add">Authenticate</span>
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
<script>

</script>

@endsection