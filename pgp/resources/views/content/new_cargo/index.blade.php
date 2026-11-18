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
            <span class="text-muted fw-light">Port OPeration System /</span>Cargo Import/Export Registration
        </h4>
    </div>
    <div class="col-sm-6">
        <button class="btn btn-primary btn-right" onclick="window.location.href='{{ url('/new_cargo_add')}}'"
            type="button">
            <i class="i-Add"></i> Add New
        </button>
    </div>
</div>

<!-- Responsive Datatable -->
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-2 mt-3"> <label class="form-label me-2" for="transaction_type">Chit No</label></div>
            <div class="col-sm-2 mt-3">
                <input type="text" class="form-control me-4" id="category_type" name="category_type" />
            </div>
            <div class="col-sm-2 mt-3"> <label class="form-label me-2" for="transaction_type">Application No</label>
            </div>
            <div class="col-sm-2 mt-3">
                <input type="text" class="form-control me-4" id="category_type" name="category_type" />
            </div>
            <div class="col-sm-2 mt-3"> <label class="form-label me-2" for="transaction_type">Cargo Code/Cargo</label>
            </div>
            <div class="col-sm-2 mt-3">
                <input type="text" class="form-control me-4" id="category_type" name="category_type" />
            </div>
            <div class="col-sm-2 mt-3"> <label class="form-label me-2" for="transaction_type">Vehicle No</label></div>
            <div class="col-sm-2 mt-3">
                <input type="text" class="form-control me-4" id="category_type" name="category_type" />
            </div>
            <div class="col-sm-2 mt-3">
                <button class="btn btn-primary btn-right md-2" type="button">
                    <i class="i-Add"></i> GO
                </button>
            </div>
        </div>
        <div class=" card-body">

            <div class="card-datatable table-responsive">
                <table class="dt-responsive table border-top data-table">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Chit No</th>
                            <th>Application No</th>
                            <th>Vehicle No</th>
                            <th>Cargo code</th>
                            <th>Cargo</th>
                            <th>Cargo Type</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="categoryTableBody">
                        <tr>
                            <td>1</td>
                            <td>IN1234</td>
                            <td>EA123</td>
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
                                    <a href="#" class="text-danger mr-2">
                                        <i class="fas fa-trash-alt"></i> <!-- Delete Icon -->
                                    </a>
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