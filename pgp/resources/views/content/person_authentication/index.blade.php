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
</style>
<div class="col-sm-6">
    <h4 class=""><span class="text-muted fw-light">Authentication /</span>Person Authentication </h4>
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
                <label class="form-label">Name</label>
                <input type="text" class="form-control" value="">
            </div>

            <div class="col-2"><label for="example-select" class="form-label">Status Filter</label><select
                    name="taluk_name" id="taluk_name" class="select2 form-control" required>
                    <option value="">Status Filter</option>
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Reject">Reject</option>
                </select></div>
            <div class="col-md-2 align-self-center mt-3">
                <div class="page-title-right"><button class="btn btn-primary">GO</button></div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="card-datatable table-responsive">
            <table id="usertypeTable" class="table table-bordered dt-responsive nowrap">
                <thead>
                    <tr>
                        <th>S.No</th>

                        <th>REG NO</th>
                        <th>REG Date</th>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Aadhaar/ Driving License No</th>
                        <th>Mobile No</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>

                        <td>R123</td>
                        <td>21-09-2024</td>
                        <td>Harish</td>
                        <td>Port Officer</td>
                        <td>908765432101</td>
                        <td>9089765432</td>
                        <td>Approved</td>


                        <td><button class="btn btn-primary btn-right btn-small"
                                onclick="window.location.href='{{ route('person_authentication_add') }}'"
                                type="button"><i class="i-Add"></i><span class="lab_add">Authenticate</span></button>
                        </td>
                    </tr>
                    <!-- <tr>
                        <td>2</td>

                        <td>20.09.2024</td>
                        <td>ID-7865</td>
                        <td>Rohit</td>
                        <td>Company 2</td>
                        <td>Muthu</td>
                        <td>Rejected</td>
                        <td>12.03.2024</td>
                        <td>01.08.2032</td>
                        <td><button class="btn btn-primary btn-right btn-small"
                                onclick="window.location.href='{{ route('person_authentication_add') }}'"
                                type="button"><i class="i-Add"></i><span class="lab_add">Authenticate</span>
                            </button>
                        </td>
                    </tr> -->
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
// $(document).ready(function() {
</script>@endsection