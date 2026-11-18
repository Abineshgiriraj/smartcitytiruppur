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
</style>

@php

//$id = $_GET['id'] ?? null;
//print_R($id);die();
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
                    <form id="pass_entry_form" action="{{ route('pass_entry.update', $passentrymain_list->id) }}"
                        method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="screen_unique_id" name="screen_unique_id"
                            value="{{ old('screen_unique_id', $passentrymain_list->screen_unique_id) }}">
                        <div class="row mb-3 mt-2">
                            <div class="col-sm-2  ">
                                <label for="colFormLabelSm" class="col-form-label">Issue Center</label>
                            </div>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" id="issue_center" name="issue_center"
                                    value="{{ old('issue_center', $passentrymain_list->issue_center) }}" />
                            </div>
                            <div class="col-sm-2 ">
                                <label class="col-form-label" for="firm_id">Pass ID:</label>
                            </div>
                            <div class="col-sm-2">
                                <label class="col-sm-12 col-form-label  firm-id-label">
                                    <b id="pass_id">{{ $passentrymain_list->pass_id}}</b>
                                </label>
                                <input type="hidden" id="hidden_pass_id" name="pass_id"
                                    value="{{ isset($passentrymain_list) ? $passentrymain_list->pass_id : '' }}">
                            </div>
                            <div class="col-sm-2  " style="display:none">
                                <label for="colFormLabelSm" class="col-form-label">Status</label>
                            </div>
                            <div class="col-sm-2" style="display:none">
                                <select class="form-select" id="pass_status" name="pass_status" required s>
                                    <option value="Approved"
                                        {{ $passentrymain_list->pass_status == 'Approved' ? 'selected' : '' }}>
                                        Approved
                                    </option>
                                    <option value="NotApproved"
                                        {{ $passentrymain_list->pass_status == 'NotApproved' ? 'selected' : '' }}>
                                        NotApproved
                                    </option>
                                </select>
                            </div>
                        </div>
                        <hr>
                        <hr>
                        <b>Company Details</b>
                        <div class="row mb-3 mt-2">
                            <div class="col-sm-2  ">
                                <label for="colFormLabelSm" class="col-form-label">Firms /Agent Type</label>
                            </div>
                            <div class="col-sm-4">
                                <select class="form-select" id="firms_agent_type" name="firms_agent_type">

                                    @foreach ($company_creation as $firm_agent)
                                    <option value="{{ $firm_agent->unique_id }}"
                                        {{ isset($passentrymain_list) && $passentrymain_list->agent_type == $firm_agent->unique_id ? 'selected' : '' }}>
                                        {{ $firm_agent->company_name }}
                                    </option>
                                    @endforeach

                                </select>

                            </div>
                            <div class="col-sm-2  ">
                                <label for="colFormLabelSm" class="col-form-label">Firms /Agent Name</label>
                            </div>
                            <div class="col-sm-4">

                                <!-- <option value="">
                                    Select Agent/Firms</option> -->
                                <select class="form-select" id="firms_agent" name="firms_agent">
                                    <option value="">
                                        Select </option>
                                    @foreach ($firm_registration_name as $agent)
                                    <option value="{{ $agent->unique_id }}"
                                        {{ isset($passentrymain_list) && $passentrymain_list->firms_agent == $agent->unique_id ? 'selected' : '' }}>
                                        {{ $agent->name }}
                                    </option>
                                    @endforeach

                                </select>


                            </div>
                            <div class="row mb-3 mt-2">

                                <!-- <div class="col-sm-4">

                                </div> -->
                                @if(isset($passentrymain_list) && $passentrymain_list->cover_letter)
                                <div class="row mb-3">
                                    <div id="upload" class="col-sm-2">
                                        <label for="photo">Cover Letter</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <!-- Left column for file input -->
                                        <input type="file" class="form-control" id="cover_letter" name="cover_letter" />
                                    </div>
                                    <div class="col-sm-4 mt-2">
                                        <!-- Right column for displaying the image or file link -->
                                        @php
                                        $coverLetterPath = asset('public/uploads/pass_entry/' .
                                        $passentrymain_list->cover_letter);
                                        $fileExtension = pathinfo($passentrymain_list->cover_letter,
                                        PATHINFO_EXTENSION);
                                        @endphp

                                        @if(in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif']))
                                        <a href="#" onclick="downloadFile('{{ $coverLetterPath }}')">
                                            <img src="{{ $coverLetterPath }}" alt="Cover Letter" class="img-fluid"
                                                style="max-width: 60px;">
                                        </a>
                                        @elseif(strtolower($fileExtension) === 'pdf')
                                        <a href="#" onclick="downloadFile('{{ $coverLetterPath }}')">
                                            <img src="{{ asset('public/uploads/pass_entry/pdf.png') }}"
                                                alt="PDF Document" style="max-width: 20px;">
                                        </a>

                                        @else
                                        <a href="#" onclick="downloadFile('{{ $coverLetterPath }}')">
                                            {{ basename($passentrymain_list->cover_letter) }}
                                        </a>
                                        <span class="text-muted"> (Click to download)</span>
                                        @endif
                                    </div>
                                    @endif
                                    <!-- <br> -->
                                </div>

                                <hr>
                                <b>Person /Vehicle Details</b>
                                <div class="row mb-3 mt-2">
                                    <div class="col-sm-2">
                                        <label for="colFormLabelSm" class="col-form-label">Pass Type</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <select class="form-select" id="passType" name="passType" onchange="pass_type()"
                                            disabled>
                                            <option value="">Select</option>
                                            <option value="vehicle"
                                                {{ $main_list->pass_type == 'vehicle' ? 'selected' : '' }}>
                                                Vehicle
                                            </option>
                                            <option value="person"
                                                {{ $main_list->pass_type == 'person' ? 'selected' : '' }}>
                                                Person
                                            </option>
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
                                            <!-- @foreach ($get_vech_type as $vech_type)
                                    <option value="{{$vech_type->unique_id}}">
                                        {{$vech_type->vehicle_type}}
                                    </option>
                                    @endforeach -->
                                            @foreach ($vehicle_creation as $vech)
                                            <option value="{{ $vech->unique_id }}"
                                                {{ $passentrymain_list->vectype == $vech->unique_id ? 'selected' : '' }}>
                                                {{ $vech->vehicle_type }}
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
                                        <select class="form-select" id="persontype" name="persontype"
                                            onchange="person_type()">
                                            <option value="">
                                                Select </option>
                                            @foreach ($firm_person_name as $person_name)
                                            <option value="{{ $person_name->unique_id }}"
                                                {{ isset($passentrymain_list) && $passentrymain_list->persontype == $person_name->unique_id ? 'selected' : '' }}>
                                                {{ $person_name->person_type }}
                                            </option>
                                            @endforeach
                                            <!-- @foreach ($get_person_type as $person_type)
                                            <option value="{{$person_type->unique_id}}">
                                                {{$person_type->person_type}}
                                            </option>
                                            @endforeach -->
                                        </select>
                                    </div>

                                    <div class="col-sm-2" id="vehicle_div" style="display:none;">
                                        <label for="colFormLabelSm" class="col-form-label">Reg. Vehicle
                                            Number</label>
                                    </div>
                                    <div class="col-sm-4" id="vehicle_div1" style="display:none;">
                                        <input type="text" class="form-control" id="vec_reg_no" name="vec_reg_no"
                                            oninput="vehicle_data()"
                                            value="{{ old('vec_reg_no', $passentrymain_list->vec_reg_no) }}" />
                                    </div>
                                    <!-- id="get_adhaar" -->
                                    <div class="col-sm-2" id="adhaar" style="display:none;">
                                        <label for="colFormLabelSm" class="col-form-label">Adhaar No.</label>
                                    </div>
                                    <!--  id="get_adhaar1" -->
                                    <div class="col-sm-4" id="adhaar1" style="display:none;">
                                        <input type="text" class="form-control" id="adhaar_no" name="adhaar_no"
                                            oninput="person_data1()"
                                            value="{{ old('adhaar_no', $passentrymain_list->adhaar_no) }}" />
                                    </div>
                                    <div id="person_driver" class="col-sm-2" style="display:none;">
                                        <label for="colFormLabelSm" class="col-form-label">Driving License
                                            No.</label>
                                    </div>
                                    <div id="person_driver1" class="col-sm-4" style="display:none;">
                                        <input type="text" class="form-control" id="driving_license"
                                            name="driving_license" oninput="person_data()"
                                            value="{{ old('driving_license', $passentrymain_list->driving_license) }}" />
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
                                            <label for="colFormLabelSm" class="col-form-label">Type of
                                                Vehicle</label>
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
                                            <label for="colFormLabelSm" class="col-form-label">PUC Valid
                                                UPTO</label>
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
                                            <label id="person" style="display:none"
                                                class="col-sm-12 col-form-label">Person</label>
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
                                            <img onclick="pdf()" target="_blank"
                                                src="{{ asset('public/assets/img/profiles.png') }}"
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
                                            <label for="colFormLabelSm" class="col-form-label">Type of ID
                                                Proof</label>
                                        </div>
                                        <div class="col-sm-2">
                                            <label class="col-sm-12 col-form-label  ">Adhaar Card</label>
                                        </div>

                                        <div class="col-sm-2  ">
                                            <label for="colFormLabelSm" class="col-form-label">ID Proof
                                                Attach</label>
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
                                            <input type="text" class="form-control" id="vech_no" name="vech_no"
                                                value="{{ old('vech_no', $passentrymain_list->vech_no) }}" />
                                        </div>


                                    </div>
                                </div>

                                <div class="row mb-3 mt-2">

                                    <div class="col-sm-2 " id="pass_weight_div" style="display:none">
                                        <label for="colFormLabelSm" class="col-form-label">Passing Weight</label>
                                    </div>
                                    <div class="col-sm-4" id="pass_weight_div1" style="display:none">
                                        <input type="text" class="form-control" id="pass_weight" name="pass_weight"
                                            value="{{ old('pass_weight', $passentrymain_list->pass_weight) }}" />
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
                                            <!-- <option value="">Select</option> -->
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

                                <div class="row mb-3 mt-2">
                                    <div class="col-sm-1">
                                        <label for="colFormLabelSm" class="col-form-label">From Date</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <!-- <input type="datetime-local" class="form-control" id="from_date" name="from_date" -->
                                        <!-- value="<?php //echo $currentdate;?>" /> -->
                                        <!-- <input type="datetime-local" class="form-control" id="from_date" name="from_date"
                                value="<?php //echo $currentDateTime; ?>" /> -->
                                        <input type="datetime-local" class="form-control" id="from_date"
                                            name="from_date" value="<?php echo $currentDateTime; ?>" />
                                        <input type="hidden" id="from_date_display" class="form-control" readonly />
                                    </div>
                                    <div class="col-sm-2">
                                        <label for="colFormLabelSm" class="col-form-label">Valitity Expiry
                                            Date</label>
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
                                    <div class="col-sm-4">
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
                                        <label for="gross_amount" style="display:none" class="col-form-label">Total
                                            Gross
                                            Amount</label>
                                    </div>

                                    <div class="col-sm-2 text-end">
                                        <label style="display:none" class="col-form-label" id="gross_amountlabel"
                                            name="gross_amountlabel"></label>
                                        <input type="hidden" class="form-control" id="gross_amount"
                                            name="gross_amount" />
                                        <input type="hidden" class="form-control" id="net_amount" name="net_amount"
                                            value="{{ old('net_amount', $totalGrossAmount) }}" />

                                    </div>
                                </div>
                                <div class="justify-content-center submit_sublist">
                                    <button id="openTableButton submit_sublist" onclick="sublist_proof(event)"
                                        class="btn btn-primary" style="float: right;">Add</button>
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
                                                <th>Valitity Expiry Date</th>
                                                <th>Gross Amount</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach ($sublist_entries as $sublist_entrie)
                                            <tr id="entry-{{ $sublist_entrie->id }}">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $firm_registration1[$sublist_entrie->agent_name] ?? 'N/A' }}</td>
                                                <td>{{ $sublist_entrie->type_of_pass }}</td>
                                                <td>{{ $sublist_entrie->no_of_days }}</td>
                                                <td>{{ $sublist_entrie->from_date }}</td>
                                                <td>{{ $sublist_entrie->to_date }}</td>
                                                <td>{{ $sublist_entrie->amount }}</td>
                                                <td>
                                                    <a onclick="deletepass({{ $sublist_entrie->id }})"
                                                        style="cursor:pointer;">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>


                                        </tbody>
                                    </table>
                                </div>
                                <div class="row mb-3 mt-2 justify-content-end">
                                    <div class="col-sm-3">
                                        <label for=" colFormLabelSm" class="col-form-label"><b>Total Pass
                                                Amount</b></label>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="col-form-label" id="pass_amount_label">
                                            {{ old('pass_amount', $totalGross) ? formatCurrencyINR(old('pass_amount', $totalGross)) : '' }}
                                        </label>

                                        <input type="hidden" class="form-control" id="pass_amount" name="pass_amount"
                                            value="{{ old('pass_amount', $totalGross) }}" />
                                    </div>


                                </div>

                                <div class="btn-float">
                                    <button type="button" class="btn btn-danger"
                                        onclick="window.location.href='{{ url('/pass_entry')}}'">Cancel</button>
                                    <!-- <button type="submit" class="btn btn-primary">Submit</button> -->
                                    <!-- <button type="submit"
                            class="btn btn-primary">{{ isset($acc_year) ? 'Update' : 'Submit' }}</button> -->
                                    <button type="submit" class="btn btn-primary" onclick="submit_update()">Payment
                                        Update</button>
                                </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

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

<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script>
// Call allowed_types on page load to set the initial state of fields
$(document).ready(function() {
    // $('#complaint_type').change(allowed_types);
    pass_type();
    person_type();
    vech_type();
    vehicle_data();
    person_data();
    person_data1();
    // deletepass();
    // $(document).ready(function() {
    // Assuming you have a way to know if you're in update mode

    // });
});

function downloadFile(filePath) {
    // Create a temporary link element
    const link = document.createElement('a');
    link.href = filePath;
    link.download = ''; // Optional: set the filename here if needed
    document.body.appendChild(link); // Append to the body
    link.click(); // Trigger the download
    document.body.removeChild(link); // Clean up
}
// $('#firms_agent_type').on('change', function() {



// });

function deletepass(unique_id) {
    console.log("Deleting unique_id:", unique_id); // Log the unique_id

    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("pass_sublist1.delete", ":unique_id") }}'.replace(":unique_id",
                    unique_id),
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Deleted!', 'Your entry has been deleted.', 'success').then(
                            () => {
                                // Remove the row from the table
                                // document.getElementById(`entry-${unique_id}`).remove();
                                // Optional: Adjust row numbers
                                window.location.reload();
                                adjustRowNumbers();
                            });
                        window.location.reload();
                    } else {
                        Swal.fire('Error!', response.message || 'Something went wrong.', 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error!', 'Something went wrong.', 'error');
                }
            });
        }
    });
}

function adjustRowNumbers() {
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach((row, index) => {
        row.cells[0].innerText = index + 1; // Update the first cell with the new index
    });
}


$('#pass_entry_form').on('submit', function(event) {
    event.preventDefault(); // Prevent the default form submission

    // Create a FormData object
    let formData = new FormData(this);

    $.ajax({
        url: $(this).attr('action'),
        method: "POST",
        data: formData,
        contentType: false, // Important: Prevent jQuery from overriding the content type
        processData: false, // Important: Prevent jQuery from converting the FormData to a query string
        success: function(response) {
            const redirectUrl = '{{ route("pass_entry") }}';
            sweetalert('update', redirectUrl); // Redirect after successful addition
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                sweetalert('already'); // Handle validation errors
            }
        }
    });
});




function pass_type() {

    const passType = document.getElementById('passType').value;
    // ALERT(passType);

    // Hide both vehicle and person divs initially
    document.getElementById("vehicle_div").style.display = "none";
    document.getElementById("vehicle_div1").style.display = "none";
    document.getElementById("person_div").style.display = "none";
    document.getElementById("vehicle_type_div").style.display = "none";
    document.getElementById("vehicle_type_div1").style.display = "none";
    document.getElementById("pass_weight_div").style.display = "none";
    document.getElementById("vehicle_type_no").style.display = "none";
    document.getElementById("vehicle_type_no1").style.display = "none";
    document.getElementById("adhaar").style.display = "none";

    document.getElementById("adhaar1").style.display = "none";


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

    if (vehicle_validate) {
        document.getElementById('adhaar_no').value = '';
        // document.getElementById('persontype').value = '';
        // document.getElementById("adhaar").style.display = "none";
        document.getElementById("person_details").style.display = "none";
        document.getElementById("vehicle_details").style.display = "block";
        document.getElementById("adhaar").style.display = "none";

        document.getElementById("adhaar1").style.display = "none";

    }

}

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
function sublist_proof(event) {
    // alert();

    event.preventDefault()
    // Get form values
    var firms_agent = $('#firms_agent').val();
    var type_of_pass = $('#type_of_pass').val();
    // alert(type_of_pass);
    var no_of_days = $('#no_of_days').val();
    var from_date = $('#from_date_display').val();
    var to_date = $('#date_display').val();
    // alert(to_date);
    var amount = $('#amount').val();
    var sgst = $('#sgst').val();
    // alert(sgst);
    var cgst = $('#cgst').val();
    // alert(cgst);
    var amount = $('#amount').val();
    // alert(gross_amount);
    var screen_unique_id = $('#screen_unique_id').val();
    // alert(screen_unique_id);
    var gross_amount = $('#gross_amount').val();
    // formData.append('net_amount', $('#net_amount').val());
    // alert(gross_amount);
    // formData.append('firms_agent_type', firms_agent_type);
    var firms_agent_type = $('#firms_agent_type').val();
    // alert(firms_agent_type);
    // var firms_agent_type = $('#firms_agent_type').val();


    // alert(screen_unique_id);

    var s_no = 1;

    var s_nos = s_no++;



    // Create FormData object to handle file uploads
    var formData = new FormData();
    formData.append('firms_agent', firms_agent);
    formData.append('type_of_pass', type_of_pass);
    formData.append('no_of_days', no_of_days);
    formData.append('from_date', from_date);
    formData.append('to_date', to_date);
    formData.append('amount', amount);
    formData.append('screen_unique_id', screen_unique_id); // Include screen_unique_id
    formData.append('amount', amount);
    formData.append('sgst', sgst);
    formData.append('cgst', cgst);
    formData.append('gross_amount', gross_amount);
    formData.append('firms_agent_type', firms_agent_type);
    formData.append('_token', '{{csrf_token()}}'); // Add CSRF token for security


    // AJAX request to submit form data
    $.ajax({
        type: "POST",
        url: '{{route("pass_sublist.create")}}', // Update the route
        data: formData,
        contentType: false, // Important for file upload
        processData: false, // Prevent jQuery from processing the data
        success: function(response) {
            window.location.reload();
            // var file_name = response.attached_proof;
            // var proof_type = response.proof_type;
            var newRow = `
<tr>
   <td>${s_nos}</td>
   <td>${response.firms_agent_name}</td>
    <td>${type_of_pass}</td>
    <td>${no_of_days}</td>
    <td>${from_date}</td>
    <td>${to_date}</td>
    <td>${amount}</td>
   

</tr>
`;

            // Append the new row to the tbody of the #sublist table
            $('#sublist tbody').append(newRow);
            // Update the pass_amount input with the total gross amount

            $('#pass_amount').val(response
                .amount); // Assuming this is how you return the total
            $('#pass_amount_label').text(response
                .amount
            ); // Update the label with the total // $('#pass_amount').val(response.gross_amount);
            // Optionally, reset form fields after successful submission
            // $('#firms_agent').val('');
            $('#type_of_pass').val('');
            $('#no_of_days').val('');
            // $('#firms_agent_type').val('');
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
    formData.append('pass_status', $('#pass_status').val());
    formData.append('net_amount', $('#net_amount').val());
    // Approved
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

function imageViews(file_name, cover_letter) {
    // Split the file names by commas (in case there are multiple)
    var fileNames = file_name.split(',');

    // Base directory wher your files are stored
    var pass_entry = 'public/uploads/pass_entry';
    // var licenceDirectory = 'public/uploads/person_registration/licence/';


    fileNames.forEach(function(fileName) {
        // Get the file extension
        var cFileName = fileName.split('.');
        var fileExtension = cFileName[cFileName.length - 1].toLowerCase();
        var filePath = `${baseDirectory}${fileName}`; // Construct the full path



        var baseDirectory;

        // if (proof_type == 'aadhaar') {
        baseDirectory = pass_entry;
        // } else if (proof_type == 'driving_licence') {
        // baseDirectory = licenceDirectory;

        // }

        var filePath = `${baseDirectory}${fileName}`;

        if (fileExtension === 'jpg' || fileExtension === 'png' || fileExtension === 'jpeg') {
            // Open image in a new tab
            window.open(filePath, '_blank');
        } else if (fileExtension === 'pdf') {

            window.open(filePath, '_blank');
            // Trigger download for PDF
            // const link = document.createElement('a');
            // link.href = filePath;
            // link.download = fileName; // Trigger download
            // link.click();
        } else if (fileExtension === 'xls' || fileExtension === 'xlsx') {
            // Trigger Excel download
            const link = document.createElement('a');
            link.href = filePath;
            link.download = fileName;
            link.click();
        } else if (fileExtension === 'txt' || fileExtension === 'docx' || fileExtension === 'doc') {
            // Trigger download for text or Word files
            const link = document.createElement('a');
            link.href = filePath;
            link.download = fileName;
            link.click();
        }
    });
}


// const type_of_pass = document.getElementById('type_of_pass').value;
// alert(type_of_pass);
// const from_date = document.getElementById('from_date').value; // expected format: dd-mm-yyyy
// const no_of_days = parseInt(document.getElementById('no_of_days').value); // convert to integer

// if (type_of_pass == "day") {
// // Process for day pass
// } else if (type_of_pass == "weekly") {
// // Process for weekly pass
// } else if (type_of_pass == "monthly") {
// // Process for monthly pass
// } else if (type_of_pass == "yearly") {
// // Process for yearly pass
// }


// // Function to calculate the "to date"
// function calculateToDate() {
// alert(from_date);
// const from_date = document.getElementById('from_date').value;
// const no_of_days = document.getElementById('no_of_days').value;
// const to_date = document.getElementById('to_date').value;
// // $po_date = $value['po_date'];
// // $delivery_due_dates = $value['delivery_due_dates'];
// const from_date = DateTime::createFromFormat('d-m-Y', from_date);
// const from_date - > modify('+'.no_of_days.
// ' days');
// const todate = from_date - > format('d-m-Y');
// alert(todate);

// // $value['delivery_due_dates']= $resultDate
// // for red colur date display
// // $currentDate = new DateTime();

// }
// window.onload = function() {
// calculateToDate();
// };
// function calculateToDate() {
// const fromDateInput = document.getElementById('from_date').value;
// const noOfDays = parseInt(document.getElementById('no_of_days').value, 10);

// // Convert the from date to a JavaScript Date object
// const fromDate = new Date(fromDateInput.split('-').reverse().join('-'));

// // Add the number of days
// fromDate.setDate(fromDate.getDate() + noOfDays);

// // Format the to date back to 'd-m-Y'
// const day = String(fromDate.getDate()).padStart(2, '0');
// const month = String(fromDate.getMonth() + 1).padStart(2, '0'); // Months are 0-based
// const year = fromDate.getFullYear();

// const toDate = `${day}-${month}-${year}`;

// // alert(toDate);
// }

// window.onload = function() {
// document.getElementById('calculate_button').onclick = calculateToDate;
// };

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
        const noOfHalfYearsInput = document.getElementById('no_of_days'); // Assuming this is for half-years
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