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
            <span class="text-muted fw-light">Port OPeration System /</span>Container
        </h4>
    </div>
    <div class="col-sm-6">
        <button class="btn btn-primary btn-right" onclick="window.location.href='{{ url('/new_container_add')}}'"
            type="button">
            <i class="i-Add"></i> Add New
        </button>
    </div>
</div>
<div class="card">

    <div class="card-body"><div class="row">
    <div class="col-sm-2 mt-3">  <label class="form-label me-2" for="transaction_type">EIR No</label></div>
                    <div class="col-sm-2 mt-3">
                    <input type="text" class="form-control me-4" id="category_type" name="category_type" />
                        </div>
                        <div class="col-sm-2 mt-3">  <label class="form-label me-2" for="transaction_type">Container No</label></div>
                    <div class="col-sm-2 mt-3">
                     <input type="text" class="form-control me-4" id="category_type" name="category_type" />
                        </div>
                        <div class="col-sm-2 mt-3">  <label class="form-label me-2" for="transaction_type">Vehicle No</label></div>
                    <div class="col-sm-2 mt-3">
                     <input type="text" class="form-control me-4" id="category_type" name="category_type" />
                        </div>
                        <div class="col-sm-2 mt-3">  <label class="form-label me-2" for="transaction_type">Liner Code</label></div>
                    <div class="col-sm-2 mt-3">
                     <input type="text" class="form-control me-4" id="category_type" name="category_type" />
                        </div>
                        <div class="col-sm-2 mt-3">
                        <button class="btn btn-primary btn-right md-2" type="button">
            <i class="i-Add"></i> GO
        </button>
        </div>
                        </div>
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
                        <th>EIR No</th>
                        <th>Vehicle No</th>
                        <th>Container No</th>
                        <th>Container Size</th>
                        <th>Quantity</th>
                        <th>Container Type</th>
                        <th>Liner Code</th>
                        <th>Applied By</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="categoryTableBody">
                    <tr>
                        <td>1</td>
                        <td>EIR123</td>
                        <td>EA123</td>
                        <td>CAD233</td>
                        <td>23</td>
                        <td>45</td>
                        <td>1000</td>
                        <td>Lkh677</td>
                        <td>Deva Agency</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <a href="#" class="text-primary mr-2">
                                    <i class="fas fa-edit"></i> <!-- Edit Icon -->
                                </a>
                                <span class="mx-1"></span>
                                <a href="#" class="text-danger mr-2">
                                    <i class="fas fa-trash-alt"></i> <!-- Delete Icon -->
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
