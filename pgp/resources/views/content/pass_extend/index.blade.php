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
            <span class="text-muted fw-light">Pass /</span>Pass Extend
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
        <div class="row">
            <div class="col-2">
                <label class="form-label">Pass Id</label>
                <input type="text" class="form-control" value="">
            </div>
            <div class="col-2">
                <label class="form-label">Card number</label>
                <input type="text" class="form-control" value="">
            </div>
            <div class="col-2">
                <label for="nationalitySelect" class="form-label">Holder Name</label>
                <input type="text" class="form-control" value="">

                <!-- <select class="form-select" id="nationalitySelect" name="nationality">
                    <option selected>Select Firms</option>
                    <option value="Stevedore">Stevedore</option>
                    <option value="Ship Agent">Ship Agent</option>
                    <option value="Custom Broker">Custom Broker</option>
                    <option value="CFS">CFS (Container Freight Station)</option>
                </select> -->
            </div>
            <!-- <div class="col-2">
                <label class="form-label">Name</label>
                <input type="text" class="form-control" value="">
            </div>
            <div class="col-2">
                <label class="form-label">Reg.No</label>
                <input type="text" class="form-control" value="">
            </div> -->
            <div class="col-2 mt-4">
                <button type="button" class="btn btn-primary">Go</button>
            </div>


        </div>
        <div class="card-body">

            <div class="card-datatable table-responsive">
                <table class="dt-responsive table border-top data-table">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Pass Id</th>
                            <th>CardNo</th>
                            <th>Holder Name</th>
                            <th>From Date</th>
                            <th>To Date</th>
                            <th>Extend Days</th>
                            <!-- <th>Approval</th> -->
                            <th>Extend Counter</th>
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
                            <td>100</td>

                            <td>
                                <div class="d-flex align-items-center">
                                    <button type="button" class="btn btn-primary"
                                        onclick="window.location.href='{{route('pass_extend')}}'">Extend</button>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </tbody>

                </table>
            </div>
        </div>
    </div>


    @endsection