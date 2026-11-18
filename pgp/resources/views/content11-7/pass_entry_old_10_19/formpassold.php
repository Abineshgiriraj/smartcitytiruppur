@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')
<div class="row   page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Pass/</span>Pass Entry</h4>
    </div>
</div>

<style>
/* btn style */
.btn-right {
    float: right;
}

.password-container {
    position: relative;
}

.input-wrapper {
    position: relative;
}

.input-wrapper .form-control {
    padding-right: 30px;
}

.input-wrapper .toggle-password {
    position: absolute;
    top: 50%;
    right: 10px;
    transform: translateY(-50%);
    cursor: pointer;
    color: #6c757d;
}

.font-weight-bold {
    font-weight: bold;
}
</style>

@php
$currentdate = date('Y-m-d');
$get_firm_reg_type =get_firm_reg_type();
$get_vech_type =get_vech_type();

$get_person_type = get_person_type();
@endphp
<!-- Basic Layout -->
<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-body">
                <form id="academicYearForm">
                    @csrf
                    @if(isset($complaint_registration))
                    @method('PUT')
                    @endif
                    <div class="row mb-3 mt-2">
                        <div class="col-sm-2  ">
                            <input type="text" id="main_unique_id" name="main_unique_id">
                            <label for="colFormLabelSm" class="col-form-label">Issue Center</label>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="issue_center" name="issue_center" />
                        </div>
                    </div>

                    <div class="row mb-3 mt-2">
                        <div class="col-sm-2  ">
                            <label for="colFormLabelSm" class="col-form-label">Firms /Agent Name</label>
                        </div>
                        <div class="col-sm-4">
                            <!-- <select class="form-select" id="firms_agent" name="firms_agent">
                                <option value="">Select Agent Name</option>
                                <option value="ship_agent">Ship Agent</option>
                                <option value="customer_broker">Customer Broker</option>
                            </select> -->
                            <select class="form-select" id="firms_agent" name="firms_agent">
                                <option value="">
                                    Select </option>
                                @foreach ($get_firm_reg_type as $firm_reg_type)
                                <option value="{{$firm_reg_type->unique_id}}">
                                    {{$firm_reg_type->firm_agent_name}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <label for="colFormLabelSm" class="col-form-label">Pass Type</label>
                        </div>
                        <div class="col-sm-4">
                            <select class="form-select" id="passType" name="passType" onchange="pass_type()">
                                <option value="">Select PassType</option>
                                <option value="vehicle">Vehicle Pass</option>
                                <option value="person">Person Pass</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3 mt-2">
                        <div class=" col-sm-2" id="vehicle_type_div1" style="display:none;">
                            <label for=" colFormLabelSm" class="col-form-label">Vehicle Type</label>
                        </div>
                        <div class="col-sm-4" id="vehicle_type_div" style="display:none;">
                            <!-- <select class="form-select" id="vectype" name="vectype" onchange="vech_type()">
                                <option value="">Select vectype</option>
                                <option value="equipment">Equipment vehicle</option>
                                <option value="heavy">Heavy Vechicle</option>
                                <option value="light">Light vehicle</option>
                            </select> -->
                            <select class="form-select" id="vectype" name="vectype" onchange="vech_type()">
                                <option value="">
                                    Select </option>
                                @foreach ($get_vech_type as $vech_type)
                                <option value="{{$vech_type->unique_id}}">
                                    {{$vech_type->vehicle_type}}
                                </option>
                                @endforeach
                            </select>

                        </div>
                        <div class="col-sm-2" id="person_div" style="display:none;">
                            <label for="colFormLabelSm" class="col-form-label">Person Type</label>
                        </div>
                        <div class="col-sm-4" id="person_div1" style="display:none;">
                            <!-- <select class="form-select" id="persontype" name="persontype" onchange="person_type()">
                                <option value="">Select Person Type</option>
                                <option value="person">Person</option>
                                <option value="employee">Employee</option>
                                <option value="driver">Driver</option>
                                <option value="cleaner">Cleaner</option>
                            </select> -->
                            <select class="form-select" id="persontype" name="persontype" onchange="person_type()">
                                <option value="">
                                    Select </option>
                                @foreach ($get_person_type as $person_type)
                                <option value="{{$person_type->unique_id}}">
                                    {{$person_type->person_type}}
                                </option>
                                @endforeach
                            </select>


                        </div>

                        <div class="col-sm-2" id="vehicle_div" style="display:none;">
                            <label for="colFormLabelSm" class="col-form-label">Reg. Vehicle Number</label>
                        </div>
                        <div class="col-sm-4" id="vehicle_div1" style="display:none;">
                            <input type="text" class="form-control" id="vec_reg_no" name="vec_reg_no"
                                oninput="vehicle_data()" />
                        </div>


                        <!-- id="get_adhaar" -->
                        <div class="col-sm-2" id="adhaar" style="display:none;">
                            <label for="colFormLabelSm" class="col-form-label">Adhaar No.</label>
                        </div>
                        <!--  id="get_adhaar1" -->
                        <div class="col-sm-4" id="adhaar1" style="display:none;">
                            <input type="text" class="form-control" id="adhaar_no" name="adhaar_no"
                                oninput="person_data1()" />
                        </div>
                        <div id="person_driver" class="col-sm-2" style="display:none;">
                            <label for="colFormLabelSm" class="col-form-label">Driving License No.</label>
                        </div>
                        <div id="person_driver1" class="col-sm-4" style="display:none;">
                            <input type="text" class="form-control" id="driving_license" name="driving_license"
                                oninput="person_data()" />
                        </div>

                    </div>

                    <div id="vehicle_details" style="display:none;">
                        <div class="row">

                            <div class="col-sm-2  ">
                                <label for="colFormLabelSm" class="col-form-label">Purpose of
                                    Cargo</label>
                            </div>
                            <div class="col-sm-2">
                                <label class="col-sm-12 col-form-label  ">Cargo 1</label>
                            </div>
                            <div class="col-sm-2  ">
                                <label for="colFormLabelSm" class="col-form-label">Card Number</label>
                            </div>
                            <div class="col-sm-2">
                                <label class="col-sm-12 col-form-label  ">2422fe1111</label>
                            </div>
                            <div class="col-sm-2 ">
                                <label for="colFormLabelSm" class="col-form-label">Type of Vehicle</label>
                            </div>
                            <div class="col-sm-2">
                                <label class="col-sm-12 col-form-label  ">Truck</label>
                            </div>
                        </div>
                        <div class="row">


                            <div class="col-sm-2  ">
                                <label for="colFormLabelSm" class="col-form-label">EIR Number</label>
                            </div>
                            <div class="col-sm-2">
                                <label class="col-sm-12 col-form-label  ">EIR0001</label>
                            </div>
                            <div class="col-sm-2  ">
                                <label for="colFormLabelSm" class="col-form-label">Insurance Valid
                                    UPTO</label>
                            </div>
                            <div class="col-sm-2">
                                <label class="col-sm-12 col-form-label ">02/08/2025</label>
                            </div>
                            <div class="col-sm-2  ">
                                <label for="colFormLabelSm" class="col-form-label">FC Valid UPTO</label>
                            </div>
                            <div class="col-sm-2">
                                <label class="col-sm-12 col-form-label  ">15/02/2025</label>
                            </div>
                        </div>

                        <div class=" row ">

                            <div class="col-sm-2  ">
                                <label for="colFormLabelSm" class="col-form-label">PUC Valid UPTO</label>
                            </div>
                            <div class="col-sm-2">
                                <label class="col-sm-12 col-form-label  ">12/12/2024</label>
                            </div>
                            <div class="col-sm-2  ">
                                <label for="colFormLabelSm" class="col-form-label">RC Attach</label>
                            </div>
                            <div class="col-sm-2">
                                <img src="resources/views/content/person_registration/PDF.png" alt="PDF"
                                    style="width: 24px; height: 24px;">
                            </div>

                        </div>

                    </div>
                    <div id="person_details" style="display:none;">
                        <div class="row ">
                            <div class="col-sm-2 ">
                                <label for="colFormLabelSm" class="col-form-label">Name</label>
                            </div>
                            <div class="col-sm-2">
                                <label class="col-sm-12 col-form-label  ">Dinesh</label>
                            </div>
                            <div class="col-sm-2 ">
                                <label for="colFormLabelSm" class="col-form-label">Blood Group</label>
                            </div>
                            <div class="col-sm-2">
                                <label class="col-sm-12 col-form-label  ">A+</label>

                            </div>
                            <div class="col-sm-2">
                                <label for="colFormLabelSm" class="col-form-label">Designation</label>
                            </div>
                            <div class="col-sm-2">
                                <label id="person" style="display:none" class="col-sm-12 col-form-label">Person</label>
                                <label id="driver" style="display:none"
                                    class="col-sm-12 col-form-label  ">Person</label>
                            </div>

                        </div>
                        <div class="row ">
                            <div class="col-sm-2  ">
                                <label for="colFormLabelSm" class="col-form-label">Card Number</label>
                            </div>
                            <div class="col-sm-2">
                                <label class="col-sm-12 col-form-label  ">2422fe1111</label>
                            </div>
                            <div class=" col-sm-2 ">
                                <label for=" colFormLabelSm" class="col-form-label">Nationality</label>
                            </div>
                            <div class="col-sm-2">
                                <label class="col-sm-12 col-form-label  ">Indian</label>
                            </div>
                            <div class="col-sm-2">
                                <label for="colFormLabelSm" class="col-form-label">Country</label>
                            </div>
                            <div class="col-sm-2">
                                <label class="col-sm-12 col-form-label">India</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-2  ">
                                <label for="colFormLabelSm" class="col-form-label">VISA No.</label>
                            </div>
                            <div class="col-sm-2">
                                <label class="col-sm-12 col-form-label  ">12345678764325</label>

                            </div>
                            <div class="col-sm-2 ">
                                <label for="colFormLabelSm" class="col-form-label">Mobile</label>
                            </div>
                            <div class="col-sm-2">
                                <label class="col-sm-12 col-form-label  ">9898989098</label>
                            </div>
                            <div class="col-sm-2">
                                <label for="colFormLabelSm" class="col-form-label">E-Mail ID</label>
                            </div>
                            <div class="col-sm-2">
                                <label class="col-sm-12 col-form-label  ">dinesh@gmail.com</label>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-sm-2  ">
                                <label for="colFormLabelSm" class="col-form-label">Type of ID Proof</label>
                            </div>
                            <div class="col-sm-2">
                                <label class="col-sm-12 col-form-label  ">Adhaar Card</label>
                            </div>
                            <div class="col-sm-2  ">
                                <label for="colFormLabelSm" class="col-form-label">ID Proof No.</label>
                            </div>
                            <div class="col-sm-2">
                                <label class="col-sm-12 col-form-label  ">3446567511</label>
                            </div>


                        </div>

                        <div class="row">
                            <div class="col-sm-2  ">
                                <label for="colFormLabelSm" class="col-form-label">ID Proof Attach</label>
                            </div>
                            <div class="col-sm-2">
                                <img onclick="pdf()" target="_blank"
                                    src="resources/views/content/person_registration/PDF.png" alt="PDF"
                                    style="width: 24px; height: 24px;">

                            </div>
                            <div class="col-sm-2  ">
                                <label for="colFormLabelSm" class="col-form-label">Photo</label>
                            </div>
                            <div class="col-sm-4">
                                <img onclick="pdf()" target="_blank"
                                    src="resources/views/content/person_registration/PDF.png" alt="PDF"
                                    style="width: 24px; height: 24px;">
                            </div>

                        </div>
                    </div>
                    <div class="row mb-3 mt-2">
                        <!-- <div class="col-sm-2  ">
                            <label for="colFormLabelSm" class="col-form-label">Access Gate</label>
                        </div>
                        <div class="col-sm-4">
                            <select class="form-select">
                                <option value="">Select Access Gate</option>
                                <option value="1">Inside/Outside</option>
                                <option value="2">Custom Bound Area</option>
                                <option value="3">Zone A & B</option>
                            </select>
                        </div> -->

                        <div class="col-sm-2  ">
                            <label for="colFormLabelSm" class="col-form-label">Access Area</label>
                        </div>
                        <div class="col-sm-4">
                            <select class="form-select me-2" name="cargo" id="cargo">
                                <option value="SBW">SBW</option>
                                <option value="NBW">NBW</option>
                                <option value="NCB">NCB</option>
                                <option value="zone_b">Zone B</option>
                            </select>
                        </div>
                        <div class="col-sm-2" id="vehicle_type_no1" style="display:none;">
                            <label for="colFormLabelSm" class="col-form-label">Vehicle No.</label>
                        </div>
                        <div class="col-sm-4" id="vehicle_type_no" style=" display:none;">
                            <input type="text" class="form-control" id="vech_no" name="vech_no" />
                        </div>
                        <div class="col-sm-2 " id="pass_weight_div" style="display:none">
                            <label for="colFormLabelSm" class="col-form-label">Passing Weight</label>
                        </div>
                        <div class="col-sm-4" id="pass_weight_div1" style="display:none">
                            <input type="text" class="form-control" id="pass_weight" name="pass_weight" />
                        </div>
                    </div>
                    <hr>

                    <div class="row mb-3 mt-2">
                        <div class="col-sm-2  ">
                            <label for="colFormLabelSm" class="col-form-label">Type of Pass</label>
                        </div>
                        <div class="col-sm-4">
                            <select class="form-select me-2" name="passtype" id="passtype">
                                <option value="day">Day</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                                <option value="half yearly">Half Yearly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>
                        <div class=" col-sm-2 ">
                            <label for=" colFormLabelSm" class="col-form-label">Period</label>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="day_period" name="day_period" />
                        </div>
                    </div>

                    <div class="row mb-3 mt-2">
                        <div class="col-sm-2  ">
                            <label for="colFormLabelSm" class="col-form-label">From Date</label>
                        </div>
                        <div class="col-sm-4">
                            <input type="date" class="form-control" id="from_date" name="from_date"
                                value="<?php echo $currentdate;?>" />
                        </div>
                        <div class="col-sm-2  ">
                            <label for="colFormLabelSm" class="col-form-label">To Date</label>
                        </div>
                        <div class="col-sm-4">
                            <input type="date" class="form-control" id="to_date" name="to_date" />
                        </div>
                    </div>

                    <div class="row mb-3 mt-2">
                        <div class="col-sm-2  ">
                            <label for="colFormLabelSm" class="col-form-label">Amount</label>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="amount" name="amount" />
                        </div>
                        <div class="col-sm-2  ">
                            <label for="colFormLabelSm" class="col-form-label">SGST</label>

                        </div>
                        <div class="col-sm-2">
                            <input type="hidden" class="form-control" id="sgst" name="sgst" />
                            <label class="col-sm-12 col-form-label" id="sgst" name="sgst"></label>
                        </div>
                        <div class="col-sm-2  ">
                            <label for="colFormLabelSm" class="col-form-label">CGST</label>
                        </div>
                        <div class="col-sm-2">
                            <input type="hidden" class="form-control" id="cgst" name="cgst" />
                            <label class="col-sm-12 col-form-label " id="cgst" name="cgst"></label>
                        </div>
                    </div>

                    <div class="row mb-3 mt-2 justify-content-center">
                        <div class="col-sm-4 mygr text-center">
                            <label for="gross_amount" class="col-form-label">Total Gross Amount</label>
                        </div>
                        <div class="col-sm-4 mygr text-center">
                            <label class="col-form-label" id="gross_amount_label"></label>
                            <input type="hidden" class="form-control" id="gross_amount" name="gross_amount" />
                        </div>
                    </div>

                    <!-- <div class="row mb-3 mt-2">
                        <div class="col-sm-2  ">
                            <label for="colFormLabelSm" class="col-form-label">C SGST</label>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="vehicle_no" name="vehicle_no" />
                        </div>
                        <div class="col-sm-2  ">
                            <label for="colFormLabelSm" class="col-form-label">C CGST</label>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="vehicle_no" name="vehicle_no" />
                        </div>
                    </div> -->


                    <div class="justify-content-center submit_sublist" style="float: right;">
                        <button id="openTableButton submit_sublist" onclick="sublist_proof(event)"
                            class="btn btn-primary">Add</button>
                    </div>

                    <br><br>
                    <div id="dataTableContainer" class="table-responsive ">
                        <table id="sublist" class="table table-striped table-bordered" style="width:100%">
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
                                    <th>Action</th>
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

                                    <td>
                                        <div class="d-flex align-items-center">
                                            <a href="#" class="text-primary mr-2">
                                                <i class="fas fa-edit"></i> <!-- Edit Icon -->
                                            </a>
                                            <span class="mx-1"></span>
                                        </div>
                                    </td>
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

                                    <td>
                                        <div class="d-flex align-items-center">
                                            <a href="#" class="text-primary mr-2">
                                                <i class="fas fa-edit"></i> <!-- Edit Icon -->
                                            </a> <span class="mx-1"></span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row mb-3 mt-2">
                        <div class="col-sm-3">
                            <label for=" colFormLabelSm" class="col-form-label">Total Pass Amount</label>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="pass_amount" name="pass_amount" value="360" />
                        </div>

                        <div class="col-sm-4">
                            <button type="button" id="openTableButton" class="btn btn-primary"
                                onclick="window.location.href='{{ url('/payment_option')}}'">Payment</button>
                        </div>

                    </div>

                    <div class="btn-float">
                        <button type="button" class="btn btn-danger"
                            onclick="window.location.href='{{ url('/pass_request_from_agent')}}'">Cancel</button>
                        <!-- <button type="submit" class="btn btn-primary">Submit</button> -->
                        <button type="submit"
                            class="btn btn-primary">{{ isset($acc_year) ? 'Update' : 'Submit' }}</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
</div>


<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>

<!-- DataTables CSS -->
<!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css"> -->

<!-- jQuery -->
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->

<!-- DataTables JS -->


<script>
function pass_type() {

    const passType = document.getElementById('passType').value;

    // Hide both vehicle and person divs initially
    document.getElementById("vehicle_div").style.display = "none";
    document.getElementById("vehicle_div1").style.display = "none";
    document.getElementById("person_div").style.display = "none";
    document.getElementById("vehicle_type_div").style.display = "none";
    document.getElementById("vehicle_type_div1").style.display = "none";
    document.getElementById("pass_weight_div").style.display = "none";
    document.getElementById("vehicle_type_no").style.display = "none";
    document.getElementById("vehicle_type_no1").style.display = "none";




    // document.getElementById("person_div1").style.display = "none";

    // Show the appropriate div based on selected option
    if (passType === 'vehicle') {
        // document.getElementById("vehicle_div").style.display = "block";
        // document.getElementById("vehicle_div1").style.display = "block";
        document.getElementById("vehicle_type_div").style.display = "block";
        document.getElementById("vehicle_type_div1").style.display = "block";
        document.getElementById("person_div").style.display = "none";
        document.getElementById("person_div1").style.display = "none";
        document.getElementById("vehicle_type_no").style.display = "none";
        document.getElementById("vehicle_type_no1").style.display = "none";


        document.getElementById("person_driver").style.display = "none";
        document.getElementById("person_driver1").style.display = "none";

        document.getElementById("adhaar").style.display = "none";

        document.getElementById("adhaar1").style.display = "none";

        document.getElementById("pass_weight_div").style.display = "block";
        document.getElementById("pass_weight_div1").style.display = "block";
        // document.getElementById("person_details").style.display = "none";
        // document.getElementById("vehicle_details").style.display = "block";

        // document.getElementById("vehicle_details").style.display = "block";
        // document.getElementById('adhaar').value = '';
        // document.getElementById("vec_vec_reg_no").value = "";

        // document.getElementById("get_adhaar").style.display = "none";
        // document.getElementById("get_adhaar1").style.display = "none";



    } else if (passType === 'person') {
        document.getElementById("person_div").style.display = "block";
        document.getElementById("person_div1").style.display = "block";
        document.getElementById("vehicle_type_no").style.display = "block";
        document.getElementById("vehicle_type_no1").style.display = "block";
        // document.getElementById("get_adhaar").style.display = "block";
        // document.getElementById("get_adhaar1").style.display = "block";

        document.getElementById("vehicle_type_no").style.display = "block";
        document.getElementById("vehicle_type_no1").style.display = "block";
        document.getElementById("pass_weight_div").style.display = "none";
        document.getElementById("pass_weight_div1").style.display = "none";
        document.getElementById("vehicle_div").style.display = "none";
        document.getElementById("vehicle_div1").style.display = "none";


    } else {
        document.getElementById("person_div").style.display = "none";
        document.getElementById("person_div1").style.display = "none";
        document.getElementById("vehicle_type_no").style.display = "none";
        document.getElementById("vehicle_type_no1").style.display = "none";
        // document.getElementById("get_adhaar").style.display = "block";
        // document.getElementById("get_adhaar1").style.display = "block";

        document.getElementById("vehicle_type_no").style.display = "none";
        document.getElementById("vehicle_type_no1").style.display = "none";
        document.getElementById("pass_weight_div").style.display = "none";
        document.getElementById("pass_weight_div1").style.display = "none";
        // document.getElementById("vehicle_details").style.display = "none";
        // document.getElementById("person_details").style.display = "none";
    }
}


function person_type() {
    const person_type = document.getElementById('persontype').value;
    // alert(person_type);

    // Hide both vehicle and person divs initially
    document.getElementById("person_driver").style.display = "none";
    document.getElementById("person_driver1").style.display = "none";
    // aaadhar
    document.getElementById("adhaar").style.display = "none";

    document.getElementById("adhaar1").style.display = "none";


    // Show the appropriate div based on selected option
    if (person_type === '66fe76256d3a792499') {
        document.getElementById("person_driver").style.display = "block";
        document.getElementById("person_driver1").style.display = "block";
    } else {
        document.getElementById("adhaar").style.display = "block";
        document.getElementById("adhaar1").style.display = "block";

    }
}



// new changes
function vech_type() {
    const vech_type = document.getElementById('vectype').value;
    // alert(vech_type);

    // Hide both vehicle and person divs initially
    document.getElementById("vehicle_div").style.display = "none";
    document.getElementById("vehicle_div1").style.display = "none";
    // vehicle_div1

    document.getElementById("vehicle_div1").style.display = "none";

    document.getElementById("vehicle_div1").style.display = "none";


    // Show the appropriate div based on selected option
    if (vech_type != "") {
        document.getElementById("vehicle_div").style.display = "block";
        document.getElementById("vehicle_div").style.display = "block";
        document.getElementById("vehicle_div1").style.display = "block";

        document.getElementById("vehicle_div1").style.display = "block";

    }


}

// end

function vehicle_data() {
    // vehicle_details
    const vehicle_validate = document.getElementById('vec_reg_no').value;
    // alert(vehicle_validate);

    document.getElementById("vehicle_details").style.display = "none";

    if (vehicle_validate != '') {

        document.getElementById('adhaar_no').value = '';
        // document.getElementById("adhaar").style.display = "none";
        document.getElementById("person_details").style.display = "none";
        document.getElementById("vehicle_details").style.display = "block";
    }

}

function person_data() {
    const person_validate = document.getElementById('driving_license').value;
    // alert(person_validate);
    document.getElementById("person_details").style.display = "none";
    if (person_validate != '') {
        document.getElementById('vec_reg_no').value = '';

        document.getElementById("vehicle_details").style.display = "none";
        document.getElementById("person_details").style.display = "block";
        document.getElementById("driver").style.display = "block";

    }
}


function person_data1() {
    const person_validate = document.getElementById('adhaar_no').value;
    // alert(person_validate);
    document.getElementById("person_details").style.display = "none";
    if (person_validate != '') {
        document.getElementById('vec_reg_no').value = '';

        // document.getElementById('vec_reg_no').value = '';

        document.getElementById("vehicle_details").style.display = "none";
        document.getElementById("person_details").style.display = "block";
        // document.getElementById("person").style.display = "block";
        document.getElementById("adhaar").style.display = "block";

        document.getElementById("adhaar1").style.display = "block";


    }

}



// nw changes


//document details
function sublist_proof(event) {
    alert();

    event.preventDefault()
    // Get form values
    var passtype = $('#passtype').val();
    var day_period = $('#day_period').val();
    var from_date = $('#from_date').val();
    var to_date = $('#to_unique_id').val();
    var amount = $('#amount').val();
    var sgst = $('#sgst').val();
    var cgst = $('#cgst').val();
    var gross_amount = $('#gross_amount').val();


    // alert(licence_val_sub);

    var s_no = 1;

    var s_nos = s_no++;

    // Set default for none values

    // if (aadhaar_num != '') {
    //     proof_value = aadhaar_num;
    // } else {
    //     proof_value = licence_no;
    // }


    // if (aadhaar_num === '') {
    //     aadhaar_num = 'none';
    // }

    // if (licence_no === '') {
    //     licence_no = 'none';
    // }

    // Create FormData object to handle file uploads
    var formData = new FormData();
    formData.append('passtype', passtype);
    formData.append('day_period', day_period);
    formData.append('from_date', from_date);
    formData.append('to_date', to_date);
    formData.append('amount', amount);
    formData.append('sgst', sgst);
    formData.append('cgst', cgst);
    formData.append('gross_amount', gross_amount);
    // formData.append('licence_no', licence_no);
    formData.append('_token', '{{csrf_token()}}'); // Add CSRF token for security


    // AJAX request to submit form data
    $.ajax({
        type: "POST",
        url: '{{route("pass_sublist.create")}}', // Update the route
        data: formData,
        contentType: false, // Important for file upload
        processData: false, // Prevent jQuery from processing the data


        success: function(response) {

            // var file_name = response.attached_proof;
            // var proof_type = response.proof_type;
            var newRow = `
            <tr>
                <td>${s_nos}</td>
                <td>${passtype}</td>
                <td>${day_period}</td>
                <td>${from_date}</td>
                <td>${to_date}</td>
                 <td>${amount}</td>
                <td>${sgst}</td>
                  <td>${cgst}</td>
                <td>${gross_amount}</td>
                
            </tr>
        `;

            // Append the new row to the tbody of the #sublist table
            $('#sublist tbody').append(newRow);

            // Optionally, reset form fields after successful submission
            $('#passtype').val('');
            $('#day_period').val('');
            $('#from_date').val('');
            $('#to_date').val('');
            $('#amount').val('');
            $('#sgst').val('');
            $('#cgst').val('');
            $('#gross_amount').val('');
        },
        error: function(xhr) {
            console.log(xhr.responseText); // Log any errors for debugging
        }
    });
}
</script>


@endsection