@extends('layouts/contentNavbarLayout')

@section('title', 'Tables - Academic Year Creation')

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
            <span class="text-muted fw-light">Port Employees/</span> Employee Registration
        </h4>
    </div>
    <div class="col-sm-6">
        <button class="btn btn-primary btn-right" onclick="window.location='{{ route('employee_registration_add') }}'"
            type="button">
            <i class="i-Add"></i> Add New
        </button>
    </div>
</div>

<!-- Responsive Datatable -->
<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-sm-3">
                <select class="form-select" id="idproofSelect" name="idproof">
                    <option selected>Select Designation</option>
                    <option value="Manager">Manager</option>
                    <option value="Employee">Employee</option>
                    <option value="Staff">Staff</option>
                    <option value="Sales Manager">Sales Manager</option>
                    <option value="Driver">Driver</option>
                    <option value="Cleaner">Cleaner</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="id_no" name="id_no" placeholder="Proof/Aadhar No" />
            </div>
            <div class="col-sm-2">
                <button id="openTableButton" class="btn btn-primary">GO</button>

            </div>
        </div>
        <div class="card-datatable table-responsive">
            <table class="table table-bordered dt-responsive nowrap" id="employee_designation">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Designation</th>
                        <th>Proof/Aadhar No</th>
                        <th>Mobile No</th>
                        <th>Description</th>
                        <th>Actions</th>

                    </tr>
                </thead>
                <tbody>
                    <td>1</td>
                    <td>Supervisor</td>
                    <td>908765432123</td>
                    <td>9089098765</td>
                    <td>Supervising the all department</td>
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
                            <button class="btn btn-primary btn-small"
                                onclick="window.location.href='{{ route('employee_renewal') }}'" type="button">
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
<!--/ Responsive Datatable -->

<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>


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

@endsection