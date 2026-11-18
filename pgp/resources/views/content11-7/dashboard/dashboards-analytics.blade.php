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
    /* Main Container */
    .btm-art {
        display: flex;
        justify-content: space-between;
    }

    /* Left Column */
    .col-md-8 {
        flex: 0 0 66.66%;
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Right Column */
    .col-lg-4,
    .col-md-6 {
        flex: 0 0 30%;
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Box Styles for Left Column */
    .box-d {
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        position: relative;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .box-d:hover {
        transform: translateY(-5px);
    }

    .box-d h5 {
        font-size: 2rem;
        color: #007bff;
        font-family: 'Roboto', sans-serif;
        font-weight: 800;
    }

    .box-d h6 {
        font-size: 1.2rem;
        color: #555;
        font-family: 'Poppins', sans-serif;
        margin-bottom: 10px;
    }

    .line-dec2 {
        width: 100%;
        height: 1.5px;
        background-color: #007bff;
        margin: 10px 0;
    }

    .button-21 {
        color: black;
        border: 2px solid #007bff;
        border-radius: 7px;
        padding: 10px;
    }

    /* Button Styles */
    .button-12,
    .button-123 {
        display: inline-flex;
        /* padding: 10px 20px; */
        /* border: none; */
        border-radius: 4px;
        font-size: 0.9rem;
        font-family: 'Roboto', sans-serif;
        text-decoration: none;
        transition: background-color 0.3s ease;
        border: 1px solid;
    }

    .button-12 {
        background-color: #007bff;
        color: #fff;
    }

    .button-12:hover {
        background-color: #0056b3;
    }

    .button-123 {
        color: black;
    }

    .button-123:hover {
        background-color: #013F7F;
        color: white;
    }

    /* Right Column Details */
    .cont-bg .item {
        display: flex;
        flex-direction: column;
    }

    .phone-call {
        display: flex;
        /* margin-bottom: 20px; */
        flex-direction: column;
    }

    .phone-call .icon {
        font-size: 22px;
        margin-right: 10px;
        color: #5859ae;
    }

    .phone-call .text p {
        margin: 0;
        font-size: 1rem;
        font-family: 'Poppins', sans-serif;
        color: #333;
    }

    .phone-call .text a {
        font-size: 1.2rem;
        color: #504b4b;
        text-decoration: none;
        font-weight: 600;
    }

    .phone-call .text a:hover {
        text-decoration: underline;
    }

    .line-dec {
        width: 100%;
        height: 2px;
        background-color: #ddd;
        margin: 15px 0;
    }

    .pass-head {
        margin-top: 34px;

    }

    .head-label {
        display: flex;
        justify-content: space-between;
    }

    .button-1 {
        margin-top: 20px;
        width: 100%;
        display: inline-block;
        height: auto;
        padding: 12px 32px;
        border: 2px solid #013F7F;
        border-radius: 80px;
        background-color: #013F7F;
        -webkit-transition: border-color 400ms ease,
            color 400ms ease,
            background-color 400ms ease;
        transition: border-color 400ms ease,
            color 400ms ease,
            background-color 400ms ease;
        color: #fff;
        line-height: 1.5em;
        font-weight: 400;
        text-align: center;
    }

    .p-tag {
        margin-top: 8px;
        margin-bottom: 0;
        margin-left: 5px;
    }

    .tag-detail {
        display: flex;
    }

    .person-details {
        height: 94%;
    }

    .profile-div {
        text-align: center;
        margin-bottom: 21px;
    }

    .profile-img {
        width: 40%;
        border-radius: 63px;
        height: 113px;
        border: 1px solid lightyellow;
    }

    .bx-lg2 {
        font-size: 22px;
    }

    .btn-label-primary {
        color: #696cff !important;
        border-color: transparent;
        background: #e7e7ff;
    }

    .new-design h4 {
        font-size: 20px;
    }

    .new-design h3 {
        font-size: 35px;
        color: #5859ae;
        margin: 12px 0px;
        font-weight: 600;
    }

    .new-design {
        text-align: center;
    }

    .avatar {
        margin: 0px auto;
        margin-bottom: 15px;
    }

    .avatar i {
        font-size: 20px;
    }

    .border-primary {
        border-color: #696cff !important;
    }

    .new-design i {
        font-size: 40px;
        margin-bottom: 12px;
    }

    .new-design a i {
        font-size: 15px;
        margin-bottom: 0px;
        padding: 0px 10px;
    }

    .card-body.new-design {
        padding-bottom: 4px;
    }

    .user-profile-img {
    border: 0px solid #efe3c9;
    width: 110px;
    text-align: center;
    margin: 0px auto !important;
}

    .d-2 i {
        font-size: 22px;
        margin-right: 23px;
        background: #5859ae;
        color: #fff;
        padding: 10px;
        border-radius: 10px;
    }

.brd {
    border-right: 1px solid #cccc;
}
hr {
    margin-top: 2px !important;
    margin-bottom: 18px;
}

    .d-2 h4 {
    font-size: 35px;
    font-weight: 700;
    margin-top: 11px;
}
    li.list-inline-item {
    margin-top: 19px;
    padding-top: 15px;
    text-align: end;
    width: 100%;
}
.addpass button {
    width: 100%;
}
.card-title h5 {
    color: #6364b8;
}
span.nam {
    color: #6e6fc0;
    font-weight: 600;
}
a.btn.btn-sm.btn-label-primary:hover {
    background: #696cff;
    color: #fff !important;
}
.addpass button.btn.btn-primary {
    font-size: 22px;
    padding: 21px 7px;
}
.addpass span {
    font-size: 23px;
    padding-right: 6px;
}
hr {
    margin-bottom: 4px;
    margin-top: 10px !important;
}
.card-body.text-center.spe-card {
    padding: 2px 2px 0px;
}
span.font-1 {
    font-size: 17px;
    font-weight: 600;
}
.avatar.avatar-sm.me-3 {
    margin-bottom: 0px;
}
td.con {
    font-weight: 600;
    font-size: 16px;
}
</style>
<div class="content-wrapper">

    <!-- Content -->
    <div class="container-xxl flex-grow-1 ">
        <div class="row">
            <div class=" col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8 align-self-center">
                                <div class="card-title mb-3">
                                    <h5 class="m-0 me-2">Registration Details</h5>

                                </div>
                            </div>
                        </div>
                      

                        <div class="row gy-6">
        <div class="col-sm-6 col-lg-3">
          <div class="card border border-primary shadow-none">
            <div class="card-body text-center spe-card">
              <img src="https://demos.themeselection.com/sneat-bootstrap-html-laravel-admin-template/demo/assets/img/front-pages/icons/keyboard.svg" alt="laptop" class="mb-2">
              <h3 class="mb-0">30</h3>
              <p class="fw-medium mb-0">
              Agent / Firm
              </p>
              <hr>
              <span class="badge bg-label-primary badge-sm mt-2">View More</span>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="card border border-success shadow-none">
            <div class="card-body text-center spe-card">
              <img src="https://demos.themeselection.com/sneat-bootstrap-html-laravel-admin-template/demo/assets/img/front-pages/icons/user-success.svg" alt="laptop" class="mb-2">
              <h3 class="mb-0">20</h3>
              <p class="fw-medium mb-0">
              Person / Driver
              </p>
              <hr>
              <span class="badge bg-label-success badge-sm mt-2">View More</span>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="card border border-info shadow-none">
            <div class="card-body text-center spe-card">
              <img src="{{asset('public/assets/img/truck.png')}}" alt="laptop" class="mb-2" style="width: 24%;margin-top: 8px;padding-bottom: 10px;">
              <h3 class="mb-0">40</h3>
              <p class="fw-medium mb-0">
              Vehicle / Equipment
              </p>
              <hr>
              <span class="badge bg-label-info badge-sm mt-2">View More</span>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="card border border-warning shadow-none">
            <div class="card-body text-center spe-card">
              <img src="{{asset('public/assets/img/cont.png')}}" alt="laptop" class="mb-2" style="width: 26%;">
              <h3 class="mb-0">20</h3>
              <p class="fw-medium mb-0">
              Cargo / Container
              </p>
              <hr>
              <span class="badge bg-label-warning badge-sm mt-2">View More</span>
            </div>
          </div>
        </div>
      </div>



                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <br>

    <div class="container-xxl flex-grow-1 ">
        <div class="row">
            <div class=" col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="ss">
                        <div class="row">
                            <div class="col-8 align-self-center">
                                <div class="card-title mb-3">
                                    <h5 class="m-0 me-2">Person Pass Details</h5>
                                </div>
                            </div>
                            <div class="col-4 mb-3"><input class="form-control" type="date" value="2021-06-18"
                                    id="html5-date-input">
                            </div>
                        </div>
                <div class="row h-100">
                <div class="col-md-4 ">
                    <div class="bg-lighter">
                    <div class="row">
                    <div class="col-sm-6">
                    <div class="d-flex align-items-end h-100 justify-content-center mt-sm-0 mt-4 ps-6">
                    <img src="{{asset('public/assets/img/Auction-amico.png')}}"class="img-fluid" alt="Image" width="120" data-app-light-img="illustrations/lady-with-laptop-light.png" data-app-dark-img="illustrations/lady-with-laptop-dark.png">
                    </div>
                    </div>
                    <div class="col-sm-6">
                     <div class="card-body text-sm-end text-center ps-sm-0">
                    <p class="fw-medium mb-0"> Issued</p>
                    <h2>30</h2>
                    <button data-bs-target="#addRoleModal" data-bs-toggle="modal" class="btn btn-sm btn-primary  text-nowrap add-new-role">View More</button>
</div>
                    </div>
                    </div>
                    </div>
                </div>
                <div class="col-md-4 ">
                <div class="bg-lighter">
                    <div class="row">
                    <div class="col-sm-6">
                    <div class="d-flex align-items-end h-100 justify-content-center mt-sm-0 mt-4 ps-6">
                    <img src="{{asset('public/assets/img/Good team-pana.png')}}" class="img-fluid" alt="Image" width="120" data-app-light-img="illustrations/lady-with-laptop-light.png" data-app-dark-img="illustrations/lady-with-laptop-dark.png">
                    </div>
                    </div>
                    <div class="col-sm-6">
                    <div class="card-body text-sm-end text-center ps-sm-0">
                    <p class="fw-medium mb-0"> Valid</p>
                    <h2>15</h2>
                    <button data-bs-target="#addRoleModal" data-bs-toggle="modal" class="btn btn-sm btn-info  text-nowrap add-new-role">View More</button>
                    
                    </div>
                    </div>
                    </div>
                </div>
                </div>
                <div class="col-md-4 ">
                <div class="bg-lighter">
                    <div class="row">
                    <div class="col-sm-6">
                    <div class="d-flex align-items-end h-100 justify-content-center mt-sm-0 mt-4 ps-6">
                    <img src="{{asset('public/assets/img/Warning-rafiki.png')}}" class="img-fluid" alt="Image" width="120" data-app-light-img="illustrations/lady-with-laptop-light.png" data-app-dark-img="illustrations/lady-with-laptop-dark.png">
                    </div>
                    </div>
                    <div class="col-sm-6">
                    <div class="card-body text-sm-end text-center ps-sm-0">
                    <p class="fw-medium mb-0"> Expiry</p>
                    <h2>15</h2>
                    <button data-bs-target="#addRoleModal" data-bs-toggle="modal" class="btn btn-sm btn-warning   text-nowrap add-new-role">View More</button>
                    
                    </div>
                    </div>
                    </div>
                </div>
                </div>
                </div>
                </div>
                <hr>

                <div class="row mt-4">
                            <div class="col-8 align-self-center">
                                <div class="card-title mb-3">
                                    <h5 class="m-0 me-2" style="color: #03c3ec;">Vehicle Pass Details</h5>
                                </div>
                            </div>
                            <div class="col-4 mb-3"><input class="form-control" type="date" value="2021-06-18"
                                    id="html5-date-input">
                            </div>
                        </div>
                        <div class="row h-100">
                <div class="col-md-4 ">
                    <div class="bg-lighter">
                    <div class="row">
                    <div class="col-sm-6">
                    <div class="d-flex align-items-end h-100 justify-content-center mt-sm-0 mt-4 ps-6">
                    <img src="{{asset('public/assets/img/Logistics-amico.png')}}"class="img-fluid" alt="Image" width="120" data-app-light-img="illustrations/lady-with-laptop-light.png" data-app-dark-img="illustrations/lady-with-laptop-dark.png">
                    </div>
                    </div>
                    <div class="col-sm-6">
                     <div class="card-body text-sm-end text-center ps-sm-0">
                    <p class="fw-medium mb-0"> Issued</p>
                    <h2>30</h2>
                    <button data-bs-target="#addRoleModal" data-bs-toggle="modal" class="btn btn-sm btn-primary  text-nowrap add-new-role">View More</button>
</div>
                    </div>
                    </div>
                    </div>
                </div>
                <div class="col-md-4 ">
                <div class="bg-lighter">
                    <div class="row">
                    <div class="col-sm-6">
                    <div class="d-flex align-items-end h-100 justify-content-center mt-sm-0 mt-4 ps-6">
                    <img src="{{asset('public/assets/img/Logistics-rafiki.png')}}" class="img-fluid" alt="Image" width="300" data-app-light-img="illustrations/lady-with-laptop-light.png" data-app-dark-img="illustrations/lady-with-laptop-dark.png">
                    </div>
                    </div>
                    <div class="col-sm-6">
                    <div class="card-body text-sm-end text-center ps-sm-0">
                    <p class="fw-medium mb-0"> Valid</p>
                    <h2>15</h2>
                    <button data-bs-target="#addRoleModal" data-bs-toggle="modal" class="btn btn-sm btn-info  text-nowrap add-new-role">View More</button>
                    
                    </div>
                    </div>
                    </div>
                </div>
                </div>
                <div class="col-md-4 ">
                <div class="bg-lighter">
                    <div class="row">
                    <div class="col-sm-6">
                    <div class="d-flex align-items-end h-100 justify-content-center mt-sm-0 mt-4 ps-6">
                    <img src="{{asset('public/assets/img/ex-vehicle.png')}}" class="img-fluid" alt="Image" width="400" data-app-light-img="illustrations/lady-with-laptop-light.png" data-app-dark-img="illustrations/lady-with-laptop-dark.png">
                    </div>
                    </div>
                    <div class="col-sm-6">
                    <div class="card-body text-sm-end text-center ps-sm-0">
                    <p class="fw-medium mb-0"> Expiry</p>
                    <h2>15</h2>
                    <button data-bs-target="#addRoleModal" data-bs-toggle="modal" class="btn btn-sm btn-warning   text-nowrap add-new-role">View More</button>
                    
                    </div>
                    </div>
                    </div>
                </div>
                </div>
                </div>

                        
                    </div>
                </div>
            </div>
        </div>
    </div>
<br>
                <div class="container-xxl flex-grow-1 ">
                <div class="row">
                    <div class=" col-12">
                    <div class="card">
                    <div class="card-body">
                    <div class="row">
                            <div class="col-8 align-self-center">
                                <div class="card-title mb-3">
                                    <h5 class="m-0 me-2">Gate Tracking</h5>
                                </div>
                            </div>
                           
                        </div>
                    <div class="col-xxl-12 mb-0">
    <div class="card">
      <div class="card-datatable table-responsive">
        <table class="invoice-list-table table table-border-top-0">
          <thead>
            <tr>
              <th>Gate Name</th>
              <th>Person In</th>
              <th>Person Out</th>
              <th >Vehicle In</th>
              <th>Vehicle Out</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            <tr>
              <td>
              <div class="d-flex justify-content-left align-items-center">
                <div class="avatar-wrapper">
                    <div class="avatar avatar-sm me-3">
                    <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-log-in"></i></span>                    </div></div><div class="d-flex flex-column gap-50">
                        <span class="text-truncate fw-medium text-heading">GATE 01</span>
                        <small class="text-truncate">Karthika</small>
                    </div>
                </div>
              </td>
              <td class="con">60</td>
              <td class="con">10</td>
              <td class="con">30</td>
              <td class="con">20</td>
          
            </tr>
            <tr>
              <td>
              <div class="d-flex justify-content-left align-items-center">
                <div class="avatar-wrapper">
                    <div class="avatar avatar-sm me-3">
                    <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-log-in"></i></span>                    </div></div><div class="d-flex flex-column gap-50">
                        <span class="text-truncate fw-medium text-heading">GATE 02</span>
                        <small class="text-truncate">Prabhu</small>
                    </div>
                </div>
              </td>
              <td class="con">60</td>
              <td class="con">10</td>
              <td class="con">30</td>
              <td class="con">20</td>
            </tr>
           
             
              
          </tbody>
        </table>
      </div>
    </div>
  </div>
                    
                    </div>
                    </div>
                    </div>
                </div>
                </div>
    
</div>

<!-- Modal -->
<div class="modal fade" id="staticBackdrop2" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Issued</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table id="example" class="table table-striped table-bordered" style="width:100%">
                    <thead class="thead-light">
                        <tr>
                            <th>S.No</th>
                            <th>RFID Card No.</th>
                            <th>Registration No.</th>
                            <th>Pass Type</th>
                            <th>No of Days</th>
                            <th>From Date</th>
                            <th>To Date</th>
                            <th>Amount</th>

                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>9867585</td>
                            <td>RFD1213131</td>
                            <td>Day</td>
                            <td>1</td>
                            <td>02.09.2024</td>
                            <td>02.09.2024</td>
                            <td>120</td>


                        </tr>
                        <tr>
                            <td>2</td>
                            <td>9067235</td>
                            <td>RFD1210101</td>
                            <td>Day</td>
                            <td>2</td>
                            <td>05.09.2024</td>
                            <td>06.09.2024</td>
                            <td>240</td>


                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection