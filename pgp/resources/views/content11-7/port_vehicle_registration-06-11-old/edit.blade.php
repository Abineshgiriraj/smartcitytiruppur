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

$acc_year = academic_year();

@endphp

<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">User Firms/</span>Edit Port Vehicle Registration</h4>
    </div>
</div>


<div class="card">
    <div class="card-body">
        <form id="usertypeForm" method="post" onsubmit="return submitForm(event);">
            @csrf
            @if(isset($user_types)) @method('PUT') @endif
            <div id="basicwizard">
                <ul class="nav nav-pills nav-justified form-wizard-header mb-4">
                    <li class="nav-item">
                        <a href="#vehiclereg" data-bs-toggle="tab" class="nav-link rounded-0 pt-2 pb-2 active">
                            Vehicle Details
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#document" data-bs-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                            Document Details
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#basictab3" data-bs-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                            Authentication
                        </a>
                    </li>
                </ul>

                <div class="tab-content b-0 mb-0">
                    <!-- Vehicle Details Tab -->
                    <div class="tab-pane active" id="vehiclereg">
                        <div class="row">
                            <!-- Vehicle Registration Number -->
                            <!-- <div class="col-md-6">
                                <div class="mb-3 d-flex align-items-center">
                                    <label class="form-label me-3" for="vech_reg_no" style="width: 250px;">Vehicle
                                        Registration Number:</label>
                                    <input type="hidden" id="screenUniqueId" name="screenUniqueId"
                                        value="{{$vehicle->screenUniqueId}}" />
                                    <input type="text" class="form-control" id="vech_reg_no" name="vech_reg_no"
                                        value="{{$vehicle->vech_reg_no}}" />

                                </div>
                            </div> -->

                            <div class="col-sm-2">
                                <label for="vech_reg_no" class="col-form-label">Vehicle Number:</label>
                            </div>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="vech_reg_no" id="vech_reg_no"
                                value="{{$vehicle->vech_reg_no}}" oninput="appendValue(this)">
                                <input type="hidden" id="screenUniqueId" name="screenUniqueId" value="{{$vehicle->screenUniqueId}}"/>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3 d-flex align-items-center">
                                    <label class="form-label me-3" for="vech_reg_no" style="width: 250px;">Vehicle ID:</label>
                                    <label class="form-label me-3" for="vech_reg_no" style="width: 250px;">{{$vehicle->vehicle_id}}</label>
                                    <!-- <input type="hidden" id="screenUniqueId" name="screenUniqueId"
                                        value="{{$vehicle->screenUniqueId}}" />
                                    <input type="text" class="form-control" id="vech_reg_no" name="vech_reg_no"
                                        value="{{$vehicle->vech_reg_no}}" /> -->
                                </div>
                            </div>

                            <!-- Vehicle Type -->
                            <div class="col-md-6 mt-3">
                                <div class="mb-3 d-flex align-items-center">
                                    <label class="form-label me-3" for="vehicle_type" style="width: 250px;">Vehicle
                                        Type:</label>
                                    <select name="vehicle_type" id="vehicle_type" class="form-select">
                                        <option value="">Select Vehicle</option>
                                        <option value="Heavy" {{ $vehicle->vehicle_type == 'Heavy' ? 'selected' : '' }}>
                                            Heavy</option>
                                        <option value="Light" {{ $vehicle->vehicle_type == 'Light' ? 'selected' : '' }}>
                                            Light</option>
                                        <option value="Two-Wheeler" {{ $vehicle->vehicle_type == 'Two-Wheeler' ? 'selected' : '' }}>Two-Wheeler</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Owner's Name -->
                            <!-- <div class="col-md-6">
                                <div class="mb-3 d-flex align-items-center">
                                    <label class="form-label me-3" for="owner_name" style="width: 250px;">Owner's
                                        Name:</label>
                                    <input type="text" class="form-control" id="owner_name" name="owner_name"
                                        value="{{$vehicle->owner_name}}" />
                                </div>
                            </div> -->

                            <div class="col-sm-2 mt-3">
                                <label for="owner_name" class="col-form-label">Owner's Name:</label>
                            </div>
                            <div class="col-sm-4 mt-3">
                                <input type="text" class="form-control" name="owner_name" id="owner_name"
                                value="{{$vehicle->owner_name}}" oninput="validateName()">
                                <small id="ownerNameError" class="text-danger"></small>
                            </div>

                            <!-- Contact Number -->
                            <!-- <div class="col-md-6">
                                <div class="mb-3 d-flex align-items-center">
                                    <label class="form-label me-3" for="contact_number" style="width: 250px;">Contact
                                        Number:</label>
                                    <input type="text" class="form-control" id="contact_number" name="contact_number"
                                        value="{{$vehicle->contact_number}}" />
                                </div>
                            </div> -->

                            <div class="col-sm-2 mt-3">
                                <label for="contact_number" class="col-form-label">Contact Number:</label>
                            </div>
                            <div class="col-sm-4 mt-3">
                                <input type="text" class="form-control" name="contact_number" value="{{$vehicle->contact_number}}"
                                id="contact_number" onchange="validateMobile(this)" maxlength="10" />
                                <span id="mobile_error" class="error-message" style="color: red; display: none;"></span>
                            </div>

                            <div class="col-md-6 mt-3">
                                <div class="mb-3 d-flex align-items-center">
                                    <label class="form-label me-3" for="acc_year" style="width: 250px;">Academic Year</label>
                                    <select class="form-select" name="acc_year" id="acc_year" readonly>
                                        @foreach ($acc_year as $acc)
                                            <option value="{{$acc->unique_id}}"
                                            {{$acc->unique_id == $vehicle->acc_year ? 'selected' : ''}}>{{$acc->acc_year}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12 text-end">
                                <button type="button" class="btn btn-primary"
                                    onclick="$('.nav-pills a[href=\'#document\']').tab('show')">Next</button>
                            </div>
                        </div>
                    </div>

                    <!-- Document Details Tab -->
                    <div class="tab-pane" id="document">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3 d-flex align-items-center">
                                    <label class="form-label me-3" style="width: 250px;">Certificate Type:</label>
                                    <select class="form-select" onchange="change()" name="certificate_type"
                                        id="certificate_type">
                                        <option value="selected">Select</option>
                                        <option value="Fitness">Fitness</option>
                                        <option value="Insurance">Insurance</option>
                                        <option value="PUC">PUC</option>
                                        <option value="RC">RC</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div id="fitness" style="display:none;">
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="mb-3 mt-2 d-flex align-items-center">
                                        <label class="form-label me-3" style="width: 250px;"
                                            for="fitness_certificate">Fitness
                                            Certificate:</label>
                                        <input type="text" class="form-control" id="fitness_certificate"
                                            name="fitness_certificate" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3 mt-2 d-flex align-items-center">
                                        <label class="form-label me-3" style="width: 250px;"
                                            for="fitness_valid_to">Fitness
                                            Valid
                                            Up To:</label>
                                        <input type="date" class="form-control" id="fitness_valid_to"
                                            name="fitness_valid_to" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3 mt-2 d-flex align-items-center">
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
                                    <div class="mb-3 mt-2 d-flex align-items-center">
                                        <label class="form-label me-3" style="width: 250px;"
                                            for="insurance_details">Insurance
                                            Details:</label>
                                        <input type="text" class="form-control" id="insurance_details"
                                            name="insurance_details" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3 mt-2 d-flex align-items-center">
                                        <label class="form-label me-3" style="width: 250px;"
                                            for="insurance_valid_to">Insurance
                                            valid UpTo
                                            :</label>
                                        <input type="date" class="form-control" id="insurance_valid_to"
                                            name="insurance_valid_to" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3 mt-2 d-flex align-items-center">
                                        <label class="form-label me-3" style="width: 250px;"
                                            for="insurance_certificate">Insurance
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
                                    <div class="mb-3 mt-2 d-flex align-items-center">
                                        <label class="form-label me-3" style="width: 250px;" for="puc_certificate">PUC
                                            Certificate:</label>
                                        <input type="text" class="form-control" id="puc_certificate"
                                            name="puc_certificate" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3 mt-2 d-flex align-items-center">
                                        <label class="form-label me-3" style="width: 250px;" for="puc_valid_to">PUC
                                            Valid UP
                                            To:</label>
                                        <input type="date" class="form-control" id="puc_valid_to" name="puc_valid_to" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3 mt-2 d-flex align-items-center">
                                        <label class="form-label me-3" style="width: 250px;"
                                            for="puc_certificate_upload">PUC
                                            Certificate Upload:</label>
                                        <input type="file" class="form-control" id="puc_certificate_upload"
                                            name="puc_certificate_upload" />
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div id="rc" style="display:none;">
                            <div class="col-md-6">
                                <div class="mb-3 mt-2 d-flex align-items-center">
                                    <label class="form-label me-3" style="width: 250px;" for="rc_upload">Registration
                                        Certificate
                                        (RC) Upload:</label>
                                    <input type="file" class="form-control" id="rc_upload" name="rc_upload" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 text-end">
                            <button type="button  btn-lg" id="add" class="btn btn-primary">Add</button>

                        </div>
                        <div class="row mt-4">
                            <div id="dataTableContainer" class="table-responsive mt-4">

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
                                        @php $s_no = 1; @endphp

                                        @if ($vehicleDocument_list && $vehicleDocument_list->count() > 0)
                                            @foreach ($vehicleDocument_list as $sublist)
                                                @if (is_object($sublist))
                                                    <tr>
                                                        <td>{{ $s_no++ }}</td>
                                                        <td>{{ $sublist->certificate_type ?? 'N/A' }}</td>
                                                        <td>{{ $sublist->certificate_no ?? 'N/A' }}</td>
                                                        <td>{{ $sublist->valid_up_to ?? 'N/A' }}</td>
                                                        <td>
                                                            @if ($sublist->attached_proof)
                                                                <a onclick="imageViews('{{ $sublist->attached_proof }}')">
                                                                    <img src='../public/uploads/download.png' width="40px"
                                                                        height="40px">
                                                                </a>
                                                            @else
                                                                N/A
                                                            @endif
                                                        </td>
                                                        <td>
                                                        <a class="text-primary" title="Edit" onclick="editDocument({{ $sublist->id }}, '{{ $sublist->certificate_type }}', '{{ $sublist->certificate_no }}', '{{ $sublist->valid_up_to }}', '{{ $sublist->attached_proof }}')">
                                                        <i class="fas fa-edit"></i></a>
                                                         <a href="#" class="text-danger" title="Delete" onclick="deleteRecord({{ $sublist->id ?? 'N/A' }})">
                                                                <i class="fas fa-trash-alt"></i></a>
                                                        </td>
                                                    </tr>
                                                @endif

                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="7">No documents found.</td>
                                            </tr>
                                        @endif
                                    </tbody>


                                </table>
                            </div>
                            <div></div>
                            <div class="col-md-12 text-end">
                                <button type="button" class="btn btn-primary"
                                    onclick="$('.nav-pills a[href=\'#basictab3\']').tab('show')">Next</button>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="basictab3">
                        <div class="row">
                            <div class="row mb-3">


                                <div class="col-sm-2 mt-3">
                                    <label for="colFormLabelSm" class="col-form-label">User Login IDs:</label>
                                </div>
                                <div class="col-sm-4 mt-3">

                                    <input type="text" class="form-control" name="login_id" id="login_id"
                                        value="{{$vehicle->user_id}}" readonly>
                                </div>

                                <div class="col-sm-2 mt-3 password-container password">
                                    <label for="colFormLabelSm" class="col-form-label">Password</label>
                                </div>
                                <div class="col-sm-4 password">
                                    <div class="input-wrapper">
                                        <input type="password" class="form-control mt-3" id="password"
                                            name="password" />
                                        <i class="fas fa-eye toggle-password" id="togglePassword"
                                            onclick="togglePasswordVisibility('password', this)"></i>
                                    </div>
                                </div>

                                <div class="col-sm-2 mt-3 password-container password">
                                    <label for="colFormLabelSm" class="col-form-label">Confirm Password</label>
                                </div>

                                <div class="col-sm-4 password">
                                    <div class="input-wrapper">
                                        <input type="password" class="form-control mt-3" onkeyup="password_change()"
                                            id="confirm_password" name="confirm_password" />
                                        <i class="fas fa-eye toggle-password" id="toggleConfirmPassword"
                                            onclick="togglePasswordVisibility('confirm_password', this)"></i>
                                    </div>
                                    <small id="password_error" class="text-danger" style="display:none;">Passwords do
                                        not match</small>
                                    <small id="password_crct" class="text-success" style="display:none;">Passwords
                                        match</small>
                                </div>

                                <div class="col-sm-2 mt-3 password-container password" ;>
                                    <label for="colFormLabelSm" class="col-form-label">RF Id:</label>
                                </div>
                                <div class="col-sm-4 password" ;>
                                    <div class="input-wrapper">
                                        <input type="text" class="form-control  mt-3" id="rf_id" readonly
                                         name="rf_id" value="{{$vehicle->rf_id}}"/>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="btn-float">
                            <button type="button" class="btn btn-danger"
                                onclick="window.location='{{ route('port_vehicle_registration.index') }}'">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                        <!-- </form> -->
                    </div> <!-- End Tab 3 -->
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



    function change() {

        var fitness = $('#fitness').val();
        var insurance = $('#insurance').val();
        var puc = $('#puc').val();
        var rc = $('#rc').val();


        var selected_element = document.getElementById('certificate_type');

        var selected_value = selected_element.value;


        // var dynamic_values = $('#dynamic_values').val();



        if (selected_value === 'Fitness') {

            // alert('1');
            $('#fitness').show();
            $('#add').show();
            $('#insurance').hide();
            $('#puc').hide();
            $('#rc').hide();

        } else if (selected_value === 'Insurance') {

            $('#insurance').show();
            $('#add').show();

            $('#puc').hide();
            $('#rc').hide();
            $('#fitness').hide();

        } else if (selected_value === 'PUC') {
            $('#puc').show();
            $('#add').show();

            $('#insurance').hide();
            $('#rc').hide();
            $('#fitness').hide();

        } else if (selected_value === 'RC') {
            $('#rc').show();
            $('#add').show();

            $('#insurance').hide();
            $('#puc').hide();
            $('#fitness').hide();
        }

    }

    function pdf() {

        var url = "resources/views/content/port_vehicle_registration/testpdf1.pdf";
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
            url: '{{ route('port_vehicle_registration.store_1') }}',
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
                        text: response.message,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // const vehicle = response.vehicle;
                        // Append new document details to the table with serial number
                        $('#documentTableBody').append(`
                            <tr id="row-${vehicle.id}">
                                <td>${serialNumber++}</td>
                                <td>${vehicle.certificate_type || 'N/A'}</td>
                                <td>${vehicle.certificate_no || 'N/A'}</td>
                                <td>${vehicle.valid_up_to || 'N/A'}</td>
                                <td>
                                    <a onclick="imageViews('${file_name}')"><img src='public/uploads/download.png' width="40px" height="40px"></a>
                                </td>
                                <td>
                                <a class="text-primary" title="Edit"  onclick="editDocument(${vehicle.id}, '${vehicle.certificate_type}', '${vehicle.certificate_no}', '${vehicle.valid_up_to}')">
                                       <i class="fas fa-edit"></i></a>
                                  <a href="#" class="text-danger" title="Delete" onclick="deleteRecord(${vehicle.id})">
                                       <i class="fas fa-trash-alt"></i></a>
                                </td>
                            </tr>
                        `);

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


    // Function to remove a row from the table
    function removeRow(button) {
        const row = button.closest('tr');
        row.parentNode.removeChild(row);
    }


    function submitForm(event) {

        // alert('yes');
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
            '#acc_year'
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

        // Collect main form data
        var formData = new FormData(document.getElementById('usertypeForm'));

        var acc_year = $('#acc_year').val();
        // alert(acc_year);

         // Hash the password and append it to formData
        var password = $('#password').val();
        formData.append('hash_password', hashPassword(password)); // Assuming hashPassword is a function that hashes the password
        var confirm_pass = $('#confirm_password').val();
        formData.append('hash_confirm_pass', hashPassword(confirm_pass)); // Assuming hashPassword is a function that hashes the password

        // Send the data using AJAX
        $.ajax({
            url: '{{ route('port_vehicle_registration.update', $vehicle->id) }}', // Adjust this if necessary
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
                        window.location.href = "{{ route('port_vehicle_registration.index')}}";

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
    }

    // Example of a simple hashing function (you should use a stronger hashing algorithm)
    function hashPassword(password) {
        // Hash the password (use a proper hashing method in a real scenario)
        return btoa(password); // Base64 encoding as an example (not secure for real passwords)
    }



    function imageViews(fileName) {
        //   alert(fileName);
        // Construct the URL for the file
        var fileUrl = `public/uploads/port_vehicle_registration/${fileName}`; // Update this path as needed

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
                    url: '{{ route('port_vehicle_registration.markAsDeleted', ':id') }}'.replace(':id', id),
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
                            $('#example').DataTable().ajax.reload();
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

function appendValue(element) {
        // Get the value from the input that triggered the function
        var vehicle_no = $(element).val().toUpperCase();
        $(element).val(vehicle_no);

        // Get the current value of login_id
        var login_id = $('#login_id').val();

        // Append the vehicle_no to the login_id value
        login_id = vehicle_no; // Append the values as a string

        // Optionally, set the new value back to login_id (if needed)
        $('#login_id').val(login_id);

        // Example action: log the new value
        console.log('New login_id value:', login_id);
    }

function validatePassword() {
const passwordField = document.getElementById('password');
const passwordErrors = document.getElementById('passwordErrors');
const regex = /^(?=.[a-z])(?=.[A-Z])(?=.\d)(?=.[@$!%?&])[A-Za-z\d@$!%?&]{8,}$/;

if (!regex.test(passwordField.value)) {
    passwordErrors.textContent =
        "Password must be at least 8 characters long, with uppercase, lowercase, digit, and special character.";
} else {
    passwordErrors.textContent = "";
}
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
</script>


@endsection
