@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('public/assets/vendor/libs/apex-charts/apex-charts.css') }}">
@endsection

@section('vendor-script')
<script src="{{ asset('public/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
@endsection

@section('page-script')
<script src="{{ asset('public/assets/js/dashboards-analytics.js') }}"></script>
@endsection

@section('content')
<h4 class="py-3 mb-1 main-hed">Grievance Details</h4>

<div class="row">
    <div class="col-sm-6 col-lg-2 mb-4">
        <div class="card card-border-shadow-primary h-100 sch-man">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                    <div class="avatar me-2">
                        <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-bowl-hot"></i></span>
                    </div>
                    <h4 class="ms-1 mb-0 info-text">Opening</h4>
                </div>
                <hr>
                
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-2 mb-4">
        <div class="card card-border-shadow-success h-100 sch-man">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                    <div class="avatar me-2">
                       <span class="avatar-initial rounded bg-label-success"><i class="icon-base bx bx-check-circle icon-lg"></i></span>
                    </div>
                    <h4 class="ms-1 mb-0 info-text">Registered</h4>
                </div>
                <hr>
                
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-2 mb-4">
        <div class="card card-border-shadow-info h-100 sch-man">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                    <div class="avatar me-2">
                        <span class="avatar-initial rounded bg-label-info"><i class="icon-base bx bx-time-five icon-lg"></i></span>
                    </div>
                    <h4 class="ms-1 mb-0 info-text">Processing</h4>
                </div>
                <hr>
                
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-2 mb-4">
        <div class="card card-border-shadow-danger h-100 sch-man">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                    <div class="avatar me-2">
                        <span class="avatar-initial rounded bg-label-danger"><i class="bx bxs-pizza"></i></span>
                    </div>
                    <h4 class="ms-1 mb-0 info-text">Completed</h4>
                </div>
                <hr>
                
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-2 mb-4">
        <div class="card card-border-shadow-info h-100 sch-man">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                    <div class="avatar me-2">
                        <span class="avatar-initial rounded bg-label-info"><i class="bx bxs-log-out-circle"></i></span>
                    </div>
                    <h4 class="ms-1 mb-0 info-text">Rejected</h4>
                </div>
                <hr>
               
            </div>
        </div>
    </div>

<div class="col-sm-12 col-lg-12 col-12">
    <div class="card">
        <h5 class="card-header"> Department Wise Report</h5>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                        <th>Department</th>
                        <th>Opening</th>
                        <th>Registered</th>
                        <th>Processing</th>
                        <th>Completed</th>
                        <th>Rejected</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        <td><span class="badge badge-dot text-bg-primary me-1" style="width: 6px; height:6px;"></span> <span>Accounts</span></td>
                        <td>Albert Cook</td>
                        <td>Barry Hunter</td>
                        <td>Barry Hunter</td>
                        <td><span class="badge bg-label-primary me-1">Active</span></td>
                        <td>
                            <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="icon-base bx bx-dots-vertical-rounded"></i></button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="javascript:void(0);"><i class="icon-base bx bx-edit-alt me-1"></i> Edit</a>
                                <a class="dropdown-item" href="javascript:void(0);"><i class="icon-base bx bx-trash me-1"></i>
                                Delete</a>
                            </div>
                            </div>
                        </td>
                        </tr>
                        <tr>
                        <td><span class="badge badge-dot text-bg-primary me-1" ></span> <span>eGovernance</span></td>
                        <td>eGovernance</td>
                        <td>Barry Hunter</td>
                        <td>Barry Hunter</td>
                        <td><span class="badge bg-label-success me-1">Completed</span></td>
                        <td>
                            <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="icon-base bx bx-dots-vertical-rounded"></i></button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="javascript:void(0);"><i class="icon-base bx bx-edit-alt me-1"></i> Edit</a>
                                <a class="dropdown-item" href="javascript:void(0);"><i class="icon-base bx bx-trash me-1"></i>
                                Delete</a>
                            </div>
                            </div>
                        </td>
                        </tr>
                        <tr>
                        <td><span class="badge badge-dot text-bg-primary me-1"></span> <span>Engineering - Sewage</span></td>
                        <td>Trevor Baker</td>
                        <td>Barry Hunter</td>
                        <td>Barry Hunter</td>
                        <td><span class="badge bg-label-info me-1">Scheduled</span></td>
                        <td>
                            <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="icon-base bx bx-dots-vertical-rounded"></i></button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="javascript:void(0);"><i class="icon-base bx bx-edit-alt me-1"></i> Edit</a>
                                <a class="dropdown-item" href="javascript:void(0);"><i class="icon-base bx bx-trash me-1"></i>
                                Delete</a>
                            </div>
                            </div>
                        </td>
                        </tr>
                        <tr>
                        <td><span class="badge badge-dot text-bg-primary me-1"></span> <span>Engineering - Street light</span>
                        </td>
                        <td>Jerry Milton</td>
                        <td>Barry Hunter</td>
                        <td>Barry Hunter</td>
                        <td><span class="badge bg-label-warning me-1">Pending</span></td>
                        <td>
                            <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="icon-base bx bx-dots-vertical-rounded"></i></button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="javascript:void(0);"><i class="icon-base bx bx-edit-alt me-1"></i> Edit</a>
                                <a class="dropdown-item" href="javascript:void(0);"><i class="icon-base bx bx-trash me-1"></i>
                                Delete</a>
                            </div>
                            </div>
                        </td>
                        </tr>
                        <tr>
                        <td><span class="badge badge-dot text-bg-primary me-1"></span> <span>Engineering - Water Supply</span>
                        </td>
                        <td>Jerry Milton</td>
                        <td>Barry Hunter</td>
                        <td>Barry Hunter</td>
                        <td><span class="badge bg-label-warning me-1">Pending</span></td>
                        <td>
                            <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="icon-base bx bx-dots-vertical-rounded"></i></button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="javascript:void(0);"><i class="icon-base bx bx-edit-alt me-1"></i> Edit</a>
                                <a class="dropdown-item" href="javascript:void(0);"><i class="icon-base bx bx-trash me-1"></i>
                                Delete</a>
                            </div>
                            </div>
                        </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
  </div>
</div>

</div> 

@endsection

