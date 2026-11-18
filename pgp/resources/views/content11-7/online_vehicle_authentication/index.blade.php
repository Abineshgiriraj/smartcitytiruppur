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
            <span class="text-muted fw-light">Port OPeration System /</span>Online Vehicle Approval
        </h4>
    </div>
    <div class="col-sm-6">
        <!-- <button class="btn btn-primary btn-right" onclick="window.location.href='{{ route('rate_master_add')}}'"
            type="button">
            <i class="i-Add"></i> Add New -->
        <!-- </button> -->
    </div>
</div>

<!-- Responsive Datatable -->
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
                <label class="form-label">Reg NO</label>
                <input type="text" class="form-control" value="">
            </div>
            <div class="col-2">
                <label class="form-label">Name</label>
                <input type="text" class="form-control" value="">
            </div>

            <div class="col-2 mt-4">
                <button type="button" class="btn btn-primary">GO</button>
            </div>

        </div>
    </div>
    <div class="card-datatable table-responsive">
        <table class="dt-responsive table border-top data-table">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Vehicle No</th>
                    <th>Type Of Vehicles</th>
                    <th>Reg Date</th>
                    <th>Reg. No</th>
                    <th>Insurance No </th>
                    <th>PUC No</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="categoryTableBody">
                <tr>
                    <td>1</td>
                    <td>R12345</td>
                    <td>Light Vehicle</td>
                    <td>30-03-2024</td>
                    <td>R112345</td>
                    <td>INS1123</td>
                    <td>9087654321</td>
                    <td>Pending</td>

                    <td>
                        <div class="d-flex align-items-center">
                            <button type="button" class="btn btn-primary"
                                onclick="window.location.href='{{route('online_vehicle_authentication_add')}}'">Approve</button>
                            <!-- <a href=" #" class="text-primary mr-2">
                                    <i class="fas fa-edit"></i>
                                    </a> -->
                            <span class="mx-1"></span>
                            <!--    <a href="#" class="text-danger mr-2">
                                    <i class="fas fa-trash-alt"></i>
                                </a> -->
                        </div>
                    </td>
                </tr>
            </tbody>

        </table>
    </div>
</div>
</div>


@endsection