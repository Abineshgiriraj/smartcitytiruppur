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
            <span class="text-muted fw-light">Settings / </span>Port Validity With Fee
        </h4>
    </div>
    <div class="col-sm-6">
        <button class="btn btn-primary btn-right"
            onclick="window.location.href='{{ url('/port_permit_validation_with_fee_add')}}'" type="button">
            <i class="i-Add"></i> Add New
        </button>
    </div>
</div>
<!-- Responsive Datatable -->
<div class="card">
    <div class="card-body">
        <div class="card-datatable table-responsive">
            <!-- <table class="dt-responsive table border-top data-table"> -->
            <table class="table table-bordered dt-responsive nowrap">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Category</th>
                        <th>Sub Category</th>
                        <th>Area Access</th>
                        <th>Validity</th>
                        <th>Fee</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="categoryTableBody">
                    <tr>
                        <td>1</td>
                        <td>Vehicle</td>
                        <td>Heavy</td>
                        <td>Inside Custom Bound Area</td>
                        <td>Yearly</td>
                        <td>9,000</td>
                        <!-- <td> -->
                        <!-- Edit Button -->
                        <!-- <button type="button" class="btn btn-primary"
                                onclick="window.location.href='{{ url('/port_permit_validation_with_fee_add')}}'">Edit</button>
                            <button type="submit" class="btn btn-danger"
                                onclick="return confirm('Are you sure you want to delete this item?');">Delete</button>
                            </form> -->
                        <td>
                            <a href="#" class="text-primary">
                                <i class="fas fa-edit"></i> <!-- Edit Icon -->
                            </a>
                            <!-- <button class="btn btn-primary  btn-medium-custom btn-edit">Edit</button> -->
                            <a href="#" class="text-danger ml-2">
                                <i class="fas fa-trash-alt"></i> <!-- Delete Icon -->
                            </a>
                        </td>
                        <!-- </td> -->
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!--/ Responsive Datatable -->

<!-- </div> -->
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