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
</style>
<div class="content-wrapper">

    <!-- Content -->
    <div class="container-xxl flex-grow-1 ">

        <div class="row g-6">



            <div class=" col-8">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8 align-self-center">
                                <div class="card-title mb-3">
                                    <h5 class="m-0 me-2">Pass Details</h5>
                                </div>
                            </div>
                            <div class="col-4 mb-3"><input class="form-control" type="date" value="2021-06-18"
                                    id="html5-date-input">
                            </div>
                        </div>
                        <hr>
                        <!-- Card Border Shadow -->
                        <div class="row">
                            <div class="col-sm-6 col-xl-4">
                                <div class="card border-primary border" style="background: #e7e7ff;">
                                    <div class="card-body new-design">

                                        <i class="tf-icons bx bx-check-shield text-primary "></i>

                                        <h4 class="card-title text-center text-capitalize mb-1">Issued</h4>

                                        <h3 class="text-primary">40</h3>
                                        <a class="btn btn-sm btn-label-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop2" href="">
                                            View <i class="fa-solid fa-arrow-right-long"></i></a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-xl-4">
                                <div class="card border-success border" style="background: #e8fadf;">
                                    <div class="card-body new-design">

                                        <i class="fa-regular fa-square-check text-success"></i>

                                        <h4 class="card-title text-center text-capitalize mb-1">Valid</h4>

                                        <h3 class="text-success">25</h3>
                                        <a class="btn btn-sm btn-label-success" data-bs-toggle="modal" data-bs-target="#staticBackdrop2" href="">
                                             View <i class="fa-solid fa-arrow-right-long"></i></a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-xl-4">
                                <div class="card border-danger border" style="background: #ffe0db;">
                                    <div class="card-body new-design">

                                        <i class="fa-regular fa-clock text-danger"></i>

                                        <h4 class="card-title text-center text-capitalize mb-1">Expiry</h4>

                                        <h3 class="text-danger">15</h3>
                                        <a class=" btn btn-sm btn-label-danger" data-bs-toggle="modal" data-bs-target="#staticBackdrop2" href="">
                                            View <i class="fa-solid fa-arrow-right-long"></i></a>
                                    </div>
                                </div>
                            </div>
                            
                           
                                <div class="col-12">
                                    <div class="card-title mt-4">
                                        <h5 class="m-0 me-2">Registration Details</h5>
                                    </div>
                                </div>

                            
                            <hr>
                            <div class="col-4 brd">
                                <div class=" d-flex align-items-center  me-6 me-sm-0 d-2 d-3">
                                    <i class="fa-regular fa-user"></i>
                                    <div class="content-right">
                                        <p class="mb-0 fw-medium">Person</p>
                                        <h4 class="text-primary mb-0">30</h4>
                                    </div>
                                </div>
                                <li class="list-inline-item">
                                <a href="{{ url('/person_registration')}}"><i class="menu-icon tf-icons bx bx-spreadsheet align-top"></i><span class="fw-medium">Register</span>
                                </a></li>
                            </div>
                            <div class="col-4 brd">
                                <div class="d-flex align-items-center  d-2 d-3">
                                    <i class="fa-solid fa-truck"></i>
                                    <div class="content-right">
                                        <p class="mb-0 fw-medium">Vehicle</p>
                                        <h4 class="text-primary mb-0">25</h4>
                                    </div>
                                </div>
                                <li class="list-inline-item">
                                <a href="{{ url('/vehicle_registration')}}"><i class="menu-icon tf-icons bx bx-spreadsheet align-top"></i><span class="fw-medium">Register</span>
                                </a></li>
                            </div>
                            <div class="col-4 ">
                                <div class="d-flex align-items-center  d-2">
                                    <i class="fa-solid fa-truck-ramp-box"></i>
                                    <div class="content-right">
                                        <p class="mb-0 fw-medium">Cargo/Container </p>
                                        <h4 class="text-primary mb-0">05</h4>
                                    </div>
                                </div>
                                <li class="list-inline-item">
                                <a href="{{ url('/new_cargo')}}"><i class="menu-icon tf-icons bx bx-spreadsheet align-top"></i><span class="fw-medium">Register</span>
                                </a></li>
                            </div>
                            <br>
                           


                            </<div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Card Border Shadow -->
            <div class=" col-4">
                <div class="card mb-6">
                    <div class="card-body">
                        <div class="flex-shrink-0 mt-1 mb-3 mx-sm-0 mx-auto">
                            <img src="https://demos.themeselection.com/sneat-bootstrap-html-laravel-admin-template/demo/assets/img/avatars/1.png"
                                alt="user image" class="d-block h-auto  ms-sm-6 rounded-3 user-profile-img">
                        </div>
                        <small class="card-text text-uppercase text-muted small">User Details</small>
                        <ul class="list-unstyled mb-0 mt-2 pt-1 new-s">
                            <li class="d-flex align-items-center mb-2"><i class="bx bx-user"></i><span
                                    class=" mx-2">Full Name:</span> <span class="nam">Dinesh</span></li>
                            <li class="d-flex align-items-center mb-2"><i class="bx bx-crown"></i><span
                                    class=" mx-2">Designation:</span> <span class="nam">Pass Operator</span></li>
                            <li class="d-flex align-items-center mb-2"><i class="bx bx-calendar"></i><span
                                    class="  mx-2">Acc Year:</span> <span class="nam">2024-2025</span></li>
                                    <li class="d-flex align-items-center mb-2"><i class="bx bx-phone"></i><span
                                    class=" mx-2">Contact:</span> <span class="nam">98989 89898</span></li>

                            <li class="d-flex align-items-center"><i class="bx bx-envelope"></i><span
                                    class=" mx-2">Email:</span> <span class="nam">dinesh2024@gmail.com</span></li>

                        </ul>
                       

                    </div>
                </div>
                <!-- <div class="col-12 addpass">
                <div class="btn-group " role="group" aria-label="First group">
                <button type="button" class="btn btn btn-primary"><i class="tf-icons bx bxs-badge-check"></i></button>
                <button type="button" class="btn btn btn-primary">Pass Issued</button>
                </div>            
              </div> -->
               <div class="col-12 addpass" onclick="window.location.href='{{ url('/PassEntry')}}'">
                            <button type="button" class="btn btn-primary">
                <span class="tf-icons bx bxs-badge-check bx-18px me-2"></span>Pass Issued
              </button>
                            </div> 
            </div>
        </div>
    </div>
</div>



<!-- 



<br>
<br>
<br>
<br>
<br>
<br>

<div class=" btm-art1">
    <div class="row btm-art">
        <div class="col-md-8">
            <div class="head-label">
                <h4>Pass Details</h4>
                <input type="date" id="current_date" name="current_date"
                    value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">

            </div>
            <div class="line-dec"></div>

            <div class="row margin-row">
                <div class="col-md-4">
                    <div class="box-d">
                        <h6>Issued </h6>
                        <h5>20</h5>
                        <div class="line-dec2"></div> 
                        <button class="button-123" data-bs-toggle="modal" data-bs-target="#staticBackdrop2">View
                            More
                            ↗</button>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="box-d">
                        <h6>Valid</h6>
                        <h5>15</h5>
                        <div class="line-dec2"></div>
                        <button class="button-123" data-bs-toggle="modal" data-bs-target="#staticBackdrop2">View
                            More
                            ↗
                            </a></button>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="box-d">
                        <h6>Expiry</h6>
                        <h5>05</h5>
                        <div class="line-dec2"></div>
                        <button class="button-123" data-bs-toggle="modal" data-bs-target="#staticBackdrop2">View
                            More
                            ↗</a></button>
                    </div>
                </div>
            </div>


            <h4 class="pass-head">Pass Details</h4>
            <h4 class="pass-head">Registration Details</h4>



            <div class="row margin-row">
                <div class="col-md-4">
                    <div class="box-d">
                        <h6>Person </h6>
                        <h5>30</h5>
                        <div class="de" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i
                                class="fa-solid fa-circle-info"></i></div>
                        <div class="line-dec2"></div>
                        <div class="book">
                            <div class="button-21"><span>Register</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="box-d">
                        <h6>Vehicle </h6>
                        <h5>45</h5>
                        <div class="de"><i class="fa-solid fa-circle-info"></i></div>
                        <div class="line-dec2"></div>
                        <div class="book">
                            <div class="button-21"><span>Register</span></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="box-d">
                        <h6>Cargo/Container </h6>
                        <h5>15</h5>
                        <div class="de"><i class="fa-solid fa-circle-info"></i></div>
                        <div class="line-dec2"></div>
                        <div class="book">
                            <div class="button-21"><span>Register</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-lg-4 col-md-6 contact cont-bg person-details">
            <div class="item">

                <div class="profile-div">
                    <img class="profile-img" src="{{ asset('public/assets/img/profiles.png') }}">
                </div>

                <div class="phone-call">
                    <div class="tag-detail">
                        <div class="icon"><span class="fa-regular fa-user"></span></div>
                        <p class="p-tag">Prabu Ram</p>
                    </div>
                    <div class="tag-detail">
                        <div class="icon"><span class="fa-regular fa-id-badge"></span></div>
                        <p class="p-tag">Pass Operator</p>
                    </div>
                    <div class="tag-detail">

                        <div class="icon"><span class="fa-solid fa-envelope"></span></div>
                        <p class="p-tag"> ram2024@gmail.com</p>
                    </div>
                    <div class="tag-detail">
                        <div class="icon"><span class="fa-solid fa-mobile"></span></div>
                        <p class="p-tag">9898989898</p>
                    </div>
                    <div class="tag-detail">
                        <div class="icon"><span class="fa-solid fa-calendar-days"></span></div>
                        <p class="p-tag">2024-2025</p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div> -->



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