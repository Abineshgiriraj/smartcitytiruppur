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
            <span class="text-muted fw-light">User Firms /</span>Port Vehicle Registration
        </h4>
    </div>
    <div class="col-sm-6">
        <button class="btn btn-primary btn-right "
            onclick="window.location.href='{{ route('port_vehicle_registration_add') }}'" type="button">
            <i class="i-Add"></i> Add New
        </button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-5 d-flex align-items-center">
                <label class="form-label" style="width: 250px;">
                    Vehicle NO
                </label>
                <input type="text" class="form-control">
            </div>
            <div class=" col-md-3">
                <button type="button" class="btn btn-primary">GO</button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="card-datatable table-responsive">
            <table id="usertypeTable" class="table table-bordered dt-responsive nowrap">
                <thead>
                    <tr>
                        <th>S.No</th> <!-- Added S.No column -->
                        <th>Vehicle Id</th>
                        <th>Vehicle Number</th>
                        <th>Vehicle Type</th>
                        <th>PUC Date</th>
                        <th>Fitness</th>
                        <th>Insurance</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <td>1</td> <!-- Added S.No column -->
                    <td>HAV4654654465</td>
                    <td>TN67-J 3401</td>
                    <td>Heavy</td>
                    <td>30-02-2026</td>
                    <td>21-09-2024</td>
                    <td>01-10-2025</td>

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