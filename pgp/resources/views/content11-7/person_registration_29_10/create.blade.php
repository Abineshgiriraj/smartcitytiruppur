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

$get_firms_registration = get_firm_company();

$firm = $get_firms_registration->first();

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
                            <form id="person_registration">
                                @csrf
                                @if(isset($acc_year))
                                @method('PUT')
                                @endif

                                <div class="row mb-3">
                                    <label for="academic_year" class="col-sm-2 col-form-label">Academic Year</label>
                                    <div class="col-sm-4">
                                        <select class="form-select" name="academic_year" id="academic_year">
                                            <option value="">Select Academic Year</option>
                                            @foreach ($get_academic_year as $acc_year)
                                            <option value="{{$acc_year->unique_id}}">{{$acc_year->acc_year}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <label for="full_name" class="col-sm-2 col-form-label">Full Name</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="full_name" name="full_name" />
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="register_number" class="col-sm-2 col-form-label">Register Number</label>
                                    <div class="col-sm-4">
                                        <label class="col-form-label"><b
                                                id="register_number">{{ isset($register_number) ? $register_number : 'Loading...' }}</b></label>
                                        <input type="hidden" id="hidden_register_number" name="register_number"
                                            value="">
                                    </div>

                                    <label for="firm_company" class="col-sm-2 col-form-label">Company</label>
                                    <div class="col-sm-4">
                                        <select class="form-select" name="firm_company" id="firm_company">
                                            <option value="">Select</option>
                                            @foreach ($get_firms_registration as $get_firms)
                                            <option value="{{$get_firms->unique_id}}">{{$get_firms->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="designation_selet" class="col-sm-2 col-form-label">Designation</label>
                                    <div class="col-sm-4">
                                        <select class="form-select" id="designation_select" name="designation_select"
                                            onchange="toggleLicenceFields()">
                                            <option selected>Select Designation</option>
                                            @foreach ($get_designations as $designation)
                                            <option value="{{$designation->unique_id}}">{{$designation->designation}}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-2 col-form-label licence_valididty_date" style="display: none;"
                                        id="licenceExpiryContainer">
                                        <label for="licence_date">Licence Expiry Date</label>
                                    </div>

                                    <div class="col-sm-4 licence_valididty_date" style="display: none;"
                                        id="licence_date_container">
                                        <input type="date" class="form-control" id="licence_date" name="licence_date" />
                                    </div>

                                </div>

                                <div class="row mb-3">
                                    <label for="aadhaar_no" class="col-sm-2 col-form-label">Aadhaar No</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="aadhaar_no"
                                            oninput="valid_aadhar_number(this)" name="aadhaar_no" />
                                        <span id="aadhaarError" style="color:red;"></span>
                                    </div>

                                    <label for="nationality" class="col-sm-2 col-form-label">Nationality</label>
                                    <div class="col-sm-4">
                                        <select class="form-select" id="nationality" name="nationality">
                                            <option selected>Select Nationality</option>
                                            <option value="Indian">Indian</option>
                                            <option value="Foreign">Foreign</option>
                                            <option value="Indian-origin">Indian Origin</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="country" class="col-sm-2 col-form-label">Country</label>
                                    <div class="col-sm-4">
                                        <select class="form-select" id="country" name="country">
                                            <option selected value="india">India</option>
                                            <option value="usa">United States</option>
                                            <option value="uk">United Kingdom</option>
                                            <option value="canada">Canada</option>
                                            <option value="australia">Australia</option>
                                            <option value="germany">Germany</option>
                                            <option value="france">France</option>
                                            <option value="japan">Japan</option>
                                            <option value="china">China</option>
                                            <option value="brazil">Brazil</option>
                                            <option value="south-africa">South Africa</option>
                                            <option value="indian-origin">Indian Origin</option>
                                        </select>
                                    </div>

                                    <label for="mobile" class="col-sm-2 col-form-label">Mobile</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="mobile" name="mobile"
                                            oninput="valid_mobile_number(this)" />
                                        <span id="mobileError" style="color:red;"></span>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="blood_group" class="col-sm-2 col-form-label">Blood Group</label>
                                    <div class="col-sm-4">
                                        <select class="form-select" id="blood_group" name="blood_group">
                                            <option selected>Select Blood Group</option>
                                            <option value="A+">A+</option>
                                            <option value="A-">A-</option>
                                            <option value="B+">B+</option>
                                            <option value="B-">B-</option>
                                            <option value="O+">O+</option>
                                            <option value="O-">O-</option>
                                            <option value="AB+">AB+</option>
                                            <option value="AB-">AB-</option>
                                        </select>
                                    </div>

                                    <label for="email_id" class="col-sm-2 col-form-label">Email ID</label>
                                    <div class="col-sm-4">
                                        <input type="email" class="form-control" id="email_id"
                                            oninput="mail_valid(this)" name="email_id" />
                                        <span id="mailError" style="color:red;"></span>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="photo_pic" class="col-sm-2 col-form-label">Photo</label>
                                    <div class="col-sm-4">
                                        <select class="form-select" name="photo_pic" id="photo_pic"
                                            onchange="image_upload()">
                                            <option value="">Select</option>
                                            <option value="upload_pic">Upload</option>
                                            <option value="snap_pic">Snap</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3" id="upload" style="display:none;">
                                    <label for="photo" class="col-sm-2 col-form-label">Upload Photo</label>
                                    <div class="col-sm-4">
                                        <input type="file" class="form-control" id="photo" name="photo" />
                                    </div>
                                    <div class="col-sm-2 mt-3">
                                        <img id="imagePreview" src="#" alt="Image Preview"
                                            style="display:none; max-width: 200px;" />
                                    </div>
                                </div>

                                <div class="row mb-3" id="snap" style="display:none;">
                                    <div class="col-sm-2">
                                        <label for="photoPreview" class="col-form-label">Preview Photo</label>
                                    </div>
                                    <div class="col-sm-4 text-center">
                                        <img src="resources/views/content/person_registration/13.png" alt="..."
                                            class="img-thumbnail"
                                            style="border: 5    px solid grey; max-width: 100%; height: auto;">
                                        <div class="mt-3">
                                            <button type="button" onclick="web_cam()"
                                                class="btn btn-primary">Snap</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="btn-float">
                                    <button type="button" class="btn btn-primary" onclick="submit_next()">Next</button>
                                </div>
                            </form>
                        </div>



                        <div class="tab-pane" id="basictab2">
                            <form id="documentVerificationForm">
                                @csrf
                                @if(isset($acc_year))
                                @method('PUT')
                                @endif

                                <div class="row mb-3">
                                    <input type="hidden" id="main_unique_id" name="main_unique_id">
                                    <input type="hidden" id="licence_val_sub" name="licence_val_sub">
                                    <input type="hidden" id="port_type" name="port_type" value="1">


                                    <label for=" idproofSelect" class="col-sm-2 col-form-label">Type
                                        of ID
                                        proof</label>
                                    <div class="col-sm-4">
                                        <select class="form-select" id="idproof" name="idproof"
                                            onchange="proof_change()">
                                            <option selected>Select ID Proof</option>
                                            <option value="Aadhaar">Aadhaar</option>
                                            <option value="Driving Licence">Driving Licence</option>
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
                                                <th>Aadhar No</th>
                                                <th>Licence Expiry</th>
                                                <th>Document</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
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
                                            <input type="text" readonly class="form-control" name="login_id"
                                                id="login_id" readonly>
                                        </div>

                                        <div class="col-sm-2 mt-3">
                                            <label for="password" class="col-form-label">Password:</label>
                                        </div>
                                        <div class="col-sm-4 mt-3">
                                            <div class="input-wrapper">

                                                <input type="password" class="form-control" name="password"
                                                    id="password" oninput="valid_password(this)"
                                                    onkeyup="password_change()">
                                                <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                                            </div>
                                            <span id="passwordError" style="color: red;"></span>
                                        </div>

                                        <div class="col-sm-2 mt-3">
                                            <label for="confirm_password" class="col-form-label">Confirm
                                                Password:</label>
                                        </div>
                                        <div class="col-sm-4 mt-3">
                                            <div class="input-wrapper">

                                                <input type="confirm_password" class="form-control"
                                                    name="confirm_password" onkeyup="password_change()"
                                                    id="confirm_password">
                                                <i class="fas fa-eye toggle-password" id="toggleConfirmPassword"></i>
                                            </div>
                                            <span id="passwordError" style="color: red;"></span>
                                        </div>
                                        <small id="password_error" class="text-danger" style="display:none;">
                                            Passwords do not match
                                        </small>
                                        <small id="password_crct" class="text-success" style="display:none;">
                                            Passwords match
                                        </small>
                                        <div class="col-sm-2 mt-3 password-container password" ;>
                                            <label for="colFormLabelSm" class="col-form-label">RF Id:</label>
                                        </div>
                                        <div class="col-sm-4 password" ;>
                                            <div class="input-wrapper">
                                                <input type="text" class="form-control  mt-3" id="rf_id" name="rf_id" />

                                                <!-- <i class="fas fa-eye toggle-password" id="toggleConfirmPassword"></i> -->

                                            </div>

                                        </div>
                                        <!-- <div id="password_type" class="w-100 mt-3">
                                            <div class="col-sm-2 password-container">
                                                <label for="password" class="col-form-label">Password</label>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="input-wrapper">
                                                    <input type="password" class="form-control" id="password"
                                                        name="password" oninput="valid_password(this)"
                                                        onkeyup="password_change()" />
                                                    <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                                                </div>
                                                <span id="passwordError" style="color: red;"></span>
                                            </div>

                                            <div class="col-sm-2 password-container">
                                                <label for="confirm_password" class="col-form-label">Confirm
                                                    Password</label>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="input-wrapper">
                                                    <input type="password" class="form-control" id="confirm_password"
                                                        name="confirm_password" onkeyup="password_change()" />
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
                                        </div> -->

                                        <!-- Optional PIN section can be added here if needed -->

                                    </div>

                                    <div class="btn-float mt-3">
                                        <button type="button" class="btn btn-danger"
                                            onclick="window.location='{{ route('person_registration') }}'">Cancel
                                        </button>
                                        <button type="button" onclick="authentication()"
                                            class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div> <!-- End Tab 3 -->

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
                        <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" -->
                        <!-- rel="stylesheet"> -->



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
<!--
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">


<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>

<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script> -->



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


</script>

<script>
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
    var proof = $('#idproof').val();

    if (proof === 'Aadhaar') {
        $('#aadhar_id').show();
        $('#licence_id').hide();
    } else if (proof === 'Driving Licence') {
        $('#licence_id').show();
        $('#aadhar_id').hide();
    }
}



//store function
function submit_next() {

    // e.preventDefault();

    // Create a new FormData object
    var formData = new FormData();

    var academic_year = $('#academic_year').val();
    var name = $('#full_name').val();
    var register_number = $('#hidden_register_number').val();
    var firm_company = $('#firm_company').val();
    var designation = $('#designation_select').val();
    var aadhaar = $('#aadhaar_no').val();
    var licence_validity_date = $('#licence_date').val();
    var nationality = $('#nationality').val();
    var country = $('#country').val();
    var mobile = $('#mobile').val();
    var blood_group = $('#blood_group').val();
    var email_id = $('#email_id').val();
    var port_type = $('#port_type').val();


    var photo = document.getElementById('photo').files[0];

    // Append form fields to the FormData object

    formData.append('academic_year', $('#academic_year').val());

    formData.append('name', $('#full_name').val());
    formData.append('register_number', $('#hidden_register_number').val());
    formData.append('firm_company', $('#firm_company').val());

    formData.append('designation', $('#designation_select').val());
    formData.append('aadhaar_no', $('#aadhaar_no').val());
    formData.append('licence_validity_date', $('#licence_date').val());
    formData.append('nationality', $('#nationality').val());
    formData.append('country', $('#country').val());
    formData.append('mobile', $('#mobile').val());
    formData.append('blood_group', $('#blood_group').val());
    formData.append('email_id', $('#email_id').val());
    formData.append('port_type', $('#port_type').val());
    formData.append('photo_pic', $('#photo_pic').val())
    formData.append('_token', '{{csrf_token()}}');





    if (!academic_year || !name || !register_number || !firm_company || !designation ||
        !aadhaar || !nationality || !country || !mobile ||
        !blood_group || !email_id || !port_type) {
        Swal.fire({
            icon: 'warning',
            title: 'Form Incomplete',
            text: 'Please fill out all fields.',
            confirmButtonText: 'OK'
        });
        return;
    }

    const isValidAadhaar = /^\d{12}$/.test(aadhaar);
    if (!isValidAadhaar) {
        let validationError = '';
        if (aadhaar.length !== 12) {
            validationError += 'Aadhaar Number must be exactly 12 characters long.\n';
        }
        if (/[^\d]/.test(aadhaar)) {
            validationError += 'Aadhaar Number must contain only numbers.\n';
        }
        Swal.fire({
            icon: 'error',
            title: 'Invalid Aadhaar Number',
            text: validationError,
            confirmButtonText: 'OK'
        });
        return;
    }

    const isValidMobile = /^\d{10,13}$/.test(mobile);
    console.log(`Mobile: ${mobile}, isValid: ${isValidMobile}`); // Log for debugging
    if (!isValidMobile) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Mobile Number',
            text: 'Mobile Number must be between 10 to 13 digits long and contain only numbers.',
            confirmButtonText: 'OK'
        });
        return;
    }


    const isValidEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email_id);
    if (!isValidEmail) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Email',
            text: 'Please enter a valid email address.',
            confirmButtonText: 'OK'
        });
        return;
    }




    // Get the file from the input and append it to the FormData object
    // var photo = document.getElementById('photo').files[0];
    if (photo) {
        formData.append('photo', photo);
    }

    // Make the AJAX request using FormData
    $.ajax({
        type: "POST",
        url: "{{route('person_registration.store')}}",
        data: formData,
        contentType: false, // Important for sending FormData
        processData: false, // Prevent jQuery from processing the data
        success: function(response) {


            if (response.unique_id) {
                var unique_id = response.unique_id;

                var aadhaar_sub = response.aadhaar_no;


                var licence_val_sub = response.licence_validity_date;



                $('#main_unique_id').val(unique_id);

                $('#aadhaar_num').val(aadhaar_sub);
                $('#login_id').val(aadhaar_sub);


                $('#licence_val_sub').val(licence_val_sub);


            } else {
                alert('Unique ID not returned');
            }

            document.querySelector('#tab2-link').click();
            // location.reload(); //
            // alert('sucess');
            sweetalert("create");
        },
        error: function(xhr) {


            if (xhr.status === 422) {
                Swal.fire({
                    icon: 'info',
                    title: 'Person Already Exist!',
                    showConfirmButton: true,
                    timer: 1500,
                    timerProgressBar: true
                });
            }
        }
    });
}


function authentication() {
    // Get values from input fields
    var user_id = $('#login_id').val();
    var password = $('#password').val();
    var confirm_password = $('#confirm_password').val();
    var rf_id = $('#rf_id').val();
    var main_unique_id = $('#main_unique_id').val();

    // Password validation checks
    const hasUpperCase = /[A-Z]/.test(password);
    const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);
    const minLength = password.length >= 8;

    if (!user_id && !password && !confirm_password && !main_unique_id) {
        sweetalert("form_alert");
        return;
    }

    let validationError = '';

    if (!minLength) {
        validationError += 'Password must be at least 8 characters long.\n';
    }
    if (!hasUpperCase) {
        validationError += 'Password must contain at least one uppercase letter.\n';
    }
    if (!hasSpecialChar) {
        validationError += 'Password must contain at least one special character.\n';
    }
    if (password !== confirm_password) {
        validationError += 'Passwords do not match.\n';
    }

    if (validationError) {
        sweetalert("password_validation", validationError);
    }

    var data = {
        user_id: user_id,
        password: password,
        confirm_password: confirm_password,
        rf_id: rf_id,
        main_unique_id: main_unique_id,
        _token: '{{csrf_token()}}'
    };

    $.ajax({
        type: "POST",
        url: "{{route('person_authentication.store')}}",
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




///function authenticationForm

// function authentication() {

//     // alert('test');

//     // var authentication_type = $('#pin_passwrd').val();

//     // alert(authentication_type);

//     var user_id = $('#user_id').val();

//     var password = $('#password').val();

//     var confirm_password = $('#confirm_password').val();

//     // alert(confirm_password);

//     // var pin_num = $('#pin_num').val();

//     // var confirm_pin = $('#conf_pin_num').val();

//     var main_unique_id = $('#main_unique_id').val();

//     const hasUpperCase = /[A-Z]/.test(password);
//     const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);

//     const minLength = password.length >= 8;


//     let ValidationError = '';

//     if (!minLength) {

//         alert("min");
//         ValidationError += "Password must be minimum 8 characters Long";
//         alert(ValidationError);

//     }

//     if (!hasUpperCase) {
//         validationError += 'Password must contain at least one uppercase letter.\n';
//     }
//     if (!hasSpecialChar) {
//         validationError += 'Password must contain at least one special character.\n';
//     }
//     if (password !== confirm_password) {
//         validationError += 'Passwords do not match.\n';
//     }

//     if (validationError) {
//         sweetalert("form_alert");

//         return;
//     }

//     var data = {

//         // authentication_type: authentication_type,
//         user_id: user_id,
//         password: password,
//         confirm_password: confirm_password,
//         // pin_num: pin_num,
//         // confirm_pin: confirm_pin,
//         main_unique_id: main_unique_id,
//         _token: '{{csrf_token()}}'
//     }

//     if (((password === confirm_password) && (password != '') && (confirm_password != ''))) {

//         $.ajax({
//             type: "POST",
//             url: "{{route('person_authentication.store')}}",
//             data: data,
//             success: function(response) {
//                 const redirecturl = "{{route('person_registration')}}";
//                 sweetalert("create", redirecturl);
//             },

//             error: function(xhr) {
//                 console.log(xhr.responseText);
//             }
//         });

//     } else {
//         sweetalert("form_alert");
//     }


// }

// sublist store//


// function sublist_proof() {


//     // $('#submit_sublist').prop('disabled', true);

//     var proof_type = $('#idproof').val();
//     var aadhaar_num = $('#aadhaar_num').val();
//     var licence_no = $('#licence_no').val();
//     var main_unique_id = $('#main_unique_id').val();

//     var none = "none";

//     if (aadhaar_num === '') {

//         var aadhaar_num = 'none';
//     }

//     if (licence_no === '') {

//         var licence_no = 'none';
//     }

//     var formData = new FormData();
//     formData.append('proof_type', proof_type);
//     formData.append('main_unique_id', main_unique_id);
//     formData.append('aadhaar_num', aadhaar_num);
//     formData.append('licence_no', licence_no);
//     formData.append('_token', '{{csrf_token()}}');

//     var aadhaar_proof = document.getElementById('aadhaar_proof').files[0];
//     var licence_proof = document.getElementById('licence_proof').files[0];

//     // Only append the proof if a file is selected.
//     if (aadhaar_proof) {
//         formData.append('aadhaar_proof', aadhaar_proof);


//     }

//     if (licence_proof) {
//         formData.append('licence_proof', licence_proof);
//     }

//     $.ajax({
//         type: "POST",
//         url: '{{route("person_sublist.create")}}',
//         data: formData,
//         contentType: false, // Important for file upload
//         processData: false, // Prevent jQuery from processing the data

//         success: function(response) {
//             $('#sublist').html(response);
//             // $('#submit_sublist').prop('disabled', false);

//         },
//         error: function(xhr) {
//             console.log(xhr.responseText);
//             // $('#submit_sublist').prop('disabled', false);

//         }
//     });
// }



$(document).ready(function() {
    // Fetch the last firm number when the document is ready
    $.ajax({
        url: '{{ url("/get-last-person-number") }}',
        method: 'GET',
        success: function(response) {
            // Update the Firm ID label with the new Firm ID
            $('#register_number').text(response.register_no);
            $('#hidden_register_number').val(response.register_no);
        },
        error: function(xhr, status, error) {
            console.error("Error fetching Firm ID:", error);
            $('#register_number').text('Error loading Firm ID');
        }
    });
});




//document details
function sublist_proof(event) {

    event.preventDefault()
    // Get form values
    var proof_type = $('#idproof').val();
    var aadhaar_num = $('#aadhaar_num').val();
    var licence_no = $('#licence_no').val();
    var main_unique_id = $('#main_unique_id').val();

    var licence_val_sub = $('#licence_val_sub').val();

    // alert(licence_val_sub);

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
    formData.append('main_unique_id', main_unique_id);
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
                   <a onclick ="imageViews('${file_name}','${proof_type}')"><img src='public/uploads/dwn.png' width="40px" height="40px"></a>
                    </td>
                    <td>
                      <a class="text-primary" title="Edit"  onclick="editDocument(${response.id}, '${response.proof_type}', '${response.aadhar_licence}', '${licence_val_sub}')"><i class="fas fa-edit"></i></a>
                      <a href="#" class="text-danger" title="Delete" onclick="deleteProof(${response.id})"><i class="fas fa-trash-alt"></i></a>
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

        if (proof_type == 'Aadhaar') {
            baseDirectory = aadhaarDirectory;
        } else if (proof_type == 'Driving Licence') {
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


function valid_aadhar_number(input) {
    const allowedChars = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    // Filter out characters that are not in the allowedChars array
    input.value = input.value.split('').filter(char => allowedChars.includes(char)).join('');

    // Get the error span
    const errorSpan = document.getElementById('aadhaarError');

    // Trim whitespace and check the length of the Aadhaar number
    const aadhaarValue = input.value.trim();

    if (aadhaarValue.length > 12) {
        input.value = aadhaarValue.slice(0, 12); // Trim to 12 characters if necessary
        errorSpan.textContent = 'Aadhaar number must be exactly 12 digits.';
    } else if (aadhaarValue.length < 12) {
        errorSpan.textContent = 'Aadhaar number must be exactly 12 digits.';
    } else {
        errorSpan.textContent = ''; // Clear error message if valid (exactly 12 digits)
    }
}



function valid_mobile_number(input) {
    const allowedChars = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '+'];

    // Filter out characters that are not in the allowedChars array
    input.value = input.value.split('').filter(char => allowedChars.includes(char)).join('');

    const errorSpan = document.getElementById('mobileError');

    const mobileValue = input.value.trim();

    // Check if the length is exactly 12
    if (mobileValue.length > 13) {
        input.value = input.value.slice(0, 13);
        errorSpan.textContent = 'Mobile Number Should be 10 to 13 digits';
    } else if (mobileValue.length < 10) {
        errorSpan.textContent = 'Mobile Number Should be 10 to 13 digits.';
    } else {
        errorSpan.textContent = '';
    }
}

function mail_valid(input) {
    const mailError = document.getElementById('mailError');

    // Allowed characters for the email
    const allowedChars = [
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j',
        'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't',
        'u', 'v', 'w', 'x', 'y', 'z',
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J',
        'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T',
        'U', 'V', 'W', 'X', 'Y', 'Z',
        ' ', '_', '.', '@'
    ];

    // Filter out characters that are not in the allowedChars array
    input.value = input.value.split('').filter(char => allowedChars.includes(char)).join('');

    // Regular expression for validating an email
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // Get the trimmed value of the input
    const emailValue = input.value.trim();

    // Test the input against the email pattern
    if (emailValue.length > 0 && !emailPattern.test(emailValue)) {
        mailError.textContent = 'Enter a valid email ID.';
    } else {
        mailError.textContent = ''; // Clear the error message if valid
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








// function imageViews(file_name) {

//     // alert(file_name);
//     // Split the file names by comma
//     var fileNames = file_name.split(',');
//     var imageView = '';

//     if (fileNames.length > 0) {
//         fileNames.forEach(function(fileName, fileKey) {
//             // Add spacing and line breaks for formatting
//             if (fileKey !== 0) {
//                 if (fileKey % 4 !== 0) {
//                     imageView += "&nbsp;";
//                 } else {
//                     imageView += "<br><br>";
//                 }
//             }

//             // Get the file extension
//             var cFileName = fileName.split('.');
//             var fileExtension = cFileName[cFileName.length - 1].toLowerCase();
//             alert(fileExtension);

//             if (fileExtension === 'jpg' || fileExtension === 'png' || fileExtension === 'jpeg') {
//                 imageView += `<a href="javascript:void(0);"onclick="view('/${fileName}')">
//                             <img src="assets/images/images.png" height="30px" width="30px">
//                           </a>`;
//             } else if (fileExtension === 'pdf') {

//                 imageView += `<a href="#" onclick="download_pdf('/${fileName}'); return false;">
//                 <img src="assets/img/pdf.png" height="30px" width="30px">
//               </a>`;


//             } else if (fileExtension === 'xls' || fileExtension === 'xlsx') {
//                 imageView += `<a href="javascript:void(0);" onclick="printExcel('/${fileName}')">
//                             <img src="assets/images/excel.png" height="30px" width="30px">
//                           </a>`;
//             } else if (fileExtension === 'txt' || fileExtension === 'docx' || fileExtension === 'doc') {
//                 imageView += `<a href="javascript:void(0);" onclick="print('/${fileName}')">
//                             <img src="assets/images/word.png" height="30px" width="30px">
//                           </a>`;
//             }
//         });
//     }



//     return imageView;
// }

// function imageViews(file_name) {
//     // Split the file names by comma
//     var fileNames = file_name.split(',');
//     var imageView = '';

//     if (fileNames.length > 0) {
//         fileNames.forEach(function(fileName, fileKey) {
//             // Add spacing and line breaks for formatting
//             if (fileKey !== 0) {
//                 if (fileKey % 4 !== 0) {
//                     imageView += "&nbsp;";
//                 } else {
//                     imageView += "<br><br>";
//                 }
//             }

//             // Get the file extension
//             var cFileName = fileName.split('.');
//             var fileExtension = cFileName[cFileName.length - 1].toLowerCase();

//             // Generate the appropriate HTML based on the file extension
//             var filePath = `/${fileName}`;
//             var imageSrc = '';

//             if (fileExtension === 'jpg' || fileExtension === 'png' || fileExtension === 'jpeg') {
//                 imageSrc = 'assets/images/images.png';
//                 imageView += `<a href="#" onclick="view('${filePath}'); return false;">
//                                 <img src="${imageSrc}" height="30px" width="30px">
//                               </a>`;
//             } else if (fileExtension === 'pdf') {
//                 imageSrc = 'assets/img/pdf.png';
//                 imageView += `<a href="#" onclick="download_pdf('${filePath}'); return false;">
//                                 <img src="${imageSrc}" height="30px" width="30px">
//                               </a>`;
//             } else if (fileExtension === 'xls' || fileExtension === 'xlsx') {
//                 imageSrc = 'assets/images/excel.png';
//                 imageView += `<a href="#" onclick="printExcel('${filePath}'); return false;">
//                                 <img src="${imageSrc}" height="30px" width="30px">
//                               </a>`;
//             } else if (fileExtension === 'txt' || fileExtension === 'docx' || fileExtension === 'doc') {
//                 imageSrc = 'assets/images/word.png';
//                 imageView += `<a href="#" onclick="print('${filePath}'); return false;">
//                                 <img src="${imageSrc}" height="30px" width="30px">
//                               </a>`;
//             }
//         });
//     }


//     return imageView;
//     // Insert the generated content into the desired container
//     document.getElementById('imageViewContainer').innerHTML = imageView;
// }



// function view(fileName) {
//     alert('View function triggered for file: ' + fileName);
//     var url = "public/uploads/person_registration/aadhaar/" + decodeURIComponent(fileName);
//     window.open(url, '_blank', "width=600,height=480,toolbar=no,menubar=no,resizable=yes");
// }


function view(fileName) {


    alert('view');

    var url = "public/uploads/person_registration/aadhaar/" + fileName;

    window.open(url, '_blank', "width=600,height=480,toolbar=no,menubar=no,resizable=yes");

}

// Example usage
// var appFileName = "file1.jpg,file2.pdf,file3.xlsx,file4.doc"; // Replace this with actual data
// document.getElementById('yourElementId').innerHTML = imageViews(appFileName);





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
</script>

<script>
function toggleLicenceFields() {
    const designationSelect = document.getElementById('designation_select');
    const licenceExpiryContainer = document.getElementById('licenceExpiryContainer');
    const licenceDateContainer = document.getElementById('licence_date_container');

    if (designationSelect.value ===
        '66fe4a0298b9a71480') { // Replace with actual value that should show the licence date
        licenceExpiryContainer.style.display = 'block';
        licenceDateContainer.style.display = 'block';
    } else {
        licenceExpiryContainer.style.display = 'none';
        licenceDateContainer.style.display = 'none';
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


function editDocument(id, proof_type, aadhar_licence, validUpTo) {
    // Set the certificate type dropdown
    $('#idproof').val(proof_type);

    // Set other fields based on the type of certificate
    if (proof_type == 'Aadhaar') {
        $('#aadhaar_num').val(aadhar_licence);

        $('#aadhar_id').show();

    } else if (proof_type == 'Driving Licence') {
        $('#licence_id').show();
        $('#licence_no').val(aadhar_licence);

        $('#licence_expire').val(licence_expiry);
    }
}
</script>

</script>


@endsection