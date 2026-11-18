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

    .new-card h5 {
        color: #5859ae;
    }
</style>

@php

$get_designations = get_person_type();

$get_firms_registration = get_firm_company();

$firm = $get_firms_registration->first();

$get_academic_year = academic_year();

$screen_unique_id=generateScreenUniqueId();


@endphp
<!-- Basic Layout -->

<!-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body new-card">
                <div id="basicwizard">

                    <input type="hidden" name="screen_unique_id" id="screen_unique_id"
                        value="<?php echo $screen_unique_id; ?>">

                    <h5>Personal Details</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-5 ">
                                    <label for="colFormLabelSm" class="col-form-label"> Designation</label>
                                </div>
                                <div class="col-sm-7">

                                    <select class="form-select mb-2" id="designation_select" name="designation_select">
                                        <option selected>Select Designation</option>
                                        @foreach ($get_designations as $designation)
                                        <option value="{{$designation->unique_id}}">{{$designation->person_type}}
                                        </option>
                                        @endforeach
                                    </select>

                                </div>

                                <div class="row mb-3" style="display:none;">
                                    <label for="register_number" class="col-sm-2 col-form-label">Register Number</label>
                                    <div class="col-sm-4">
                                        <label class="col-form-label"><b
                                                id="register_number">{{ isset($register_number) ? $register_number : 'Loading...' }}</b></label>
                                        <input type="hidden" id="hidden_register_number" name="register_number"
                                            value="">
                                    </div>

                                    <div class="col-sm-4" style="display:none;">
                                        <select class="form-select" name="academic_year" id="academic_year">
                                            @foreach ($get_academic_year as $acc_year)
                                            <option value="{{$acc_year->unique_id}}">{{$acc_year->acc_year}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-5 align-self-center">
                                    <label for="colFormLabelSm" class="col-form-label">Full Name</label>
                                </div>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control mb-2" id="full_name" name="full_name" />
                                </div>
                                <div class="col-sm-5 align-self-center">
                                    <label for="colFormLabelSm" class="col-form-label">Blood Group</label>
                                </div>
                                <div class="col-sm-7">
                                    <select class="form-select mb-2" id="blood_group" name="blood_group">
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

                                <div class="col-sm-5 align-self-center">
                                    <label for="colFormLabelSm" class="col-form-label">Nationality</label>
                                </div>
                                <div class="col-sm-7">
                                    <select class="form-select mb-2" id="nationality" name="nationality">
                                        <option selected>Select Nationality</option>
                                        <option value="Indian">Indian</option>
                                        <option value="Foreign">Foreign</option>
                                        <option value="Indian-origin">Indian Origin</option>
                                    </select>
                                </div>



                                <div class="col-sm-5 align-self-center">
                                    <label for="colFormLabelSm" class="col-form-label">Address</label>
                                </div>
                                <div class="col-sm-7">
                                    <textarea class="form-control mb-3" oninput="validateAddress('address_1')"
                                        id="address" name="address" rows="2"></textarea>
                                    <span id="address_error" style="color: red;"></span> <!-- Error message span -->
                                </div>


                                <div class="row mb-3" id="upload" style="display:none;">
                                    <div class="col-sm-5 align-self-center">
                                        <label for="colFormLabelSm" class="col-form-label">Upload Photo</label>
                                    </div>

                                    <div class="col-sm-7">
                                        <input type="file" class="form-control mb-2" id="photo" name="photo" />
                                    </div>
                                    <div class="col-sm-2 mt-3">
                                        <img id="imagePreview" src="#" alt="Image Preview"
                                            style="display:none; max-width: 200px;" />
                                    </div>
                                </div>

                                <div class="row mb-3" id="snap" style="display:none;">
                                    <div class="col-sm-5 align-self-center">
                                        <label for="colFormLabelSm" class="col-form-label">Preview Photo</label>
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
                                <div id="upload" style="display:none;">
                                    <div class="col-sm-5 align-self-center">
                                        <label for="photo" class="col-sm-2 col-form-label">Upload Photo</label>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="file" class="form-control" id="photo" name="photo" />

                                    </div>
                                    <div class="col-sm-2 mt-3">
                                        <img id="imagePreview" src="#" alt="Image Preview"
                                            style="display:none; max-width:20%; height:10%;" />
                                    </div>
                                </div>

                                <div id="snap" style="display:none;">
                                    <div class="col-sm-5 align-self-center">
                                        <label for="photoPreview" class="col-form-label">Preview Photo</label>
                                    </div>
                                    <div class="col-sm-7">
                                        <img src="resources/views/content/person_registration/13.png" alt="..."
                                            class="img-thumbnail"
                                            style="border: 5    px solid grey; max-width: 100%; height: auto;">
                                        <div class="mt-3">
                                            <button type="button" onclick="web_cam()"
                                                class="btn btn-primary">Snap</button>
                                        </div>
                                    </div>

                                </div>




                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-5 align-self-center ">
                                    <label for="colFormLabelSm" class="col-form-label">Aadhaar No</label>
                                </div>
                                <div class="col-sm-7">
                                    <input type="tel" class="form-control mb-2" id="aadhaar_no"
                                        oninput="valid_aadhar_number(this)" name="aadhaar_no" />
                                    <!-- <span id="aadhaarError" style="color:red;"></span> -->
                                    <span id="aadhaar_error" class="error-message"
                                        style="color: red; display: none;"></span>
                                </div>
                                <div class="col-sm-5 align-self-center">
                                    <label for="colFormLabelSm" class="col-form-label">Mobile No</label>
                                </div>
                                <div class="col-sm-7">
                                    <input type="tel" class="form-control mb-2" id="mobile" name="mobile"
                                        oninput="valid_mobile_number(this)" minlength="10" maxlength="13" />
                                    <!-- <span id="mobileError" style="color:red;"></span> -->
                                    <span id="mobile_error" class="error-message"
                                        style="color: red; display: none;"></span>
                                </div>
                                <div class="col-sm-5 align-self-center">
                                    <label for="colFormLabelSm" class="col-form-label">Email ID</label>
                                </div>
                                <div class="col-sm-7">
                                    <input type="email" class="form-control mb-2" id="email_id"
                                        oninput="mail_valid(this)" name="email_id" />
                                    <span id="mailError" style="color:red;"></span>
                                </div>

                                <div class="col-sm-5 align-self-center">
                                    <label for="colFormLabelSm" class="col-form-label">Country</label>
                                </div>
                                <div class="col-sm-7">
                                    <select class="form-select mb-2" id="country" name="country">
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

                                <div class="col-sm-5 align-self-center">
                                    <label for="colFormLabelSm" class="col-form-label">Photo</label>
                                </div>
                                <div class="col-sm-7">
                                    <select class="form-select" name="photo_pic" id="photo_pic"
                                        onchange="image_upload()">
                                        <option value="">Select</option>
                                        <option value="upload_pic">Upload</option>
                                        <option value="snap_pic">Snap</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>


                    <hr>


                </div>




                <!-- <div class="" id=""> -->
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="mt-4">Document Details</h5>

                        <form id="documentVerificationForm"> @csrf
                            @if(isset($acc_year))
                            @method('PUT')
                            @endif



                            <div class="row mb-3 mt-3">
                                <input type="hidden" id="main_unique_id" name="main_unique_id">
                                <input type="hidden" id="port_type" name="port_type" value="1">

                                <div class="col-sm-5 align-self-center">
                                    <label for="idproofSelect" class="col-form-label">Type
                                        of ID
                                        proof</label>
                                </div>
                                <div class="col-sm-7">
                                    <select class="form-select" id="idproof" name="idproof" onchange="proof_change()">
                                        <option value="">Select ID Proof</option>
                                        <option value="Aadhaar">Aadhaar</option>
                                        <option value="Driving Licence">Driving Licence</option>
                                    </select>
                                </div>
                                <div id="aadhar_id" style="display:none;" class="row mb-3 mt-3">

                                    <div class="col-sm-5 align-self-center">
                                        <label for="colFormLabelSm" class="col-form-label">
                                            Aadhar
                                            No.
                                        </label>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control mb-3" style="margin-left:10px;"
                                            id="aadhaar_num" name="aadhaar_num" />
                                    </div>

                                    <div class="col-sm-5 align-self-center">
                                        <label for="colFormLabelSm" class="col-form-label">ID
                                            Proof</label>
                                    </div>
                                    <div class="col-sm-7">

                                        <input type="file" class="form-control mb-3" id="aadhaar_proof"
                                            style="margin-left:10px;" name="aadhaar_proof" />
                                    </div>

                                </div>



                                <div id="licence_id" style="display:none;" class="row mb-3 mt-3">
                                    <div class="col-sm-5 align-self-center">
                                        <label for="colFormLabelSm" class="col-form-label">Licence No.</label>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control mb-3" id="licence_no"
                                            style="margin-left:10px;" name="licence_no" />
                                    </div>
                                    <div class="col-sm-5 align-self-center">
                                        <label for="colFormLabelSm" class="col-form-label">ID Proof</label>
                                    </div>

                                    <div class="col-sm-7">
                                        <input type="file" class="form-control mb-3" style="margin-left:10px;"
                                            id="licence_proof" name="licence_proof"
                                            accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" />
                                    </div>

                                    <div class="col-sm-5 align-self-center">
                                        <label for="licence_expire" class="col-form-label">Licence
                                            Expire Date</label>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="date" class="form-control mb-3" style="margin-left:10px;"
                                            id="expiry_date" name="expiry_date" />
                                    </div>
                                </div>
                            </div>




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

                    </form>
                </div>
            </div>


            <div class="btn-float mt-3">
                <button type="button" class="btn btn-danger"
                    onclick="window.location='{{ route('person_registration') }}'">Cancel
                </button>
                <button type="button" onclick="submit_function()" class="btn btn-primary">Submit</button>
            </div>
        </div>

    </div>
    <!-- End Tab 3 -->

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
    // const passwordField = document.getElementById('password');
    // const togglePassword = document.getElementById('togglePassword');

    // togglePassword.addEventListener('click', function() {
    //     // Toggle the type attribute for Password field
    //     const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
    //     passwordField.setAttribute('type', type);

    //     // Toggle the eye icon for Password field
    //     this.classList.toggle('fa-eye');
    //     this.classList.toggle('fa-eye-slash');
    // });

    // // JavaScript to toggle password visibility for Confirm Password
    // const confirmPasswordField = document.getElementById('confirm_password');
    // const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');

    // toggleConfirmPassword.addEventListener('click', function() {
    //     // Toggle the type attribute for Confirm Password field
    //     const type = confirmPasswordField.getAttribute('type') === 'password' ? 'text' : 'password';
    //     confirmPasswordField.setAttribute('type', type);

    //     // Toggle the eye icon for Confirm Password field
    //     this.classList.toggle('fa-eye');
    //     this.classList.toggle('fa-eye-slash');
    // });



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


    $(document).ready(function() {

        function proof_change() {

            var designation_type = $('#designation_select').val();
            var proofDropdown = $('#idproof');

            // Debugging: Check if #idproof exists
            console.log("Proof dropdown element:", proofDropdown);

            // Check if #idproof exists before manipulating it
            if (!proofDropdown.length) {
                console.error("#idproof element not found.");
                return;
            }

            // Clear existing options
            proofDropdown.empty();

            // Add default "Select ID Proof" option
            proofDropdown.append('<option value="">Select ID Proof</option>');

            // Add options based on designation_type
            if (designation_type === '66fe76256d3a792499') {
                // Show both Aadhaar and Driving Licence options
                proofDropdown.append('<option value="Aadhaar">Aadhaar</option>');
                proofDropdown.append('<option value="Driving Licence">Driving Licence</option>');
            } else {
                // Show Aadhaar only
                proofDropdown.append('<option value="Aadhaar">Aadhaar</option>');
            }
        }

        // Call proof_change initially to set the correct options
        proof_change();


        // Attach the function to change event on designation select dropdown
        $('#designation_select').on('change', proof_change);

        var proof = $('#idproof').val();
        if ((proof === 'Aadhaar')) {
            $('#aadhar_id').show();
            $('#licence_id').hide();


        } else if (proof === 'Driving Licence') {
            $('#licence_id').show();
            $('#aadhar_id').hide();
        }
    });


    $(document).ready(function() {
        $('#designation_select').on('change', proof_change);
        $('#idproof').on('change', proof_change);
    });


    function proof_change() {

        var proof = $('#idproof').val();
        if ((proof === 'Aadhaar')) {
            $('#aadhar_id').show();
            $('#licence_id').hide();


        } else if (proof === 'Driving Licence') {
            $('#licence_id').show();
            $('#aadhar_id').hide();
        }
    }




    // function designation_proof() {


    //     var designation_type = $('#designation_select').val();

    //     if (designation_type == '66fe76256d3a792499') {

    //         $('#aadhar_id').show();
    //         $('#licence_id').show();

    //     } else {

    //         $('#aadhar_id').show();
    //         $('#licence_id').hide();


    //     }
    // }


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



    //store function
    function submit_function() {



        // Create a new FormData object
        var formData = new FormData();



        // var rf_id = $('#rf_id').val();

        var main_unique_id = $('#screen_unique_id').val();

        var academic_year = $('#academic_year').val();
        var name = $('#full_name').val();
        var register_number = $('#hidden_register_number').val();

        var address = $('#address').val();
        // var firm_company = $('#firm_company').val();
        var designation = $('#designation_select').val();
        var aadhaar = $('#aadhaar_no').val();
        var licence_validity_date = $('#expiry_date').val();
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

        formData.append('designation', $('#designation_select').val());
        formData.append('aadhaar_no', $('#aadhaar_no').val());
        formData.append('address', $('#address').val());

        formData.append('licence_validity_date', $('#expiry_date').val());
        formData.append('nationality', $('#nationality').val());
        formData.append('country', $('#country').val());
        formData.append('mobile', $('#mobile').val());
        formData.append('blood_group', $('#blood_group').val());
        formData.append('email_id', $('#email_id').val());
        formData.append('port_type', $('#port_type').val());
        formData.append('photo_pic', $('#photo_pic').val());

        formData.append('main_unique_id', $('#screen_unique_id').val());


        formData.append('_token', '{{csrf_token()}}');






        // const hasUpperCase = /[A-Z]/.test(password);
        // const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);
        // const minLength = password.length >= 8;

        // if (!user_id && !password && !confirm_password && !main_unique_id) {
        //     sweetalert("form_alert");
        //     return;
        // }




        if (!academic_year || !name || !register_number || !designation ||
            !aadhaar || !nationality || !country || !mobile || !blood_group || !
            email_id || !port_type) {
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


                // if (response.unique_id) {
                //     var unique_id = response.unique_id;

                //     var aadhaar_sub = response.aadhaar_no;


                //     var licence_val_sub = response.licence_validity_date;



                //     $('#main_unique_id').val(unique_id);

                //     $('#aadhaar_num').val(aadhaar_sub);
                //     $('#login_id').val(aadhaar_sub);


                //     $('#licence_val_sub').val(licence_val_sub);


                // } else {
                //     alert('Unique ID not returned');
                // }

                // document.querySelector('#tab2-link').click();
                // location.reload(); //
                // alert('sucess');

                const redirecturl = "{{route('person_registration')}}";
                sweetalert("create", redirecturl);
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
        // sublist_proof();
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

    function sublist_proof(event) {
        event.preventDefault();

        // Get form values
        var proof_type = $('#idproof').val();
        var aadhaar_num = $('#aadhaar_num').val();
        var licence_no = $('#licence_no').val();
        var main_unique_id = $('#screen_unique_id').val();
        var licence_val_sub = $('#licence_val_sub').val();
        var expiry_date = $('#expiry_date').val();
        // alert(expiry_date);
        // var licence_validity_date = $('#licence_date').val();
        // Set default for non-existing values
        var proof_value = aadhaar_num !== '' ? aadhaar_num : licence_no;
        var s_no = 1;

        var s_nos = s_no++;
        // Create FormData object to handle file uploads
        var formData = new FormData();
        formData.append('proof_type', proof_type);
        formData.append('main_unique_id', main_unique_id);
        formData.append('proof_value', proof_value);
        formData.append('licence_val_sub', licence_val_sub);
        formData.append('expiry_date', expiry_date);
        formData.append('_token', '{{ csrf_token() }}'); // Add CSRF token for security

        // Handle file inputs (append only if the file is selected)
        var aadhaar_proof = document.getElementById('aadhaar_proof').files[0];
        var licence_proof = document.getElementById('licence_proof').files[0];
        if (aadhaar_proof) formData.append('aadhaar_proof', aadhaar_proof);
        if (licence_proof) formData.append('licence_proof', licence_proof);

        // AJAX request to submit form data
        $.ajax({
            type: "POST",
            url: '{{ route("person_sublist.create") }}', // Update the route
            data: formData,
            contentType: false, // Important for file upload
            processData: false, // Prevent jQuery from processing the data
            success: function(response) {
                // Get the current count of rows to set the S.No.
                loadTableData();

            },
            error: function(xhr) {
                console.log(xhr.responseText); // Log any errors for debugging
            }
        });
    }

    // Function to load table data
    function loadTableData() {
        var main_unique_id = $('#screen_unique_id').val();

        $.ajax({
            type: "GET",
            url: '{{ route("personSublist.index") }}',
            data: {
                main_unique_id: main_unique_id, // Pass the parameter directly
                _token: '{{ csrf_token() }}' // Include CSRF token for security
            },

            success: function(data) {

                $('#sublist tbody').empty();

                data.forEach(function(item, index) {
                    var expiryDisplay = (item.proof_type === 'Licence' && item.expiry_date) ? item
                        .expiry_date : '-';

                    var newRow = `
                <tr id="row-${item.unique_id}">
                    <td>${index + 1}</td>
                    <td>${item.proof_type}</td>
                    <td>${item.aadhar_licence}</td>
                    <td>${item.expiry_date ? item.expiry_date : 'N/A'}</td>
                    <td>
                    
                        <a onclick="imageViews('${item.attached_proof}', '${item.proof_type}')">
                            <img src="{{ asset('/public/uploads/dwn.png') }}" width="40px"
                                                            height="40px">
                        </a>
                    </td>
                    <td>
                        <a class="text-primary" title="Edit" onclick="editDocument('${item.unique_id}', '${item.proof_type}', '${item.aadhar_licence}', '${item.licence_val_sub}')">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="#" class="text-danger" title="Delete" onclick="deleteProof('${item.unique_id}')">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </td>
                </tr>`;
                    $('#sublist tbody').append(newRow);
                });

                // Reset form fields after successful submission
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


    // function sublist_proof(event) {
    //     event.preventDefault();

    //     // Get form values
    //     var proof_type = $('#idproof').val();
    //     var aadhaar_num = $('#aadhaar_num').val();
    //     var licence_no = $('#licence_no').val();
    //     var main_unique_id = $('#main_unique_id').val();
    //     var licence_val_sub = $('#licence_val_sub').val();
    //     // alert(licence_val_sub);
    //     // var licence_validity_date = $('#licence_date').val();
    //     // Set default for non-existing values
    //     var proof_value = aadhaar_num !== '' ? aadhaar_num : licence_no;
    //     var s_no = 1;

    //     var s_nos = s_no++;
    //     // Create FormData object to handle file uploads
    //     var formData = new FormData();
    //     formData.append('proof_type', proof_type);
    //     formData.append('main_unique_id', main_unique_id);
    //     formData.append('proof_value', proof_value);
    //     formData.append('licence_val_sub', licence_val_sub);
    //     formData.append('_token', '{{ csrf_token() }}'); // Add CSRF token for security

    //     // Handle file inputs (append only if the file is selected)
    //     var aadhaar_proof = document.getElementById('aadhaar_proof').files[0];
    //     var licence_proof = document.getElementById('licence_proof').files[0];
    //     if (aadhaar_proof) formData.append('aadhaar_proof', aadhaar_proof);
    //     if (licence_proof) formData.append('licence_proof', licence_proof);

    //     // AJAX request to submit form data
    //     $.ajax({
    //         type: "POST",
    //         url: '{{ route("person_sublist.create") }}', // Update the route
    //         data: formData,
    //         contentType: false, // Important for file upload
    //         processData: false, // Prevent jQuery from processing the data
    //         success: function(response) {
    //             // Get the current count of rows to set the S.No.
    //             const person_sublist = response.person_sublist;

    //             var file_name = response.attached_proof;
    //             // var licence_validity_date = response.licence_validity_date;

    //             $('#sublist tbody').append(`
    //     <tr id="row-${person_sublist.id}">
    //     <td>${s_no}</td>
    //                     <td>${response.proof_type}</td>
    //                     <td>${response.aadhar_licence}</td>
    //                     <td>${licence_val_sub}</td>
    //                     <td>
    //                         <a onclick="imageViews('${response.attached_proof}', '${response.proof_type}')">
    //                             <img src="public/uploads/dwn.png" width="40px" height="40px" alt="View Image">
    //                         </a>
    //                     </td>
    //                     <td>
    //                         <a class="text-primary" title="Edit" onclick="editDocument('${response.unique_id}', '${response.proof_type}', '${response.aadhar_licence}', '${licence_val_sub}')">
    //                             <i class="fas fa-edit"></i>
    //                         </a>
    //                         <a href="#" class="text-danger" title="Delete" onclick="deleteProof('${response.unique_id}')">
    //                             <i class="fas fa-trash-alt"></i>
    //                         </a>

    //     </td>
    //     </tr>
    // `);
    //             // Append the new row to the tbody of the #sublist table
    //             ;

    //             // Reset form fields after successful submission
    //             $('#idproof').val('');
    //             $('#aadhaar_num').val('');
    //             $('#licence_no').val('');
    //             $('#aadhaar_proof').val('');
    //             $('#licence_proof').val('');
    //         },
    //         error: function(xhr) {
    //             console.log(xhr.responseText); // Log any errors for debugging
    //         }
    //     });
    // }


    //-------------------------image and pdf view download function------///////


    function imageViews(file_name, proof_type) {
        // alert();
        // Split the file names by commas (in case there are multiple)
        var fileNames = file_name.split(',');

        // Base directory wher your files are stored

        var aadhaarDirectory = '{{ asset("public/uploads/person_registration/aadhaar/") }}';
        var licenceDirectory = '{{ asset("public/uploads/person_registration/licence/") }}';

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
            var filePath = `${baseDirectory}/${fileName}`;
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
        var aadhaarDirectory = '{{ asset("public/uploads/person_registration/aadhaar/") }}';
        var licenceDirectory = '{{ asset("public/uploads/person_registration/licence/") }}';
        // var aadhaarDirectory = 'public/uploads/person_registration/aadhaar/';
        // var licenceDirectory = 'public/uploads/person_registration/licence/';


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

            var filePath = `${baseDirectory}/${fileName}`;

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



    // function deleteProof(unique_id) {
    //     // alert(unique_id);
    //     // Confirm deletion with a basic alert
    //     if (confirm("Are you sure you want to delete this item?")) {
    //         $.ajax({
    //             type: 'DELETE',
    //             url: `{{ route('person_sublist_delete.delete', '') }}/${unique_id}`, // Correct URL construction

    //             data: {
    //                 _token: '{{ csrf_token() }}' // CSRF token for security
    //             },
    //             success: function(response) {
    //                 // Remove the row from the table without reloading the page
    //                 $(`#sublist tbody tr:has(a[onclick*="${unique_id}"])`).remove();
    //                 alert('The pass has been deleted.'); // Basic confirmation alert
    //             },
    //             error: function(xhr) {
    //                 var errorMessage = xhr.responseJSON && xhr.responseJSON.message ?
    //                     xhr.responseJSON.message :
    //                     'An error occurred. Please try again.';
    //                 // Show error message
    //                 alert(errorMessage);
    //             }
    //         });
    //     }
    // }
    function deleteProof(unique_id) {
        // Show confirmation prompt using SweetAlert
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
                // Perform AJAX delete request if confirmed
                $.ajax({
                    type: 'DELETE',
                    url: `{{ route('person_sublist_delete.delete', '') }}/${unique_id}`, // Correct URL construction
                    data: {
                        _token: '{{ csrf_token() }}' // CSRF token for security
                    },
                    success: function(response) {
                        // Remove the row from the table without reloading the page
                        $(`#sublist tbody tr:has(a[onclick*="${unique_id}"])`).remove();

                        // Show success message with automatic close
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'The proof has been deleted.',
                            icon: 'success',
                            timer: 2000, // Automatically close after 2 seconds
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        var errorMessage = xhr.responseJSON && xhr.responseJSON.message ?
                            xhr.responseJSON.message :
                            'An error occurred. Please try again.';

                        // Show error message with automatic close
                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error',
                            timer: 3000, // Automatically close after 3 seconds
                            showConfirmButton: false
                        });
                    }
                });
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


    // function valid_aadhar_number(input) {
    //     const allowedChars = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    //     // Filter out characters that are not in the allowedChars array
    //     input.value = input.value.split('').filter(char => allowedChars.includes(char)).join('');

    //     // Get the error span
    //     const errorSpan = document.getElementById('aadhaarError');

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

    function valid_aadhar_number(input) {
        // Define the regex pattern
        const regex = /^[2-9][0-9]{3}[0-9]{4}[0-9]{4}$/;
        const aadharNumber = input.value;
        const errorMessage = document.getElementById('aadhaar_error');

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
            input.value = aadharNumber.slice(0, 12); // Trim to 12 characters if necessary
            errorSpan.textContent = 'Aadhaar number must be exactly 12 digits.';
        } else if (aadharNumber.length < 12) {
            errorSpan.textContent = 'Aadhaar number must be exactly 12 digits.';
        } else {
            errorSpan.textContent = ''; // Clear error message if valid (exactly 12 digits)
        }
    }
    document.getElementById('aadhaar_no').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, ''); // Remove any non-numeric characters
    });

    // function valid_mobile_number(input) {
    //     const allowedChars = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '+'];

    //     // Filter out characters that are not in the allowedChars array
    //     input.value = input.value.split('').filter(char => allowedChars.includes(char)).join('');

    //     const errorSpan = document.getElementById('mobileError');

    //     const mobileValue = input.value.trim();

    //     // Check if the length is exactly 12
    //     if (mobileValue.length > 13) {
    //         input.value = input.value.slice(0, 13);
    //         errorSpan.textContent = 'Mobile Number Should be 10 to 13 digits';
    //     } else if (mobileValue.length < 10) {
    //         errorSpan.textContent = 'Mobile Number Should be 10 to 13 digits.';
    //     } else {
    //         errorSpan.textContent = '';
    //     }
    // }

    // new changes 25-10

    // function valid_mobile_number(input) {
    //     var nationality = document.getElementById('nationality').value; // Get nationality from the input element
    //     const allowedChars = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '+'];

    //     // Filter out characters that are not in the allowedChars array
    //     input.value = input.value.split('').filter(char => allowedChars.includes(char)).join('');

    //     const errorSpan = document.getElementById('mobileError');
    //     const mobileValue = input.value.trim();

    //     // Check based on nationality
    //     if (nationality.toLowerCase() === 'indian') {
    //         // For India, check for exactly 10 digits
    //         if (mobileValue.length > 10) {
    //             input.value = mobileValue.slice(0, 10); // Limit input to 10 digits
    //             errorSpan.textContent = 'For Indian nationality, mobile number should be exactly 10 digits.';
    //         } else if (mobileValue.length !== 10) {
    //             errorSpan.textContent = 'For Indian nationality, mobile number should be exactly 10 digits.';
    //         } else {
    //             errorSpan.textContent = '';
    //         }
    //     } else {
    //         // For foreign nationality, check for exactly 13 digits
    //         if (mobileValue.length > 13) {
    //             input.value = mobileValue.slice(0, 13); // Limit input to 13 digits
    //             errorSpan.textContent = 'For foreign nationality, mobile number should be exactly 13 digits.';
    //         } else if (mobileValue.length !== 13) {
    //             errorSpan.textContent = 'For foreign nationality, mobile number should be exactly 13 digits.';
    //         } else {
    //             errorSpan.textContent = '';
    //         }
    //     }
    // }
    function valid_mobile_number(input) {
        var nationality = document.getElementById('nationality').value; // Get nationality from the input element
        // Define the regex pattern for Indian mobile numbers (10 digits starting with 7, 8, or 9)
        const regex = /^[789]\d{9}$/;
        const mobileNumber = input.value;
        const length = mobileNumber.length;
        const errorMessage = document.getElementById('mobile_error');

        // Clear any previous error message
        errorMessage.style.display = 'none';
        errorMessage.textContent = '';
        if (nationality.toLowerCase() === 'indian') {
            if (length !== 10) {
                // Show error for incorrect length
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'Mobile number must contain exactly 10 digits.';
            } else if (!regex.test(mobileNumber)) {
                // Show error for invalid mobile number
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'Invalid mobile number. Please start with 7, 8, or 9.';
            }
        } else {
            if (length !== 13) {
                // Show error for incorrect length
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'Mobile number must contain exactly 13 digits.';
                // } else if (!regex.test(mobileNumber)) {
                //     // Show error for invalid mobile number
                //     errorMessage.style.display = 'block';
                //     errorMessage.textContent = 'Invalid mobile number. Please start with 7, 8, or 9.';
            }
        }
    }



    document.getElementById('mobile').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, ''); // Remove any non-numeric characters
    });

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

        // var url = "public/uploads/person_registration/aadhaar/" + fileName;
        var url = "{{ asset('public/uploads/person_registration/aadhaar/') }}" + fileName;

        window.open(url, '_blank', "width=600,height=480,toolbar=no,menubar=no,resizable=yes");

    }

    // Example usage
    // var appFileName = "file1.jpg,file2.pdf,file3.xlsx,file4.doc"; // Replace this with actual data
    // document.getElementById('yourElementId').innerHTML = imageViews(appFileName);





    function download_pdf(fileName) {

        // alert('ji');
        // var pdfUrl = "public/uploads/person_registration/aadhaar/" + fileName;
        var pdfUrl = "{{ asset('public/uploads/person_registration/aadhaar/') }}" + fileName;
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
            '66fe76256d3a792499') { // Replace with actual value that should show the licence date
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


    // function editDocument(id, proof_type, aadhar_licence, validUpTo) {
    //     // alert();
    //     // Set the certificate type dropdown
    //     $('#idproof').val(proof_type);

    //     // Set other fields based on the type of certificate
    //     if (proof_type == 'Aadhaar') {
    //         $('#aadhaar_num').val(aadhar_licence);

    //         $('#aadhar_id').show();

    //     } else if (proof_type == 'Driving Licence') {
    //         $('#licence_id').show();
    //         $('#licence_no').val(aadhar_licence);

    //         $('#licence_expire').val(licence_expiry);
    //     }
    // }

    // function editDocument(id, proof_type, aadhar_licence, validUpTo) {
    //     // Set the certificate type dropdown
    //     $('#idproof').val(proof_type);
    //     // alert(proof_type);
    //     // alert(id);

    //     // alert(proof_type);
    //     // Set other fields based on the type of certificate
    //     if (proof_type == 'Aadhaar') {
    //         $('#aadhaar_num').val(aadhar_licence);

    //         $('#aadhar_id').show();

    //     } else if (proof_type == 'Driving Licence') {
    //         $('#licence_id').show();
    //         $('#licence_no').val(aadhar_licence);

    //         $('#licence_expire').val(licence_expiry);
    //     }
    // }


    function editDocument(unique_id, proof_type, aadhar_licence, validUpTo, licence_val_sub) {
        // alert(unique_id);
        // alert(proof_type);
        // alert(aadhar_licence);

        // // Set the certificate type dropdown
        // $('#idproof').val(proof_type);

        // // alert(proof_type);
        // // Set other fields based on the type of certificate
        // if (proof_type == 'aadhaar') {
        //     $('#aadhaar_num').val(aadhar_licence);

        //     $('#aadhar_id').show();

        // } else if (proof_type == 'driving_licence') {
        //     $('#licence_id').show();
        //     $('#licence_no').val(aadhar_licence);

        //     $('#licence_expire').val(licence_expiry);
        // }
        console.log("Editing item with ID:", unique_id);
        console.log("Proof Type:", proof_type);
        console.log("Aadhaar License:", aadhar_licence);
        console.log("License Expiry:", licence_val_sub);

        // Set the certificate type dropdown
        $('#idproof').val(proof_type);

        // Show/hide fields and set values based on the proof type
        if (proof_type === 'Aadhaar') {
            $('#aadhaar_num').val(aadhar_licence); // Set Aadhaar number
            $('#aadhar_id').show(); // Show Aadhaar section
            $('#licence_id').hide(); // Hide Driving License section

            // Clear Driving License fields if previously filled
            $('#licence_no').val('');
            $('#licence_expire').val('');
            console.log("Aadhaar section displayed.");
        } else if (proof_type === 'Driving Licence') {
            $('#licence_no').val(aadhar_licence); // Set Driving License number
            $('#licence_expire').val(licence_val_sub); // Set Driving License expiry date
            $('#licence_id').show(); // Show Driving License section
            $('#aadhar_id').hide(); // Hide Aadhaar section

            // Clear Aadhaar fields if previously filled
            $('#aadhaar_num').val('');
            console.log("Driving Licence section displayed.");
        } else {
            console.log("No valid proof type selected.");
        }
    }
</script>

</script>


@endsection