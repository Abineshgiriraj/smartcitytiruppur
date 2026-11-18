@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')
<div class="row mb-1 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">User Firms/</span> Firms Registration</h4>
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

    .headname {
        color: #5859ae;
    }

    .new-card h5 {
        color: #5859ae;
    }
</style>
@php
    $get_academic_year = academic_year();
    $get_firms_registration = get_firms();
    $screen_unique_id =generateScreenUniqueId();
@endphp
<!-- Basic Layout -->



<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-body new-card">
                <h5>Company details</h5>
                <div class="col-sm-2  ">
                            <input type="text" id="screen_unique_id" name="screen_unique_id"
                                value="<?php echo $screen_unique_id;?>">
                            <!-- <input type="text" id="pass_id" name="pass_id"> -->

                        </div>
                        <div class="row mb-3">
    <label for="acc_year" class="col-sm-2 col-form-label">A/C Year</label>
    <div class="col-sm-4">
        <select class="form-select" name="academic_year" id="academic_year">
            @foreach ($get_academic_year as $acc_year)
            <option value="{{$acc_year->unique_id}}">{{$acc_year->acc_year}}</option>
            @endforeach
        </select>
    </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-sm-5 ">
                                <label for="colFormLabelSm" class="col-form-label"> Agent Type</label>
                            </div>
                            <div class="col-sm-7">
                                <select class="form-select mb-2" id="firms" name="firms">
                                    <option selected>Select Firms</option>
                                    @foreach ($get_firms_registration as $get_firms)
                                        <option value="{{$get_firms->unique_id}}">{{$get_firms->company_name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-sm-5 align-self-center">
                                <label for="colFormLabelSm" class="col-form-label">CIN No</label>
                            </div>
                            <div class="col-sm-7">
                                <input type="text" class="form-control mb-2" oninput="validateCompanyName()" id="company_reg_no"
                                    name="company_reg_no" value="{{ old('company_reg_no', $firm->company_reg_no ?? '') }}" />
                            </div>
                            <div class="col-sm-5 align-self-center">
                                <label for="colFormLabelSm" class="col-form-label">Pan No</label>
                            </div>
                            <div class="col-sm-7">
                                <input type="text" class="form-control mb-2" oninput="validateCompanyName()" id="pan_no"
                                    name="pan_no" value="{{ old('pan_no', $firm->pan_no ?? '') }}" />
                            </div>

                            <div class="col-sm-5 align-self-center">
                                <label for="colFormLabelSm" class="col-form-label">Address</label>
                            </div>
                            <div class="col-sm-7">
                                <textarea class="form-control mb-3" oninput="validateAddress('address')"
                                    id="address" name="address" rows="1"></textarea>
                                <span id="address_error" style="color: red;"></span> <!-- Error message span -->
                            </div>


                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-sm-5 align-self-center ">
                                <label for="colFormLabelSm" class="col-form-label">Company Name</label>
                            </div>
                            <div class="col-sm-7">
                                <input type="text" class="form-control mb-2" oninput="validateCompanyName()" id="name"
                                    name="name" value="{{ old('name', $firm->name ?? '') }}" />
                            </div>
                            <div class="col-sm-5 align-self-center">
                                <label for="colFormLabelSm" class="col-form-label">GST No</label>
                            </div>
                            <div class="col-sm-7">
                                <input type="text" class="form-control mb-2" oninput="validateCompanyName()" id="gstin"
                                    name="gstin" value="{{ old('gstin', $firm->gstin ?? '') }}" />
                            </div>
                            <div class="col-sm-5 align-self-center">
                                <label for="colFormLabelSm" class="col-form-label">Email Id</label>
                            </div>
                            <div class="col-sm-7">
                                <input type="text" class="form-control mb-2" oninput="validateCompanyName()" id="email"
                                    name="email" value="{{ old('email', $firm->email ?? '') }}" />
                            </div>
                        </div>
                    </div>
                </div>
                <hr>


                <h5>Authorised person details</h5>

                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-sm-5 align-self-center">
                                <label for="colFormLabelSm" class="col-form-label">Proprietor Name</label>
                            </div>
                            <div class="col-sm-7">
                                <input type="text" class="form-control mb-2" oninput="validateCompanyName()" id="proprietor_name"
                                    name="proprietor_name" value="{{ old('proprietor_name', $firm->proprietor_name ?? '') }}" />
                            </div>
                            <div class="col-sm-5 align-self-center">
                                <label for="colFormLabelSm" class="col-form-label">Adhaar No</label>
                            </div>
                            <div class="col-sm-7">
                                <input type="text" class="form-control mb-2" oninput="validateCompanyName()" id="aadhar_no"
                                    name="aadhar_no" value="{{ old('aadhar_no', $firm->aadhar_no ?? '') }}" />
                            </div>



                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">

                            <div class="col-sm-5 align-self-center">
                                <label for="colFormLabelSm" class="col-form-label">Mobile No</label>
                            </div>
                            <div class="col-sm-7">
                                <input type="text" class="form-control mb-2" oninput="validateCompanyName()" id="mobile"
                                    name="mobile" value="{{ old('mobile', $firm->mobile ?? '') }}" />
                            </div>
                            <div class="col-sm-5 align-self-center">
                                <label for="colFormLabelSm" class="col-form-label">Adhaar Upload</label>
                            </div>
                            <div class="col-sm-7">
                                <input type="file" class="form-control mb-2" oninput="validateCompanyName()" id="adhaar_upload"
                                    name="adhaar_upload" value="{{ old('adhaar_upload', $firm->adhaar_upload ?? '') }}" />
                            </div>


                        </div>
                    </div>
                    <div class="col-md-12 mt-1">
                        <p><B class="headname">Authorised Person - 1</B></p>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-sm-5 align-self-center">
                                <label for="colFormLabelSm" class="col-form-label">Adhaar No</label>
                            </div>
                            <div class="col-sm-7">
                                <input type="text" class="form-control mb-2" oninput="validateCompanyName()" id="adhaar_1"
                                    name="adhaar_1" value="{{ old('adhaar_1', $firm->adhaar_1 ?? '') }}" />
                            </div>
                            <div class="col-sm-5 align-self-center">
                                <label for="colFormLabelSm" class="col-form-label">Proprietor Name</label>
                            </div>
                            <div class="col-sm-7">
                                <input type="text" class="form-control mb-2" oninput="validateCompanyName()" id="proprietor_name_1"
                                    name="proprietor_name_1" value="{{ old('proprietor_name_1', $firm->proprietor_name_1 ?? '') }}" />
                            </div>
                            <div class="col-sm-5 align-self-center">
                                <label for="colFormLabelSm" class="col-form-label">Email Id</label>
                            </div>
                            <div class="col-sm-7">
                                <input type="text" class="form-control mb-2" oninput="validateCompanyName()" id="email_1"
                                    name="email_1" value="{{ old('email_1', $firm->email_1 ?? '') }}" />
                            </div>
                            

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-sm-5">
                                <button type="button" class="btn btn-primary">Get OTP</button>
                            </div>
                            <div class="col-sm-7 align-self-center">
                                <div
                                    class="auth-input-wrapper d-flex align-items-center justify-content-between numeral-mask-wrapper mb-2">
                                    <input type="tel"
                                        class="form-control auth-input h-px-40 text-center numeral-mask mx-sm-1 "
                                        maxlength="1" autofocus="">
                                    <input type="tel"
                                        class="form-control auth-input h-px-40 text-center numeral-mask mx-sm-1 "
                                        maxlength="1">
                                    <input type="tel"
                                        class="form-control auth-input h-px-40 text-center numeral-mask mx-sm-1 "
                                        maxlength="1">
                                    <input type="tel"
                                        class="form-control auth-input h-px-40 text-center numeral-mask mx-sm-1"
                                        maxlength="1">

                                </div>
                            </div>

                            <div class="col-sm-5 align-self-center">
                                <label for="colFormLabelSm" class="col-form-label">Mobile No</label>
                            </div>
                            <div class="col-sm-7">
                                <input type="text" class="form-control mb-2" oninput="validateCompanyName()" id="mobile_1"
                                    name="mobile_1" value="{{ old('mobile_1', $firm->mobile_1 ?? '') }}" />
                            </div>
                            <div class="col-sm-5 align-self-center">
                                <label for="colFormLabelSm" class="col-form-label">Signature Upload</label>
                            </div>
                            <div class="col-sm-7">
                                <input type="file" class="form-control mb-2" oninput="validateCompanyName()" id="signature_upload_1"
                                    name="signature_upload_1" value="{{ old('signature_upload_1', $firm->signature_upload_1 ?? '') }}" />
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 mt-2">
                        <p><B class="headname">Authorised Person - 2</B></p>
                    </div>
                    <div class="col-md-6">
                        <div class="row">

                            <div class="col-sm-5 align-self-center">
                                <label for="colFormLabelSm" class="col-form-label">Proprietor Name</label>
                            </div>
                            <div class="col-sm-7">
                                <input type="text" class="form-control mb-2" oninput="validateCompanyName()" id="proprietor_name_2"
                                    name="proprietor_name_2" value="{{ old('proprietor_name_2', $firm->proprietor_name_2 ?? '') }}" />
                            </div>
                            <div class="col-sm-5 align-self-center">
                                <label for="colFormLabelSm" class="col-form-label">Adhaar No</label>
                            </div>
                            <div class="col-sm-7">
                                <input type="text" class="form-control mb-2" oninput="validateCompanyName()" id="adhaar_2"
                                    name="adhaar_2" value="{{ old('adhaar_2', $firm->adhaar_2 ?? '') }}" />
                            </div><div class="col-sm-5 align-self-center">
                                <label for="colFormLabelSm" class="col-form-label">Email Id</label>
                            </div>
                            <div class="col-sm-7">
                                <input type="text" class="form-control mb-2" oninput="validateCompanyName()" id="email_2"
                                    name="email_2" value="{{ old('email_2', $firm->email_2 ?? '') }}" />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">


                            <div class="col-sm-5 align-self-center">
                                <label for="colFormLabelSm" class="col-form-label">Mobile No</label>
                            </div>
                            <div class="col-sm-7">
                                <input type="text" class="form-control mb-2" oninput="validateCompanyName()" id="mobile_2"
                                    name="mobile_2" value="{{ old('mobile_2', $firm->mobile_2 ?? '') }}" />
                            </div>
                            <div class="col-sm-5 align-self-center">
                                <label for="colFormLabelSm" class="col-form-label">Adhaar Upload</label>
                            </div>
                            <div class="col-sm-7">
                                <input type="file" class="form-control mb-2" oninput="validateCompanyName()" id="adhaar_upload_2"
                                    name="adhaar_upload_2" value="{{ old('adhaar_upload_2', $firm->adhaar_upload_2 ?? '') }}" />
                            </div>
                            <div class="col-sm-5 align-self-center">
                                <label for="colFormLabelSm" class="col-form-label">Signature Upload</label>
                            </div>
                            <div class="col-sm-7">
                                <input type="file" class="form-control mb-2" oninput="validateCompanyName()" id="signature_upload_2"
                                    name="signature_upload_2" value="{{ old('signature_upload_2', $firm->signature_upload_2 ?? '') }}" />
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <h5>Authenication </h5>

                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-sm-5 mt-3">
                                <label for="colFormLabelSm" class="col-form-label">User Login ID:</label>
                            </div>
                            <div class="col-sm-7 mt-3">
                                <input type="text" class="form-control" name="login_id" id="login_id">

                            </div>


                            <div class="col-sm-5 mt-3 password-container">
                                <label for="colFormLabelSm" class="col-form-label">Confirm Password</label>
                            </div>
                            <div class="col-sm-7">
                                <div class="input-wrapper">
                                    <input type="password" class="form-control  mt-3" onkeyup="password_change()"
                                        id="confirm_password" name="confirm_password" />

                                    <i class="fas fa-eye-slash toggle-password" id="toggleConfirmPassword"></i>

                                </div>
                                <span id="passwordError" style="color: red;"></span>
                                <small id="password_error" class="text-danger" style="display:none;">Passwords
                                    do not match</small>
                                <small id="password_crct" class="text-success" style="display:none;">Passwords
                                    match</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-sm-5 mt-3 password-container">
                                <label for="colFormLabelSm" class="col-form-label">Password</label>
                            </div>
                            <div class="col-sm-7">
                                <div class="input-wrapper">
                                    <input type="password" class="form-control  mt-3" id="password" name="password"
                                        oninput="valid_password(this)" onkeyup="password_change()" />
                                    <i class="fas fa-eye-slash toggle-password" id="togglePassword"></i>
                                </div>
                                <span id="passwordError" style="color: red;"></span>
                            </div>
                            <div class="col-sm-5 align-self-center">
                                <label for="colFormLabelSm" class="col-form-label">Port SAP No</label>
                            </div>
                            <div class="col-sm-7">
                                <input type="test" class="form-control mt-3" id="port_sap_no"
                                    name="port_sap_no" value="{{ old('port_sap_no', $firm->port_sap_no ?? '') }}" />
                            </div>
                        </div>
                        

                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="btn-float">
                            <button type="button" class="btn btn-danger"
                                onclick="window.location.href='{{ url('/firms_registration_list')}}'">Cancel</button>
                            <!-- <button type="submit" class="btn btn-primary">Submit</button> -->
                            <button type="button" class="btn btn-primary" onclick="submit_next()">Submit</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>





</div>


<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>
<script src="{{ asset('public/assets/vendor/jquery/jquery.min.js') }}"></script>
<link href="{{ asset('public/assets/vendor/datatables-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet">

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script src="{{ asset('public/assets/vendor/datatables-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('public/assets/vendor/datatables/jquery.dataTables.min.js') }}"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">


<script>
    const passwordField = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');

    togglePassword.addEventListener('click', function () {
        // Toggle the type attribute for Password field
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);

        // Toggle the eye icon for Password field
        this.classList.toggle('fa-eye-slash');
        this.classList.toggle('fa-eye');
    });

    // JavaScript to toggle password visibility for Confirm Password
    const confirmPasswordField = document.getElementById('confirm_password');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');

    toggleConfirmPassword.addEventListener('click', function () {
        // Toggle the type attribute for Confirm Password field
        const type = confirmPasswordField.getAttribute('type') === 'password' ? 'text' : 'password';
        confirmPasswordField.setAttribute('type', type);

        // Toggle the eye icon for Confirm Password field
        this.classList.toggle('fa-eye-slash');
        this.classList.toggle('fa-eye');
    });



    function proof_change() {
        var selectedProof = $('#idproofSelect').val();

        if (selectedProof === 'aadhaar') {
            $('#aadhar_id').show();
            $('#licence_id').hide();

            // Copy Aadhaar number from Tab 1 to Tab 2
            var aadharNumber = $('#aadhar_no').val(); // Get Aadhaar number from Tab 1
            $('#aadhaar_num').val(aadharNumber); // Set it in Tab 2
        } else if (selectedProof === 'company') {
            $('#licence_id').show();
            $('#aadhar_id').hide();
        } else {
            $('#aadhar_id').hide();
            $('#licence_id').hide();
        }
    }

    function submit_next() {
        // alert("hii");
        // e.preventDefault(); // Prevent the default form submission
        // alert($('#firms').val());
        $('#firm_id').parent().hide();
        console.log("Firm ID is stored and displayed hidden.");

        var company_reg_no = $('#company_reg_no').val();
	    var pan_no = $('#pan_no').val();
        var address = $('#address').val().trim();
        var name = $('#name').val().trim();
        var gst = $('#gstin').val().trim();
        var email = $('#email').val().trim();
        var proprietor_name = $('#proprietor_name').val().trim();
        var aadhaar = $('#aadhar_no').val().trim();
        var mobile = $('#mobile').val().trim();
        // var adhaar_upload = $('#adhaar_upload').val().trim();
        var adhaar_1 = $('#adhaar_1').val().trim();
        var proprietor_name_1 = $('#proprietor_name_1').val().trim();
        var email_1 = $('#email_1').val().trim();
        var otp = $('#otp').val();
        var mobile_1 = $('#mobile_1').val().trim();
        // var signature_upload_1 = $('#signature_upload_1').val().trim();
        var proprietor_name_2 = $('#proprietor_name_2').val().trim();
        var adhaar_2 = $('#adhaar_2').val().trim();
        var email_2 = $('#email_2').val().trim();
        var mobile_2 = $('#mobile_2').val().trim();
        // var adhaar_upload_2 = $('#adhaar_upload_2').val().trim();
        // var signature_upload_2 = $('#signature_upload_2').val().trim();
        var login_id = $('#login_id').val();
        var password = $('#password').val();
        var confirm_password = $('#confirm_password').val();
        var academicYear = $('#academic_year').val();
        var formData = new FormData();

// Append form fields to the FormData object
formData.append('academic_year', $('#academic_year').val());
formData.append('screen_unique_id', $('#screen_unique_id').val());
formData.append('firms', $('#firms').val());
formData.append('firm_id', $('#hidden_firm_id').val());
formData.append('company_reg_no', $('#company_reg_no').val());
formData.append('name', $('#name').val());
formData.append('aadhar_no', $('#aadhar_no').val());
formData.append('mobile', $('#mobile').val());
formData.append('email', $('#email').val());
formData.append('proprietor_name', $('#proprietor_name').val());
formData.append('adhaar_1', $('#adhaar_1').val());
formData.append('pan_no', $('#pan_no').val());
formData.append('gstin', $('#gstin').val());
formData.append('address', $('#address').val());
formData.append('proprietor_name_1', $('#proprietor_name_1').val());
formData.append('email_1', $('#email_1').val());
formData.append('otp', $('#otp').val());
formData.append('mobile_1', $('#mobile_1').val());
formData.append('proprietor_name_2', $('#proprietor_name_2').val());
formData.append('adhaar_2', $('#adhaar_2').val());
formData.append('email_2', $('#email_2').val());
formData.append('mobile_2', $('#mobile_2').val());
formData.append('login_id', $('#login_id').val());
formData.append('password', $('#password').val());
formData.append('password_confirmation', $('#confirm_password').val());
formData.append('_token', '{{csrf_token()}}');


        let validationErrors = [];

        if (!aadhaar || !mobile || !email || !gst || !name  || !address) {
            Swal.fire('Error', 'Please fill out all mandatory fields.', 'error');
            return;
        }
        if (!/^[2-9][0-9]{11}$/.test(aadhaar)) {
            validationErrors.push('Aadhaar Number must be exactly 12 digits, starting with a digit between 2-9.');
        }

        // Mobile validation (10 digits only, starting with 6-9)
        if (!/^[6-9]\d{9}$/.test(mobile)) {
            validationErrors.push('Mobile Number must be 10 digits and start with a digit between 6-9.');
        }

        // Email validation
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            validationErrors.push('Please enter a valid Email address.');
        }


        // GST validation (15 characters: alphanumeric and starting with a number)
        const gstPattern = /^[0-9][A-Z0-9]{14}$/;
        if (!gstPattern.test(gst)) {
            validationErrors.push(
                'GST Number must be 15 characters, starting with a number and containing only alphanumeric characters.');
        }

        // Check if there are any validation errors
        if (validationErrors.length > 0) {
            // Display all validation errors in a SweetAlert popup
            Swal.fire('Validation Errors', validationErrors.join('<br>'), 'error');
            return;
        }

        $.ajax({
        type: "POST",
        url: "{{route('firms_registration.store')}}",
        data: formData,
        contentType: false, // Important for sending FormData
        processData: false, // Prevent jQuery from processing the data
        success: function(response) {
            // if (response.unique_id) {
            //     $('#screen_unique_id').val(response.unique_id);
            //     $('#login_id').val(response.login_id);
            //     console.log("Unique ID and Login ID set successfully.");
            // } else {
            //     Swal.fire('Error', 'Unique ID not returned', 'error');
            // }

            // Proceed with authentication
            $.ajax({
                type: "POST",
                url: "{{route('firms_authentication.store')}}",
                data: {
                    login_id: login_id,
                    password: password,
                    confirm_password: confirm_password,
                    main_unique_id: main_unique_id,
                    _token: '{{csrf_token()}}'
                },
                success: function(authResponse) {
                    const redirecturl = "{{route('firms_registration_list')}}";
                    Swal.fire("Success", "Registration and Authentication Successful!", "success").then(() => {
                        window.location.href = redirecturl;
                    });
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        },
        error: function(xhr) {
            console.log(xhr.responseText);
        }
    });

    }



    
    $(document).ready(function () {

        // Fetch the last firm number when the document is ready
        $.ajax({
            url: '{{ url("/get-last-firm") }}',
            method: 'GET',
            success: function (response) {
                // Update the Firm ID label with the new Firm ID
                $('#firm_id').text(response.firm_id);
                $('#hidden_firm_id').val(response.firm_id);
            },
            error: function (xhr, status, error) {
                console.error("Error fetching Firm ID:", error);
                $('#firm_id').text('Error loading Firm ID');
            }
        });
    });

    $(document).ready(function () {
        // Assuming you have a way to know if you're in update mode
        var isUpdateMode = "{{isset($firm) ? 'true' : 'false'}}";

        var firmId = "{{ isset($firm) ? $firm->firm_id : '' }}";

        if (isUpdateMode) {
            // If in update mode, display the existing firm_id
            $('#firm_id').text(firmId);
            $('#hidden_firm_id').val(firmId);
        } else {
            // Fetch the last firm number when the document is ready
            $.ajax({
                url: '{{ url("/get-last-firm-number") }}',
                method: 'GET',
                success: function (response) {
                    // Update the Firm ID label with the new Firm ID
                    $('#firm_id').text(response.firm_id);
                    $('#hidden_firm_id').val(response.firm_id);
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching Firm ID:", error);
                    $('#firm_id').text('Error loading Firm ID');
                }
            });
        }
    });

    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const firmId = this.getAttribute('data-id');
            if (confirm('Are you sure you want to delete this firm?')) {
                fetch(this.action, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.message) {
                            // Remove the deleted firm's row from the table
                            document.querySelector(`#firm-row-${firmId}`).remove();
                            alert('Firm deleted successfully');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        });
    });


    function sublist_proof(event) {
        event.preventDefault(); // Prevent default form submission

        // Get form values
        var proof_type = $('#idproofSelect').val();
        var aadhaar_num = $('#aadhaar_num').val();
        var licence_no = $('#licence_no').val();
        var main_unique_id = $('#main_unique_id').val();

        var s_no = 1;

        var s_nos = s_no++;

        // Determine proof value
        var proof_value = aadhaar_num ? aadhaar_num : licence_no;

        // Create FormData object to handle file uploads
        var formData = new FormData();
        formData.append('proof_type', proof_type);
        formData.append('main_unique_id', main_unique_id);
        formData.append('proof_value', proof_value);
        formData.append('_token', '{{ csrf_token() }}'); // Add CSRF token for security

        var aadhaarProofElement = document.getElementById('aadhaar_license');
        var licenceProofElement = document.getElementById('licence_proof');

        var aadhaar_proof = aadhaarProofElement ? aadhaarProofElement.files[0] : null;
        var licence_proof = licenceProofElement ? licenceProofElement.files[0] : null;

        if (aadhaar_proof) {
            formData.append('aadhaar_proof', aadhaar_proof);
        }

        if (licence_proof) {
            formData.append('licence_proof', licence_proof);
        }

        // AJAX request to submit form data
        $.ajax({
            type: "POST",
            url: '{{ route("firms_sublist.create") }}', // Ensure this route is correct
            data: formData,
            contentType: false, // Important for file upload
            processData: false, // Prevent jQuery from processing the data
            _token: "{{csrf_token()}}",


            success: function (response) {

                loadTableData();

                //     const FirmsRegistrationSub = response.FirmsRegistrationSub;

                //     var file_name = response.attached_proof;

                //     $('#sublist tbody').append(`
                //          <tr id="row-${FirmsRegistrationSub.id}">
                //             <td>${s_nos}</td>
                //             <td>${proof_type}</td> 
                //             <td>${proof_value}</td> 
                //             <td>
                //                 <a onclick="imageViews('${file_name}', '${proof_type}')">
                //                     <img src='public/uploads/download.png' width="40px" height="40px">
                //                 </a>
                //             </td>
                //             <td>
                // <a class="text-primary" title="Edit"  onclick="editDocument(${response.id}, '${response.proof_type}', '${proof_value}')"><i class="fas fa-edit"></i></a>
                // <a href="#" class="text-danger" title="Delete" onclick="delete_record(${response.FirmsRegistrationSub.id})"><i class="fas fa-trash-alt"></i></a>

                //             </td>
                //         </tr>
                //     `);
                //     // Append the new row to the tbody of the #sublist table


                //     // Reset form fields after successful submission
                //     $('#idproofSelect').val('');
                //     $('#aadhaar_num').val('');
                //     $('#licence_no').val('');
                //     $(
                //         '#aadhaar_proof').val('');
                //     $('#licence_proof').val('');
            },
            error: function (xhr) {
                console.log(xhr.responseText); // Log any errors for debugging
                alert('An error occurred. Please try again.'); // Provide user feedback
            }
        });
    }




    function loadTableData() {
        var main_unique_id = $('#main_unique_id').val();

        $.ajax({
            type: "GET",
            url: '{{ route("firm_sublist_data.index") }}',
            data: {
                main_unique_id: main_unique_id, // Pass the parameter directly
                _token: '{{ csrf_token() }}' // Include CSRF token for security
            },
            // Ensure this route fetches the latest sublist data
            success: function (data) {
                // Clear the current table body
                $('#sublist tbody').empty();

                // Populate the table with new data
                data.forEach(function (item, index) {



                    var Directory = '{{ asset("public/uploads/firms_registration/licence/") }}';

                    var newRow = `
                <tr id="row-${item.id}">
                    <td>${index + 1}</td>
                    <td>${item.proof_type}</td>
                    <td>${item.aadhar_licence}</td>
       
                    <td>
                        <a onclick="imageViews('${item.attached_proof}')">
                            <img src="/harbour/public/uploads/dwn.png" width="40px" height="40px" alt="View Image">
                        </a>
                    </td>
                    <td>
                        <a class="text-primary" title="Edit" onclick="editDocument('${item.unique_id}', '${item.proof_type}', '${item.aadhar_licence}','${item.attached_proof}')">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="#" class="text-danger" title="Delete" onclick="delete_record('${item.id}')">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </td>
                </tr>`;

                    $('#sublist tbody').append(newRow);
                });
                // Reset form fields after successful submission
                $('#licence_no').val('');
                $('#licence_proof').val('');
            },
            error: function (xhr) {
                console.log(xhr.responseText); // Log any errors for debugging
            }
        });
    }


    function editDocument(id, proof_type, aadhar_licence, licence_expiry) {
        // Set the certificate type dropdown
        $('#idproofSelect').val(proof_type);

        // Set other fields based on the type of certificate
        if (proof_type == 'aadhaar') {
            $('#aadhaar_num').val(aadhar_licence);

            $('#aadhar_id').show();
            $('#licence_id').hide();
        } else if (proof_type == 'company') {
            // alert('ji');
            $('#licence_id').show();
            $('#aadhar_id').hide();
            $('#licence_no').val(aadhar_licence);

            $('#licence_expire').val(licence_expiry);


        }
    }



    function imageViews(file_name, proof_type) {

        // Split the file names by commas (in case there are multiple)
        var fileNames = file_name.split(',');

        // Base directory where your files are stored
        var baseDirectory = '/harbour/public/uploads/firms_registration/licence/';

        fileNames.forEach(function (fileName) {

            // alert('loop');
            // Get the file extension
            var cFileName = fileName.split('.');
            var fileExtension = cFileName[cFileName.length - 1].toLowerCase();
            var filePath = `${baseDirectory}${fileName}`; // Construct the full path




            if (fileExtension === 'jpg' || fileExtension === 'png' || fileExtension === 'jpeg') {
                // Open image in a new tab
                window.open(filePath, '_blank');
            } else if (fileExtension === 'pdf') {
                // Open PDF in a new tab
                window.open(filePath, '_blank');
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


    function delete_record(id) {
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
                    url: '{{ route("firm_registration.delete", ":id") }}'.replace(':id', id),
                    method: 'POST',
                    data: {
                        is_delete: 1,
                        _token: '{{ csrf_token() }}' // Include CSRF token
                    },
                    success: function (response) {

                        $(`#row-${id}`).remove();

                        if (response.success) {
                            Swal.fire(
                                'Deleted!',
                                'The record has been deleted.',
                                'success'
                            );


                        } else {
                            Swal.fire(
                                'Error!',
                                response.message || 'Failed to delete the record.',
                                'error'
                            );
                        }
                    },
                    error: function () {
                        Swal.fire(
                            'Error!',
                            'An error occurred while deleting the record.',
                            'error'
                        );
                    }
                });
            }
        });
    }



    // $(document).ready(function() {
    //     user_id();
    // });

    // function user_id() {
    //     var aadhaar_no = $('#aadhar_no').val();

    //     if (aadhaar_no) {
    //         alert('success');
    //         $('#login_id').val(aadhaar_no);
    //     }
    // }



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

    function authentication() {
        // Get values from input fields
        var login_id = $('#login_id').val();
        var password = $('#password').val();
        var confirm_password = $('#confirm_password').val();
        var main_unique_id = $('#main_unique_id').val();

        // Password validation checks
        const hasUpperCase = /[A-Z]/.test(password);
        const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);
        const minLength = password.length >= 8;

        if (!login_id && !password && !confirm_password && !main_unique_id) {
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
            login_id: login_id,
            password: password,
            confirm_password: confirm_password,
            main_unique_id: main_unique_id,
            _token: '{{csrf_token()}}'
        };

        $.ajax({
            type: "POST",
            url: "{{route('firms_authentication.store')}}",
            data: data,
            success: function (response) {
                const redirecturl = "{{route('firms_registration_list')}}";
                sweetalert("create", redirecturl);
            },
            error: function (xhr) {
                console.log(xhr.responseText);
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
        c
    }

    function valid_aadhar_numbser(input) {
        const allowedChars = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        // Filter out characters that are not in the allowedChars array
        input.value = input.value.split('').filter(char => allowedChars.includes(char)).join('');

        // Check if the length is exactly 12
        if (input.value.length >= 12) {
            input.value = input.value.slice(0, 12); // Trim to 12 characters if necessary
        }
    }

    function valid_mobile_number(input) {
        const allowedChars = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        // Filter out characters that are not in the allowedChars array
        input.value = input.value.split('').filter(char => allowedChars.includes(char)).join('');

        // Check if the length is exactly 12
        if (input.value.length >= 10) {
            input.value = input.value.slice(0, 10);
        }
    }


    function validateCompanyRegNum() {
        const regNumInput = document.getElementById('company_reg_no');
        let regNum = regNumInput.value;

        // Allowed characters: numbers and alphabets
        const allowedChars = /^[a-zA-Z0-9\s]*$/; // Allows letters, numbers, and spaces

        // Filter out invalid characters
        regNum = regNum.split('').filter(char => allowedChars.test(char)).join('');
        regNumInput.value = regNum; // Update the input value


        // Validate registration number format

    }



    function validateCompanyName() {
        const nameInput = document.getElementById('name');
        let name = nameInput.value;

        // Allowed characters: only alphabets
        const allowedChars = /^[a-zA-Z\s]*$/; // Allows letters and spaces

        // Filter out invalid characters
        name = name.split('').filter(char => allowedChars.test(char)).join('');
        nameInput.value = name; // Update the input value

        const nameError = document.getElementById('name_error');

        // Validate name format

    }

    function validateName() {
        const nameInput = document.getElementById('contact_person');
        let name = nameInput.value;

        // Allowed characters: only alphabets
        const allowedChars = /^[a-zA-Z\s]*$/; // Allows letters and spaces

        // Filter out invalid characters
        name = name.split('').filter(char => allowedChars.test(char)).join('');
        nameInput.value = name; // Update the input value

        const nameError = document.getElementById('name_error');

        // Validate name format

    }

    // if (!allowedChars.test(name) || name.length === 0) {
    //     nameError.textContent = "Name can contain letters and spaces only."; // Show error message
    // } else {
    //     nameError.textContent = ""; // Clear error message
    // }



    function mail_valid(input) {

        const allowedChars = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't',
            'u',
            'v', 'w', 'x', 'y', 'z',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T',
            'U',
            'V', 'W', 'X', 'Y', 'Z',
            ' ', '_', '.', '@'
        ];

        const mailError = document.getElementById('mailError');

        // Filter out characters that are not in the allowedChars array
        input.value = input.value.split('').filter(char => allowedChars.includes(char)).join('');

        // Define a basic email pattern (this is a simple version, you can use a more strict one if needed)
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        // Test the input value against the regular expression for email validation
        const isValidEmail = emailPattern.test(input.value);

        if (!isValidEmail) {
            let errorMessage = 'Enter a valid email address.';
            mailError.textContent = errorMessage;
        } else {
            mailError.textContent = ''; // Clear the error message if the email is valid
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
      
        document.getElementById('gstin').addEventListener('blur', validateGstin);
        document.getElementById('aadhar_no').addEventListener('blur', validateAadharNo);


       

        function validateGstin() {
            const gstin = document.getElementById('gstin').value;
            const gstinPattern = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/; // GSTIN format
            const gstinError = document.getElementById('gstin_error');

            if (!gstinPattern.test(gstin)) {
                gstinError.textContent = "Give correct format"; // Show error message
                return false;
            } else {
                gstinError.textContent = ""; // Clear error message
            }
            return true;
        }

        function validateAadharNo() {
            const aadharNo = document.getElementById('aadhar_no').value;
            const aadharPattern = /^[0-9]{12}$/; // Exactly 12 digits
            const aadharError = document.getElementById('aadhar_no_error');

            if (!aadharPattern.test(aadharNo)) {
                aadharError.textContent = "Aadhar number must be exactly 12 digits";
                return false;
            } else {
                aadharError.textContent = "";
            }
            return true;
        }


    });






    function validateTelephoneNo() {
        const telephoneInput = document.getElementById('fax_no');
        let telephoneNo = telephoneInput.value;

        const allowedChars = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        // Filter out characters that are not in the allowedChars array
        telephoneNo = telephoneNo.split('').filter(char => allowedChars.includes(char)).join('');

        // Update the input value
        telephoneInput.value = telephoneNo;

        // Define a regex pattern for validation (10 digits)
        const telephonePattern = /^[0-9]{10}$/; // Only 10 digits are allowed
        const telephoneError = document.getElementById('fax_no_error');

        // Validate telephone number format and length
        if (!telephonePattern.test(telephoneNo)) {
            telephoneError.textContent = "Please enter a valid telephone number (10 digits required)"; // Show error message
        } else {
            telephoneError.textContent = ""; // Clear error message
        }
    }


    function validateAddress(addressId) {
        const addressInput = document.getElementById(addressId);
        let address = addressInput.value;

        // Allowed characters: numbers, alphabets, ., /, ,, and -
        const allowedChars = /^[0-9a-zA-Z.,/ -]*$/;

        // Filter out invalid characters
        address = address.split('').filter(char => allowedChars.test(char)).join('');
        addressInput.value = address; // Update the input value


    }

    function valid_name(input) {

        const allowedChars = /^[a-zA-Z.,/ ]*$/;


    }






    // function valid_aadhar_number(input) {
    //     const allowedChars = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    //     // Filter out characters that are not in the allowedChars array
    //     input.value = input.value.split('').filter(char => allowedChars.includes(char)).join('');

    //     // Get the error span
    //     const errorSpan = document.getElementById('aadhar_no_error');

    //     // Trim whitespace and check the length of the Aadhaar number
    //     const aadhaarValue = input.value.trim();

    //     if (aadhaarValue.length > 12) {
    //         input.value = aadhaarValue.slice(0, 12); // Trim to 12 characters if necessary
    //         errorSpan.textContent = 'Aadhaar number must be exactly 12 digits.';
    //     } else if (aadhaarValue.length < 12) {
    //         errorSpan.textContent = 'Aadhaar number must be exactly 12 digits.';
    //     } else {
    //         errorSpan.textContent = ''; // Clear error message if valid (exactly 12 digits)
    //     }
    // }


    function validateAadhar(input) {
        // Define the regex pattern

        const allowedChars = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        input.value = input.value.split('').filter(char => allowedChars.includes(char)).join('');


        const regex = /^[2-9][0-9]{11}$/;
        const aadharNumber = input.value;
        const errorMessage = document.getElementById('aadhar_no_error');

        if (regex.test(aadharNumber)) {
            // Clear any previous error message
            errorMessage.style.display = 'none';
            errorMessage.textContent = '';
        } else {
            // Display error message in the span
            errorMessage.style.display = 'block';
            errorMessage.textContent = 'Invalid Aadhar number. Please enter in the format XXXX XXXX XXXX';
        }

        if (aadharNumber.length > 12) {
            input.value = aadharNumber.slice(0, 12); // Truncate extra digits
        }
    }

    function fetchAadharToLoginID() {
        const aadharNo = document.getElementById('aadhar_no').value;
        const loginIdField = document.getElementById('login_id');
        loginIdField.value = aadharNo;
    }

    function restrictToNumbers(event) {
        // Remove any non-numeric characters
        const inputField = event.target;
        inputField.value = inputField.value.replace(/[^0-9]/g, '');
    }
</script>


@endsection