@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Port Employee/</span> Employee Registration</h4>
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
    hr{
        height:2px;
        background-color:skyblue;
    }
</style>

@php
    $department_list = get_department();
    $designation_list = get_designation();
    $acc_year = academic_year();
@endphp

<!-- Basic Layout -->
<div class="row">
    <div class="col-12">
        <form id="emp_registration" method="POST" onsubmit="return submitForm(event);">
            @csrf
            @method('PUT')
            <input type="hidden" id="screenUniqueId" name="screenUniqueId"
                value="{{ $employeeregistration->screenUniqueId }}">
            <div class="card">
                <div class="card-body">
                    <div id="basicwizard">
                        <!-- <ul class="nav nav-pills nav-justified form-wizard-header mb-4">
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
                                    <span class="d-sm-inline">Authentication</span>
                                </a>
                            </li>
                        </ul> -->

                        <!-- <div class="tab-content b-0 mb-0"> -->

                            <!-- <div class="tab-pane active" id="basictab1"> -->
                            <b>Personal Information</b>
                            <br><br>
                                <div class="row">
                                    <label for="colFormLabelSm" class="col-sm-2 col-form-label">Full Name</label>
                                    <div class="col-sm-4 fm">
                                        <input type="text" class="form-control" id="fullName" name="fullName"
                                            value="{{ $employeeregistration->fullname }}" />
                                    </div>

                                    <label for="colFormLabelSm" class="col-sm-2 col-form-label">Registration No:</label>
                                    <div class="col-sm-4 fm">
                                        <label for="colFormLabelSm">{{ $employeeregistration->register_no}}</label>
                                        <!-- <input type="text" class="form-control" id="reg_no" name="reg_no"
                                            value="{{ $employeeregistration->register_no}}" /> -->
                                    </div>

                                    <label for="nationalitySelect" class="col-sm-2 col-form-label">Employee ID</label>
                                    <div class="col-sm-4 fm">
                                        <input type="text" class="form-control" id="employee_id" name="employee_id"
                                            value="{{ $employeeregistration->employee_id }}">
                                    </div>

                                    <label for="countrySelect" class="col-sm-2 col-form-label">DOB</label>
                                    <div class="col-sm-4 fm">
                                        <input type="date" class="form-control" id="dob" name="dob"
                                            value="{{ $employeeregistration->dob }}">
                                    </div>

                                    <label for="colFormLabelSm" class="col-sm-2 col-form-label">Blood Group</label>
                                    <div class="col-sm-4 fm">
                                        <select class="form-select" id="blood_grp" name="blood_grp">
                                            <option value="">Select Blood Group</option>
                                            <option value="A+" {{ $employeeregistration->blood_group == 'A+' ? 'selected' : '' }}>A+</option>
                                            <option value="A-" {{ $employeeregistration->blood_group == 'A-' ? 'selected' : '' }}>A-</option>
                                            <option value="A1+" {{ $employeeregistration->blood_group == 'A1+' ? 'selected' : '' }}>A1+</option>
                                            <option value="A1-" {{ $employeeregistration->blood_group == 'A1-' ? 'selected' : '' }}>A1-</option>
                                            <option value="A2+" {{ $employeeregistration->blood_group == 'A2+' ? 'selected' : '' }}>A2+</option>
                                            <option value="A2-" {{ $employeeregistration->blood_group == 'A2-' ? 'selected' : '' }}>A2-</option>
                                            <option value="B+" {{ $employeeregistration->blood_group == 'B+' ? 'selected' : '' }}>B+</option>
                                            <option value="B-" {{ $employeeregistration->blood_group == 'B-' ? 'selected' : '' }}>B-</option>
                                            <option value="AB+" {{ $employeeregistration->blood_group == 'AB+' ? 'selected' : '' }}>AB+</option>
                                            <option value="AB-" {{ $employeeregistration->blood_group == 'AB-' ? 'selected' : '' }}>AB-</option>
                                            <option value="AB1+" {{ $employeeregistration->blood_group == 'AB1+' ? 'selected' : '' }}>AB1+</option>
                                            <option value="AB1-" {{ $employeeregistration->blood_group == 'AB1-' ? 'selected' : '' }}>AB1-</option>
                                            <option value="AB2+" {{ $employeeregistration->blood_group == 'AB2+' ? 'selected' : '' }}>AB2+</option>
                                            <option value="AB2-" {{ $employeeregistration->blood_group == 'AB2-' ? 'selected' : '' }}>AB2-</option>
                                            <option value="O+" {{ $employeeregistration->blood_group == 'O+' ? 'selected' : '' }}>O+</option>
                                            <option value="O-" {{ $employeeregistration->blood_group == 'O-' ? 'selected' : '' }}>O-</option>
                                        </select>

                                    </div>

                                    <label for="colFormLabelSm" class="col-sm-2 col-form-label">Aadhaar No</label>
                                    <div class="col-sm-4 fm">
                                        <input type="text" id="aadhaar_no" name="aadhaar_no" class="form-control"
                                            value="{{ $employeeregistration->aadhar_no }}"
                                            onchange="validateAadhar(this)" maxlength="12">
                                        <span id="aadhaar_error" class="error-message"
                                            style="color: red; display: none;"></span>
                                    </div>

                                    <label for="colFormLabelSm" class="col-sm-2 col-form-label">Email ID</label>
                                    <div class="col-sm-4 fm">
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="{{ $employeeregistration->email_id }}"
                                            onchange="validateEmail(this)" />
                                        <small id="email_error" class="text-danger" style="display:none;"></small>
                                    </div>

                                    <label for="colFormLabelSm" class="col-sm-2 col-form-label">Department</label>
                                    <div class="col-sm-4 fm">
                                        <select class="form-select" name="department" id="department">
                                            <option value="">Select Department</option>
                                            @foreach ($department_list as $department)
                                                <option value="{{$department->unique_id}}" {{ $department->unique_id == $employeeregistration->department ? 'selected' : '' }}>{{$department->department}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <label for="colFormLabelSm" class="col-sm-2 col-form-label">Designation</label>
                                    <div class="col-sm-4 fm">
                                        <select class="form-select" name="designation" id="designation"
                                            onchange="checkSelection()">
                                            <option value="">Select Designation</option>
                                            @foreach ($designation_list as $designation)
                                                <option value="{{$designation->unique_id}}" {{ $designation->unique_id == $employeeregistration->designation ? 'selected' : '' }}>{{$designation->designation}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- @if ($employeeregistration->designation == '66fe4a0298b9a71480') -->

                                        <label for="colFormLabelSm" class="col-sm-2 col-form-label" id="lic_exp"
                                            style="display:none">Licence Expire</label>
                                        <div class="col-sm-4 fm" style="display:none" id="lic_exp_div">
                                            <input type="date" class="form-control" id="licence_expire"
                                                name="licence_expire" value="{{ $employeeregistration->lic_exp_date }}" />
                                        </div>

                                    <!-- @endif -->

                                    <label for="colFormLabelSm" class="col-sm-2 col-form-label">Area</label>
                                    <div class="col-sm-4 fm">
                                        <input type="text" class="form-control" id="area" name="area"
                                            value="{{ $employeeregistration->area }}" />
                                    </div>


                                    <!-- <label for="colFormLabelSm" class="col-sm-2 col-form-label">RFID NO</label>
                                    <div class="col-sm-4 fm">
                                        <input type="text" class="form-control" id="rfid_no" name="rfid_no"
                                            value="{{ $employeeregistration->rfid_no }}" />
                                    </div> -->

                                    <label for="colFormLabelSm" class="col-sm-2 col-form-label">Issuing
                                        Authority</label>
                                    <div class="col-sm-4 fm">
                                        <input type="text" class="form-control" id="authority" name="authority"
                                            value="{{ $employeeregistration->authority }}" />
                                    </div>

                                    <label for="colFormLabelSm" class="col-sm-2 col-form-label">Mobile Number</label>
                                    <div class="col-sm-4 fm">
                                        <input type="text" class="form-control" id="mobile" name="mobile" maxlength="10"
                                            value="{{ $employeeregistration->mobile }}"
                                            onchange="validateMobile(this)" />
                                        <span id="mobile_error" class="error-message"
                                            style="color: red; display: none;"></span>
                                    </div>

                                    <label for="colFormLabelSm" class="col-sm-2 col-form-label">Address</label>
                                    <div class="col-sm-4 fm">
                                        <textarea class="form-control" id="address"
                                            name="address">{{ $employeeregistration->address }}</textarea>
                                    </div>

                                    <label for="colFormLabelSm" class="col-sm-2 col-form-label">A/C Year:</label>
                                    <div class="col-sm-4 fm">
                                        <select class="form-select" name="acc_year" id="acc_year" readonly>
                                            @foreach ($acc_year as $acc)
                                                <option value="{{ $acc->unique_id }}" {{ $acc->unique_id == $employeeregistration->acc_year ? 'selected' : '' }}>
                                                    {{ $acc->acc_year }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- <div class="btn-float">
                                    <button type="button" class="btn btn-primary"
                                        onclick="document.querySelector('#tab2-link').click();">Next</button>
                                </div> -->

                            <!-- </div> end tab-pane -->

                            <!-- <div class="tab-pane" id="basictab2"> -->
                            <hr>
                            <b>Document Details</b>
                            <br><br>

                                <div class="row mb-3">
                                    <label for="idproof" class="col-sm-2 col-form-label">Type of ID proof</label>
                                    <div class="col-sm-4 fm">
                                        <select class="form-select" id="idproof" name="idproof" onchange="proof_type()">
                                            <option value="">Select ID Proof</option>
                                            <option value="Aadhaar" {{ $employeeregistration->idproof == 'Aadhaar' ? 'selected' : '' }}>Aadhaar</option>
                                            <option value="Driving Licence" {{ $employeeregistration->idproof == 'Driving Licence' ? 'selected' : '' }}>
                                                Driving Licence</option>
                                        </select>
                                    </div>

                                    <label for="colFormLabelSm" class="col-sm-2 col-form-label">ID proof No.</label>
                                    <div class="col-sm-4 fm">
                                        <input type="text" class="form-control" id="proof_no" name="proof_no"
                                            value="{{ $employeeregistration->proof_no }}" />
                                    </div>

                                    <label for="colFormLabelSm" class="col-sm-2 col-form-label">Proof attachment</label>
                                    <div class="col-sm-4 fm">
                                        <input type="file" class="form-control" id="proof_attach" name="proof_attach" />
                                    </div>

                                    <label for="colFormLabelSm" id="lic_label" class="col-sm-2 col-form-label"
                                        style="display: none;">Licence Expire Date</label>
                                    <div class="col-sm-4 fm">
                                        <input type="date" class="form-control" id="lic_exp_date" name="lic_exp_date"
                                            value="{{ $employeeregistration->lic_exp_date }}" style="display: none;" />
                                    </div>

                                </div>

                                <div class="col-md-12 text-end">
                                    <button type="button  btn-lg" id="add" class="btn btn-primary">Add</button>

                                </div>

                                <!-- <div class="d-flex justify-content-center mb-4">
                                    <button id="openTableButton submit_sublist" onclick="sublist_proof(event)"
                                        class="btn btn-primary">Add</button>
                                </div> -->

                                <div id="dataTableContainer" class="table-responsive mt-4">
                                    <table id="employeeTable" class="table table-striped table-bordered"
                                        style="width:100%">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>S.No</th>
                                                <th>Type of ID Proof</th>
                                                <th>Proof No.</th>
                                                <th>Proof Expire Date</th>
                                                <th>Document</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>

                                <!-- <div class="btn-float">
                                    <button type="button" class="btn btn-primary"
                                        onclick="document.querySelector('#tab3-link').click();">Next</button>
                                </div> -->
                            <!-- </div> end tab-pane -->
                            <hr>
                            <b>Authenication</b>
                            <br><br>
                            <!-- <div class="tab-pane" id="basictab3"> -->
                                <div class="row mb-3">
                                    <label for="colFormLabelSm" class="col-sm-2 col-form-label">User ID</label>
                                    <div class="col-sm-4 fm">
                                        <input type="text" class="form-control" id="login_id" name="login_id" readonly
                                            value="{{ $employeeregistration->user_id }}" />
                                    </div>

                                    <div class="col-sm-2 password-container passwordmb-3">
                                        <label for="colFormLabelSm" class="col-form-label">Password</label>
                                    </div>

                                    <div class="col-sm-4 password">
                                        <div class="input-wrapper">
                                            <input type="password" class="form-control" id="password" name="password"
                                                onclick="validatePassword()" />
                                            <i class="fas fa-eye-slash toggle-password" id="togglePassword"
                                                onclick="togglePasswordVisibility('password', this); validatePassword();"></i>
                                        </div>
                                    </div>
                                    <span id="passwordError" class="error-message"
                                        style="color: red; display: none;">
                                        Password must contain at least one uppercase letter, one lowercase
                                        letter, one number, and one special character.
                                    </span>

                                    <div class="col-sm-2 password-container password">
                                        <label for="colFormLabelSm" class="col-form-label">Confirm Password</label>
                                    </div>
                                    <div class="col-sm-4 password">
                                        <div class="input-wrapper">
                                            <input type="password" class="form-control" onkeyup="password_change()"
                                                id="confirm_password" name="confirm_password" />
                                            <i class="fas fa-eye-slash toggle-password" id="toggleConfirmPassword"
                                                onclick="togglePasswordVisibility('confirm_password', this)"></i>
                                        </div>
                                        <small id="password_error" class="text-danger" style="display:none;">Passwords
                                            do not match</small>
                                        <small id="password_crct" class="text-success" style="display:none;">Passwords
                                            match</small>
                                    </div>

                                    <label for="colFormLabelSm" class="col-sm-2 col-form-label">RFID NO</label>

                                    <div class="col-sm-4 fm">
                                        <input type="text" class="form-control" id="rfid_no" name="rfid_no" readonly
                                            value="{{ $employeeregistration->rfid_no }}" />
                                    </div>

                                </div>

                                <div class="btn-float">
                                    <button type="button" class="btn btn-danger"
                                        onclick="window.location='{{ route('employee_registration') }}'">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                    <!-- onclick="window.location='{{ route('user_registration_data') }}'" -->
                                </div>
                            <!-- </div> end tab-pane -->
                        <!-- </div> end tab-content -->
                    </div>
                </div> <!-- end card-body -->
            </div> <!-- end card -->
        </form>
    </div> <!-- end col -->
</div>


<script src=" {{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>


<script>

    $(document).ready(function () {
     
        // Call the function to load table data when the document is ready
        loadTableData();
    });

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

    function hashPassword(password) {
        return CryptoJS.SHA256(password).toString(CryptoJS.enc.Hex);
    }



    function checkSelection() {
        const selectElement = document.getElementById('designation');
        const inputContainer1 = document.getElementById('lic_exp');
        const inputContainer2 = document.getElementById('lic_exp_div');
        const selectedLabel = selectElement.options[selectElement.selectedIndex].text;

        if (selectedLabel === "Driver" || selectedLabel === "Drivers" || selectedLabel === "drivers" || selectedLabel === "driver") {
            inputContainer1.style.display = 'block'; // Show the input
            inputContainer2.style.display = 'block'; // Show the input
        } else {
            inputContainer1.style.display = 'none'; // Hide the input
            inputContainer2.style.display = 'none'; // Hide the input
        }
    }

    let serialNumber = 1;

    $('#add').on('click', function (event) {
        event.preventDefault(); // Prevent the default form submission
        // alert('Hi');

        var idproof = $('#idproof').val();
        var proof_no = $('#proof_no').val();
        var proof_attach = $('#proof_attach').val();
        var lic_exp_date = $('#lic_exp_date').val();
        var screenUniqueId = $('#screenUniqueId').val();
       
        if (idproof == '') {
            Swal.fire({
                icon: 'warning',
                title: 'Warning',
                text: 'Select Document Type',
                confirmButtonText: 'OK',
                timer: 2000, // Close after 2 seconds
                timerProgressBar: true, // Show a progress bar for the timer
            });
            return; // Stop further execution
        }

        if (idproof == 'Driving Licence' && lic_exp_date == '') {
            Swal.fire({
                icon: 'warning',
                title: 'Warning',
                text: 'Enter Licence Expire Date',
                confirmButtonText: 'OK',
                timer: 2000, // Close after 2 seconds
                timerProgressBar: true, // Show a progress bar for the timer
            });
            return; // Stop further execution
        }
// Append file inputs if they exist
// var proof_attach = document.getElementById('proof_attach').files[0];
        // if (proof_attach) {

        //     formData.append('proof_name', proof_attach);
        // }

           if (idproof == 'Aadhaar') {
            $('#lic_exp_date').val = '';
        }

        // Create a FormData object to handle the file uploads
        var formData = new FormData();
        formData.append('screenUniqueId', screenUniqueId);
        formData.append('idproof', idproof);
        formData.append('proof_no', proof_no);
        formData.append('lic_exp_date', lic_exp_date);
        formData.append('_token', '{{ csrf_token() }}');

        // Append file inputs if they exist
        var proof_attach = document.getElementById('proof_attach').files[0];
        if (proof_attach) {
            formData.append('proof_attach', proof_attach);
        }

        $.ajax({
            url: '{{ route('employee_registration.store_1') }}',
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.message) {
                    const employeeregistration = response.data; // Access the correct property
                    var file_name = employeeregistration.proof_name;
                    // Show success alert
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        confirmButtonText: 'OK'
                    }).then(() => {

                        loadTableData();

                        // const employeeregistration = response.employeeregistration;
                        // Append new document details to the table with serial number
                        // $('#example').append(`
                        //     <tr id="row-${employeeregistration.id}">
                        //         <td>${serialNumber++}</td>
                        //         <td>${employeeregistration.proof_type || 'N/A'}</td>
                        //         <td>${employeeregistration.proof_no || 'N/A'}</td>
                        //         <td>
                        //             <a onclick="imageViews('${file_name}')"><img src='../public/uploads/download.png' width="40px" height="40px"></a>
                        //         </td>
                        //         <td>
                        //             <a class="text-primary" title="Edit"  onclick="editDocument(${employeeregistration.id}, '${employeeregistration.proof_type}', '${employeeregistration.proof_no})">
                        //              <i class="fas fa-edit"></i></a>
                        //              <a href="#" class="text-danger" title="Delete" onclick="deleteRecord(${employeeregistration.id})"><i class="fas fa-trash-alt"></i></a>
                        //         </td>
                        //     </tr>
                        // `);

                        // Reset the form inputs
                        $('#idproof').val('selected');
                        $('#proof_no').val('');
                        $('#proof_attach').val('');
                        $('#lic_exp_date').val('');

                        $('#tab2-link').click(); // This will refresh the tab content
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

    // Function to load table data
    function loadTableData() {
        var screenUniqueId = $('#screenUniqueId').val();
$s_no=1;
        $.ajax({
            type: "GET",
            url: '{{ route("employee_registration_sublist.index") }}',
            data: {
                screenUniqueId: screenUniqueId, // Pass the parameter directly
                _token: '{{ csrf_token() }}' // Include CSRF token for security
            },
            // Ensure this route fetches the latest sublist data
            success: function (data) {
                // Clear the current table body
                $('#employeeTable tbody').empty();

                // Populate the table with new data
                data.forEach(function (item, index) {

                    var newRow = `
                            <tr id="row-${$s_no++}">
                                <td>${serialNumber++}</td>
                                <td>${item.proof_type || 'N/A'}</td>
                                <td>${item.proof_no || 'N/A'}</td>
                                <td>${item.lic_exp_date || 'N/A'}</td>
                                <td>
                                    <a onclick="imageViews('${item.proof_name}')"><img src='{{ asset("public/uploads/download.png")}}' width="40px" height="40px"></a>
                                </td>
                                <td>
                                    <a class="text-primary" title="Edit" onclick="editDocument(${item.id}, '${item.proof_type}', '${item.proof_no}', '${item.lic_exp_date}','${item.proof_name}')">
                                     <i class="fas fa-edit"></i></a>
                                     <a href="#" class="text-danger" title="Delete" onclick="deleteRecord(${item.id})"><i class="fas fa-trash-alt"></i></a>
                                </td>
                            </tr>
                        `;
                    $('#employeeTable tbody').append(newRow);
                });
            },
            error: function (xhr) {
                console.log(xhr.responseText); // Log any errors for debugging
            }
        });
    }

    function submitForm(event) {

        // alert('yes');
        event.preventDefault(); // Prevent the default form submission

        var isValid = true; // Flag to track form validity

        // Clear any previous error messages
        $('.error-message').remove();

        // Check specific fields for required validation
        var requiredFields = [
            '#fullName',
            '#employee_id',
            '#dob',
            '#blood_grp',
            '#aadhaar_no',   // Add only the fields you want to be required
            '#email',
            '#department',
            '#designation',
            '#login_id',
            '#mobile'
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
        // Loop through required fields and validate them
        // requiredFields.forEach(function (field) {
        //     var input = $(field);
        //     if (input.val() === '') {
        //         isValid = false;
        //         input.after('<div><span class="error-message" style="color: red;">This field is required.</span></div>');
        //     }
        // });

        // If the form is not valid, stop the submission
        if (!isValid) {
            return false;
        }

        var password = $('#password').val();
        var confirm_pass = $('#confirm_password').val();

        if (password != '' && confirm_pass != '') {
            if (password != confirm_pass) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Password and Confirm Password should be same',
                    confirmButtonText: 'OK'
                })
                return false;
            }
        }

        const regex1 = /^[2-9][0-9]{3}[0-9]{4}[0-9]{4}$/;
        const aadharNumber = $('#aadhaar_no').val();

        const regex2 = /^[789]\d{9}$/;
        const mobileNumber = $('#mobile').val();


        // Collect main form data
        var formData = new FormData(document.getElementById('emp_registration'));

        // Hash the password and append it to formData
        // var password = $('#password').val();
        // formData.append('hash_password', hashPassword(password)); // Assuming hashPassword is a function that hashes the password
        // var confirm_pass = $('#confirm_password').val();
        // formData.append('hash_confirm_pass', hashPassword(confirm_pass)); // Assuming hashPassword is a function that hashes the password

        // Send the data using AJAX
        if (regex1.test(aadharNumber) && regex2.test(mobileNumber)) {
            $.ajax({
                url: '{{ route('employee_registration_update', $employeeregistration->id) }}', // Adjust this if necessary
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
                            window.location.href = "{{ route('employee_registration')}}";

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
        } else if (!(regex1.test(aadharNumber))) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Enter the Valid Aadhar Number',
                confirmButtonText: 'OK'
            })
        } else if (!(regex2.test(mobileNumber))) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Enter the Valid Mobile Number',
                confirmButtonText: 'OK'
            })
        }
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
                    url: '{{ route('employee_registration.markAsDeleted', ':id') }}'.replace(':id', id),
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
                            // $('#example').DataTable().ajax.reload();
                            // Optionally, remove the row from the table
                            $('#row-' + id).remove();
                            // Reload the Document Details tab
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

    function editDocument(id, proof_type, proof_no, lic_exp_date,proof_name) {
        // alert(proof_name);
        // Set the certificate type dropdown
        const licExpDateLabel = document.getElementById('lic_label');
        const licExpDateInput = document.getElementById('lic_exp_date');

        if(proof_type =='Driving Licence'){
            licExpDateLabel.style.display = 'block'; // Show the label
            licExpDateInput.style.display = 'block';
        }else if(proof_type =='Aadhaar'){
            licExpDateLabel.style.display = 'none'; // Show the label
            licExpDateInput.style.display = 'none';
        }
        $('#idproof').val(proof_type);
        $('#proof_no').val(proof_no);
        $('#lic_exp_date').val(lic_exp_date);

    }

    function imageViews(fileName) {
        //   alert(fileName);
        // Construct the URL for the file
        var fileUrl = `../public/uploads/employee_registration/${fileName}`; // Update this path as needed
        // alert(fileUrl);

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

    function proof_type() {
    const aadhaarInput = document.getElementById('aadhaar_no');
    const proofNoInput = document.getElementById('proof_no');
    const licExpDateLabel = document.getElementById('lic_label');
    const licExpDateInput = document.getElementById('lic_exp_date');

    const idProofSelect = document.getElementById('idproof');

    // Check if Aadhaar is selected
    if (idProofSelect.value === 'Aadhaar') {
        proofNoInput.value = aadhaarInput.value; // Set the proof_no value to the Aadhaar number
        proofNoInput.readOnly = true; // Set the input to readonly
        licExpDateLabel.style.display = 'none'; // Hide the label
        licExpDateInput.style.display = 'none'; // Hide the input
    } else {
        proofNoInput.value = ''; // Clear the proof_no field if other option is selected
        proofNoInput.readOnly = false; // Make it editable
        licExpDateLabel.style.display = 'block'; // Show the label
        licExpDateInput.style.display = 'block'; // Show the input
    }
}

// Add event listener to the dropdown
document.getElementById('idproof').addEventListener('change', proof_type);


    document.getElementById('aadhaar_no').addEventListener('input', function () {
        const loginIdInput = document.getElementById('login_id');
        loginIdInput.value = this.value; // Update the login_id field with the Aadhaar number
    });

    document.getElementById('aadhaar_no').addEventListener('input', function (e) {
        this.value = this.value.replace(/[^0-9]/g, ''); // Remove any non-numeric characters
    });

    function validateAadhar(input) {
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
    }

    document.getElementById('mobile').addEventListener('input', function (e) {
        this.value = this.value.replace(/[^0-9]/g, ''); // Remove any non-numeric characters
    });

    document.getElementById('authority').addEventListener('input', function (e) {
        this.value = this.value.replace(/[^a-zA-Z\s]/g, ''); // Allow letters and spaces
    });

    document.getElementById('address').addEventListener('input', function (e) {
        this.value = this.value.replace(/[^a-zA-Z0-9\/,. ]/g, ''); // Allow letters, numbers, /, ,, ., and spaces
    });

    document.getElementById('area').addEventListener('input', function (e) {
        this.value = this.value.replace(/[^a-zA-Z\s]/g, ''); // Allow letters and spaces
    });

    document.getElementById('employee_id').addEventListener('input', function (e) {
        this.value = this.value.replace(/[^a-zA-Z0-9\ ]/g, ''); // Allow letters, numbers, /, ,, ., and spaces
    });

    function validateMobile(input) {
        // Define the regex pattern for Indian mobile numbers (10 digits starting with 7, 8, or 9)
        const regex = /^[789]\d{9}$/;
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

    function togglePasswordVisibility(inputId, icon) {
        const inputField = document.getElementById(inputId);

        if (inputField.type === 'password') {
            inputField.type = 'text';
            icon.classList.add('fa-eye');
            icon.classList.remove('fa-eye-slash'); // Change to eye slash icon
        } else {
            inputField.type = 'password';
            icon.classList.add('fa-eye-slash');
            icon.classList.remove('fa-eye'); // Change back to eye icon
        }
    }

    function validateEmail(input) {
        // Define the regex pattern for a valid email
        const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        const email = input.value;
        const errorMessage = document.getElementById('email_error');

        // Clear any previous error message
        errorMessage.style.display = 'none';
        errorMessage.textContent = '';

        // Check if the email is valid
        if (!regex.test(email)) {
            // Show error for invalid email
            errorMessage.style.display = 'block';
            errorMessage.textContent = 'Invalid email format. Please enter a valid email address.';
        }
    }

    function validatePassword() {
        const password = document.getElementById('password').value;
        const errorMessage = document.getElementById('passwordError');

        // Regular expression for password validation
        const isValid = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+=[\]{};':"\\|,.<>?~`-])[A-Za-z\d!@#$%^&*()_+=[\]{};':"\\|,.<>?~`-]{8,}$/.test(password);

        if (isValid || password == '') {
            errorMessage.style.display = 'none'; // Hide error message
        } else {
            errorMessage.style.display = 'block'; // Show error message
        }
    }

    // Attach the event listener
    document.getElementById('password').addEventListener('input', validatePassword);


</script>

@endsection