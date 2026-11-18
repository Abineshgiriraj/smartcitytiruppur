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
            <span class="text-muted fw-light">Port Operation System /</span>Assign Cargo
        </h4>
    </div>
    <!-- <div class="col-sm-6">
        <button class="btn btn-primary btn-right" onclick="window.location.href='{{ url('/new_cargo_add')}}'"
            type="button">
            <i class="i-Add"></i> Add New
        </button>
    </div> -->
</div>

<!-- Responsive Datatable -->
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-2">
                <label class="form-label">From Date:</label>
                <input type="date" class="form-control" name="from_date" id="from_date"></input>
            </div>
            <div class="col-md-2">
                <label class="form-label">To Date:</label>
                <input type="date" class="form-control" name="to_date" id="to_date"></input>
            </div>
            <div class="col-md-2">
                <label class="form-label">IA/EA No:</label>
                <input type="text" class="form-control" name="ieno" id="ieno"></input>
            </div>
            <div class="col-md-2">
                <label class="form-label">Vessel Name:</label>
                <input type="text" class="form-control" name="ieno" id="ieno"></input>
            </div>
            <div class="col-md-2">
                <label class="form-label">Cargo</label>
                <input type="text" class="form-control" name="ieno" id="ieno"></input>
            </div>
            <div class="col-md-2">
                <label class="form-label">CHA Name</label>
                <input type="text" class="form-control" name="ieno" id="ieno"></input>
            </div>
            <div class="col-md-2 mt-4">
                <button type="button" class="btn btn-primary">GO</button>
            </div>
        </div>

        <div class=" card-body">
            <div class="card-datatable table-responsive">
                <table class="dt-responsive table border-top data-table">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>IA/EA No</th>
                            <th>Vessel No</th>
                            <th>Vessel Name</th>
                            <th>CHA Name</th>
                            <th>Cargo</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="categoryTableBody">
                        <tr>
                            <td>1</td>
                            <td>IN1234</td>
                            <td>EA123</td>
                            <td>Test</td>
                            <td>Deva Agency</td>
                            <td>Test Cargo</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <a href="#" class="text-primary mr-2">
                                        <i class="fas fa-edit"></i> <!-- Edit Icon -->
                                    </a>
                                    <span class="mx-1"></span>

                                    <!-- <a href="#" class="text-danger mr-2">
                                        <i class="fas fa-trash-alt"></i>
                                    </a> -->
                                    <a href="#" class="text-primary mr-3">
                                        <i class="fas fa-eye"></i> <!-- Edit Icon -->
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