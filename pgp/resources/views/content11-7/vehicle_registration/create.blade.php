@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')

<style>
    .input {
        margin-left: 125px;
    }

    .pad1 {
        margin-left: 110px;
    }

    #vech_reg_no {
        text-transform: uppercase;
    }

    #login_id {
        text-transform: uppercase;
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

    .headname {
        color: #5859ae;
    }

    .new-card h5 {
        color: #5859ae;
    }
</style>
@php

    $acc_year = academic_year();
    $acc_year1 = acc_year();
    $vehicle_category = get_vehicle_category();
    $vehicle_type = get_vehicle_type();
@endphp
<div class="row mb-1 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">User Firms/</span>Vehicle Registration</h4>
    </div>
</div>

<div class="card">
    <div class="card-body new-card">
        <form id="usertypeForm" method="post" onsubmit="return submitForm(event);">
            <div class="row mb-3">
                <h5>Vehicle details</h5>
                <!-- Vehicle Registration Number -->
                <div class="col-sm-2 mb-2">
                    <label for="vech_reg_no" class="col-form-label">Vehicle Number:</label>
                </div>
                <div class="col-sm-4 mb-2">
                    <input type="text" class="form-control" name="vech_reg_no" id="vech_reg_no"
                        onchange="validateVehicleNumber(this);" oninput="appendValue(this);">
                    <small id="vehicle_error" class="text-danger" style="display:none;"></small>
                    <input type="hidden" id="screenUniqueId" name="screenUniqueId" />
                </div>

                <!-- Vehicle Type -->
                <div class="col-sm-2 mb-2">
                    <button type="button" class="btn btn-primary">Go</button>
                </div>
                <div class="col-sm-4 mb-2">

                </div>
                <div class="col-sm-2 mb-2">
                    <label for="status" class="col-form-label">Vehicle Type:</label>
                </div>
                <div class="col-sm-4 mb-2">
                    <select name="status" id="status" class="form-select"
                        onchange="getVehicleCategory(this.value);toggleGetData(this.value);checkVehicleType(this);">
                        <option value="">Select Vehicle Type</option>
                        @foreach ($vehicle_type as $vc_type)
                            <option value="{{ $vc_type->unique_id }}">{{ $vc_type->cargo }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2 mb-2">
                    <label for="vehicle_category" class="col-form-label">Vehicle Category:</label>
                </div>
                <div class="col-sm-4 mb-2">
                    <select name="vehicle_category" id="vehicle_category" class="form-select">
                        <option value="">Select Vehicle Category</option>
                    </select>
                </div>
                <div class="col-sm-2 mb-2">
                    <label for="owner_name" class="col-form-label">Owner's Name:</label>
                </div>
                <div class="col-sm-4 mb-2">
                    <input type="text" class="form-control" id="owner_name" name="owner_name"
                        oninput="validateName()" />
                    <small id="ownerNameError" class="text-danger"></small>
                </div>
                <!-- Contact Number -->
                <div class="col-sm-2 mb-2">
                    <label for="contact_number" class="col-form-label">Contact Number:</label>
                </div>
                <div class="col-sm-4 mb-2">
                    <input type="text" class="form-control" maxlength="10" id="contact_number" name="contact_number"
                        oninput="validateMobile(this)" maxlength="10" />
                    <span id="mobile_error" class="error-message" style="color: red; display: none;"></span>
                </div>
                <!-- Next Button -->
            </div>
            <!-- <hr> -->
            <div class="row">
                <div style="display:none" id="get_data">
                    <div class="col-sm-2 mb-2">
                        <label for="model_name" class="col-form-label">Model Name:</label>
                    </div>
                    <div class="col-sm-4 mb-2">
                        <input type="text" class="form-control" id="model_name" name="model_name"
                            oninput="validateName()" />
                        <small id="ownerNameError" class="text-danger"></small>
                    </div>
                    <div class="col-sm-2 mb-2">
                        <label for="chase_no" class="col-form-label">Chase No:</label>
                    </div>
                    <div class="col-sm-4 mb-2">
                        <input type="text" class="form-control" id="chase_no" name="chase_no"
                            oninput="validateName()" />
                        <small id="ownerNameError" class="text-danger"></small>
                    </div>
                </div>
            </div>

            <hr>
            <div class="row">
                <h5>Document Details</h5>
                <div class="row" style="display:block;" id="old">
                    <div class="col-md-6">
                        <div class="mb-2 d-flex align-items-center">
                            <label class="form-label me-3" style="width: 250px;">Certificate Type:</label>
                            <select class="form-select" onchange="change('old');" name="certificate_type_old"
                                id="certificate_type_old">
                                <option value="selected">Select</option>
                                <option value="Fitness">Fitness</option>
                                <option value="Insurance">Insurance</option>
                                <option value="PUC">PUC</option>
                                <option value="RC">RC</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row" style="display:none" id="new">
                    <div class="col-md-6">
                        <div class="mb-2 d-flex align-items-center">
                            <label class="form-label me-3" style="width: 250px;">Certificate Type:</label>
                            <select class="form-select" onchange="change('new');" name="certificate_type_new"
                                id="certificate_type_new">
                                <option value="selected">Select</option>
                                <option value="Fitness">Fitness</option>
                                <option value="Insurance">Insurance</option>
                                <option value="PUC">PUC</option>
                                <option value="RC">RC</option>
                                <option value="form3">Form3</option>
                                <option value="form4">Form4</option>
                                <option value="form5">Form5</option>
                                <option value="form8">Form8</option>
                                <option value="medical_fitness">Medical Fitness</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div id="fitness" style="display:none;">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class=" mb-2 d-flex align-items-center">
                            <label class="form-label me-3" style="width: 250px;" for="fitness_certificate">Fitness
                                Certificate:</label>
                            <input type="text" class="form-control" id="fitness_certificate"
                                name="fitness_certificate" />
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-2 d-flex align-items-center">
                            <label class="form-label me-3" style="width: 250px;" for="fitness_valid_to">Fitness
                                Valid
                                Up To:</label>
                            <input type="date" class="form-control" id="fitness_valid_to" name="fitness_valid_to" />
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class=" mb-2 d-flex align-items-center">
                            <label class="form-label me-3" style="width: 250px;"
                                for="fitness_certificate_upload">Fitness
                                Certificate Upload:</label>
                            <input type="file" class="form-control" id="fitness_certificate_upload"
                                name="fitness_certificate_upload" />
                        </div>
                    </div>

                </div>
            </div>

            <div id="insurance" style="display:none;">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="mb-3 mb-2 d-flex align-items-center">
                            <label class="form-label me-3" style="width: 250px;" for="insurance_details">Insurance
                                Details:</label>
                            <input type="text" class="form-control" id="insurance_details" name="insurance_details" />
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3 mb-2 d-flex align-items-center">
                            <label class="form-label me-3" style="width: 250px;" for="insurance_valid_to">Insurance
                                valid UpTo
                                :</label>
                            <input type="date" class="form-control" id="insurance_valid_to" name="insurance_valid_to" />
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3 mb-2 d-flex align-items-center">
                            <label class="form-label me-3" style="width: 250px;" for="insurance_certificate">Insurance
                                Certificate Upload:</label>
                            <input type="file" class="form-control" id="insurance_certificate"
                                name="insurance_certificate" />
                        </div>
                    </div>
                </div>
            </div>

            <div id="puc" style="display:none;">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="mb-3 mb-2 d-flex align-items-center">
                            <label class="form-label me-3" style="width: 250px;" for="puc_certificate">PUC
                                Certificate:</label>
                            <input type="text" class="form-control" id="puc_certificate" name="puc_certificate" />
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3 mb-2 d-flex align-items-center">
                            <label class="form-label me-3" style="width: 250px;" for="puc_valid_to">PUC
                                Valid UP
                                To:</label>
                            <input type="date" class="form-control" id="puc_valid_to" name="puc_valid_to" />
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3 mb-2 d-flex align-items-center">
                            <label class="form-label me-3" style="width: 250px;" for="puc_certificate_upload">PUC
                                Certificate Upload:</label>
                            <input type="file" class="form-control" id="puc_certificate_upload"
                                name="puc_certificate_upload" />
                        </div>
                    </div>
                </div>
            </div>


            <div id="rc" style="display:none;">
                <div class="col-md-6">
                    <div class="mb-3 mb-2 d-flex align-items-center">
                        <label class="form-label me-3" style="width: 250px;" for="rc_upload">Registration
                            Certificate
                            (RC) Upload:</label>
                        <input type="file" class="form-control" id="rc_upload" name="rc_upload" />
                    </div>
                </div>
            </div>

            <!--new changes  -->
            <div id="form3" style="display:none;">
                <div class="col-md-6">
                    <div class="mb-3 mb-2 d-flex align-items-center">
                        <label class="form-label me-3" style="width: 250px;" for="form3_upload">

                            form3 Upload:</label>
                        <input type="file" class="form-control" id="form3_upload" name="form3_upload" />
                    </div>
                </div>
            </div>

            <!-- form4 -->
            <div id="form4" style="display:none;">
                <div class="col-md-6">
                    <div class="mb-3 mb-2 d-flex align-items-center">
                        <label class="form-label me-3" style="width: 250px;" for="form4_upload">Registration
                            form4
                            Upload:</label>
                        <input type="file" class="form-control" id="form4_upload" name="form4_upload" />
                    </div>
                </div>
            </div>
            <!-- form5 -->
            <div id="form5" style="display:none;">
                <div class="col-md-6">
                    <div class="mb-3 mb-2 d-flex align-items-center">
                        <label class="form-label me-3" style="width: 250px;" for="form5_upload">Registration

                            (form5) Upload:</label>
                        <input type="file" class="form-control" id="form5_upload" name="form5_upload" />
                    </div>
                </div>
            </div>
            <!-- form8 -->
            <div id="form3" style="display:none;">
                <div class="col-md-6">
                    <div class="mb-3 mb-2 d-flex align-items-center">
                        <label class="form-label me-3" style="width: 250px;" for="form3_upload">Registration

                            (form8) Upload:</label>
                        <input type="file" class="form-control" id="form3_upload" name="form3_upload" />
                    </div>
                </div>
            </div>
            <!-- mediacal insurance -->
            <div id="form3" style="display:none;">
                <div class="col-md-6">
                    <div class="mb-3 mb-2 d-flex align-items-center">
                        <label class="form-label me-3" style="width: 250px;" for="medic_fit">Registration
                            Medical Fitness
                            Upload:</label>
                        <input type="file" class="form-control" id="medic_fit" name="medic_fit" />
                    </div>
                </div>
            </div>
            <!-- end -->


            <div class="col-md-12 text-left">
                <button type="button  btn-lg" id="add" class="btn btn-primary">Add</button>

            </div>



            <div class="row mt-4">
                <div id="dataTableContainer" class="table-responsive mt-2">

                    <table id="example" class="table table-striped table-bordered" style="width:100%">

                        <thead class="thead-light">
                            <th>S.No</th>
                            <th>Certificate Type</th>
                            <th>Certificate NO</th>
                            <th>Valid Up To</th>
                            <th>Attached Proof</th>
                            <th>Action</th>
                        </thead>
                        <tbody id="documentTableBody">

                        </tbody>
                    </table>
                </div>
                <div></div>

            </div>
            <hr>
            <div class="row ">
                <h5> Authentication</h5>
                <div class="row">
                    <div class="row mb-3">


                        <div class="col-sm-2 mb-2">
                            <label for="colFormLabelSm" class="col-form-label">User Login IDs:</label>
                        </div>
                        <div class="col-sm-4 mb-2">

                            <input type="text" class="form-control" name="login_id" id="login_id" readonly>
                        </div>



                        <div class="col-sm-2 mb-2 password-container password">
                            <label for="colFormLabelSm" class="col-form-label">Password</label>
                        </div>
                        <div class="col-sm-4 password">
                            <div class="input-wrapper">
                                <input type="password" class="form-control " id="password" name="password"
                                    oninput="validatePassword()" />
                                <i class="fas fa-eye toggle-password" id="togglePassword"
                                    onclick="togglePasswordVisibility('password', this)"></i>
                            </div>
                            <small id="passwordErrors" class="text-danger"></small>
                        </div>

                        <div class="col-sm-2 mb-2 password-container password">
                            <label for="colFormLabelSm" class="col-form-label">Confirm Password</label>
                        </div>
                        <div class="col-sm-4 password">
                            <div class="input-wrapper">
                                <input type="password" class="form-control mb-2" onkeyup="password_change()"
                                    id="password_confirmation" name="password_confirmation" />
                                <i class="fas fa-eye toggle-password" id="toggleConfirmPassword"
                                    onclick="togglePasswordVisibility('password_confirmation', this)"></i>
                            </div>
                            <small id="password_error" class="text-danger" style="display:none;">Passwords do
                                not match</small>
                            <small id="password_crct" class="text-success" style="display:none;">Passwords
                                match</small>
                        </div>



                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="btn-float">
                        <button type="button" class="btn btn-danger"
                            onclick="window.location='{{ route('vehicle_registration.index') }}'">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>


<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}">
</script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>


<script>


    function getVehicleCategory(vehicleTypeId) {
        if (vehicleTypeId) {
            let url = '{{ route("get-vehicle-category", ":id") }}'; // Use :id as a placeholder
            url = url.replace(':id', vehicleTypeId); // Replace :id with the actual vehicleTypeId

            $.ajax({
                url: url, // Use the modified URL
                type: 'GET',
                success: function (data) {
                    let options = '<option value="">Select Vehicle Category</option>';
                    data.forEach(category => {
                        options += `<option value="${category.unique_id}">${category.vehicle_type}</option>`;
                    });
                    $('#vehicle_category').html(options);
                },
                error: function () {
                    alert('Failed to load vehicle categories. Please try again.');
                }
            });
        } else {
            $('#vehicle_category').html('<option value="">Select Vehicle Category</option>');
        }
    }
    function validateVehicleNumber11(input) {
        // alert();
        // Define the regex pattern for the vehicle registration number
        const regex = /^[A-Z]{2}\d{2}[A-Z]{2}\d{4}$/;
        const vehicleNumber = input.value.trim();
        const errorMessage = document.getElementById('vehicle_error');

        // Clear any previous error message
        errorMessage.style.display = 'none';
        errorMessage.textContent = '';

        // Validate the vehicle number
        if (!regex.test(vehicleNumber)) {
            // Show error if validation fails
            errorMessage.style.display = 'block';
            errorMessage.textContent = 'Invalid vehicle number format. Please use the format: XX00XX0000';
        }
    }

    function validateVehicleNumber(input) {
        // Regular expressions for various vehicle number formats
        // const usaRegex = /^[A-Z]{3} \d{4}$/;
        // const ukRegex = /^[A-Z]{2}\d{2} [A-Z]{3}$/;
        const regex = /^[A-Z]{2}\d{2}[A-Z]{2}\d{4}$/;
        const inputData = input.value;
        console.log(input.value);
        if (regex.test(inputData)) {
            console.log("Valid Indian vehicle number.");
            return true;
        } else {
            console.log('not valid');
        }
    }

    function password_change() {
        var password = $("#password").val();
        var confirm_password = $('#password_confirmation').val();

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

    function hashPassword(password) {
        return CryptoJS.SHA256(password).toString(CryptoJS.enc.Hex);
    }



    $('#submit').on('click', function (event) {
        // alert('jiii');
        event.preventDefault(); // Prevent the default form submission

        var unique_id = $('#unique_id2').val();
        // alert("unique_id " + unique_id);
        var login_id = $('#login_id').val();
        // alert("login_id " + login_id);
        var pass_pin = $('#pass_pin').val();
        // alert("pass_pin " + pass_pin);
        var password = $('#password').val();
        // alert("password " + password);
        var confirm_password = $('#password_confirmation').val();
        // alert("confirm_password " + confirm_password);
        var pin = $('#pin').val();
        // alert("pin " + pin);
        var confirm_pin = $('#confirm_pin').val();
        // alert("confirm_pin " + confirm_pin);


        // Gather form data
        var formData = {
            unique_id: $('#unique_id2').val(),
            login_id: $('#login_id').val(),
            pass_pin: $('#pass_pin').val(),
            password: $('#password').val(),
            confirm_password: $('#password_confirmation').val(),
            pin: $('#pin').val(),
            confirm_pin: $('#confirm_pin').val(),
            hash_pass_pin: hashPassword(password),
            _token: '{{ csrf_token() }}'
        };

        // Determine method and URL based on whether $acc_year is set
        var method = "POST";
        var url = "{{route('vehicle_registration_sub_store')}}";

        if (fullName != '') {

            $.ajax({
                url: url,
                method: method,
                data: formData,

                success: function (response) {

                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Vehicle Registration create successfully!',
                        timer: 1500, // Time in milliseconds (1500ms = 1.5 seconds)
                    }).then(() => {
                        window.location.href = "{{ route('vehicle_registration.index')}}";
                    });
                },

                error: function (xhr) {
                    console.log(xhr.responseText);
                    if (xhr.status === 422) { // Validation error status code
                        sweetalert("already");
                    } else {
                        Swal.fire(
                            'Error!',
                            'An error occurred. Please try again.',
                            'error'
                        );
                    }
                }
            });
        } else {
            if (fullName === '') {
                sweetalert("form_alert");
                $('#designationerror').text('Vehicle is required.');
            } else {
                $('#designationerror').text('');
            }
        }
    });

    function generateUniqueId() {
        return Date.now() + Math.floor(Math.random() * 1000);
    }

    $(document).ready(function () {
        // Generate a unique ID when the form loads
        var screenUniqueId = generateUniqueId();
        $('#screenUniqueId').val(screenUniqueId); // Set the unique ID in the hidden input field
    });


    function change() {

        var fitness = $('#fitness').val();
        var insurance = $('#insurance').val();
        var puc = $('#puc').val();
        var rc = $('#rc').val();

        var selected_element = document.getElementById('certificate_type_old');
        var selected_element_new = document.getElementById('certificate_type_new');

        var selected_value = selected_element.value;

        var selected_value_new = selected_element_new.value;


        // var dynamic_values = $('#dynamic_values').val();

        if (selected_value === 'Fitness' || selected_value_new === 'Fitness') {

            // alert('1');
            $('#fitness').show();
            $('#add').show();
            $('#insurance').hide();
            $('#puc').hide();
            $('#rc').hide();

        } else if (selected_value === 'Insurance' || selected_value_new === 'Insurance') {

            $('#insurance').show();
            $('#add').show();
            $('#puc').hide();
            $('#rc').hide();
            $('#fitness').hide();

        } else if (selected_value === 'PUC' || selected_value_new === 'PUC') {
            $('#puc').show();
            $('#add').show();
            $('#insurance').hide();
            $('#rc').hide();
            $('#fitness').hide();

        } else if (selected_value === 'RC' || selected_value_new === 'RC') {
            $('#rc').show();
            $('#add').show();
            $('#insurance').hide();
            $('#puc').hide();
            $('#fitness').hide();
        }
        // new form3
        else if (selected_value_new === 'form3') {
            $('#rc').hide();
            $('#add').show();
            $('#insurance').hide();
            $('#puc').hide();
            $('#fitness').hide();
            $('#form3').show();
            $('#form4').hide();
            $('#form5').hide();
            $('#form8').hide();
            $('#medical_fitness').hide();
        }
        // new form4
        else if (selected_value_new === 'form4') {
            $('#rc').hide();
            $('#add').show();
            $('#insurance').hide();
            $('#puc').hide();
            $('#fitness').hide();
            $('#form3').hide();
            $('#form4').show();
            $('#form5').hide();
            $('#form8').hide();
            $('#medical_fitness').hide();
        }

        else if (selected_value_new === 'form5') {
            $('#rc').hide();
            $('#add').show();
            $('#insurance').hide();
            $('#puc').hide();
            $('#fitness').hide();
            $('#form3').show();
            $('#form4').hide();
            $('#form5').hide();
            $('#form8').hide();
            $('#medical_fitness').hide();
        }

        else if (selected_value_new === 'form8') {
            $('#rc').hide();
            $('#add').show();
            $('#insurance').hide();
            $('#puc').hide();
            $('#fitness').hide();
            $('#form3').show();
            $('#form4').hide();
            $('#form5').hide();
            $('#form8').hide();
            $('#medical_fitness').hide();
        }

        else if (selected_value_new === 'medical_fitness') {
            $('#rc').show();
            $('#add').show();
            $('#insurance').hide();
            $('#puc').hide();
            $('#fitness').hide();
            $('#form3').show();
            $('#form4').hide();
            $('#form5').hide();
            $('#form8').hide();
            $('#medical_fitness').hide();
        }

    }

    function pdf() {

        var url = "resources/views/content/vehicle_registration/testpdf1.pdf";
        window.location.href = url;
    }

    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });



    let serialNumber = 1;

    $('#add').on('click', function (event) {
        event.preventDefault(); // Prevent the default form submission


        var certificate_type = $('#certificate_type').val();
        var vech_reg_no = $('#vech_reg_no').val();
        var screenUniqueId = $('#screenUniqueId').val();

        var certificate_no = ''; // Initialize certificate_no
        var valid_up_to = ''; // Initialize valid_up_to

        var fitness_certificate = $('#fitness_certificate').val();
        if (fitness_certificate !== '') {
            certificate_no = fitness_certificate;
        }

        var insurance_details = $('#insurance_details').val();
        if (insurance_details !== '') {
            certificate_no = insurance_details;
        }

        var puc_certificate = $('#puc_certificate').val();
        if (puc_certificate !== '') {
            certificate_no = puc_certificate;
        }

        var fitness_valid_to = $('#fitness_valid_to').val();
        if (fitness_valid_to !== '') {
            valid_up_to = fitness_valid_to;
        }

        var insurance_valid_to = $('#insurance_valid_to').val();
        if (insurance_valid_to !== '') {
            valid_up_to = insurance_valid_to;
        }

        var puc_valid_to = $('#puc_valid_to').val();
        if (puc_valid_to !== '') {
            valid_up_to = puc_valid_to;
        }

        // Create a FormData object to handle the file uploads
        var formData = new FormData();
        formData.append('screenUniqueId', screenUniqueId);
        formData.append('vech_reg_no', vech_reg_no);
        formData.append('certificate_type', certificate_type);
        formData.append('certificate_no', certificate_no);
        formData.append('valid_up_to', valid_up_to);

        // Append file inputs if they exist
        var fitness_certificate_upload = document.getElementById('fitness_certificate_upload').files[0];
        if (fitness_certificate_upload) {
            formData.append('attached_proof', fitness_certificate_upload);
        }

        var insurance_certificate = document.getElementById('insurance_certificate').files[0];
        if (insurance_certificate) {
            formData.append('attached_proof', insurance_certificate);
        }

        var puc_certificate_upload = document.getElementById('puc_certificate_upload').files[0];
        if (puc_certificate_upload) {
            formData.append('attached_proof', puc_certificate_upload);
        }

        var rc_upload = document.getElementById('rc_upload').files[0];
        if (rc_upload) {
            formData.append('attached_proof', rc_upload);
        }


        $.ajax({
            url: '{{ route('vehicle_registration.store_1') }}',
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.message) {
                    const vehicle = response.vehicle;
                    var file_name = vehicle.attached_proof;
                    // Show success alert
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Document added successfully!',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        loadTableData();
                        // const vehicle = response.vehicle;
                        // Append new document details to the table with serial number
                        // $('#documentTableBody').append(`
                        //     <tr id="row-${vehicle.id}">
                        //         <td>${serialNumber++}</td>
                        //         <td>${vehicle.certificate_type || 'N/A'}</td>
                        //         <td>${vehicle.certificate_no || 'N/A'}</td>
                        //         <td>${vehicle.valid_up_to || 'N/A'}</td>
                        //         <td>
                        //             <a onclick="imageViews('${file_name}')"><img src='public/uploads/download.png' width="40px" height="40px"></a>
                        //         </td>
                        //         <td>
                        //         <a class="text-primary" title="Edit"  onclick="editDocument(${vehicle.id}, '${vehicle.certificate_type}', '${vehicle.certificate_no}', '${vehicle.valid_up_to}')"><i class="fas fa-edit"></i></a>
                        //             <a href="#" class="text-danger" title="Delete" onclick="deleteRecord(${vehicle.id})"><i class="fas fa-trash-alt"></i></a>
                        //         </td>
                        //     </tr>
                        // `);

                        // Reset the form inputs
                        $('#certificate_type').val('selected');
                        $('#fitness_certificate').val('');
                        $('#fitness_valid_to').val('');
                        $('#fitness_certificate_upload').val('');
                        $('#insurance_details').val('');
                        $('#insurance_valid_to').val('');
                        $('#insurance_certificate').val('');
                        $('#puc_certificate').val('');
                        $('#puc_valid_to').val('');
                        $('#puc_certificate_upload').val('');
                        $('#rc_upload').val('');
                    });
                }
            },
            error: function (xhr) {
                var errors = xhr.responseJSON.errors;
                if (errors) {
                    $.each(errors, function (key, value) {
                        alert(value[0]);
                    });
                } else {
                    alert('An error occurred while saving. Please try again.');
                }
            }
        });
    });


    function loadTableData() {
        var screenUniqueId = $('#screenUniqueId').val();

        $.ajax({
            type: "GET",
            url: '{{ route("VehicleReistrationSublist.index") }}',
            data: {
                screenUniqueId: screenUniqueId, // Pass the parameter directly
                _token: '{{ csrf_token() }}' // Include CSRF token for security
            },
            success: function (data) {
                // Clear the current table body
                $('#example tbody').empty();

                // Populate the table with new data
                data.forEach(function (item, index) {
                    var newRow = `
                <tr id="row-${item.unique_id}">
                    <td>${index + 1}</td>
                    <td>${item.certificate_type || 'N/A'}</td>
                    <td>${item.certificate_no || 'N/A'}</td>
                    <td>${item.valid_up_to || 'N/A'}</td>
                     <td>
                                     <a onclick="imageViews('${item.attached_proof}')"><img src='public/uploads/download.png' width="40px" height="40px"></a>
                                 </td>
                                 <td>
                                 <a class="text-primary" title="Edit"  onclick="editDocument(${item.id}, '${item.certificate_type}', '${item.certificate_no}', '${item.valid_up_to}')"><i class="fas fa-edit"></i></a>
                                     <a href="#" class="text-danger" title="Delete" onclick="deleteRecord(${item.id})"><i class="fas fa-trash-alt"></i></a>
                                 </td>
                </tr>`;
                    // Append the new row to the correct table body
                    $('#example tbody').append(newRow);
                });
            },
            error: function (xhr) {
                console.log(xhr.responseText); // Log any errors for debugging
            }
        });
    }




    function editDocument(id, certificateType, certificateNo, validUpTo) {
        // Set the certificate type dropdown
        $('#certificate_type').val(certificateType);

        // Set other fields based on the type of certificate
        if (certificateType === 'Fitness') {
            $('#fitness_certificate').val(certificateNo);
            $('#fitness_valid_to').val(validUpTo);
            // $('#fitness_certificate_upload').val(attachedProof);
            // Show fitness section and hide others
            $('#fitness').show();
            $('#insurance').hide();
            $('#puc').hide();
            $('#rc').hide();
        } else if (certificateType === 'Insurance') {
            $('#insurance_details').val(certificateNo);
            $('#insurance_valid_to').val(validUpTo);
            // $('#insurance_certificate').val(attachedProof);
            // Show insurance section and hide others
            $('#insurance').show();
            $('#fitness').hide();
            $('#puc').hide();
            $('#rc').hide();
        } else if (certificateType === 'PUC') {
            $('#puc_certificate').val(certificateNo);
            $('#puc_valid_to').val(validUpTo);
            // $('#puc_certificate_upload').val(attachedProof);
            // Show puc section and hide others
            $('#puc').show();
            $('#fitness').hide();
            $('#insurance').hide();
            $('#rc').hide();
        } else if (certificateType === 'RC') {
            // $('#rc_upload').val(attachedProof);
            // Show RC section and hide others
            $('#rc').show();
            $('#fitness').hide();
            $('#insurance').hide();
            $('#puc').hide();
        }
    }
    // Function to remove a row from the table
    function removeRow(button) {
        const row = button.closest('tr');
        row.parentNode.removeChild(row);
    }


    function submitForm(event) {

        event.preventDefault(); // Prevent the default form submission

        var isValid = true; // Flag to track form validity

        // Clear any previous error messages
        $('.error-message').remove();

        // Check specific fields for required validation
        var requiredFields = [
            '#vech_reg_no',
            '#owner_name',
            '#contact_number',
            '#login_id',
            '#password',   // Add only the fields you want to be required
            '#password_confirmation',
            '#acc_year',
            '#Vehicle_image',
        ];
        requiredFields.forEach(function (field) {
            var input = $(field);

            // Remove any existing error message
            input.next('.error-message').remove();

            // Check if the field is empty
            if (input.val() === '') {
                isValid = false;
                // Append the error message below the input field
                input.after('<div><span class="error-message" style="color: red;">This field is required.</span></div>');
            }
        });
        var vehicleImageInput = $('#Vehicle_image');
        if (vehicleImageInput[0].files.length === 0) {
            isValid = false;
            vehicleImageInput.after('<div><span class="error-message" style="color: red;">Image file is required.</span></div>');
        } else {
            var file = vehicleImageInput[0].files[0];
            var validTypes = ['image/jpeg', 'image/png', 'image/jpg']; // Define allowed file types
            if (!validTypes.includes(file.type)) {
                isValid = false;
                vehicleImageInput.after('<div><span class="error-message" style="color: red;">Invalid image type. Only JPEG and PNG are allowed.</span></div>');
            }
            if (file.size > 250 * 1024) { // Limit size to 250KB
                isValid = false;
                vehicleImageInput.after('<div><span class="error-message" style="color: red;">Image size exceeds 250KB.</span></div>');
            }
        }
        if (!isValid) {
            return false;
        }

        // Collect main form data
        var formData = new FormData(document.getElementById('usertypeForm'));

        // Hash the password and append it to formData
        var password = $('#password').val();
        var confirm_password = $('#password_confirmation').val();
        // const regex1 = /^[A-Z]{2}\d{2}[A-Z]{2}\d{4}$/;
        var vech_reg_no = $('#vech_reg_no').val();
        // alert(vech_reg_no);

        if (password == confirm_password && vech_reg_no != '') {
            // Send the data using AJAX
            $.ajax({
                url: '{{ route('vehicle_registration.store') }}', // Adjust this if necessary
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    // Handle the success response
                    if (response.message) {
                        // Show success message (SweetAlert or any other method)
                        Swal.fire('Success', response.message, 'success').then(() => {
                            // Redirect or further processing if necessary
                            window.location.href = 'vehicle_registration';
                        });
                    }
                },
                error: function (xhr) {
                    // Handle the error
                    var errors = xhr.responseJSON.errors;
                    if (errors) {
                        $.each(errors, function (key, value) {
                            alert(value[0]); // Display the first error message
                        });
                    } else {
                        alert('An error occurred while saving. Please try again.');
                    }
                }
            });
        } else if (password != confirm_password) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Password and Confirm Password should be same',
                confirmButtonText: 'OK'
            })
        } else if (!(regex1.test(vech_reg_no))) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Enter the Valid Vehicle Number',
                confirmButtonText: 'OK'
            })
        }
    }

    // Example of a simple hashing function (you should use a stronger hashing algorithm)
    function hashPassword(password) {
        // Hash the password (use a proper hashing method in a real scenario)
        return btoa(password); // Base64 encoding as an example (not secure for real passwords)
    }



    function imageViews(fileName) {
        //   alert(fileName);
        // Construct the URL for the file
        var fileUrl = `../../harbour/public/uploads/vehicle_registration/${fileName}`; // Update this path as needed
        // console.log("File URL: ", fileUrl);
        // Create a temporary anchor element
        var a = document.createElement('a');
        a.href = fileUrl;
        a.download = fileName; // Set the file name for download

        // Append the anchor to the body (not necessary, but helps in some cases)
        document.body.appendChild(a);

        // Simulate click on the anchor
        a.click();

        // Clean up by removing the anchor
        document.body.removeChild(a);
    }

    function deleteRecord(id) {
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
                    url: '{{ route('vehicle_registration.markAsDeleted', ':id') }}'.replace(':id', id),
                    method: 'PUT', // Assuming you're using PUT to update the record
                    data: {
                        is_delete: 1,
                        _token: '{{ csrf_token() }}' // Include CSRF token
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire(
                                'Deleted!',
                                'The record has been marked as deleted.',
                                'success'
                            );
                            loadTableData();
                            // Optionally, remove the row from the table
                            $('#row-' + id).remove();
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

    function appendValue(element) {
        // Get the value from the input that triggered the function
        var vehicle_no = $(element).val();

        // Get the current value of login_id
        var login_id = $('#login_id').val();

        // Append the vehicle_no to the login_id value
        login_id = vehicle_no; // Append the values as a string

        // Optionally, set the new value back to login_id (if needed)
        $('#login_id').val(login_id);

        // Example action: log the new value
        console.log('New login_id value:', login_id);
    }
    function validateName() {

        const nameField = document.getElementById('owner_name');
        const nameError = document.getElementById('ownerNameError');
        const regex = /^[a-zA-Z\s]*$/;

        if (!regex.test(nameField.value)) {
            nameField.classList.add('is-invalid');
            nameError.textContent = "Name can only contain alphabets and spaces.";
        } else {
            nameField.classList.remove('is-invalid');
            nameError.textContent = "";
        }
    }

    document.getElementById('contact_number').addEventListener('input', function (e) {
        this.value = this.value.replace(/[^0-9]/g, ''); // Remove any non-numeric characters
    });

    function validateMobile(input) {
        // Define the regex pattern for Indian mobile numbers (10 digits starting with 7, 8, or 9)
        const regex = /^[6789]\d{9}$/;
        const mobileNumber = input.value;
        const length = mobileNumber.length;
        const errorMessage = document.getElementById('mobile_error');

        // Clear any previous error message
        errorMessage.style.display = 'none';
        errorMessage.textContent = '';

        if (length !== 10) {
            // Show error for incorrect length
            errorMessage.style.display = 'block';
            errorMessage.textContent = 'Mobile number must contain exactly 10 digits.';
        } else if (!regex.test(mobileNumber)) {
            // Show error for invalid mobile number
            errorMessage.style.display = 'block';
            errorMessage.textContent = 'Invalid mobile number. Please start with 7, 8, or 9.';
        }
    }


    function validatePassword() {
        const passwordField = document.getElementById('password');
        const passwordErrors = document.getElementById('passwordErrors');
        const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

        if (!regex.test(passwordField.value)) {
            passwordErrors.textContent =
                "Password must be 8+ characters with uppercase, lowercase, number, and symbol.";
        } else {
            passwordErrors.textContent = "";
        }
    }




    function togglePasswordVisibility(inputId, icon) {
        const inputField = document.getElementById(inputId);

        if (inputField.type === 'password') {
            inputField.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash'); // Change to eye slash icon
        } else {
            inputField.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye'); // Change back to eye icon
        }
    }

    function previewImage(input) {
        const file = input.files[0];
        const imagePreview = document.getElementById('imagePreview');

        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                imagePreview.src = e.target.result; // Set the image source to the file's data URL
                imagePreview.style.display = 'block'; // Show the image
            }
            reader.readAsDataURL(file); // Read the file as a data URL
        } else {
            imagePreview.src = '#'; // Reset the image source
            imagePreview.style.display = 'none'; // Hide the image
        }
    }


    // new changes

    function toggleGetData(value) {
        const getDataDiv = document.getElementById("get_data");
        const newDiv = document.getElementById("new");
        const oldDiv = document.getElementById("old");
        // alert(oldDiv);

        if (value === '6711f18659aad63646') {
            getDataDiv.style.display = 'block';
            newDiv.style.display = 'block';
            oldDiv.style.display = 'none';
        } else {
            getDataDiv.style.display = 'none';
            newDiv.style.display = 'none';
            oldDiv.style.display = 'block';
        }
    }

    function checkVehicleType(selectElement) {
        const selectedValue = selectElement.value;
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const cargoValue = selectedOption.getAttribute('data-cargo');

        // Check if the selected value is '6711f18659aad63646' or if the cargo is "Equipment" or "Equipments"
        if (selectedValue === '6711f18659aad63646' || cargoValue === 'Equipment' || cargoValue === 'Equipments') {
            document.getElementById('get_data').style.display = 'block'; // Show the div
        } else {
            document.getElementById('get_data').style.display = 'none'; // Hide the div
        }
    }

</script>


@endsection