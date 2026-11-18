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
            <span class="text-muted fw-light">Port OPeration System /</span>Vehicle Complaint Registration
        </h4>
    </div>
    <div class="col-sm-6">
        <button class="btn btn-primary btn-right"
            onclick="window.location.href='{{ url('/vehicle_complaint_registration_add') }}'" type="button">
            <i class="i-Add"></i> Add New
        </button>
    </div>

</div>

<!-- Responsive Datatable -->
<div class="card">
    <div class="card-body">
        <div class="card-datatable table-responsive">
            <table class="dt-responsive table border-top data-table">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Vehicle No</th>
                        <th>Reg No</th>
                        <th>Reg Date</th>
                        <th>Aadhaar No</th>
                        <th>DL No</th>
                        <th>Driver Name</th>
                        <th>Release Option</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="categoryTableBody">
                    <tr>
                        <td>1</td>
                        <td>TN45 JS 6789</td>
                        <td>RT6789</td>
                        <td>23-08-2024</td>
                        <td>908765432123</td>
                        <td>D2356</td>
                        <td>Ramesh</td>
                        <td> <button class="btn btn-primary btn-small" type="button">Release</td>

                        <!-- <td>Account Officer</td> -->


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
                            </div>
                        </td>
                    </tr>
                </tbody>

            </table>
        </div>
    </div>
</div>


@endsection