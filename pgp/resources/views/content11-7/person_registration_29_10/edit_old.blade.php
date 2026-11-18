@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">User Firms/</span> Person Registration</h4>
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
</style>

@php

$get_designations = get_designation();

$get_academic_year = academic_year();

@endphp
<!-- Basic Layout -->

<!-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div id="basicwizard">
                    <ul class="nav nav-pills nav-justified form-wizard-header mb-4">
                        <li class="nav-item">
                            <a href="#basictab1" data-bs-toggle="tab" class="nav-link rounded-0 pt-2 pb-2 active">
                                <span class="d-sm-inline">Personal Details</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a id="tab2-link" href="#basictab2" data-bs-toggle="tab"
                                class="nav-link rounded-0 pt-2 pb-2">
                                <span class="d-sm-inline">Document Details</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a id="tab3-link" href="#basictab3" data-bs-toggle="tab"
                                class="nav-link rounded-0 pt-2 pb-2">
                                <span class="d-sm-inline">Authenication </span>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content b-0 mb-0">
                        <div class="tab-pane active" id="basictab1">
                            <form id="person_registration_edit">
                                @csrf
                                @method('PUT')
                                @if(isset($person_registration) && $person_registration)
                                <input type="hidden" name="id" id="reg_unique_id"
                                    value="{{ $person_registration->unique_id }}">
                                @endif

                                @if(isset($person_sublist_reg_first) && $person_sublist_reg_first)
                                <input type="hidden" name="sublist_id" id="sublist_id"
                                    value="{{ $person_sublist_reg_first->form_unique_id }}">
                                @endif
                                <!-- </form> -->



                                <!-- Hidden ID field -->


                                <div class="row mb-3">
                                    <label for="acc_year" class="col-sm-2 col-form-label">Academic Year</label>
                                    <div class="col-sm-4">
                                        <select class="form-select" name="academic_year" id="academic_year">
                                            <option value="">Select Academic Year</option>

                                            @foreach ($get_academic_year as $acc_year )
                                            <option value="{{$acc_year->unique_id}}"
                                                {{ $acc_year->unique_id == $person_registration->academic_year ? 'selected' : ''}}>
                                                {{$acc_year->acc_year}}
                                            </option>


                                            @endforeach
                                        </select>
                                    </div>


                                    <label for="fullName" class="col-sm-2 col-form-label">Full Name</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="full_name" name="full_name"
                                            value="{{$person_registration->full_name}}" />
                                    </div>

                                    <div class="col-sm-2 mt-3">
                                        <label class="col-form-label" for="register_number">Register
                                            Number:</label>
                                    </div>
                                    <div class="col-sm-4 mt-4">

                                        {{$person_registration->register_no}}
                                    </div>

                                    <label for="DesignationSelect"
                                        class="col-sm-2 mt-2 col-form-label">Designation</label>
                                    <div class="col-sm-4 mt-3">
                                        <select class="form-select" id="designation_select" name="designation_select"
                                            onchange="toggleLicenceFields()">
                                            <option value="">Select Designation</option>
                                            @foreach ($get_designations as $designation)
                                            <option value="{{ $designation->unique_id }}"
                                                {{ $designation->unique_id == $person_registration->designation ? 'selected' : '' }}>
                                                {{ $designation->designation }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>

                                <div class="licence_valididty_date" style="display: none;">
                                    <div class="row mb-3">
                                        <label for="licence_date" class="col-sm-2 col-form-label">Licence validity
                                            date</label>
                                        <div class="col-sm-4">
                                            <input type="date" class="form-control" id="licence_date"
                                                name="licence_date"
                                                value="{{$person_registration->licence_validity_date}}" />
                                        </div>
                                    </div>

                                </div>

                                <div class="row mb-3">
                                    <label for="licence_date" class="col-sm-2 col-form-label">Aadhaar No
                                    </label>
                                    <div class="col-sm-4">
                                        <input type="text" id="aadhaar_no" class="form-control" name="aadhaar_no"
                                            oninput="valid_aadhar_number(this)"
                                            value="{{ old('aadhaar_no', $person_registration->aadhaar_no) }}">

                                    </div>

                                    <span id="aadhaarError" style="color:red;"></span>

                                </div>


                                <div class="row mb-3">
                                    <label for="nationalitySelect" class="col-sm-2 col-form-label">Nationality</label>
                                    <div class="col-sm-4">
                                        <select class="form-select" id="nationality" name="nationality">
                                            <option value="">Select Nationality</option>
                                            <option value="indian"
                                                {{$person_registration->nationality == 'indian' ? 'selected' :''}}>
                                                Indian</option>
                                            <option value="foreign"
                                                {{$person_registration->nationality == 'foreign' ? 'selected' :''}}>
                                                Foreign</option>
                                            <option value="indian-origin"
                                                {{$person_registration->nationality == 'indian-origin' ? 'selected' :''}}>
                                                Indian Origin</option>
                                        </select>
                                    </div>

                                    <label for="countrySelect" class="col-sm-2 col-form-label">Country</label>
                                    <div class="col-sm-4">
                                        <select class="form-select" id="country" name="country">
                                            <option value="">Select</option>
                                            <option value="india"
                                                {{$person_registration->country == 'india' ? 'selected' : ''}}>India
                                            </option>
                                            <option value="usa"
                                                {{$person_registration->country == 'usa' ? 'selected' : ''}}>
                                                United States</option>
                                            <option value="uk"
                                                {{$person_registration->country == 'uk' ? 'selected' : ''}}>
                                                United
                                                Kingdom</option>
                                            <option value="canada"
                                                {{$person_registration->country == 'canada' ? 'selected' : ''}}>Canada
                                            </option>
                                            <option value="australia"
                                                {{$person_registration->country == 'australia' ? 'selected' : ''}}>
                                                Australia
                                            </option>
                                            <option value="germany"
                                                {{$person_registration->country == 'germany' ? 'selected' : ''}}>Germany
                                            </option>
                                            <option value="france"
                                                {{$person_registration->country == 'france' ? 'selected' : ''}}>France
                                            </option>

                                            <option value="japan"
                                                {{$person_registration->country == 'japan' ? 'selected' : ''}}>Japan
                                            </option>
                                            <option value="china"
                                                {{$person_registration->country == 'china' ? 'selected' : ''}}>China
                                            </option>
                                            <option value="brazil"
                                                {{$person_registration->country == 'brazil' ? 'selected' : ''}}>Brazil
                                            </option>
                                            <option value="south-africa"
                                                {{$person_registration->country == 'south-africa' ? 'selected' : ''}}>
                                                South
                                                Africa</option>
                                            <option value="indian-origin"
                                                {{$person_registration->country == 'indian-origin' ? 'selected' : ''}}>
                                                Indian
                                                Origin</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="Mobile" class="col-sm-2 col-form-label">Mobile</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="mobile" name="mobile"
                                            value="{{$person_registration->mobile}}" />
                                    </div>

                                    <label for="blood_groupSelect" class="col-sm-2 col-form-label">Blood Group</label>
                                    <div class="col-sm-4">
                                        <select class="form-select" id="blood_group" name="blood_group">
                                            <option selected>Select Blood Group</option>
                                            <option value="A+"
                                                {{$person_registration->blood_group == 'A+' ? 'selected' : ''}}>
                                                A+</option>
                                            <option value="A-"
                                                {{$person_registration->blood_group == 'A-' ? 'selected' : ''}}>
                                                A-</option>
                                            <option value="B+"
                                                {{$person_registration->blood_group == 'B+' ? 'selected' : ''}}>
                                                B+
                                            </option>
                                            <option value="B-"
                                                {{$person_registration->blood_group == 'B-' ? 'selected' : ''}}>
                                                B-
                                            </option>
                                            <option value="O+"
                                                {{$person_registration->blood_group == 'O+' ? 'selected' : ''}}>
                                                O+
                                            </option>
                                            <option value="O-"
                                                {{$person_registration->blood_group == 'O-' ? 'selected' : ''}}>
                                                O-
                                            </option>
                                            <option value="AB+"
                                                {{$person_registration->blood_group == 'AB+' ? 'selected' : ''}}>AB+
                                            </option>
                                            <option value="AB-"
                                                {{$person_registration->blood_group == 'AB-' ? 'selected' : ''}}>AB-
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="email" class="col-sm-2 col-form-label">Email ID</label>
                                    <div class="col-sm-4">
                                        <input type="email" class="form-control" id="email_id" name="email_id"
                                            value="{{$person_registration->email_id}}" />
                                    </div>

                                    <label for="photo" class="col-sm-2 col-form-label">Photo</label>
                                    <div class="col-sm-4">
                                        <select class="form-select" name="photo_pic" id="photo_pic"
                                            onchange="image_upload()">
                                            <option value="">Select </option>
                                            <option value="upload_pic"
                                                {{$person_registration->photo_pic == 'upload_pic' ? 'selected' : ''}}>
                                                Upload</option>
                                            <option value="snap_pic"
                                                {{$person_registration->photo_pic == 'snap_pic' ? 'selected' : ''}}>Snap
                                            </option>
                                        </select>
                                    </div>

                                    <div id="upload" style="display:none;">
                                        <label for="photo" class="col-sm-2 col-form-label">Photo</label>
                                        <div class="col-sm-4">
                                            <input type="file" class="form-control" id="photo" name="photo" />
                                        </div>
                                    </div>

                                    @if($person_registration->image_upload)
                                    <div>
                                        <label>Current Image:</label>
                                        <img id="current_image"
                                            src="{{asset($person_registration->image_upload) }}"
                                            alt="Current Image" style="max-width: 200px; display: inline-block;" />
                                    </div>
                                    @endif

                                    <img id="imagePreview" src="#" alt="Image Preview"
                                        style="display:none; max-width: 200px;" />



                                </div>

                                <div class="row mb-3">
                                    <div id="snap" style="display:none;">
                                        <div class="col-sm-2">
                                            <label for="photoPreview" class="col-form-label">Preview Photo</label>
                                        </div>
                                        <div class="col-sm-4 text-center">
                                            <img src="resources/views/content/person_registration/13.png" alt="..."
                                                class="img-thumbnail"
                                                style="border: 5px solid grey; max-width: 100%; height: auto;">
                                            <div class="mt-3">
                                                <button type="button" onclick="web_cam()"
                                                    class="btn btn-primary">Snap</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="btn-float">

                                    <!-- <button type="submit" class="btn btn-primary">save</button> -->
                                    <button type="button" class="btn btn-primary" onclick="submit_next()">Next</button>
                                </div>
                                <!-- onclick="document.querySelector('#tab2-link').click();" -->
                            </form>
                        </div> <!-- end tab-pane -->


                        <div class="tab-pane" id="basictab2">
                            <form id="documentVerificationForm">
                                @csrf
                                @if(isset($acc_year))
                                @method('PUT')
                                @endif

                                <div class="row mb-3">
                                    <input type="text" id="main_unique_id" name="main_unique_id" value="{{$person_registration->unique_id}}">
                                    <input type="hidden" id="licence_val_sub" name="licence_val_sub">

                                    <label for=" idproofSelect" class="col-sm-2 col-form-label">Type
                                        of ID
                                        proof</label>
                                    <div class="col-sm-4">
                                        <!-- <select class="form-select" id="idproof" name="idproof" onchange="proof_change()">
                                            <option selected>Select ID Proof</option>
                                            <option value="aadhaar"> Aadhaar </option>
                                            <option value="driving_licence">
                                                Driving Licence
                                            </option>
                                        </select> -->
                                        <select class="form-select" id="idproof" name="idproof"
                                            onchange="proof_change()">
                                            <option selected>Select ID Proof</option>
                                            <option value="aadhaar">Aadhaar</option>
                                            <option value="driving_licence">Driving Licence</option>
                                        </select>
                                    </div>
                                    <div id="aadhar_id" style="display:none;" class="row mb-3 mt-3"> <label
                                            for="colFormLabelSm" class="col-sm-2 col-form-label">
                                            Aadhar
                                            No.
                                        </label>
                                        <div class="col-sm-4">
                                            <input type="text" readonly class="form-control" id="aadhaar_num"
                                                name="aadhaar_num" />
                                        </div>


                                        <label for="colFormLabelSm" class="col-sm-2 col-form-label">ID
                                            Proof</label>
                                        <div class="col-sm-4">
                                            <input type="file" class="form-control" id="aadhaar_proof"
                                                name="aadhaar_proof" />
                                        </div>
                                    </div>


                                    <!-- <div id="licence_id" style="display:none;"> -->

                                    <div id="licence_id" style="display:none;" class="row mb-3 mt-3">
                                        <label for="licence" class="col-sm-2 col-form-label">Licence No.</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" id="licence_no" name="licence_no" />
                                        </div>

                                        <label for="licence_proof" class="col-sm-2 col-form-label">ID Proof</label>
                                        <div class="col-sm-4">
                                            <input type="file" class="form-control" id="licence_proof"
                                                name="licence_proof" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" />
                                        </div>

                                        <label for="licence_expire" class="col-sm-2 col-form-label mt-3 driver"
                                            style="display: none;">Licence Expire Date</label>
                                        <div class="col-sm-4 mt-3 driver" style="display: none;">
                                            <input type="date" class="form-control" id="licence_expire"
                                                name="licence_expire" />
                                        </div>
                                    </div>


                                    <!-- <label for="colFormLabelSm" class="col-sm-2 col-form-label mt-3 driver"
                                        style="display: none;">Licence
                                        Proof</label>
                                    <div class="col-sm-4 mt-3 driver" style="display: none;">
                                        <input type="file" class="form-control" id="licence_proof"
                                            name="licence_proof" />
                                    </div> -->

                                </div>





                                <div class="d-flex justify-content-center mb-4">
                                    <button id="openTableButton submit_sublist" onclick="sublist_proof(event)"
                                        class="btn btn-primary">Add</button>
                                </div>

                                <div id="imageViewContainer"></div>
                                <div id="dataTableContainer" class="table-responsive mt-4">
                                    <table id="sublist" class="table table-striped table-bordered" style="width:100%">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>S.No</th>
                                                <th>Type of ID Proof</th>
                                                <th>Aadhar No./Licence No.</th>
                                                <th>Document</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $s_no = 1; @endphp
                                            @if ($person_sublist_reg && $person_sublist_reg->count() > 0)
                                            @foreach ($person_sublist_reg as $sublist)
                                            <tr>
                                                <td>{{ $s_no++ }}</td>
                                                <td>{{ $sublist->proof_type }}</td>
                                                <td>{{ $sublist->aadhar_licence }}</td>
                                                <!-- <td>{{ $sublist->licence_expiry }}</td> -->
                                                <td>
                                                    <a
                                                        onclick="imageeditViews('{{ $sublist->attached_proof }}', '{{ $sublist->proof_type }}')">

                                                        <!-- /harbour/public/uploads -->
                                                        <img src="/harbour/public/uploads/dwn.png" width="40px"
                                                            height="40px">
                                                    </a>
                                                </td>
                                                <td>
                                                    <!-- <button class="btn btn-primary btn-sm"
                                                        onclick="editSublistItem('{{ $sublist->form_unique_id }}','{{ $sublist->proof_type }}','{{ $sublist->aadhar_licence }}')">Edit</button> -->
                                                    <button class="btn btn-danger btn-sm"
                                                        onclick="deleteSublistItem('{{ $sublist->form_unique_id }}')">Delete</button>
                                                </td>
                                            </tr>
                                            @endforeach
                                            @else
                                            <tr>
                                                <td colspan="6" class="text-center">No sublist records found.</td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>



                                <!-- <div id="dataTableContainer" class="table-responsive mt-4">
                                    <table id="sublist" class="table table-striped table-bordered" style="width:100%">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>S.No</th>
                                                <th>Type of ID Proof</th>
                                                <th>Aadhar No./Licence No.</th>
                                                <th>Licence Expiry</th>
                                                <th>Document</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div> -->
                                <div class="btn-float">
                                    <button type="button" class="btn btn-primary"
                                        onclick="document.querySelector('#tab3-link').click();">Next</button>
                                </div>
                            </form>
                        </div> <!-- End Tab 2 -->



                        <div class="tab-pane" id="basictab3">
                            <form id="authenticationForm">
                                @csrf
                                @if(isset($acc_year)) @method('PUT') @endif
                                <div class="row">
                                    <div class="row mb-3">


                                        <div class="col-sm-2 mt-3">
                                            <label for="colFormLabelSm" class="col-form-label">User Login ID:</label>
                                        </div>
                                        <div class="col-sm-4 mt-3">
                                            <input type="text" class="form-control" name="login_id" id="login_id"
                                                value="{{$person_registration->user_id}}">

                                        </div>
                                        <!-- <div class="row">
                                            <div class="col-4">
                                                <div class="col-sm-2 mt-3">
                                                    <label for="colFormLabelSm" class="col-form-label">Pin/Password
                                                    </label>
                                                </div>

                                                <select class="form-select" id="pin_passwrd"
                                                    onchange="pin_password_change()" name="pin_passwrd">
                                                    <option value="select">Select Pin/Pasword</option>
                                                    <option value="Pin">Pin</option>
                                                    <option value="Password">Password</option>
                                                </select>
                                            </div>
                                        </div> -->


                                        <!-- {---this is the correct authentication--below} -->
                                        <div id="password_type">
                                            <div class="col-sm-2 mt-3 password-container">
                                                <label for="colFormLabelSm" class="col-form-label">Password</label>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="input-wrapper">
                                                    <input type="password" class="form-control mt-3" id="password"
                                                        name="password" oninput="valid_password(this)"
                                                        onkeyup="password_change()"
                                                        value="{{$person_registration->password}}" />
                                                    <i class="fas fa-eye toggle-password" id="togglePassword"></i>

                                                </div>
                                                <span id="passwordError" style="color: red;"></span>
                                            </div>

                                            <div class="col-sm-2 mt-3 password-container">
                                                <label for="colFormLabelSm" class="col-form-label">Confirm
                                                    Password</label>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="input-wrapper">
                                                    <input type="password" class="form-control mt-3"
                                                        id="confirm_password" name="confirm_password"
                                                        onkeyup="password_change()"
                                                        value="{{$person_registration->confirm_password}}" />
                                                    <i class="fas fa-eye toggle-password"
                                                        id="toggleConfirmPassword"></i>
                                                </div>
                                                <span id="passwordError" style="color: red;"></span>

                                                <small id="password_error" class="text-danger" style="display:none;">
                                                    Passwords do not match
                                                </small>
                                                <small id="password_crct" class="text-success" style="display:none;">
                                                    Passwords match
                                                </small>
                                            </div>
                                        </div>



                                        <!-- <div id="pin_type" style="display: none;">
                                            <div class="col-sm-2 mt-3 password-container">
                                                <label for="pin_num" class="col-form-label">Pin Number</label>
                                            </div>
                                            <div class="col-3 position-relative">
                                                <input type="password" class="form-control" id="pin_num" name="pin_num"
                                                    maxlength="6" pattern="\d{6}" placeholder="******"
                                                    oninput="this.value=this.value.replace(/[^0-9]/g, '').slice(0, 6)"
                                                    onkeyup="validatePin()" required>
                                                <span class="position-absolute top-50 end-0 translate-middle-y me-2">
                                                    <i class="fa fa-eye" id="togglePinVisibility"
                                                        onclick="toggleVisibility('pin_num', 'togglePinVisibility')"></i>
                                                </span>
                                            </div>
                                            <div class="col-sm-2 mt-3 password-container">
                                                <label for="conf_pin_num" class="col-form-label">Confirm Pin
                                                    Number</label>
                                            </div>
                                            <div class="col-3 position-relative">
                                                <input type="password" class="form-control" id="conf_pin_num"
                                                    name="conf_pin_num" maxlength="6" pattern="\d{6}"
                                                    placeholder="******"
                                                    oninput="this.value=this.value.replace(/[^0-9]/g, '').slice(0, 6)"
                                                    onkeyup="validatePin()" required>
                                                <span class="position-absolute top-50 end-0 translate-middle-y me-2">
                                                    <i class="fa fa-eye" id="toggleConfPinVisibility"
                                                        onclick="toggleVisibility('conf_pin_num', 'toggleConfPinVisibility')"></i>
                                                </span>
                                            </div>
                                            <div class="col-12 mt-2">
                                                <span id="pinError" class="text-danger" style="display: none;">PIN
                                                    numbers do not match. Please try again.</span>
                                                <span id="pinSuccess" class="text-success" style="display: none;">PIN
                                                    numbers match!</span>
                                            </div>
                                        </div> -->

                                        <script>
                                            function toggleVisibility(inputId, toggleIconId) {
                                                const inputField = document.getElementById(inputId);
                                                const toggleIcon = document.getElementById(toggleIconId);

                                                if (inputField.type === "password") {
                                                    inputField.type = "text";
                                                    toggleIcon.classList.remove('fa-eye');
                                                    toggleIcon.classList.add('fa-eye-slash');
                                                } else {
                                                    inputField.type = "password";
                                                    toggleIcon.classList.remove('fa-eye-slash');
                                                    toggleIcon.classList.add('fa-eye');
                                                }
                                            }

                                            function validatePin() {
                                                const pin = document.getElementById('pin_num').value;
                                                const confirmPin = document.getElementById('conf_pin_num').value;
                                                const errorElement = document.getElementById('pinError');
                                                const successElement = document.getElementById('pinSuccess');

                                                if (pin.length === 6 && confirmPin.length === 6) {
                                                    if (pin === confirmPin) {
                                                        errorElement.style.display = 'none';
                                                        successElement.style.display = 'block';
                                                    } else {
                                                        errorElement.style.display = 'block';
                                                        successElement.style.display = 'none';
                                                    }
                                                } else {
                                                    errorElement.style.display = 'none';
                                                    successElement.style.display = 'none';
                                                }
                                            }
                                        </script>

                                        <!-- Include Font Awesome for the eye icon -->
                                        <link
                                            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
                                            rel="stylesheet">


                                    </div>

                                    <!-- Add more fields... -->
                                </div>
                                <div class="btn-float">
                                    <button type="button" class="btn btn-danger"
                                        onclick="window.location='{{ route('person_registration') }}'">Cancel</button>
                                    <button type="button" onclick="authentication()"
                                        class="btn btn-primary">Submit</button>
                                    <!-- onclick="window.location='{{ route('user_registration_data') }}'" -->
                                </div>
                            </form>
                        </div> <!-- End Tab 3 -->


                        <!-- <label for="colFormLabelSm" class="col-sm-2 col-form-label mt-3 driver"
                                        style="display: none;">Licence
                                        Proof</label>
                                    <div class="col-sm-4 mt-3 driver" style="display: none;">
                                        <input type="file" class="form-control" id="licence_proof"
                                            name="licence_proof" />
                                    </div> -->





                    </div>

                    </form>
                    </class>

                </div> <!-- end tab-pane -->
            </div> <!-- enaneab-content -->
        </div> <!-- end #basicwizard -->
    </div>
</div>
</div>


<!-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->

<script src=" {{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>

<script>
    const passwordField = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');

    togglePassword.addEventListener('click', function() {
        // Toggle the type attribute for Password field
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);

        // Toggle the eye icon for Password field
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });

    // JavaScript to toggle password visibility for Confirm Password
    const confirmPasswordField = document.getElementById('confirm_password');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');

    toggleConfirmPassword.addEventListener('click', function() {
        // Toggle the type attribute for Confirm Password field
        const type = confirmPasswordField.getAttribute('type') === 'password' ? 'text' : 'password';
        confirmPasswordField.setAttribute('type', type);

        // Toggle the eye icon for Confirm Password field
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });


    document.getElementById('idproof').addEventListener('change', function() {
        alert('hi hello');
    });

    document.getElementById('photo').addEventListener('change', function(event) {
        const file = event.target.files[0]; // Get the selected file

        if (file) {
            const reader = new FileReader(); // Create a FileReader object

            // When the file is loaded, set the image src to the loaded result
            reader.onload = function(e) {
                const imgElement = document.getElementById('imagePreview');
                imgElement.src = e.target.result; // Set the image preview
                imgElement.style.display = 'block'; // Make the image visible
            };

            reader.readAsDataURL(file); // Read the file as a data URL
        }
    });

    function image_upload() {


        var choose_pic = $('#photo_pic').val();


        if (choose_pic == 'upload_pic') {
            $('#upload').show();
            $('#snap').hide();
        }

        if (choose_pic == 'snap_pic') {
            $('#snap').show();
            $('#upload').hide();
        }
    }


    function pin_password_change() {

        var pin_passwrd = $('#pin_passwrd').val();


        if (pin_passwrd === 'Password') {
            $('#password_type').show();
            $('#pin_type').hide();

        } else if (pin_passwrd === 'Pin') {
            $('#pin_type').show();

            $('#password_type').hide();

        }
    }




    function toggleVisibility(inputId, toggleIconId) {
        const inputField = document.getElementById(inputId);
        const toggleIcon = document.getElementById(toggleIconId);

        if (inputField.type === "password") {
            inputField.type = "text";
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            inputField.type = "password";
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }

    function validatePin() {
        const pin = document.getElementById('pin_num').value;
        const confirmPin = document.getElementById('conf_pin_num').value;
        const errorElement = document.getElementById('pinError');

        if (pin !== confirmPin) {
            errorElement.style.display = 'block';
            return false; // Prevent form submission if pins don't match
        } else {
            errorElement.style.display = 'none';
            return true; // Allow form submission if pins match
        }
    }


    function password_change() {
        var password = $("#password").val();

        var confirm_password = $('#confirm_password').val();

        var password_err = $('#password_error');
        var password_crct = $('#password_crct');

        if (password !== confirm_password) {
            password_err.show();
            password_crct.hide();
        } else if (password !== '' && confirm_password !== '') {
            password_err.hide();
            password_crct.show();
        } else {
            password_err.hide();
            password_crct.hide();
        }
    }


    function proof_change() {
        alert('pc');
        var proof = $('#idproof').val();

        if (proof === 'aadhaar') {
            $('#aadhar_id').show();
            $('#licence_id').hide();
        } else if (proof === 'driving_licence') {
            $('#licence_id').show();
            $('#aadhar_id').hide();
        }
    }



    //store function
    function submit_next() {

        // e.preventDefault();

        // Create a new FormData object
        var formData = new FormData();

        // Append form fields to the FormData object
        formData.append('academic_year', $('#academic_year').val());

        formData.append('name', $('#full_name').val());
        formData.append('designation', $('#designation_select').val());
        formData.append('aadhaar_no', $('#aadhaar_no').val());
        formData.append('licence_validity_date', $('#licence_date').val());
        formData.append('nationality', $('#nationality').val());
        formData.append('country', $('#country').val());
        formData.append('mobile', $('#mobile').val());
        formData.append('blood_group', $('#blood_group').val());
        formData.append('email_id', $('#email_id').val());
        formData.append('photo_pic', $('#photo_pic').val());
        formData.append('_token', '{{csrf_token()}}');


        var aadhaar = $('#aadhaar_no').val();

        // var validationError = $('#')

        const isValidAadhaar = /^\d{12}$/.test(aadhaar);

        if (!isValidAadhaar) {
            let validationError = '';

            if (aadhaar.length !== 12) {
                validationError += 'Aadhaar Number must be exactly 12 characters long.\n';
            }
            if (/[^\d]/.test(aadhaar)) {
                validationError += 'Aadhaar Number must contain only numbers.\n';
            }

            sweetalert("aadhaar", validationError);
            return;
        }
        // Get the file from the input and append it to the FormData object
        // var photo = document.getElementById('photo').files[0];
        // if (photo) {
        //     formData.append('photo', photo);
        // }

        // Get the file from the input
        var photo = document.getElementById('photo').files[0];
        if (photo) {
            formData.append('photo', photo);
        } else {
            formData.append('current_image', "{{ $person_registration->image_upload }}");
        }

        // Make the AJAX request using FormData
        $.ajax({
            type: "POST",
            url: "{{route('person_registration.update',$person_registration->id)}}",
            data: formData,
            contentType: false, // Important for sending FormData
            processData: false, // Prevent jQuery from processing the data
            success: function(response) {


                if (response.unique_id) {
                    var unique_id = response.unique_id;

                    var aadhaar_sub = response.aadhaar_no;

                    // alert(aadhaar_sub);

                    var licence_val_sub = response.licence_validity_date;

                    // alert(aadhaar_sub);

                    // alert('Unique ID: ' + unique_id);

                    $('#main_unique_id').val(unique_id);

                    $('#aadhaar_num').val(aadhaar_sub);

                    $('#licence_val_sub').val(licence_val_sub);


                } else {
                    alert('Unique ID not returned');
                }

                document.querySelector('#tab2-link').click();
                // location.reload(); //
                // alert('sucess');
                sweetalert("update");
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    }


    function authentication() {
        // Get values from input fields
        var user_id = $('#login_id').val();
        var password = $('#password').val();
        var confirm_password = $('#confirm_password').val();
        var main_unique_id = $('#reg_unique_id').val();



        // Password validation checks
        // const hasUpperCase = /[A-Z]/.test(password);
        // const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);
        // const minLength = password.length >= 8;

        if (!user_id) {
            sweetalert("form_alert");
            return;
        }

        // let validationError = '';

        // if (!minLength) {
        //     validationError += 'Password must be at least 8 characters long.\n';
        // }
        // if (!hasUpperCase) {
        //     validationError += 'Password must contain at least one uppercase letter.\n';
        // }
        // if (!hasSpecialChar) {
        //     validationError += 'Password must contain at least one special character.\n';
        // }
        // if (password !== confirm_password) {
        //     validationError += 'Passwords do not match.\n';
        // }

        // if (validationError) {
        //     sweetalert("password_validation", validationError);
        // }

        var data = {
            user_id: user_id,
            // password: password,
            // confirm_password: confirm_password,
            main_unique_id: main_unique_id,
            _token: '{{csrf_token()}}'
        };

        $.ajax({
            type: "POST",
            url: "{{route('person_authentication.update',$person_registration->id)}}",
            data: data,
            success: function(response) {
                const redirecturl = "{{route('person_registration')}}";
                sweetalert("create", redirecturl);
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    }


    // $(document).ready(function() {


    //     alert('12');

    //     const mainUniqueId = $('#main_unique_id').val();

    //     alert(mainUniqueId);

    //     if (mainUniqueId) {
    //         $.ajax({
    //             type: "GET",
    //             url: "{{ route('get_person_sublist_data') }}", // Replace with your route
    //             data: {
    //                 main_unique_id: mainUniqueId
    //             },
    //             success: function(response) {

    //                 alert('success');
    //                 if (response.data && Array.isArray(response.data)) {
    //                     sublistData = response.data.map((item) => ({
    //                         idProofType: item.proof_type,
    //                         aadhaarNum: item.aadhar_num,
    //                         licenceNo: item.licence_no,
    //                         licenceExpire: item.licence_expire,
    //                         fileName: item.attached_proof
    //                     }));
    //                     renderSublistTable();
    //                 }
    //             },
    //             error: function(xhr) {
    //                 console.log("Error fetching data:", xhr.responseText);
    //             }
    //         });
    //     }

    // });

    //
    // function renderSublistTable() {
    //     const tbody = $('#sublist tbody');
    //     tbody.empty();

    //     sublistData.forEach((item, index) => {
    //         const row = `
    //             <tr>
    //                 <td>${index + 1}</td>
    //                 <td>${item.idProofType}</td>
    //                 <td>${item.idProofType === 'aadhaar' ? item.aadhaarNum : item.licenceNo}</td>
    //                 <td>${item.licenceExpire || '-'}</td>
    //                 <td>${item.fileName || 'No File'}</td>
    //                 <td>
    //                     <button class="btn btn-warning btn-sm" onclick="editSublistItem(${index})">Edit</button>
    //                     <button class="btn btn-danger btn-sm" onclick="deleteSublistItem(${index})">Delete</button>
    //                 </td>
    //             </tr>
    //         `;
    //         tbody.append(row);
    //     });
    // }




    //sublist edit function

    // Function to handle editing a sublist item


    function deleteSublistItem(form_unique_id) {
        if (confirm('Are you sure you want to delete this item?')) {
            $.ajax({
                type: "POST",
                url: "{{ route('sublist.delete', ':form_unique_id') }}".replace(':form_unique_id', form_unique_id),
                data: {
                    _token: '{{ csrf_token() }}' // Include CSRF token for security
                },
                success: function(response) {
                    // alert(response.message);
                    // table.ajax.reload();
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    alert('An error occurred while deleting the item: ' + xhr.responseText);
                }
            });
        }
    }




    //document details
    function sublist_proof(event) {

        event.preventDefault()
        // Get form values
        var proof_type = $('#idproof').val();
        var aadhaar_num = $('#aadhaar_num').val();
        var licence_no = $('#licence_no').val();
        var main_unique_id = $('#reg_unique_id').val();

        // alert(main_unique_id);


        var licence_val_sub = $('#licence_val_sub').val();

        // alert(main_unique_id);

        var s_no = 1;

        var s_nos = s_no++;

        // Set default for none values

        if (aadhaar_num != '') {
            proof_value = aadhaar_num;
        } else {
            proof_value = licence_no;
        }


        // if (aadhaar_num === '') {
        //     aadhaar_num = 'none';
        // }

        // if (licence_no === '') {
        //     licence_no = 'none';
        // }

        // Create FormData object to handle file uploads
        var formData = new FormData();
        formData.append('proof_type', proof_type);
        formData.append('main_unique_id',
            main_unique_id);
        formData.append('proof_value', proof_value);
        // formData.append('licence_no', licence_no);
        formData.append('_token', '{{csrf_token()}}'); // Add CSRF token for security

        // Handle file inputs (append only if the file is selected)
        var aadhaar_proof = document.getElementById('aadhaar_proof').files[0];
        var licence_proof = document.getElementById('licence_proof').files[0];

        if (aadhaar_proof) {
            formData.append('aadhaar_proof', aadhaar_proof);
        }

        if (licence_proof) {
            formData.append('licence_proof', licence_proof);
        }

        // AJAX request to submit form data
        $.ajax({
            type: "POST",
            url: '{{route("person_sublist.create")}}', // Update the route
            data: formData,
            contentType: false, // Important for file upload
            processData: false, // Prevent jQuery from processing the data


            success: function(response) {

                var file_name = response.attached_proof;
                var proof_type = response.proof_type;
                var newRow = `
                <tr>
                    <td>${s_nos}</td>
                    <td>${response.proof_type}</td>
                    <td>${response.aadhar_licence}</td>
                    <td>${licence_val_sub}</td>
                    <td>
                   <a onclick ="imageViews('${file_name}','${proof_type}')"><img src='public/uploads/download.png' width="40px" height="40px"></a>
                    </td>
                    <td>
                        <button class="btn btn-danger btn-sm" onclick="deleteProof(${response.id})">Delete</button>
                    </td>
                </tr>
            `;

                // Append the new row to the tbody of the #sublist table
                $('#sublist tbody').append(newRow);

                // Optionally, reset form fields after successful submission
                $('#idproof').val('');
                $('#aadhaar_num').val('');
                $('#licence_no').val('');
                $('#aadhaar_proof').val('');
                $('#licence_proof').val('');
            },
            error: function(xhr) {
                console.log(xhr.responseText); // Log any errors for debugging
            }
        });
    }

    //-------------------------image and pdf view download function------///////

    function imageViews(file_name, proof_type) {
        // Split the file names by commas (in case there are multiple)
        var fileNames = file_name.split(',');

        // Base directory wher your files are stored
        var aadhaarDirectory = 'public/uploads/person_registration/aadhaar/';
        var licenceDirectory = 'public/uploads/person_registration/licence/';


        fileNames.forEach(function(fileName) {
            // Get the file extension
            var cFileName = fileName.split('.');
            var fileExtension = cFileName[cFileName.length - 1].toLowerCase();
            var filePath = `${baseDirectory}${fileName}`; // Construct the full path



            var baseDirectory;

            if (proof_type == 'aadhaar') {
                baseDirectory = aadhaarDirectory;
            } else if (proof_type == 'driving_licence') {
                baseDirectory = licenceDirectory;

            }

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

    function imageeditViews(file_name, proof_type) {
        // Split the file names by commas (in case there are multiple)
        var fileNames = file_name.split(',');

        // Base directory wher your files are stored
        var aadhaarDirectory = '/harbour/public/uploads/person_registration/aadhaar/';
        var licenceDirectory = '/harbour/public/uploads/person_registration/licence/';


        fileNames.forEach(function(fileName) {
            // Get the file extension
            var cFileName = fileName.split('.');
            var fileExtension = cFileName[cFileName.length - 1].toLowerCase();
            var filePath = `${baseDirectory}${fileName}`; // Construct the full path



            var baseDirectory;

            if (proof_type == 'aadhaar') {
                baseDirectory = aadhaarDirectory;
            } else if (proof_type == 'driving_licence') {
                baseDirectory = licenceDirectory;

            }

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


    function valid_password(input) {
        const password = input.value;
        const passwordError = document.getElementById('passwordError');

        // Regular expressions to check the password requirements
        const hasUpperCase = /[A-Z]/.test(password);
        const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);
        const minLength = password.length >= 8;

        passwordError.textContent = '';

        // Validate password
        if (!minLength || !hasUpperCase || !hasSpecialChar) {
            let errorMessage =
                'Password must be at least 8 characters long, contain at least one uppercase letter, and one special character.';
            passwordError.textContent = errorMessage;
        }
    }

    function validateConfirmPassword() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const confirmPasswordError = document.getElementById('confirmPasswordError');

        // Clear previous error message
        confirmPasswordError.textContent = '';

        // Validate confirm password
        if (password !== confirmPassword) {
            confirmPasswordError.textContent = 'Passwords do not match.';
        }
    }







    function view(fileName) {


        alert('view');

        var url = "public/uploads/person_registration/aadhaar/" + fileName;

        window.open(url, '_blank', "width=600,height=480,toolbar=no,menubar=no,resizable=yes");

    }



    function valid_aadhar_number(input) {
        const allowedChars = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        // Filter out characters that are not in the allowedChars array
        input.value = input.value.split('').filter(char => allowedChars.includes(char)).join('');

        // Check if the length is exactly 12
        if (input.value.length >= 12) {
            input.value = input.value.slice(0, 12); // Trim to 12 characters if necessary
        }
    }


    function valid_mobile_number(input) {
        const allowedChars = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '+'];

        // Filter out characters that are not in the allowedChars array
        input.value = input.value.split('').filter(char => allowedChars.includes(char)).join('');

        // Check if the length is exactly 12
        if (input.value.length >= 13) {
            input.value = input.value.slice(0, 13); // Trim to 12 characters if necessary
        }
    }

    function mail_valid(input) {

        const allowedChars = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u',
            'v', 'w', 'x', 'y', 'z',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U',
            'V', 'W', 'X', 'Y', 'Z',
            ' ', '_', '.', '@'
        ];

        const mailError = document.getElementById('mailError');


        // Filter out characters that are not in the allowedChars array
        input.value = input.value.split('').filter(char => allowedChars.includes(char)).join('');

        const mail = (input.value).test(allowedChars);


        if (!mail) {
            let errorMessage =
                'Enter valid mail id.';
            mailError.textContent = errorMessage;
        }
    }




    function download_pdf(fileName) {

        alert('ji');
        var pdfUrl = "public/uploads/person_registration/aadhaar/" + fileName;
        var link = document.createElement("a");
        link.href = pdfUrl;
        link.download = fileName;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }








    function acc_year_change() {
        document.getElementById('acc_year').value = " ";
        // Get the input elements
        let fMonth = document.getElementById('from_year').value;
        let tMonth = document.getElementById('to_year').value;

        // Split the values to get the year parts
        let fMonthArray = fMonth.split('-'); // ["YYYY", "MM"]
        let tMonthArray = tMonth.split('-'); // ["YYYY", "MM"]

        // Extract the year parts
        let fYear = fMonthArray[0];
        let tYear = tMonthArray[0];

        if (fYear >= tYear) {
            document.getElementById('acc_year').value = '';
        } else {
            // Concatenate the years
            let accYear = fYear + " - " + tYear;

            // Set the concatenated year to the target element
            document.getElementById('acc_year').value = accYear;
        }
    }

    function validateForm() {
        let isValid = true;
        let accyear = document.getElementById('acc_year');
        let accerr = document.getElementById('acc_err');

        if (accyear.value.trim() === '') {
            accerr.textContent = 'Academic Year must not me empty';
            isValid = false;
        }

        return isValid;
    }

    function toggleLicenceFields() {

        alert('licence');
        const designation = document.getElementById('designation_select').value;
        const licenceProofFields = document.getElementsByClassName('licence_valididty_date');

        for (let i = 0; i < licenceProofFields.length; i++) {
            if (designation === '66fe4a0298b9a71480') {
                licenceProofFields[i].style.display = 'block';
            } else {
                licenceProofFields[i].style.display = 'none';
            }
        }
    }

    $(document).ready(function() {
        $('#openTableButton').click(function(event) {
            event.preventDefault(); // Prevent the default action

            // Your code to open the DataTable or any other action
            $('#dataTableContainer').show();
        });
    });

    function pdf() {

        var url = "resources/views/content/person_registration/testpdf1.pdf";
        window.open(url, '_blank');
    }

    function editSublistItem(form_unique_id, proof_type, aadhar_licence) {

        // Set the certificate type dropdown
        $('#idproof').val(proof_type);


        // Set other fields based on the type of certificate
        if (proof_type === 'aadhaar') {
            $('#aadhar_id').show();

            $('#aadhaar_num').val(aadhar_licence);


        } else if (proof_type === 'driving_licence') {
            $('#licence_id').show();
            $('#licence_no').val(aadhar_licence);

        }
    }
</script>


@endsection