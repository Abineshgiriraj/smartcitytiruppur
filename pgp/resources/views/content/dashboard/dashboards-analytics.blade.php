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
<style>
  .layout-navbar{ box-shadow: none;;}
  .nav-pills .nav-link.active, .nav-pills .show>.nav-link{background-color: #05689e;}
  thead, tr th {    background-color: #dae5eb !important;}
    table tr:nth-child(even) {    background-color: #eff7fb;}
</style>

<div class="container-xxl flex-grow-1 container-p">
              <div class="row">

                <!-- Total Revenue -->
                <div class="col-12 col-lg-8 order-2 order-md-3 order-lg-2 mb-4">
                  <div class="card">
                    <div class="row row-bordered g-0">
                      <div class="col-md-8">
                        <h5 class="card-header m-0 me-2 pb-3">Total Complaint Report</h5>
                        <div id="totalRevenueChart" class="px-2"></div>
                      </div>
                      <div class="col-md-4">
                        <div class="card-body">
                          <div class="text-center">
                            <div class="dropdown">
                              <button
                                class="btn btn-sm btn-outline-primary dropdown-toggle"
                                type="button"
                                id="growthReportId"
                                data-bs-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false"
                              >
                                2025
                              </button>
                              <div class="dropdown-menu dropdown-menu-end" aria-labelledby="growthReportId">
                                <a class="dropdown-item" href="javascript:void(0);">2021</a>
                                <a class="dropdown-item" href="javascript:void(0);">2020</a>
                                <a class="dropdown-item" href="javascript:void(0);">2019</a>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div id="growthChart"></div>
                        <div class="text-center fw-semibold pt-3 mb-2">78% Completed </div>

                        <div class="d-flex px-xxl-4 px-lg-2 p-4 gap-xxl-3 gap-lg-1 gap-3 justify-content-between">
                          <div class="d-flex">
                            <div class="me-2">
                              <span class="badge bg-label-primary p-2"><i class="bx bx-check-circle text-primary"></i></span>
                            </div>
                            <div class="d-flex flex-column">
                              <small>2025</small>
                              <h6 class="mb-0">32.5k</h6>
                            </div>
                          </div>
                          <div class="d-flex">
                            <div class="me-2">
                              <span class="badge bg-label-info p-2"><i class="bx bx-check-circle text-info"></i></span>
                            </div>
                            <div class="d-flex flex-column">
                              <small>2024</small>
                              <h6 class="mb-0">41.2k</h6>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!--/ Total Revenue -->
                
                <div class="col-12 col-md-8 col-lg-4 order-3 order-md-2">
                  <div class="row">
                    <div class="col-6 mb-4">
                      <div class="card">
                        <div class="card-body">
                          <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                              <span class="avatar-initial rounded bg-label-primary"><i class="icon-base bx bx-user"></i></span>
                            </div>
                            <div class="dropdown">
                              <button
                                class="btn p-0"
                                type="button"
                                id="cardOpt4"
                                data-bs-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false"
                              >
                                <i class="bx bx-dots-vertical-rounded"></i>
                              </button>
                              <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt4">
                                <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                              </div>
                            </div>
                          </div>
                          <span class="fw-semibold d-block mb-1">Registered</span>
                          <h3 class="card-title text-nowrap mb-2">3,056</h3>
                          <small class="text-danger fw-semibold"><i class="bx bx-up-arrow-alt"></i> +14.82%</small>
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-6 mb-4">
                      <div class="card">
                        <div class="card-body">
                          <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                             <span class="avatar-initial rounded bg-label-warning"><i class="icon-base bx bx-info-circle"></i></span>
                            </div>
                            <div class="dropdown">
                              <button
                                class="btn p-0"
                                type="button"
                                id="cardOpt1"
                                data-bs-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false"
                              >
                                <i class="bx bx-dots-vertical-rounded"></i>
                              </button>
                              <div class="dropdown-menu" aria-labelledby="cardOpt1">
                                <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                              </div>
                            </div>
                          </div>
                          <span class="fw-semibold d-block mb-1">Pending</span>
                          <h3 class="card-title mb-2">14,857</h3>
                          <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> +28.14%</small>
                        </div>
                      </div>
                    </div>
                    <!-- </div>    <div class="row"> -->
                    <div class="col-12 mb-4">
                        <div class="row">
                      <div class="col-6 mb-4">
                      <div class="card">
                        <div class="card-body">
                          <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                              <span class="avatar-initial rounded bg-label-success"><i class="icon-base bx bx-donate-heart"></i></span>
                              
                            </div>
                            <div class="dropdown">
                              <button
                                class="btn p-0"
                                type="button"
                                id="cardOpt4"
                                data-bs-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false"
                              >
                                <i class="bx bx-dots-vertical-rounded"></i>
                              </button>
                              <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt4">
                                <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                              </div>
                            </div>
                          </div>
                          <span class="fw-semibold d-block mb-1">Completed</span>
                          <h3 class="card-title text-nowrap mb-2">2,456</h3>
                          <small class="text-danger fw-semibold"><i class="bx bx-down-arrow-alt"></i> -14.82%</small>
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-6 mb-4">
                      <div class="card">
                        <div class="card-body">
                          <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                             <span class="avatar-initial rounded bg-label-danger"><i class="bx bx-x"></i></span>
                            </div>
                            <div class="dropdown">
                              <button
                                class="btn p-0"
                                type="button"
                                id="cardOpt1"
                                data-bs-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false"
                              >
                                <i class="bx bx-dots-vertical-rounded"></i>
                              </button>
                              <div class="dropdown-menu" aria-labelledby="cardOpt1">
                                <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                              </div>
                            </div>
                          </div>
                          <span class="fw-semibold d-block mb-1">Rejected</span>
                          <h3 class="card-title mb-2">14,857</h3>
                          <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> +28.14%</small>
                        </div>
                      </div>
                    </div>
                    </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <!-- Order Statistics -->
                <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-4">
                  <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between pb-0">
                      <div class="card-title mb-0">
                        <h5 class="m-0 me-2">Pending Complaints</h5>
                        <small class="text-muted">42,098 Total Openings</small>
                      </div>
                      <div class="dropdown">
                        <button
                          class="btn p-0"
                          type="button"
                          id="orederStatistics"
                          data-bs-toggle="dropdown"
                          aria-haspopup="true"
                          aria-expanded="false"
                        >
                          <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="orederStatistics">
                          <a class="dropdown-item" href="javascript:void(0);">Select All</a>
                          <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                          <a class="dropdown-item" href="javascript:void(0);">Share</a>
                        </div>
                      </div>
                    </div>
                    <div class="card-body">
                      <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex flex-column align-items-center gap-1">
                          <h2 class="mb-2">8,258</h2>
                          <span>Total Reports</span>
                        </div>
                        <div id="orderStatisticsChart"></div>
                      </div>
                      <ul class="p-0 m-0">
                        <li class="d-flex mb-4 pb-1">
                          <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-rupee"></i></span>
                          </div>
                          <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                              <h6 class="mb-0">Accounts</h6>
                              <small class="text-muted">MSpecial Provident Fund</small>
                            </div>
                            <div class="user-progress">
                              <small class="fw-semibold">82</small>
                            </div>
                          </div>
                        </li>
                        <li class="d-flex mb-4 pb-1">
                          <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-success"><i class="bx bxs-bank"></i></span>
                          </div>
                          <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                              <h6 class="mb-0">eGovernance</h6>
                              <small class="text-muted">Online Payment</small>
                            </div>
                            <div class="user-progress">
                              <small class="fw-semibold">23</small>
                            </div>
                          </div>
                        </li>
                        <li class="d-flex mb-4 pb-1">
                          <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-info"><i class="bx bx-wrench"></i></span>
                          </div>
                          <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                              <h6 class="mb-0"> Engineering - Sewage</h6>
                              <small class="text-muted">UGD-Sewer Odor</small>
                            </div>
                            <div class="user-progress">
                              <small class="fw-semibold">84</small>
                            </div>
                          </div>
                        </li>
                        <li class="d-flex">
                          <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-secondary"><i class="bx bx-wrench"></i></span>
                          </div>
                          <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                              <h6 class="mb-0">Engineering - Street light</h6>
                              <small class="text-muted">Poor Lighting</small>
                            </div>
                            <div class="user-progress">
                              <small class="fw-semibold">05</small>
                            </div>
                          </div>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
                <!--/ Order Statistics -->

                <!-- Expense Overview -->
                <div class="col-md-6 col-lg-4 order-1 mb-4">
                  <div class="card h-100">
                    <div class="card-header">
                      <ul class="nav nav-pills" role="tablist">
                        <li class="nav-item">
                          <button style=""
                            type="button"
                            class="nav-link active"
                            role="tab"
                            data-bs-toggle="tab"
                            data-bs-target="#navs-tabs-line-card-income"
                            aria-controls="navs-tabs-line-card-income"
                            aria-selected="true"> Registered </button>
                        </li>
                        <li class="nav-item">
                          <button type="button" class="nav-link" role="tab">Pending</button>
                        </li>
                        <li class="nav-item">
                          <button type="button" class="nav-link" role="tab">Completed</button>
                        </li>
                      </ul>
                    </div>
                    <div class="card-body px-0">
                      <div class="tab-content p-0">
                        <div class="tab-pane fade show active" id="navs-tabs-line-card-income" role="tabpanel">
                          <div class="d-flex p-4 pt-3">
                            <div class="avatar flex-shrink-0 me-3">
                              <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-file"></i></span>
                            </div>
                            <div>
                              <small class="text-muted d-block">Total Registered Complaints</small>
                              <div class="d-flex align-items-center">
                                <h6 class="mb-0 me-1">459</h6>
                                <small class="text-success fw-semibold">
                                  <i class="bx bx-chevron-up"></i>
                                  42.9%
                                </small>
                              </div>
                            </div>
                          </div>
                          <div id="incomeChart"></div>
                          <div class="d-flex justify-content-center pt-4 gap-2">
                            <div class="flex-shrink-0">
                              <div id="expensesOfWeek"></div>
                            </div>
                            <div>
                              <p class="mb-n1 mt-1">Registered This Week</p>
                              <small class="text-muted">39 less than last week</small>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!--/ Expense Overview -->

                <!-- Transactions -->
                <div class="col-md-6 col-lg-4 order-2 mb-4">
                  <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                      <h5 class="card-title m-0 me-2">Feedback</h5>
                      <div class="dropdown">
                        <button
                          class="btn p-0"
                          type="button"
                          id="transactionID"
                          data-bs-toggle="dropdown"
                          aria-haspopup="true"
                          aria-expanded="false"
                        >
                          <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="transactionID">
                          <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
                          <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
                          <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
                        </div>
                      </div>
                    </div>
                    <div class="card-body">
                      <ul class="p-0 m-0">
                        <li class="d-flex mb-4 pb-1">
                          <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-rupee"></i></span>
                          </div>
                          <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                              <small class="text-muted d-block mb-1">Special Provident Fund</small>
                              <h6 class="mb-0">	Accounts</h6>
                            </div>
                            <div class="user-progress d-flex align-items-center gap-1">
                              <h6 class="mb-0">04</h6>
                              <a href="#"><span class="text-muted"> More</span></a>
                            </div>
                          </div>
                        </li>
                        <li class="d-flex mb-4 pb-1">
                          <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-info"><i class="bx bx-wrench"></i></span>                          
                          </div>
                          <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                              <small class="text-muted d-block mb-1">UGD-Open Man-hole</small>
                              <h6 class="mb-0">Engineering</h6>
                            </div>
                            <div class="user-progress d-flex align-items-center gap-1">
                              <h6 class="mb-0">02</h6>
                              <a href="#"><span class="text-muted"> More</span></a>
                            </div>
                          </div>
                        </li>
                        <li class="d-flex mb-4 pb-1">
                          <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-success"><i class="bx bxs-bank"></i></span>                          
                          </div>
                          <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                              <small class="text-muted d-block mb-1">Online Payment</small>
                              <h6 class="mb-0">	eGovernance</h6>
                            </div>
                            <div class="user-progress d-flex align-items-center gap-1">
                              <h6 class="mb-0">08</h6>
                              <a href="#"><span class="text-muted"> More</span></a>
                            </div>
                          </div>
                        </li>
                        <li class="d-flex mb-4 pb-1">
                          <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-dark"><i class="bx bx-buildings"></i></span>                          
                          </div>
                          <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                              <small class="text-muted d-block mb-1">	New Storm water Drainage</small>
                              <h6 class="mb-0">	New Infrastructure</h6>
                            </div>
                            <div class="user-progress d-flex align-items-center gap-1">
                              <h6 class="mb-0">01</h6>
                              <a href="#"><span class="text-muted"> More</span></a>
                            </div>
                          </div>
                        </li>
                        <li class="d-flex mb-4 pb-1">
                          <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-danger"><i class="icon-base bx bx-plus-medical"></i></span>                          
                          </div>
                          <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                              <small class="text-muted d-block mb-1"> Bakery smoke Nuisance</small>
                              <h6 class="mb-0">	Public Health</h6>
                            </div>
                            <div class="user-progress d-flex align-items-center gap-1">
                              <h6 class="mb-0">03</h6>
                              <a href="#"><span class="text-muted"> More</span></a>
                            </div>
                          </div>
                        </li>
                        <li class="d-flex">
                          <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-money"></i></span>                          
                          </div>
                          <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                              <small class="text-muted d-block mb-1">Sweeping Not Done</small>
                              <h6 class="mb-0">Revenue</h6>
                            </div>
                            <div class="user-progress d-flex align-items-center gap-1">
                              <h6 class="mb-0">19</h6>
                              <a href="#"><span class="text-muted"> More</span></a>
                            </div>
                          </div>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
                <!--/ Transactions -->
              </div>
              <div class="row">
  

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
                            <td>102</td>
                            <td>89</td>
                            <td>50</td>
                            <td>49</td>
                            <td>51</td>
                            <!-- <td><span class="badge bg-label-primary me-1">12</span></td> -->
                            <!-- <td>
                                <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="icon-base bx bx-dots-vertical-rounded"></i></button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="javascript:void(0);"><i class="icon-base bx bx-edit-alt me-1"></i> Edit</a>
                                    <a class="dropdown-item" href="javascript:void(0);"><i class="icon-base bx bx-trash me-1"></i>
                                    Delete</a>
                                </div>
                                </div>
                            </td> -->
                            </tr>
                            <tr>
                            <td><span class="badge badge-dot text-bg-primary me-1" style="width: 6px; height:6px;"></span> <span>eGovernance</span></td>
                            <td>332</td>
                            <td>124</td>
                            <td>54</td>
                            <td>64</td>
                            <td>7</td>
                            <!-- <td><span class="badge bg-label-success me-1">Completed</span></td>
                            <td>
                                <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="icon-base bx bx-dots-vertical-rounded"></i></button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="javascript:void(0);"><i class="icon-base bx bx-edit-alt me-1"></i> Edit</a>
                                    <a class="dropdown-item" href="javascript:void(0);"><i class="icon-base bx bx-trash me-1"></i>
                                    Delete</a>
                                </div>
                                </div>
                            </td> -->
                            </tr>
                            <tr>
                            <td><span class="badge badge-dot text-bg-primary me-1" style="width: 6px; height:6px;"></span> <span>Engineering - Sewage</span></td>
                            <td>3425</td>
                            <td>1252</td>
                            <td>245</td>
                            <td>754</td>
                            <td>37</td>
                            <!-- <td><span class="badge bg-label-info me-1">Scheduled</span></td>
                            <td>
                                <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="icon-base bx bx-dots-vertical-rounded"></i></button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="javascript:void(0);"><i class="icon-base bx bx-edit-alt me-1"></i> Edit</a>
                                    <a class="dropdown-item" href="javascript:void(0);"><i class="icon-base bx bx-trash me-1"></i>
                                    Delete</a>
                                </div>
                                </div>
                            </td> -->
                            </tr>
                            <tr>
                            <td><span class="badge badge-dot text-bg-primary me-1" style="width: 6px; height:6px;"></span> <span>Engineering - Street light</span>
                            </td>
                            <td>5433</td>
                            <td>3466</td>
                            <td>788</td>
                            <td>98</td>
                            <td>60</td>
                            <!-- <td><span class="badge bg-label-warning me-1">Pending</span></td>
                            <td>
                                <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="icon-base bx bx-dots-vertical-rounded"></i></button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="javascript:void(0);"><i class="icon-base bx bx-edit-alt me-1"></i> Edit</a>
                                    <a class="dropdown-item" href="javascript:void(0);"><i class="icon-base bx bx-trash me-1"></i>
                                    Delete</a>
                                </div>
                                </div>
                            </td> -->
                            </tr>
                            <tr>
                            <td><span class="badge badge-dot text-bg-primary me-1" style="width: 6px; height:6px;"></span> <span>Engineering - Water Supply</span>
                            </td>
                            <td>8432</td>
                            <td>466</td>
                            <td>3456</td>
                            <td>876</td>
                            <td>975</td>
                            <!-- <td><span class="badge bg-label-warning me-1">Pending</span></td>
                            <td>
                                <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="icon-base bx bx-dots-vertical-rounded"></i></button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="javascript:void(0);"><i class="icon-base bx bx-edit-alt me-1"></i> Edit</a>
                                    <a class="dropdown-item" href="javascript:void(0);"><i class="icon-base bx bx-trash me-1"></i>
                                    Delete</a>
                                </div>
                                </div>
                            </td> -->
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
    </div>
    </div>

</div> 
<div class="pt-3 m-0"></div>
@endsection

            </div>









