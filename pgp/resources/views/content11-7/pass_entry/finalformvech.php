@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')
<div class="row   page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Pass/</span>Pass Entry</h4>
    </div>
</div>

<style>
/* new changes */
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

.pdf-container {
    width: 50px;
    height: 50px;
    background-size: cover;
    /* Ensure the image covers the entire div */
    background-position: center;
    /* Center the image */
    cursor: pointer;
    /* Show pointer cursor on hover */
}
</style>

@php



//$currentdate = date('Y-m-d');
//$currentdate = date('Y-m-d\TH:i'); // Format: YYYY-MM-DDTHH:MM
$get_firm_reg_type =get_firm_reg_type();
$get_agent_type =get_agent_type();

$get_vech_type =get_vech_type();

$get_person_type = get_person_type();

$screen_unique_id=generateScreenUniqueId();


date_default_timezone_set('Asia/Kolkata');

// Get the current date and time in the correct format
$currentDateTime = date('Y-m-d\TH:i'); // Format: YYYY-MM-DDTH:MM
@endphp
<!-- Basic Layout -->
<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-body">
                <div id="academicYearForm">
                    @csrf
                    @if(isset($pass_entry_main))
                    @method('PUT')
                    @endif
                    <div class="row mb-3 mt-2">
                        <div class="col-sm-2  ">
                            <input type="hidden" id="screen_unique_id" name="screen_unique_id"
                                value="<?php echo $screen_unique_id;?>">
                            <!-- <input type="text" id="pass_id" name="pass_id"> -->
                            <label for="colFormLabelSm" class="col-form-label">Issue Center</label>
                        </div>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" id="issue_center" name="issue_center" />
                        </div>
                        <div class="col-sm-2" style="display:none">
                            <label for="colFormLabelSm" class="col-form-label">Status</label>
                        </div>
                        <div class="col-sm-2">
                            <select class="form-select" name="pass_status" id="pass_status" style="display:none">
                                <!-- <option value="">Select Status</option> -->
                                <option value="Approved"
                                    {{ isset($pass_entry_main) && $pass_entry_main->pass_status == 'Approved' ? 'selected' : '' }}>
                                    Approved</option>
                                <option value="NotApproved"
                                    {{ isset($pass_entry_main) && $pass_entry_main->pass_status == 'NotApproved' ? 'selected' : '' }}>
                                    NotApproved</option>
                            </select>
                        </div>
                        <div class="col-sm-2 mt-2">
                            <label class="col-form-label" for="pas_id" style="display:none;">PASS ID:</label>
                        </div>
                        <div class="col-sm-2">
                            <label class="col-sm-12 col-form-label mt-3 firm-id-label">
                                <!-- <b id="pass_id">{{ isset($pass_id) ? $pass_id : '' }}</b> -->
                            </label>
                            <input type="hidden" id="hidden_pass_id" name="pass_id" value="">
                        </div>
                        <!-- <div class="col-md-2"> -->

                    </div>
                    <hr>
                    <b>Company Details</b>
                    <div class="row mb-3 mt-2">
                        <div class="col-sm-2  ">
                            <label for="colFormLabelSm" class="col-form-label">Firms /Agent Type</label>
                        </div>
                        <div class="col-sm-4">
                            <!-- <select class="form-select" id="firms_agent" name="firms_agent">
                                <option value="">Select Agent Name</option>
                                <option value="ship_agent">Ship Agent</option>
                                <option value="customer_broker">Customer Broker</option>
                            </select> -->
                            <select class="form-select" id="firms_agent_type" name="firms_agent_type">
                                <option value="">
                                    Select </option>
                                @foreach ($get_agent_type as $firm_agent_type)
                                <option value="{{$firm_agent_type->unique_id}}">
                                    {{$firm_agent_type->company_name}}
                                </option>
                                @endforeach
                            </select>
                        </div>
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
                                    Select Agent/Firms</option>

                            </select>
                        </div>
                        <!-- <div class="row mb-3 mt-2">
                            <div id="upload" class="col-sm-2"> <label for="photo">Cover Letter</label></div>
                            <div class="col-sm-4">
                                <input type="file" class="form-control" id="cover_letter" name="cover_letter" />
                            </div>
                        </div> -->
                        <div class="row mb-3 mt-2">
                            <div id="upload" class="col-sm-2">
                                <label for="cover_letter">Upload File</label>
                            </div>
                            <div class="col-sm-4">
                                <input type="file" class="form-control" id="cover_letter" name="cover_letter"
                                    accept=".pdf, .xlsx, .xls, .png, .jpg, .jpeg" onchange="showFilePreview()" />
                            </div>
                            <div class="col-sm-2" id="file_preview"></div>
                            <div class="col-sm-2" id="file_name" style="display:none"></div>
                        </div>
                    </div>
                    <hr>
                    <b>Person /Vehicle Details</b>
                    <div class="row mb-3 mt-2">

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
                        <div class="col-sm-3" id="vehicle_div1" style="display:none;">
                            <input type="text" class="form-control" id="vec_reg_no" name="vec_reg_no"
                                oninput="vehicle_data()" />
                            <span id="reg_error" style="color: red; display: none;">Invalid registration number. Please
                                Reg.</span>
                        </div>
                        <div class="col-sm-1" id="vehicle_div1_add" style="display:none;">
                            <span><button type="button">add</button></span>
                            <!-- <button type="button">add</button> -->
                            <!-- <input type="text" class="form-control" id="vec_reg_no"
                                name="vec_reg_no" oninput="vehicle_data()" /> -->
                            <!-- <span id="reg_error" style="color: red; display: none;">Invalid registration number. Please -->
                            <!-- Reg.</span> -->
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
                                <label class="col-sm-12 col-form-label  "></label>
                            </div>
                            <div class="col-sm-2  ">
                                <label for="colFormLabelSm" class="col-form-label">Insurance
                                    Valid
                                    UPTO</label>
                            </div>
                            <div class="col-sm-2">
                                <label class="col-sm-12 col-form-label" id="ins_valid_up_to"></label>
                            </div>
                            <div class="col-sm-2 ">
                                <label for="colFormLabelSm" class="col-form-label">FC Valid UPTO</label>
                            </div>
                            <div class="col-sm-2">
                                <label class="col-sm-12 col-form-label  " id="fc_valid_up_to"></label>
                            </div>
                        </div>

                        <div class=" row ">

                            <div class="col-sm-2  ">
                                <label for="colFormLabelSm" class="col-form-label">PUC Valid UPTO</label>
                            </div>
                            <div class="col-sm-2">
                                <label class="col-sm-12 col-form-label" id="puc_valid_up_to"></label>
                            </div>
                            <div class="col-sm-2  ">
                                <label for="colFormLabelSm" class="col-form-label">RC Attach</label>
                            </div>
                            <!-- <div class="col-sm-2"> -->
                            <!-- <img id="pdf_image" src="" onclick="pdf()" style="width: 138px; height: 104px;" -->
                            <!-- alt="PDF Thumbnail"> -->
                            <!-- <img onclick="pdf()" target="_blank" id="pdf_image"
                                    src="{{ asset('public/uploads/vehicle_registration/PDF.png') }}"
                                    style="width: 50px;height: 50px;"> -->
                            <!-- </div> -->
                            <div class="col-sm-2 pdf-container" id="pdf_container" onclick="downloadPdf()">
                                <span> <img target="_blank" id="pdf_image"
                                        src="{{ asset('public/uploads/vehicle_registration/PDF.png') }}"
                                        style="width: 50px;height: 50px;"></span>
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
                            <div class="col-sm-2  ">
                                <label for="colFormLabelSm" class="col-form-label">Photo</label>
                            </div>
                            <div class="col-sm-2">
                                <img onclick="pdf()" target="_blank" src="{{ asset('public/assets/img/profiles.png') }}"
                                    style="width: 138px;height: 104px;">
                            </div>

                        </div>
                        <div class="row" style="margin-top:-58px">
                            <div class="col-sm-2">
                                <label for="colFormLabelSm" class="col-form-label">E-Mail ID</label>
                            </div>
                            <div class="col-sm-2">
                                <label class="col-sm-12 col-form-label  ">dinesh@gmail.com</label>
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
                                <label for="colFormLabelSm" class="col-form-label">Type of ID Proof</label>
                            </div>
                            <div class="col-sm-2">
                                <label class="col-sm-12 col-form-label  ">Adhaar Card</label>
                            </div>

                            <div class="col-sm-2  ">
                                <label for="colFormLabelSm" class="col-form-label">ID Proof Attach</label>
                            </div>
                            <div class="col-sm-2">
                                <img onclick="pdf()" target="_blank"
                                    src="resources/views/content/person_registration/PDF.png" alt="PDF"
                                    style="width: 24px; height: 24px;">

                            </div>



                        </div>



                        <div class="row">
                            <!-- <div class="col-sm-2  ">
                                <label for="colFormLabelSm" class="col-form-label">ID Proof Attach</label>
                            </div>
                            <div class="col-sm-2">
                                <img onclick="pdf()" target="_blank"
                                    src="resources/views/content/person_registration/PDF.png" alt="PDF"
                                    style="width: 24px; height: 24px;">

                            </div> -->
                            <div class="col-sm-2" id="vehicle_type_no1" style="display:none;">
                                <label for="colFormLabelSm" class="col-form-label">Vehicle No.</label>
                            </div>
                            <div class="col-sm-4" id="vehicle_type_no" style=" display:none;">
                                <input type="text" class="form-control" id="vech_no" name="vech_no" />
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





                        <div class="col-sm-2 " id="pass_weight_div" style="display:none">
                            <label for="colFormLabelSm" class="col-form-label">Passing Weight</label>
                        </div>
                        <div class="col-sm-4" id="pass_weight_div1" style="display:none">
                            <input type="text" class="form-control" id="pass_weight" name="pass_weight" />
                        </div>
                    </div>

                    <hr>
                    <b>Pass Entry Details</b>
                    <div class="row mb-3 mt-2">
                        <div class="col-sm-2">
                            <label for="colFormLabelSm" class="col-form-label">Access Area</label>
                        </div>
                        <div class="col-sm-2">
                            <select class="form-select me-2" name="access_area" id="access_area">
                                <option value="">Select</option>
                                <option value="SBW">SBW</option>
                                <option value="NBW">NBW</option>
                                <option value="NCB">NCB</option>
                                <option value="zone_b">Zone B</option>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <label for="colFormLabelSm" class="col-form-label">Type of Pass</label>
                        </div>
                        <!-- </div> -->
                        <div class="col-sm-2">
                            <select class="form-select me-2" name="type_of_pass" id="type_of_pass"
                                Onchange="get_vec_pass()">
                                <option value="">Select</option>
                                <option value="day">Day</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                                <option value="halfyearly">Half Yearly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>
                        <div class=" col-sm-2">
                            <label for=" colFormLabelSm" class="col-form-label">Period</label>
                        </div>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" id="no_of_days" name="no_of_days"
                                oninput="calculateToDate()" />
                        </div>

                        <!-- Element to display formatted date -->
                    </div>

                    <!-- <div class="row mb-3 mt-2">


                    </div> -->

                    <div class="row mb-3 mt-2">
                        <div class="col-sm-2">
                            <label for="colFormLabelSm" class="col-form-label">From Date</label>
                        </div>
                        <div class="col-sm-2">
                            <!-- <input type="datetime-local" class="form-control" id="from_date" name="from_date" -->
                            <!-- value="<?php //echo $currentdate;?>" /> -->
                            <!-- <input type="datetime-local" class="form-control" id="from_date" name="from_date"
                                value="<?php //echo $currentDateTime; ?>" /> -->
                            <input type="datetime-local" class="form-control" id="from_date" name="from_date"
                                value="<?php echo $currentDateTime; ?>" />
                            <input type="hidden" id="from_date_display" class="form-control" readonly />
                        </div>
                        <div class="col-sm-2">
                            <label for="colFormLabelSm" class="col-form-label">Valitity Expiry Date</label>
                        </div>
                        <div class="col-sm-2">
                            <!-- <input type="date" class="form-control" id="to_date" name="to_date" /> -->
                            <input type="datetime-local" class="form-control" id="to_date" name="to_date" />
                        </div>
                        <!-- style="display:none" -->
                        <input type="hidden" id="date_display" name="date_display">

                        <div class="col-sm-1">
                            <label for="amount" class="col-form-label">Amount</label>
                        </div>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="amount" name="amount"
                                oninput="calculateamount()" />
                        </div>
                    </div>
                    <div class="row  justify-content-end no-margin">
                        <div class="col-sm-2 text-end">
                            <label for="sgst" class="col-form-label">SGST9%</label>
                        </div>
                        <div class="col-sm-2 text-end">
                            <label class="col-form-label" id="sgstLabel"></label>
                            <input type="hidden" class="form-control" id="sgst" name="sgst" />
                        </div>
                    </div>
                    <div class="row justify-content-end">
                        <div class="col-sm-2 text-end">
                            <label for="cgst" class="col-form-label">CGST9%</label>
                        </div>
                        <div class="col-sm-2 text-end">
                            <label class="col-form-label" id="cgstLabel"></label>
                            <input type="hidden" class="form-control" id="cgst" name="cgst" />
                        </div>
                    </div>
                    <div class="row  justify-content-end">
                        <div class="col-sm-2 text-end">
                            <label for="gross_amount" style="display:none" class="col-form-label">Total Gross
                                Amount</label>
                        </div>

                        <div class="col-sm-2 text-end">
                            <label style="display:none" class="col-form-label" id="gross_amountlabel"
                                name="gross_amountlabel"></label>
                            <input type="hidden" class="form-control" id="gross_amount" name="gross_amount" />
                            <input type="hidden" class="form-control" id="net_amount" name="net_amount" />
                        </div>
                    </div>

                </div>
                <!-- </div> -->
                <!-- </form> -->


                <!-- <div class="row mb-2 mt-2 justify-content-center"> -->
                <!-- <div class="col-sm-4 text-center ">
                        <label for="gross_amount" class="col-form-label">Total Gross Amount</label>
                    </div>
                    <div class="col-sm-4  text-center">
                        <label class="col-form-label" id="gross_amountlabel" name="gross_amountlabel"></label>
                        <input type="hidden" class="form-control" id="gross_amount" name="gross_amount" />
      div           </div> -->
                <!-- </div> -->
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
                                <th>Agent Name.</th>
                                <th>Pass Type.</th>
                                <th>No of Period</th>
                                <th>From Date</th>
                                <th>Validity Expiry Date</th>
                                <th>gross Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="row mb-3 mt-2 justify-content-end">
                    <div class="col-sm-3">
                        <label for=" colFormLabelSm" class="col-form-label"><b>Total Pass Amount</b></label>
                    </div>
                    <div class="col-sm-2">
                        <label class=" col-form-label" id="pass_amount_label"></label>
                        <input type="hidden" class="form-control" id="pass_amount" name="pass_amount" />
                    </div>

                    <!-- <div class="col-sm-4">
                            <button type="button" id="openTableButton" class="btn btn-primary"
                                onclick="window.location.href='{{ url('/payment_option')}}'">Payment</button>
                        </div> -->

                </div>

                <div class="btn-float">
                    <button type="button" class="btn btn-danger"
                        onclick="window.location.href='{{ url('/pass_entry')}}'">Cancel</button>
                    <!-- <button type="submit" class="btn btn-primary">Submit</button> -->
                    <!-- <button type="submit"
                            class="btn btn-primary">{{ isset($acc_year) ? 'Update' : 'Submit' }}</button> -->
                    <button type="button" class="btn btn-primary" onclick="submit_save()">Payment Go to
                        Save</button>

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

<script>
function showFilePreview() {
    const input = document.getElementById('cover_letter');
    const fileNameDisplay = document.getElementById('file_name');
    const filePreviewDisplay = document.getElementById('file_preview');

    fileNameDisplay.textContent = '';

    if (input.files.length > 0) {
        const file = input.files[0];
        fileNameDisplay.textContent = ` ${file.name}`;

        // Clear previous preview
        filePreviewDisplay.innerHTML = '';

        // Create a link for downloading
        const fileLink = document.createElement('a');
        fileLink.href = URL.createObjectURL(file); // Create a URL for the file
        fileLink.download = file.name; // Set the download attribute to the file name

        // Show preview for images
        if (file.type.startsWith('image/')) {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.style.maxWidth = '50%';
            img.style.maxHeight = '100px';

            fileLink.appendChild(img);
        }
        // Show PDF icon for PDF files
        else if (file.type === 'application/pdf') {
            const pdfIcon = document.createElement('img');
            pdfIcon.src = "{{ asset('public/uploads/pass_entry/pdf.png') }}"; // PDF icon path
            pdfIcon.alt = 'PDF Document';
            pdfIcon.style.maxWidth = '20px'; // Adjust size as needed

            fileLink.appendChild(pdfIcon);
        }
        // Handle Excel files and other types
        else {
            fileLink.textContent = `Download ${file.name}`;
            fileLink.style.display = 'block'; // Make the link visible
        }

        filePreviewDisplay.appendChild(fileLink);
    } else {
        filePreviewDisplay.textContent = '';
    }
}
$('#firms_agent_type').on('change', function() {
    // alert('hi');
    // alert($('#off_division_name').val());
    $('#firms_agent').empty();
    $.ajax({
        type: "GET",
        url: '{{ route("firms_list.index") }}',
        data: {
            'firms_agent_type': $(this).val()
        },
        cache: false,
        success: function(data) {
            if (data && data.length > 0) {
                // alert(data);
                data = $.parseJSON(data); //parse response string
                // alert(data.offence_names);
                // var k=0;
                // console.log(data);

                $.each(data.firms_list, function(i, item) {
                    console.log(item.unique_id);
                    // alert(item.unique_id);
                    $('#firms_agent').append($('<option/>', {
                        value: item.unique_id,
                        text: item.name
                    }));

                });

            }
            //   console.log("3:"+data);
        }
    });
})
$(document).ready(function() {
    // Fetch the last firm number when the document is ready
    $.ajax({
        url: '{{ url("/get-last-firm-number") }}',
        method: 'GET',
        success: function(response) {
            // Update the Firm ID label with the new Firm ID
            // $('#pass_id').text(response.pass_id);
            $('#hidden_pass_id').val(response.pass_id);
        },
        error: function(xhr, status, error) {
            console.error("Error fetching Pass ID:", error);
            $('#pass_id').text('Error loading Pass ID');
        }
    });
});

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

        document.getElementById('persontype').value = '';
        // document.getElementById('adhaar_no').value = '';
        // document.getElementById('person_details').value = '';

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
        document.getElementById('vectype').value = '';
        // document.getElementById('vec_reg_no').value = '';
        // document.getElementById('vehicle_details').value = '';

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

// function vehicle_data() {
//     // vehicle_details
//     const vehicle_validate = document.getElementById('vec_reg_no').value;
//     // alert(vehicle_validate);

//     document.getElementById("vehicle_details").style.display = "none";

//     if (vehicle_validate) {
//         document.getElementById('adhaar_no').value = '';
//         // document.getElementById('persontype').value = '';
//         // document.getElementById("adhaar").style.display = "none";
//         document.getElementById("person_details").style.display = "none";
//         document.getElementById("vehicle_details").style.display = "block";
//     }
//     $.ajax({
//         type: "GET",
//         url: '{{ route("vech_register_no.index") }}',
//         data: {
//             'vec_reg_no': $(this).val()
//         },
//         cache: false,
//         success: function(data) {
//             if (data && data.length > 0) {
//                 // alert(data);
//                 data = $.parseJSON(data); //parse response string
//                 // alert(data.offence_names);
//                 // var k=0;
//                 // console.log(data);

//                 $.each(data.vech_register_no, function(i, item) {
//                     console.log(item.vech_reg_no);
//                     // alert(item.unique_id);
//                     // $('#firms_agent').append($('<option/>', {
//                     //     value: item.unique_id,
//                     //     text: item.name
//                     // }));
//                     docuent.getElementById(item.vech_reg_no).value;

//                 });

//             }
//             //   console.log("3:"+data);
//         }
//     });

// }

// function vehicle_data() {
//     const vehicle_validate = document.getElementById('vec_reg_no').value.trim();

//     // Initially hide vehicle details
//     document.getElementById("vehicle_details").style.display = "none";

//     if (vehicle_validate) {
//         // AJAX request to fetch vehicle registration details
//         $.ajax({
//             type: "GET",
//             url: '{{ route("vech_register_no.index") }}',
//             data: {
//                 'vec_reg_no': vehicle_validate // Pass the vehicle registration number
//             },
//             cache: false,
//             success: function(data) {
//                 // Check if any vehicle registration was found
//                 if (data.length > 0) {
//                     // Process the response data
//                     $.each(data, function(i, item) {
//                         console.log(item.vech_reg_no); // Adjust according to your data structure
//                         // Display or process the vehicle details as needed
//                     });

//                     // Show vehicle details input
//                     document.getElementById("vehicle_details").style.display = "block";
//                 } else {
//                     // Alert if no vehicle registration found
//                     alert("Invalid registration number. Please check and try again.");
//                     console.log("No vehicle registration found.");
//                 }
//             },
//             error: function(xhr, status, error) {
//                 console.error("AJAX Error: " + status + " - " + error);
//                 alert("An error occurred while checking the registration number.");
//             }
//         });
//     }
// }

// function vehicle_data() {
//     const vehicle_validate = document.getElementById('vec_reg_no').value.trim();

//     // Initially hide vehicle details
//     document.getElementById("vehicle_details").style.display = "none";

//     // Check if the input is empty
//     if (!vehicle_validate) {

//         alert("Please enter a registration number.");
//         document.getElementById('vec_reg_no').focus(); // Focus back on the input
//         return; // Exit the function if validation fails
//     }

//     // AJAX request to fetch vehicle registration details
//     $.ajax({
//         type: "GET",
//         url: '{{ route("vech_register_no.index") }}',
//         data: {
//             'vec_reg_no': vehicle_validate // Pass the vehicle registration number
//         },
//         cache: false,
//         success: function(data) {
//             // Check if data is an array and not empty
//             if (Array.isArray(data) && data.length > 0 && data[0].vech_reg_no === vehicle_validate) {
//                 // Show vehicle details input if valid data is returned
//                 document.getElementById("vehicle_details").style.display = "block";
//             } else {
//                 // Hide vehicle details if no registration is found
//                 document.getElementById("vehicle_details").style.display = "none";
//                 console.log("No vehicle registration found.");

//                 document.getElementById('vec_reg_no').focus(); // Focus back on the input
//             }
//             // alert("An error occurred while checking the registration number.");

//         },

//         error: function(xhr, status, error) {
//             console.error("AJAX Error: " + status + " - " + error);
//             alert("An error occurred while checking the registration number.");
//             document.getElementById('vec_reg_no').focus(); // Focus back on the input
//         }

//     });
// }
// function vehicle_data() {
//     const vehicle_validate = document.getElementById('vec_reg_no').value.trim();
//     const regError = document.getElementById('reg_error');

//     // Initially hide vehicle details and error message
//     document.getElementById("vehicle_details").style.display = "none";
//     document.getElementById("vehicle_div1_add").style.display = "none";
//     regError.style.display = "none";

//     // Check if the input is empty
//     if (!vehicle_validate) {
//         // alert("Please enter a registration number.");
//         document.getElementById('vec_reg_no').focus(); // Focus back on the input
//         return; // Exit the function if validation fails
//     }

//     // AJAX request to fetch vehicle registration details
//     $.ajax({
//         type: "GET",
//         url: '{{ route("vech_register_no.index") }}',
//         data: {
//             'vec_reg_no': vehicle_validate // Pass the vehicle registration number
//         },
//         cache: false,
//         success: function(data) {
//             // Check if registration details are returned
//             if (data.registration_details && data.registration_details.length > 0) {
//                 // Show vehicle details if valid data is returned
//                 document.getElementById("vehicle_details").style.display = "block";
//                 document.getElementById("vehicle_div1_add").style.display = "none";
//                 regError.style.display = "none";

//                 // Display valid_up_to date
//                 document.getElementById('valid_up_to_label').innerText = data.valid_up_to ||
//                     'Not available';
//             } else {
//                 // Hide vehicle details and show error message if no registration is found
//                 document.getElementById("vehicle_details").style.display = "none";
//                 document.getElementById("vehicle_div1_add").style.display = "block";

//                 regError.style.display = "block"; // Show error message
//                 document.getElementById('vec_reg_no').focus(); // Focus back on the input
//             }
//         },
//         error: function(xhr, status, error) {
//             console.error("AJAX Error: " + status + " - " + error);
//             alert("An error occurred while checking the registration number.");
//             document.getElementById('vec_reg_no').focus(); // Focus back on the input
//             regError.style.display = "none"; // Hide error message on AJAX error
//         }
//     });
// }

function vehicle_data() {
    const vehicle_validate = document.getElementById('vec_reg_no').value.trim();
    const regError = document.getElementById('reg_error');

    // Initially hide vehicle details and error message
    document.getElementById("vehicle_details").style.display = "none";
    document.getElementById("vehicle_div1_add").style.display = "none";
    regError.style.display = "none";

    // Check if the input is empty
    if (!vehicle_validate) {
        alert("Please enter a registration number.");
        document.getElementById('vec_reg_no').focus(); // Focus back on the input
        return; // Exit the function if validation fails
    }

    // AJAX request to fetch vehicle registration details
    $.ajax({
        type: "GET",
        url: '{{ route("vech_register_no.index") }}',
        data: {
            'vec_reg_no': vehicle_validate // Pass the vehicle registration number
        },
        cache: false,
        success: function(data) {
            console.log(data); // Log response

            if (data.vech_register_no) {
                const vehicle = data.vech_register_no; // Access the vehicle

                // Access the vehicle document data
                const documents = data.vech_get_data;

                // Show vehicle details
                document.getElementById("vehicle_details").style.display = "block";
                document.getElementById("vehicle_div1_add").style.display = "none";
                regError.style.display = "none";

                // Clear previous values
                document.getElementById('ins_valid_up_to').innerText = 'Not available';
                document.getElementById('fc_valid_up_to').innerText = 'Not available';
                document.getElementById('puc_valid_up_to').innerText = 'Not available';
                // document.getElementById('pdf_image').src = ''; // Clear previous image source

                // Loop through documents and set validity dates
                documents.forEach(doc => {
                    if (doc.certificate_type === 'Insurance') {
                        document.getElementById('ins_valid_up_to').innerText = doc.valid_up_to ||
                            'Not available';
                    } else if (doc.certificate_type === 'Fitness') {
                        document.getElementById('fc_valid_up_to').innerText = doc.valid_up_to ||
                            'Not available';
                    } else if (doc.certificate_type === 'PUC') {
                        document.getElementById('puc_valid_up_to').innerText = doc.valid_up_to ||
                            'Not available';
                    } else if (doc.certificate_type === 'RC') {
                        const imagePath = "{{ asset('public/uploads/vehicle_registration/') }}/" +
                            doc.attached_proof;
                        console.log(imagePath); // Log the formatted path for debugging

                        // Set the PDF container background image
                        const pdfContainer = document.getElementById('pdf_container');
                        pdfContainer.style.backgroundImage =
                            `url('${imagePath}')`; // Set background image
                        pdfContainer.setAttribute('data-pdf-url',
                            imagePath); // Store the full PDF URL
                    }
                });
            } else {
                // Handle case where no registration is found
                document.getElementById("vehicle_details").style.display = "none";
                document.getElementById("vehicle_div1_add").style.display = "block";
                regError.style.display = "block"; // Show error message
                document.getElementById('vec_reg_no').focus(); // Focus back on the input
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error: " + status + " - " + error);
            alert("An error occurred while checking the registration number.");
            document.getElementById('vec_reg_no').focus(); // Focus back on the input
            regError.style.display = "none"; // Hide error message on AJAX error
        }
    });
}

function downloadPdf() {
    alert();
    const pdfUrl = document.getElementById('pdf_container').getAttribute('data-pdf-url');
    if (pdfUrl) {
        const a = document.createElement('a');
        a.href = pdfUrl;
        a.download = pdfUrl.split('/').pop(); // Set the filename
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a); // Clean up
    } else {
        console.error('PDF URL not found');
    }
}

// Function to handle PDF download
// function downloadPdf() {
//     alert();
//     const pdfUrl = document.getElementById('pdf_image').getAttribute('data-pdf-url');
//     if (pdfUrl) {
//         const a = document.createElement('a');
//         a.href = pdfUrl;
//         a.download = pdfUrl.split('/').pop(); // Set the filename
//         document.body.appendChild(a);
//         a.click();
//         document.body.removeChild(a); // Clean up
//     } else {
//         console.error('PDF URL not found');
//     }
// }

function person_data() {
    const person_validate = document.getElementById('driving_license').value;
    // alert(person_validate);
    document.getElementById("person_details").style.display = "none";
    if (person_validate) {
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



// nw changes sublist


//document details
// Declare s_no outside the function
var s_no = 1;

function sublist_proof(event) {
    event.preventDefault();

    // Get form values
    var firms_agent = $('#firms_agent').val();
    var type_of_pass = $('#type_of_pass').val();
    var no_of_days = $('#no_of_days').val();
    var from_date = $('#from_date_display').val();
    var to_date = $('#date_display').val();
    var amount = $('#amount').val();
    var sgst = $('#sgst').val();
    var cgst = $('#cgst').val();
    var screen_unique_id = $('#screen_unique_id').val();
    var gross_amount = $('#gross_amount').val();
    var access_area = $('#access_area').val();
    var firms_agent_type = $('#firms_agent_type').val();

    // Create FormData object to handle file uploads
    var formData = new FormData();
    formData.append('firms_agent', firms_agent);
    formData.append('type_of_pass', type_of_pass);
    formData.append('no_of_days', no_of_days);
    formData.append('from_date', from_date);
    formData.append('to_date', to_date);
    formData.append('amount', amount);
    formData.append('screen_unique_id', screen_unique_id);
    formData.append('sgst', sgst);
    formData.append('cgst', cgst);
    formData.append('gross_amount', gross_amount);
    formData.append('firms_agent_type', firms_agent_type);
    formData.append('access_area', access_area);
    formData.append('_token', '{{csrf_token()}}'); // CSRF token

    // AJAX request to submit form data
    $.ajax({
        type: "POST",
        url: '{{route("pass_sublist.create")}}', // Update the route
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            var newRow = `
<tr>
    <td>${s_no}</td> <!-- Use the current value of s_no -->
    <td>${response.firms_agent_name}</td>
    <td>${type_of_pass}</td>
    <td>${no_of_days}</td>
    <td>${from_date}</td>
    <td>${to_date}</td>
    <td>${amount}</td>
    <td>
        <a onclick="deletepass('${response.unique_id}')"> <i class="fas fa-trash-alt"></i></a>
    </td>
</tr>
`;

            // Append the new row to the tbody of the #sublist table
            $('#sublist tbody').append(newRow);
            // Increment s_no for the next entry
            s_no++;

            // Update the pass_amount input
            $('#pass_amount').val(response.amount);
            $('#pass_amount_label').text(response.amount);

            // Optionally, reset form fields
            $('#type_of_pass').val('');
            $('#no_of_days').val('');
            $('#to_date').val('');
            $('#amount').val('');
            $('#sgst').val('');
            $('#cgst').val('');
            $('#gross_amount').val('');
            $('#access_area').val('');
        },
        error: function(xhr) {
            console.log(xhr.responseText);
        }
    });
}

function deletepass(unique_id) {
    $.ajax({
        type: 'DELETE',
        url: `{{ route('pass_sublist.delete', '') }}/${unique_id}`, // Dynamic URL
        method: 'DELETE',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            // Show a success message with auto close
            Swal.fire({
                title: 'Deleted!',
                text: 'The pass  has been deleted.',
                icon: 'success',
                timer: 2000, // Time in milliseconds (2 seconds)
                showConfirmButton: false
            });
            window.location.reload();
            // // Wait for a few seconds before removing the row
            // setTimeout(function() {
            //     // Remove the row from the table
            //     $(`#sublist tbody tr:has(a[onclick*="${unique_id}"])`).remove();
            //     // Optionally reload the page
            //     // window.location.reload();
            // }, 2000); // This should match the timer duration
            // // window.location.reload();
        },

        error: function(xhr) {
            var errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr
                .responseJSON.message :
                'An error occurred. Please try again.';
            // Show error message
            alert(errorMessage);
        }
    });
}


//main list store function
function submit_save() {
    // alert();

    var screen_unique_id = $('#screen_unique_id').val(); // Get the unique ID from the input field
    // alert(screen_unique_id);
    $('#pass_id').parent().hide();
    // Create a new FormData object
    var formData = new FormData();

    // Append form fields to the FormData object
    formData.append('screen_unique_id', screen_unique_id); // Include screen_unique_id in the FormData
    formData.append('issue_center', $('#issue_center').val());
    formData.append('firms_agent_type', $('#firms_agent_type').val());
    formData.append('pass_id', $('#hidden_pass_id').val());
    formData.append('firms_agent', $('#firms_agent').val());
    formData.append('vectype', $('#vectype').val());
    formData.append('passType', $('#passType').val());
    formData.append('persontype', $('#persontype').val());
    formData.append('vec_reg_no', $('#vec_reg_no').val());
    formData.append('driving_license', $('#driving_license').val());
    formData.append('access_area', $('#access_area').val());
    formData.append('adhaar_no', $('#adhaar_no').val());
    formData.append('vech_no', $('#vech_no').val());
    formData.append('pass_amount', $('#pass_amount').val());
    formData.append('pass_weight', $('#pass_weight').val());
    formData.append('net_amount', $('#net_amount').val());
    formData.append('pass_status', $('#pass_status').val());

    // $net_amount
    formData.append('_token', '{{csrf_token()}}');
    // Get the file from the input and append it to the FormData object
    var cover_letter = document.getElementById('cover_letter').files[0];
    if (cover_letter) {
        formData.append('cover_letter', cover_letter);
    }

    // Make the AJAX request using FormData
    $.ajax({
        type: "POST",
        url: "{{route('pass_entry.store')}}",
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            console.log(response); // Log the whole response for debugging
            if (response.screen_unique_id) {
                $('#screen_unique_id').val(response.screen_unique_id);
                // $('#pass_id').val(response.pass_id);
                // alert('Unique ID: ' + response.screen_unique_id);
                sweetalert("create");


                window.location.href = "{{ route('pass_entry') }}";


            }

        },


        error: function(xhr) {
            console.error(xhr.responseText); // Log the error response
            alert('An error occurred: ' + xhr.responseText);
        }
    });
}



// main list end
//-------------------------image and pdf view download function------///////

function imageViews(file_name) {
    // Split the file names by commas (in case there are multiple)
    var fileNames = file_name.split(',');

    // Base directory where your files are stored
    var baseDirectory = 'public/uploads/pass_entry/';

    fileNames.forEach(function(fileName) {
        // Get the file extension
        var fileExtension = fileName.split('.').pop().toLowerCase();
        var filePath = `${baseDirectory}${fileName}`; // Construct the full path

        // Handle file types
        switch (fileExtension) {
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
                // Open image in a new tab
                window.open(filePath, '_blank');
                break;
            case 'pdf':
            case 'doc':
            case 'docx':
            case 'txt':
            case 'xls':
            case 'xlsx':
                // Open documents in a new tab
                window.open(filePath, '_blank');
                break;
            default:
                // For any other file type, trigger download
                const link = document.createElement('a');
                link.href = filePath;
                link.download = fileName; // Trigger download
                link.click();
                break;
        }
    });
}


// ne change

function calculateamount() {
    const amount = parseFloat(document.getElementById('amount').value);
    // const amount = 1000; // Taxable amount
    const gstRate = 1.09; // GST rate in percentage

    // Calculate total tax
    const totalTax = amount / (gstRate);

    // Calculate CGST and SGST (both are half of the total tax)
    const sgstAmount = (amount - totalTax);
    const cgstAmount = (amount - totalTax);



    // Update the HTML elements
    document.getElementById('sgstLabel').textContent = `₹${sgstAmount.toFixed(2)}`;
    document.getElementById(
        'sgst').value = sgstAmount.toFixed(2);
    document.getElementById('cgstLabel').textContent =
        `₹${cgstAmount.toFixed(2)}`;
    document.getElementById('cgst').value = cgstAmount.toFixed(2);


    const gross_amount = amount;
    // const gross_amount = amount + sgstAmount + cgstAmount;


    // net amount caluculate// 
    const total_cgst = sgstAmount + cgstAmount;
    const total_gross_amount = gross_amount - total_cgst;

    document.getElementById('gross_amountlabel')
        .textContent = `₹${total_gross_amount.toFixed(2)}`;
    document.getElementById('gross_amount').value =
        total_gross_amount.toFixed(2);
    // document.getElementById('net_amount').value =
    //     total_gross_amount.toFixed(2);




}




function calculateToDate() {
    const fromDateInput = document.getElementById('from_date');
    const noOfDaysInput = document.getElementById('no_of_days');
    const typeOfPassInput = document.getElementById('type_of_pass');

    const noOfDays = parseInt(noOfDaysInput.value, 10) || 0; // Default to 0 if empty
    const type_of_pass = typeOfPassInput.value;

    if (type_of_pass === "day") {
        const fromDateValue = fromDateInput.value;

        // Check if the from date is valid
        if (!fromDateValue) {
            alert("Please select a valid from date.");
            return;
        }

        // Create a new Date object from the from date input
        const fromDate = new Date(fromDateValue);

        // Check if the date is valid
        if (isNaN(fromDate.getTime())) {
            alert("Invalid date. Please check the input.");
            return;
        }

        // Update the display for the from date in the desired format
        document.getElementById('from_date_display').value = formatDateForDisplayWithTime(fromDate);

        // Add the number of days
        fromDate.setDate(fromDate.getDate() + noOfDays);

        // Format the to date for storage (datetime-local)
        const year = fromDate.getFullYear();
        const month = String(fromDate.getMonth() + 1).padStart(2, '0'); // Months are 0-based
        const day = String(fromDate.getDate()).padStart(2, '0');
        const hours = String(fromDate.getHours()).padStart(2, '0');
        const minutes = String(fromDate.getMinutes()).padStart(2, '0');

        // Combine for datetime-local storage
        const toDateStorage = `${year}-${month}-${day}T${hours}:${minutes}`;

        // Set the value of the to_date input for storage
        document.getElementById('to_date').value = toDateStorage;

        // Format for display (AM/PM format)
        const displayHours = fromDate.getHours();
        const ampm = displayHours >= 12 ? 'PM' : 'AM';
        const displayHour12 = (displayHours % 12) || 12; // Convert to 12-hour format
        const formattedDisplayDate =
            `${year}-${month}-${day} ${String(displayHour12).padStart(2, '0')}:${minutes} ${ampm}`;

        // Display the formatted date
        const displayElement = document.getElementById('date_display');
        displayElement.value = formattedDisplayDate;
        // }

    } else if (type_of_pass == "weekly") {

        const fromDateInput = document.getElementById('from_date');
        const noOfWeeksInput = document.getElementById('no_of_days'); // Assuming this is for weeks
        const noOfWeeks = parseInt(noOfWeeksInput.value, 10) || 0; // Default to 0 if empty

        const fromDateValue = fromDateInput.value;

        // Check if the from date is valid
        if (!fromDateValue) {
            alert("Please select a valid from date.");
            return;
        }

        // Create a new Date object from the from date input
        const fromDate = new Date(fromDateValue);

        // Check if the date is valid
        if (isNaN(fromDate.getTime())) {
            alert("Invalid date. Please check the input.");
            return;
        }

        // Update the display for the from date in the desired format
        document.getElementById('from_date_display').value = formatDateForDisplayWithTime(fromDate);

        // Add the number of weeks (7 days per week)
        fromDate.setDate(fromDate.getDate() + noOfWeeks * 7);

        // Format the to date for storage (datetime-local)
        const year = fromDate.getFullYear();
        const month = String(fromDate.getMonth() + 1).padStart(2, '0'); // Months are 0-based
        const day = String(fromDate.getDate()).padStart(2, '0');
        const hours = String(fromDate.getHours()).padStart(2, '0');
        const minutes = String(fromDate.getMinutes()).padStart(2, '0');

        // Combine for datetime-local storage
        const toDateStorage = `${year}-${month}-${day}T${hours}:${minutes}`;

        // Set the value of the to_date input for storage
        document.getElementById('to_date').value = toDateStorage;

        // Format for display (AM/PM format)
        const displayHours = fromDate.getHours();
        const ampm = displayHours >= 12 ? 'PM' : 'AM';
        const displayHour12 = (displayHours % 12) || 12; // Convert to 12-hour format
        const formattedDisplayDate =
            `${year}-${month}-${day} ${String(displayHour12).padStart(2, '0')}:${minutes} ${ampm}`;

        // Display the formatted date
        const displayElement = document.getElementById('date_display');
        displayElement.value = formattedDisplayDate;
        // Optionally alert the formatted date for user visibility
        // alert(toDateDisplay); // Uncomment if you want to show an alert
        // Alert or display the formatted date (for user visibility)
        // alert(toDateDisplay); // Show the alert for display
    } else if (type_of_pass == "monthly") {

        const fromDateInput = document.getElementById('from_date');
        const noOfMonthsInput = document.getElementById('no_of_days'); // Assuming this is for months
        const noOfMonths = parseInt(noOfMonthsInput.value, 10) || 0; // Default to 0 if empty

        const fromDateValue = fromDateInput.value;

        // Check if the from date is valid
        if (!fromDateValue) {
            alert("Please select a valid from date.");
            return;
        }

        // Create a new Date object from the from date input
        const fromDate = new Date(fromDateValue);

        // Check if the date is valid
        if (isNaN(fromDate.getTime())) {
            alert("Invalid date. Please check the input.");
            return;
        }

        // Update the display for the from date in the desired format
        document.getElementById('from_date_display').value = formatDateForDisplayWithTime(fromDate);

        // Add the number of months
        fromDate.setMonth(fromDate.getMonth() + noOfMonths);

        // Format the to date for storage (datetime-local)
        const year = fromDate.getFullYear();
        const month = String(fromDate.getMonth() + 1).padStart(2, '0'); // Months are 0-based
        const day = String(fromDate.getDate()).padStart(2, '0');
        const hours = String(fromDate.getHours()).padStart(2, '0');
        const minutes = String(fromDate.getMinutes()).padStart(2, '0');

        // Combine for datetime-local storage
        const toDateStorage = `${year}-${month}-${day}T${hours}:${minutes}`;

        // Set the value of the to_date input for storage
        document.getElementById('to_date').value = toDateStorage;

        // Format for display (AM/PM format)
        const displayHours = fromDate.getHours();
        const ampm = displayHours >= 12 ? 'PM' : 'AM';
        const displayHour12 = (displayHours % 12) || 12; // Convert to 12-hour format
        const formattedDisplayDate =
            `${year}-${month}-${day} ${String(displayHour12).padStart(2, '0')}:${minutes} ${ampm}`;

        // Display the formatted date
        const displayElement = document.getElementById('date_display');
        displayElement.value = formattedDisplayDate;

    } else if (type_of_pass == "halfyearly") {
        const fromDateInput = document.getElementById('from_date');
        const noOfHalfYearsInput = document.getElementById(
            'no_of_days'); // Assuming this is for half-years
        const noOfHalfYears = parseInt(noOfHalfYearsInput.value, 10) || 0; // Default to 0 if empty

        const fromDateValue = fromDateInput.value;

        // Check if the from date is valid
        if (!fromDateValue) {
            alert("Please select a valid from date.");
            return;
        }

        // Create a new Date object from the from date input
        const fromDate = new Date(fromDateValue);

        // Check if the date is valid
        if (isNaN(fromDate.getTime())) {
            alert("Invalid date. Please check the input.");
            return;
        }

        // Update the display for the from date in the desired format
        document.getElementById('from_date_display').value = formatDateForDisplayWithTime(fromDate);

        // Add the number of half-years (6 months each)
        fromDate.setMonth(fromDate.getMonth() + noOfHalfYears * 6);

        // Format the to date for storage (datetime-local)
        const year = fromDate.getFullYear();
        const month = String(fromDate.getMonth() + 1).padStart(2, '0'); // Months are 0-based
        const day = String(fromDate.getDate()).padStart(2, '0');
        const hours = String(fromDate.getHours()).padStart(2, '0');
        const minutes = String(fromDate.getMinutes()).padStart(2, '0');

        // Combine for datetime-local storage
        const toDateStorage = `${year}-${month}-${day}T${hours}:${minutes}`;

        // Set the value of the to_date input for storage
        document.getElementById('to_date').value = toDateStorage;

        // Format for display (AM/PM format)
        const displayHours = fromDate.getHours();
        const ampm = displayHours >= 12 ? 'PM' : 'AM';
        const displayHour12 = (displayHours % 12) || 12; // Convert to 12-hour format
        const formattedDisplayDate =
            `${year}-${month}-${day} ${String(displayHour12).padStart(2, '0')}:${minutes} ${ampm}`;

        // Display the formatted date
        const displayElement = document.getElementById('date_display');
        displayElement.value = formattedDisplayDate;

    } else if (type_of_pass == "yearly") {

        const fromDateInput = document.getElementById('from_date');
        const noOfYearsInput = document.getElementById('no_of_days'); // Assuming this is for years
        const noOfYears = parseInt(noOfYearsInput.value, 10) || 0; // Default to 0 if empty

        const fromDateValue = fromDateInput.value;

        // Check if the from date is valid
        if (!fromDateValue) {
            alert("Please select a valid from date.");
            return;
        }

        // Create a new Date object from the from date input
        const fromDate = new Date(fromDateValue);

        // Check if the date is valid
        if (isNaN(fromDate.getTime())) {
            alert("Invalid date. Please check the input.");
            return;
        }

        // Update the display for the from date in the desired format
        document.getElementById('from_date_display').value = formatDateForDisplayWithTime(fromDate);

        // Add the number of years
        fromDate.setFullYear(fromDate.getFullYear() + noOfYears);

        // Format the to date for storage (datetime-local)
        const year = fromDate.getFullYear();
        const month = String(fromDate.getMonth() + 1).padStart(2, '0'); // Months are 0-based
        const day = String(fromDate.getDate()).padStart(2, '0');
        const hours = String(fromDate.getHours()).padStart(2, '0');
        const minutes = String(fromDate.getMinutes()).padStart(2, '0');

        // Combine for datetime-local storage
        const toDateStorage = `${year}-${month}-${day}T${hours}:${minutes}`;

        // Set the value of the to_date input for storage
        document.getElementById('to_date').value = toDateStorage;

        // Format for display (AM/PM format)
        const displayHours = fromDate.getHours();
        const ampm = displayHours >= 12 ? 'PM' : 'AM';
        const displayHour12 = (displayHours % 12) || 12; // Convert to 12-hour format
        const formattedDisplayDate =
            `${year}-${month}-${day} ${String(displayHour12).padStart(2, '0')}:${minutes} ${ampm}`;

        // Display the formatted date
        const displayElement = document.getElementById('date_display');
        displayElement.value = formattedDisplayDate;
    }

}


// }

window.onload = function() {
    document.getElementById('calculate_button').onclick = calculateToDate;
};


function get_vec_pass() {
    const noOfDays = parseInt(document.getElementById('no_of_days').value);
    const type_of_pass = document.getElementById('type_of_pass').value;
    const amount = document.getElementById('amount').value;
    if (type_of_pass != "")
        document.getElementById('no_of_days').value = "";
    document.getElementById('amount').value = "";
    document.getElementById('to_date').value = "";

}


function formatDateForDisplayWithTime(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = date.getHours();
    const minutes = String(date.getMinutes()).padStart(2, '0');
    const ampm = hours >= 12 ? 'PM' : 'AM';
    const displayHour12 = (hours % 12) || 12; // Convert to 12-hour format

    return `${year}-${month}-${day} ${String(displayHour12).padStart(2, '0')}:${minutes} ${ampm}`;
}
</script>


@endsection