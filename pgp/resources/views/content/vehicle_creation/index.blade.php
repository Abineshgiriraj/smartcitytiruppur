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
            <span class="text-muted fw-light">Port OPeration System /</span>Vehicle Creation
        </h4>
    </div>
    <div class="col-sm-6">
        <button class="btn btn-primary btn-right" onclick="window.location.href='{{ url('/vehicle_creation_add')}}'"
            type="button">
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
                        <th>Transaction Type</th>
                        <th>IA / EA No</th>
                        <th>Name Of Cargo</th>
                        <th>Agent</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="categoryTableBody">
                    <tr>
                        <td>1</td>
                        <td>Export</td>
                        <td>EA123</td>
                        <td>Titanic</td>
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