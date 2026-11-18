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
</style>

<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">User Firms/</span>Vehicle Registration</h4>
    </div>
</div>



<div class="card">
    <div class="card-body">
        <form id="usertypeForm" method="post" onsubmit="return submitForm(event);">
            @csrf
            <meta name="csrf-token" content="{{ csrf_token() }}">

            @if(isset($user_types))
            @method('PUT')
            @endif

            <input type="hidden" id="vehicle" value ="{{$vehicle->id}}">
            <input type="hidden" id="vehicle_sublist" value ="{{$vehicleDocument->id}}">



            <div id="basicwizard">
            <meta name="csrf-token" content="{{ csrf_token() }}">

                <ul class="nav nav-pills nav-justified form-wizard-header mb-4">
                    <li class="nav-item">
                        <a href="#vehiclereg" data-bs-toggle="tab" class="nav-link rounded-0 pt-2 pb-2 active">
                            <span class="d-sm-inline">Vehicle Details</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#document" data-bs-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                            <span class="d-sm-inline">Document Details</span>
                        </a>
                    </li>
                </ul>


                <div class="tab-content b-0 mb-0">
                    <div class="tab-pane active" id="vehiclereg">


                        <div class="row">
                            <!-- Vehicle Registration Number -->
                            <div class="col-md-6">
                                <div class="mb-3 mt-2 d-flex align-items-center">
                                    <label class="form-label me-3" style="width: 250px;" for="vech_reg_no">Vehicle
                                        Registration
                                        Number:</label>
                                    <input type="text" class="form-control" id="vech_reg_no" name="vech_reg_no" value="{{$vehicle->vech_reg_no}}" />
                                    <input type="hidden" class="form-control" id="screenUniqueId" name="screenUniqueId" value="{{$vehicle->screenUniqueId}}" />
                                </div>
                            </div>

                            <!-- Vehicle Type -->
                            <div class="col-md-6">
                                <div class="mb-3 mt-2 d-flex align-items-center">
                                    <label class="form-label me-3" style="width: 250px;" for="status">Vehicle
                                        Type:</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="Heavy" >Heavy</option>
                                        <option value="Light">Light</option>
                                        <option value="Two-Wheeler">Two-Wheeler</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Owner's Name -->
                            <div class="col-md-6">
                                <div class="mb-3 mt-2 d-flex align-items-center">
                                    <label class="form-label me-3" style="width: 250px;" for="owner_name">Owner's
                                        Name:</label>
                                    <input type="text" class="form-control" id="owner_name" name="owner_name" value="{{$vehicle->owner_name}}"/>
                                </div>
                            </div>

                            <!-- Contact Number -->
                            <div class="col-md-6">
                                <div class="mb-3 mt-2 d-flex align-items-center">
                                    <label class="form-label me-3" style="width: 250px;" for="contact_number">Contact
                                        Number:</label>
                                    <input type="text" class="form-control" id="contact_number" name="contact_number" value="{{$vehicle->owner_name}}"/>
                                </div>
                            </div>

                        </div>


                        <div class="row">
                            <div class="btn-float">
                                <button type="button" class="btn btn-primary"
                                    onclick="$('.nav-pills a[href=\'#document\']').tab('show')">Next</button>
                            </div>
                        </div>
                    </div>
                    <!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->

                    <!-- documment tab -->
                    <div class="tab-pane" id="document">

                        <!-- Fitness Certificate Upload -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3 mt-2 d-flex align-items-center">
                                <label class="form-label me-3" style="width:250px;">Certificate Type</label>
        <select class="form-select" onchange="change()" name="certificate_type" id="certificate_type">
            <option value="selected" disabled>Select</option>
            <option value="Fitness" {{ $vehicleDocument->certificate_type == 'Fitness' ? 'selected' : '' }}>Fitness</option>
            <option value="Insurance" {{ $vehicleDocument->certificate_type == 'Insurance' ? 'selected' : '' }}>Insurance</option>
            <option value="PUC" {{ $vehicleDocument->certificate_type == 'PUC' ? 'selected' : '' }}>PUC</option>
            <option value="RC" {{ $vehicleDocument->certificate_type == 'RC' ? 'selected' : '' }}>RC</option>
        </select>
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
                                                name="fitness_valid_to" value="{{$vehicleDocument->valid_up_to}}"/>
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
                                                name="insurance_valid_to" value="{{$vehicleDocument->valid_up_to}}"/>
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
                                            <label class="form-label me-3" style="width: 250px;"
                                                for="puc_certificate">PUC
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
                                            <input type="date" class="form-control" id="puc_valid_to"
                                                name="puc_valid_to" value="{{$vehicleDocument->valid_up_to}}"/>
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
                                        <label class="form-label me-3" style="width: 250px;"
                                            for="rc_upload">Registration
                                            Certificate
                                            (RC) Upload:</label>
                                        <input type="file" class="form-control" id="rc_upload" name="rc_upload" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 text-end">
                                <button type="button  btn-lg" id="add" class="btn btn-primary" >Add</button>

                            </div>
                        </div>
                        <div class="row">

                        </div>

                        <div class="row mt-4">
                            <div id="dataTableContainer" class="table-responsive mt-4">
                                <table class="table mt-3" id="documentTable">
                                    <thead>
                                        <tr>
                                            <th>S.No</th>
                                            <th>Certificate Type</th>
                                            <th>Certificate Number</th>
                                            <th>Valid Up To</th>
                                            <th>Certificate</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="documentTableBody">
                                        <!-- Rows will be dynamically added here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="basictab3">
                        <div class="row">
                            <div class="row mb-3">


                                <div class="col-sm-2 mt-3">
                                    <label for="colFormLabelSm" class="col-form-label">User Login IDs:</label>
                                </div>
                                <div class="col-sm-4 mt-3">

                                    <input type="text" class="form-control" name="login_id" id="login_id">
                                </div>
                                <div class="col-sm-2 mt-3 password-container password" ;>
                                    <label for="colFormLabelSm" class="col-form-label">Password</label>
                                </div>
                                <div class="col-sm-4 password" ;>
                                    <div class="input-wrapper">
                                        <input type="password" class="form-control  mt-3" id="password"
                                            name="password" />
                                        <!-- <i class="fas fa-eye toggle-password" id="togglePassword"></i> -->
                                    </div>
                                </div>

                                <div class="col-sm-2 mt-3 password-container password" ;>
                                    <label for="colFormLabelSm" class="col-form-label">Confirm Password</label>
                                </div>
                                <div class="col-sm-4 password" ;>
                                    <div class="input-wrapper">
                                        <input type="password" class="form-control  mt-3" onkeyup="password_change()"
                                            id="confirm_password" name="confirm_password" />

                                        <!-- <i class="fas fa-eye toggle-password" id="toggleConfirmPassword"></i> -->

                                    </div>
                                    <small id="password_error" class="text-danger" style="display:none;">Passwords
                                        do not match</small>
                                    <small id="password_crct" class="text-success" style="display:none;">Passwords
                                        match</small>
                                </div>

                                <div class="col-sm-2 mt-3 password-container pin" style="display:none" ;>
                                    <label for="colFormLabelSm" class="col-form-label">Pin</label>
                                </div>
                                <div class="col-sm-4 pin" style="display:none" ;>
                                    <div class="input-wrapper">
                                        <input type="password" class="form-control  mt-3" id="pin" name="pin" />
                                        <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                                    </div>
                                </div>

                                <div class="col-sm-2 mt-3 password-container pin" style="display:none" ;>
                                    <label for="colFormLabelSm" class="col-form-label">Confirm Pin</label>
                                </div>
                                <div class="col-sm-4 pin" style="display:none" ;>
                                    <div class="input-wrapper">
                                        <input type="password" class="form-control  mt-3" onkeyup="password_change()"
                                            id="confirm_pin" name="confirm_pin" />

                                        <i class="fas fa-eye toggle-password" id="toggleConfirmPassword"></i>

                                    </div>
                                    <small id="password_error" class="text-danger" style="display:none;">Pin
                                        do not match</small>
                                    <small id="password_crct" class="text-success" style="display:none;">Pin
                                        match</small>
                                </div>

                            </div>
                        </div>
                        <div class="btn-float">
                            <button type="button" class="btn btn-danger"
                                onclick="window.location='{{ route('vehicle_registration') }}'">Cancel</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <!-- onclick="window.location='{{ route('user_registration_data') }}'" -->
                        </div>
                        <!-- </form> -->
                    </div> <!-- End Tab 3 -->
                        <div class=" row">
                            <div class="btn-float">
                                <button type="button" class="btn btn-danger"
                                    onclick="window.location.href='{{ route('vehicle_registration') }}'">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
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
 function generateUniqueId() {
    return Date.now() + Math.floor(Math.random() * 1000);
}

$(document).ready(function() {
    // Generate a unique ID when the form loads
    var screenUniqueId = generateUniqueId();
    $('#screenUniqueId').val(screenUniqueId);

    // Set the unique ID in the hidden input field
});

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

        var url = "resources/views/content/vehicle_registration/testpdf1.pdf";
        window.location.href = url;
    }

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });



    let serialNumber = 1;

$('#add').on('click', function(event) {
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
        success: function(response) {
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
                                    <button type="button" class="btn btn-danger" onclick="deleteRecord(${vehicle.id})">
                                        <i class="fas fa-trash"></i> <!-- FontAwesome trash icon -->
                                    </button>
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
        error: function(xhr) {
            var errors = xhr.responseJSON.errors;
            if (errors) {
                $.each(errors, function(key, value) {
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
    event.preventDefault(); // Prevent the default form submission

    // Collect main form data
    var formData = new FormData(document.getElementById('usertypeForm'));


    // Send the data using AJAX
    $.ajax({
    url: '{{ route('vehicle_registration.store') }}',
    method: 'POST',
    data: formData,
    contentType: false,
    processData: false,
    success: function(response) {
        // Handle the response
        if (response.message) {
            // Show success message (SweetAlert or any other method)
            Swal.fire('Success', response.message, 'success').then(() => {

                // Redirect after success
                window.location.href = '{{ route('vehicle_registration') }}';
            });
        }
    },
    error: function(xhr) {
        // Handle the error
        var errors = xhr.responseJSON.errors;
        if (errors) {
            $.each(errors, function(key, value) {
                alert(value[0]); // Display the first error message
            });
        } else {
            alert('An error occurred while saving. Please try again.');
        }
    }
});

}
function imageViews(fileName) {
//   alert(fileName);
    // Construct the URL for the file
    var fileUrl = `public/uploads/vehicle_registration/${fileName}`; // Update this path as needed

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

</script>


@endsection
