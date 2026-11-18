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
            <span class="text-muted fw-light">Port OPeration System /</span>Rate Master Approval
        </h4>
    </div>
    <div class="col-sm-6">
        <!-- <button class="btn btn-primary btn-right" onclick="window.location.href='{{ route('rate_master_add')}}'"
            type="button">
            <i class="i-Add"></i> Add New -->
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
                        <th>Access Gate</th>
                        <th>Allowed Type</th>
                        <th>Vehicle Type</th>
                        <th>Person Type</th>
                        <th>Validity</th>
                        <th>Rate</th>
                        <!-- <th>Approval</th> -->
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="categoryTableBody">
                    <tr>
                        <td>1</td>
                        <td>Inside</td>
                        <td>Vehicle</td>
                        <td>Light</td>
                        <td>Port Person</td>
                        <td>Half Year</td>
                        <td>1200</td>
                        <!-- <td>Account Officer</td> -->
                        <td>Pending</td>

                        <td>
                            <div class="d-flex align-items-center">
                                <button type="button" class="btn btn-primary"
                                    onclick="window.location.href='{{route('rate_master_approval_add')}}'">Approve</button>
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