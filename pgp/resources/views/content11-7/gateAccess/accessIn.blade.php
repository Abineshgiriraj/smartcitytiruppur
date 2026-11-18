@extends('layouts/contentNavbarLayout')

@section('title', 'Tables - Academic Year Creation')

@section('content')
<style>
    /* btn style */
    .btn-right {
        float: right;
    }

    .rfidDiv {
        display: flex;
        justify-content: center;
    }

    .datetimediv {
        margin-top: 21px !important;
    }

    .validity-icon {
        display: inline-block;
        width: 80px;
        /* Adjust size as needed */
        height: 70px;
        /* Adjust size as needed */
        border-radius: 50%;
        /* Makes it circular */
        display: flex;
        /* Centers the icon */
        align-items: center;
        /* Centers vertically */
        justify-content: center;
        /* Centers horizontally */
        border: 2px solid transparent;
        border-color: greenyellow;
        /* Optional: gives a border */
    }

    .validity-icon .icon {
        font-size: 70px;
        /* Adjust icon size */
    }
</style>
<!-- \TH:i -->
@php
    $currentDateTime = date('d-m-Y');



    $curr_date_time = date('Y-m-d\TH:i');

    $day = date("l");
    $time = date('h:i a');
    // Format: YYYY-dMM-DDTH:MM

@endphp
<div class="row mb-3 page-hed">
    <div class="col-sm-6">
        <h4 class="">
            <span class="text-muted fw-light">Gate Access/</span> Access Gate IN
        </h4>
    </div>
</div>

<!-- Responsive Datatable -->
<div class="card">
    <div class="card-header border-bottom">

        <div class="row rfidDiv">
            <div class="col-md-4 user_role">
                <label for="email" class="form-label">RFID No</label>
                <input type="text" class="form-control" id="rfidData" name="rfidData" autofocus="">
            </div>
            <div class="col-md-1 user_role align-self-center mt-4">
                <button type="button" class="btn btn-primary" onclick="RfidSearch()">Go</button>
            </div>
        </div>
    </div>

    <div class="card mb-6" id="details-tab1" style="display:none;">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">Personal Details</h5>
        </div>

        <div class="card-body" id="card_body">
            <form class="fomr-new">
                <div class="row">
                    <div class="col-md-4">
                        <div class="row mb-1">
                            <label class="col-sm-6 ">Academic Year</label>
                            <div class="col-sm-6">
                                <h6 id="acc_year"></h6>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <label class="col-sm-6 ">Register Number:</label>
                            <div class="col-sm-6">
                                <h6 id="reg_no"></h6>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <label class="col-sm-6 ">Company</label>
                            <div class="col-sm-6">
                                <h6 id="company"></h6>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <label class="col-sm-6">Aadhaar No</label>
                            <div class="col-sm-6">
                                <h6 id="aadhar_licence"></h6>
                                <!-- <input type="text" id="aadhar_licence"> -->
                            </div>
                        </div>
                        <div class="row mb-1">
                            <label class="col-sm-6 ">Nationality</label>
                            <div class="col-sm-6">
                                <h6 id="nationality"></h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="row mb-1">
                            <label class="col-sm-6 ">Country</label>
                            <div class="col-sm-6">
                                <h6 id="country"></h6>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <label class="col-sm-6 ">Mobile</label>
                            <div class="col-sm-6">
                                <h6 id="mobile"></h6>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <label class="col-sm-6 ">Email ID</label>
                            <div class="col-sm-6">
                                <h6 id="email"></h6>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <label class="col-sm-6 ">Blood Group</label>
                            <div class="col-sm-6">
                                <h6 id="blood"></h6>
                            </div>
                        </div>

                        <div class="row" style="align:center;margin-top:100px">
                            <!-- <label class="col-sm-6 ">Current Date And Time</label> -->

                            <!-- <h5><?php echo strtoupper($day) . ' | ' . ' ' . '  ' . $currentDateTime . ' | ' . ' ' . '  ' . $time; ?>
                            </h5> -->
                            <h5 style="color: #696cff;">
                                <!-- --bs-purple: #696cff; -->
                                <?php echo strtoupper($day) . ' | <span style="color: black;">' . $currentDateTime . '</span> | ' . $time; ?>
                            </h5>

                        </div>

                    </div>
                    <div class="col-md-4">
                        <div class=" align-items-sm-center gap-6 " style="float: inline-end;text-align: center;">

                            <div id="image_container"></div>
                            <div class="button-wrapper">

                                <p class=" mb-1">Name: <b id="user_name"> </b></p>
                                <p class=" mb-1">Designation: <b id="designation"> </b></p>
                                <h3 id="person_validity_icon" class="validity-icon">
                                </h3>


                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-6" id="vehicle_details" style="display:none;">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">Vehicle Details</h5>
        </div>

        <div class="card-body">
            <form class="fomr-new">
                <div class="row">
                    <div class="col-md-4">
                        <div class="row mb-1">
                            <label class="col-sm-6 ">Accounting Year</label>
                            <div class="col-sm-6">
                                <h6 id="vech_acc_year"></h6>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <label class="col-sm-6 ">Vehicle Register Number:</label>
                            <div class="col-sm-6">
                                <h6 id="vech_reg_no"></h6>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <label class="col-sm-6 ">Vehicle Type</label>
                            <div class="col-sm-6">
                                <h6 id="vech_type"></h6>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <label class="col-sm-6">Vehicle Category</label>
                            <div class="col-sm-6">
                                <h6 id="vech_categ"></h6>

                            </div>
                        </div>

                    </div>
                    <div class="col-md-4">
                        <div class="row mb-1">
                            <label class="col-sm-6">Owners Name</label>
                            <div class="col-sm-6">
                                <h6 id="vech_owner"></h6>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <label class="col-sm-6 ">Contact Number</label>
                            <div class="col-sm-6">
                                <h6 id="vech_mobile_num"></h6>
                            </div>
                        </div>


                        <div class="row" style="align:center;margin-top:100px">
                            <!-- <label class="col-sm-6 ">Current Date And Time</label> -->

                            <!-- <h5><?php echo strtoupper($day) . ' | ' . ' ' . '  ' . $currentDateTime . ' | ' . ' ' . '  ' . $time; ?>
                            </h5> -->
                            <h5 style="color: #696cff;">
                                <!-- --bs-purple: #696cff; -->
                                <?php echo strtoupper($day) . ' | <span style="color: black;">' . $currentDateTime . '</span> | ' . $time; ?>
                            </h5>

                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class=" align-items-sm-center gap-6 " style="float: inline-end;text-align: center;">
                            <div id="vech_image_container"></div>
                            <div class="button-wrapper">
                                <h3 id="validity_icon" class="validity-icon">
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card-body" id="card-body" style=" display:none;">
        <div class="card-datatable table-responsive">
            <table class="table table-bordered dt-responsive nowrap" id="pass_entry_table">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Area Access</th>
                        <th>Vehicle/Person</th>
                        <th>Pass ID</th>
                        <th>Pass Type</th>
                        <th>From Date</th>
                        <th>To Date</th>
                        <th>Validity</th>

                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>




    </div>

    <div class="row mb-1 datetimediv" id="datetime" style="display:none;">

        <div class=" row rfidDiv">
            <div class="col-md-4 user_role" style="display:none">
                <label for="email" class="form-label">Current Date/Time</label>
                <input type="datetime-local" class="form-control" id="indatetime" name="indatetime"
                    value="<?php echo $curr_date_time; ?>" />
                <input type="hidden" id="gate_id" name="gate_id" value="in" />
            </div>
            <div class=" col-md-1 user_role align-self-center mt-4">
                <button type="button" class="btn btn-primary" onclick="GateInSave()">Submit</button>
            </div>
        </div>
    </div>

</div>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>
<link rel="stylesheet" href="{{ asset('public/assets/css/bootstrap.min.css') }}" />
<link rel="stylesheet" href="{{ asset('public/assets/css/dataTables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('public/assets/css/responsive.bootstrap.min.css') }}" />

<link rel="stylesheet" href="{{ asset('public/assets/css/core.css') }}" />

<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('public/assets/js/dataTables.js') }}"></script>
<script src="{{ asset('public/assets/js/dataTables.bootstrap5.js') }}"></script>
<script src="{{ asset('public/assets/js/dataTables.responsive.js') }}"></script>
<script src="{{ asset('public/assets/js/responsive.bootstrap5.js') }}"></script>

<script>
    function RfidSearch() {
        var rfidData = $('#rfidData').val();

        var method = "GET";
        var url = "{{route('getPassDetails.index')}}";
        if (rfidData) {
            $.ajax({
                url: url,
                method: method,
                data: {
                    rfidData: $('#rfidData').val(),
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {

                    $('#pass_entry_table tbody').empty();
                    $('#validity_icon').empty();
                    $('#image_container').empty();
                    $('#vech_image_container').empty();

                    $('#details-tab1').hide();
                    $('#vehicle_details').hide();
                    $('#card-body').hide();
                    $('#datetime').hide();

                    console.log(response);

                    const pass_main = response.pass_main;


                    var person_data = response.data;



                    pass_main.forEach(item => {
                        if (item.pass_type === "person") {

                            $('#details-tab1').show();
                            $('#card-body').show();
                            $('#datetime').show();
                            var person_data = response.data;

                            document.getElementById("details-tab1").style.display = "block";
                            document.getElementById("card-body").style.display = "block";
                            document.getElementById("datetime").style.display = "block";


                            document.getElementById("aadhar_licence").innerText = person_data
                                .aadhaar_no;
                            document.getElementById("acc_year").innerText = person_data.acc_year;
                            document.getElementById("reg_no").innerText = person_data.register_no;
                            document.getElementById("company").innerText = person_data.name;
                            document.getElementById("nationality").innerText = person_data.nationality;
                            document.getElementById("country").innerText = person_data.country;
                            document.getElementById("mobile").innerText = person_data.mobile;
                            document.getElementById("email").innerText = person_data.email_id;
                            document.getElementById("blood").innerText = person_data.blood_group;
                            document.getElementById("user_name").innerText = person_data.full_name;
                            document.getElementById("designation").innerText = person_data.person_type;


                            const imageName = person_data.image_upload;

                            const imagepath = "{{ asset('public/uploads/person_registration') }}/" + imageName;

                            const img = document.createElement('img');
                            img.src = imagepath;
                            img.style.width = "138px";
                            img.style.height = "104px";
                            img.id = "get_pic";

                            document.getElementById("image_container").innerHTML = "";
                            document.getElementById("image_container").appendChild(img);

                            processPassEntries(pass_main);




                        } else if (item.pass_type === "vehicle") {


                            $('#vehicle_details').show();
                            $('#card-body').show();
                            $('#datetime').show();

                            var person_data = response.data;


                            document.getElementById("vehicle_details").style.display = "block";
                            document.getElementById("card-body").style.display = "block";
                            document.getElementById("datetime").style.display = "block";

                            document.getElementById("vech_reg_no").innerText = person_data.vech_reg_no;
                            document.getElementById("vech_acc_year").innerText = person_data.acc_yr;
                            document.getElementById("reg_no").innerText = person_data.register_no;
                            document.getElementById("vech_type").innerText = person_data.vech_type;
                            document.getElementById("vech_categ").innerText = person_data.vehicle_categ;
                            document.getElementById("vech_owner").innerText = person_data.owner_name;
                            document.getElementById("vech_mobile_num").innerText = person_data
                                .contact_number;


                            const imageName = person_data.Vehicle_image;

                            const imagepath = "{{ asset('public/uploads/vehicle_registration') }}/" + imageName;

                            const img = document.createElement('img');
                            img.src =
                                imagepath;
                            img
                                .style.width = "138px";
                            img.style.height = "104px";
                            // img.id = "get_pic";

                            document.getElementById("vech_image_container").innerHTML = "";
                            document.getElementById("vech_image_container").appendChild(img);


                            const pass_main = response.pass_main;

                            $('#pass_entry_table tbody').empty();

                            processPassEntries(pass_main);

                        }
                    });

                }
            });
        }

    }

    function processPassEntries(pass_main) {
        $('#pass_entry_table tbody').empty();
        if (Array.isArray(pass_main)) {
            pass_main.forEach(function (item, index) {
                const toDate = new Date(item.to_date);
                const currentDate = new Date();

                // Calculate validity in milliseconds
                const validityTime = toDate - currentDate;

                // Determine validity icon
                let validityIcon = '';
                if (validityTime > 0) {
                    validityIcon = `<span class="text-success">&#10004;</span>`; // Green tick
                } else {
                    validityIcon = `<span class="text-danger">&#10008;</span>`; // Red cross
                }

                if (index === pass_main.length - 1) {
                    $('#validity_icon').html(validityIcon);
                    $('#person_validity_icon').html(validityIcon);

                }

                var newrow = `<tr id="row-${item.unique_id}">
                <td>${index + 1}</td>
                <td>${item.access_area}</td>
                <td>${item.pass_type}</td>
                <td>${item.pass_id}<input type="hidden" class="form-control" id="pass_id" name="pass_id" value="${item.pass_id}" /></td>
                <td>${item.pass_type}</td>
                <td>${item.from_date}</td>
                <td>${item.to_date}</td>
                <td>${validityIcon}</td>
            </tr>`;
                $('#pass_entry_table tbody').append(newrow);
            });
        }
    }

    function GateInSave() {
        var rfidData = $('#rfidData').val();
        var pass_id = $('#pass_id').val();
        var indatetime = $('#indatetime').val();
        var gate_id = $('#gate_id').val();

        var formData = new FormData();
        formData.append('rfidData', $('#rfidData').val());
        formData.append('pass_id', $('#pass_id').val());
        formData.append('indatetime', $('#indatetime').val());
        formData.append('gate_id', $('#gate_id').val());
        formData.append('_token', '{{csrf_token()}}');

        $.ajax({
            type: "POST",
            url: "{{route('accessInStore.store')}}",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {

                const redirectUrl = "{{route('penaltyList.index')}}";
                // console.log(response); // Log the whole response for debugging
                sweetalert("create", redirectUrl);

            },
            error: function (xhr) {
                console.error(xhr.responseText); // Log the error response
                alert('An error occurred: ' + xhr.responseText);
            }
        });
    }

    function pass_type_data() {


        var rf_id = $('#rfidData').val();

        $.ajax({
            type: "GET",
            url: "{{route('getPasstable.data')}}",
            data: {
                rf_id: rf_id,
                _token: "{{csrf_token()}}"
            },
            success: function (data) {
                $('#pass_entry_table tbody').empty();

                data.forEach(function (item, index) {


                    var newrow = `<tr id="row-${item.unique_id}">
                            <td>${item.access_area}</td>
                            <td>${item.pass_type}</td>
                            <td>${item.pass_id}</td>
                            <td>${item.pass_type}</td>
                            <td>${item.from_date}</td>
                            <td>${item.to_date}</td>
                            
                                    </tr>`;
                    $('#pass_entry_table tbody').append(newRow);


                });
            }
        });


    }
</script>

@endsection