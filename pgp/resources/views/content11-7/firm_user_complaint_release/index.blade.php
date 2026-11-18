@extends('layouts/contentNavbarLayout')

@section('title', 'Tables - vehicle registration')

@section('content')
<style>
/* btn style */
.btn-right {
    float: right;
}

.btn-medium-custom {
    padding: 6px 12px;
    font-size: 14px;
}

.btn-small {
    /* width: auto; */
    white-space: nowrap;
    padding: 0;
    /* Remove padding */
    margin: 0;
    display: inline-flex;
    /* Align content properly */
    align-items: center;
    width: 55px;
    /* Center icons and text vertically */
    /* Remove margin */
}

.lab_add {
    white-space: nowrap;
    font-size: 10px;
}
</style>
<div class="row mb-3 page-hed">
    <div class="col-sm-6">
        <h4 class="">
            <span class="text-muted fw-light">Complaint /</span> Firm User Complaint Release
        </h4>
    </div>
    <div class="col-sm-6">
        <!-- <button class="btn btn-primary btn-right "
            onclick="window.location.href='{{ route('firm_user_complaint_release_add') }}'" type="button">
            <i class="i-Add"></i> Add New
        </button> -->
    </div>
</div>
<!-- 
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
            <div class="col-3">
                <label class="form-label" style="width: 250px;">
                    Vehicle NO
                </label>
                <input type="text" class="form-control">
            </div>
            <div class="col-3 mt-4">
                <button type="button" class="btn btn-primary">GO</button>
            </div>
        </div>
    </div> -->
    <div class="card">
    <div class="card-body">
        <div class="card-datatable table-responsive">
            <table id="usertypeTable" class="table table-bordered dt-responsive nowrap">
                <thead>
                    <tr>
                        <th>S.No</th> <!-- Added S.No column -->
                        <th>Firms User Name</th>
                        <th>Email Id</th>
                        <th>Reg Date</th>
                        <th>Reg No</th>
                        <th>Contact Person</th>
                        <th>Phone/Mobile No</th>
                        <th>Address 1</th>
                        <th>Address 2</th>
                        <th>Fax No</th>
                        <th>GSTIN</th>
                        <th>Status</th>
                        <th>Penalty Amount </th>
                        <th>Reason</th>
                       <th>Reason Option</th>
                        
                        <th>Action:</th>
                    </tr>
                </thead>
                <tbody>
                    <td>1</td> <!-- Added S.No column -->
                    <td>Jeeva</td>
                    <td>jeeva@123</td>
                    <td>01-10-2024</td>
                    <td>12345678</td>
                    <td>test</td>
                    <td>9787463299</td>
                    <td>tiruppur-638660</td>
                    <td>erode-638660</td>
                    <td>FX-123</td>
                    <td>4666</td>
                    <td>Pending</td>
                    <td>100</td>
                    <td>bad</td>
                    <td>Good</td>
                    <td> <button class="btn btn-primary btn-small"
                                onclick="window.location.href='{{ route('firm_user_complaint_release_add') }}'" type="button">Release</td>

                    
                   

                    <td>
                        <div class="d-flex align-items-center">
                            <a href="#" class="text-primary mr-2">
                                <i class="fas fa-edit"></i> <!-- Edit Icon -->
                            </a>
                            <span class="mx-1"></span>
                            <a href="#" class="text-danger mr-2">
                                <i class="fas fa-trash-alt"></i> <!-- Delete Icon -->
                            </a>
                            <span class="mx-2"> </span>
                            <a href="#" class="text-primary mr-2">
                                <i class="fas fa-eye"></i> <!-- Edit Icon -->
                            </a>
                            <span class="mx-3"></span>

                            <button class="btn btn-primary btn-small"
                                onclick="window.location.href='{{ route('vehicle_renewal') }}'" type="button">
                                <i class="i-Add"></i>
                                <span class="lab_add">Renewal</span>
                            </button>
                        </div>
                    </td>


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
</script>

@endsection