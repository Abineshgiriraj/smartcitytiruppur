@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
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
</style>
@php
$get_academic_year = academic_year();
$get_firms_registration = get_firms();
@endphp
<!-- Basic Layout -->
<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-body">
                <div id="basicwizard">
                    <ul class="nav nav-pills nav-justified form-wizard-header ">
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
                            <form id="firmsForm">
                                @csrf

                                <div class="row mb-3">
                                    <label for="acc_year" class="col-sm-2 col-form-label">Academic Year</label>
                                    <div class="col-sm-4">
                                        <select class="form-select" name="academic_year" id="academic_year">
                                            <option value="">Select Academic Year</option>

                                            @foreach ($get_academic_year as $acc_year)
                                            <option value="{{$acc_year->unique_id}}">{{$acc_year->acc_year}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-2 mt-3">
                                        <label class="col-form-label" for="firm_id">Firm ID:</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-12 col-form-label mt-3 firm-id-label">
                                            <!-- <b id="firm_id">{{isset($firm_id) ? $firm_id : 'Loading...' }}</b> -->
                                        </label>
                                        <input type="hidden" id="hidden_firm_id" name="firm_id" value="">
                                    </div>

                                    <div class="col-sm-2 mt-3">
                                        <label for="nationalitySelect" class="col-form-label">Type of Firms /
                                            Agent</label>
                                    </div>
                                    <div class="col-sm-4 mt-3">
                                        <select class="form-select" id="firms" name="firms">
                                            <option selected>Select Firms</option>
                                            @foreach ($get_firms_registration as $get_firms)
                                            <option value="{{$get_firms->unique_id}}">{{$get_firms->company_name}}
                                            </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" id="firm_id" name="firm_id"
                                            value="{{$get_firms->unique_id}}">
                                    </div>
                                  


                                   

                                    <div class="col-sm-2 mt-3">
                                        <label for="colFormLabelSm" class="col-form-label">Company Name</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control mt-3" id="name" name="name"
                                            value="{{ old('name', $firm->name ?? '') }}" />
                                    </div>
                                    <div class="col-sm-2 mt-3">
                                        <label for="colFormLabelSm" class="col-form-label">Company Reg No.</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control mt-3" id="company_reg_no"
                                            name="company_reg_no" />
                                    </div>
                                    <div class="col-sm-2 mt-3">
                                        <label for="colFormLabelSm" class="col-form-label">Contact Person</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control mt-3" id="contact_person"
                                            name="contact_person" />
                                    </div>
                                    <div class="col-sm-2 mt-3">
                                        <label for="colFormLabelSm" class="col-form-label">Contact Person Aadhar No</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control mt-3" id="aadhar_no"
                                            oninput="fetchAadharToLoginID()" name="aadhar_no" maxlegth="12">
                                            <span id="aadhar_no_error" class="text-danger"></span>
                                    </div>
                                    <div class="col-sm-2 mt-3">
                                        <label for="colFormLabelSm" class="col-form-label">Mobile no</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" oninput="valid_mobile_number(this)" class="form-control mt-3"
                                            id="mobile" name="mobile" />
                                        <!-- <div id="mobileError" class="text-danger"></div> -->
                                    </div>

                                    <div class="col-sm-2 mt-3">
                                        <label for="colFormLabelSm" class="col-form-label">Email Id</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="email" class="form-control mt-3" oninput="mail_valid(this)"
                                            id="email" name="email" />
                                    </div>
                                
                                    <div class="col-sm-2 mt-3">
                                        <label for="colFormLabelSm" class="col-form-label">Phone No</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control mt-3" id="fax_no" name="fax_no"  maxlength="10"/>
                                        <span id="fax_no_error" class="text-danger"></span>
                                    </div>
                                    <div class="col-sm-2 mt-3">
                                        <label for="colFormLabelSm" class="col-form-label">GSTIN</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control mt-3" id="gstin" name="gstin" />
                                        <span id="gstin_error" class="text-danger"></span>
                                    </div>

                                    <div class="col-sm-2 mt-3">
                                        <label for="colFormLabelSm" class="col-form-label">Address 1</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <textarea class="form-control mt-3" id="address_1" name="address_1"
                                            rows="1"></textarea>
                                    </div>

                                    <div class="col-sm-2 mt-3">
                                        <label for="colFormLabelSm" class="col-form-label">Address 2</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <textarea class="form-control mt-3" id="address_2" name="address_2"
                                            rows="1"></textarea>
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

                                    <label for="idproofSelect" class="col-sm-2 col-form-label">Type of ID proof</label>
                                    <div class="col-sm-4 fm" onchange="proof_change()">
                                        <select class="form-select" id="idproofSelect" name="idproof">
                                            <option selected>Select ID Proof</option>
                                            <!-- <option value="aadhaar">Aadhaar</option> -->
                                            <option value="company">Company Licence</option>
                                        </select>
                                    </div>

                                    <!-- <label for="colFormLabelSm" class="col-sm-2 col-form-label">Aadhar No.</label>
                                    <div class="col-sm-4 fm">
                                        <input type="text" class="form-control" id="adhaar" name="adhaar" />
                                    </div>

                                    <label for="colFormLabelSm" class="col-sm-2 col-form-label mt-3">ID Proof</label>
                                    <div class="col-sm-4 fm">
                                        <input type="file" class="form-control" id="id_proof" name="id_proof" />
                                    </div>

                                </div> -->
                                    <div id="aadhar_id" style="display:none;" class="row mb-3 mt-3"> <label
                                            for="colFormLabelSm" class="col-sm-2 col-form-label">
                                            Aadhar
                                            No.
                                        </label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" id="aadhaar_num"
                                                name="aadhaar_num" />
                                        </div>


                                        <label for="colFormLabelSm" class="col-sm-2 col-form-label">ID
                                            Proof</label>
                                        <div class="col-sm-4">
                                            <input type="file" class="form-control" id="aadhaar_license"
                                                name="aadhaar_license" />
                                        </div>
                                    </div>


                                    <!-- <div id="licence_id" style="display:none;"> -->

                                    <div id="licence_id" style="display:none;" class="row mb-3 mt-3">
                                        <label for="licence" class="col-sm-2 col-form-label">Company Licence No.</label>
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
                                                <th>Licence No.</th>
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
                                            <input type="text"  readyonly class="form-control" name="login_id" id="login_id">

                                        </div>
                                        <div class="col-sm-2 mt-3 password-container">
                                            <label for="colFormLabelSm" class="col-form-label">Password</label>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="input-wrapper">
                                                <input type="password" class="form-control  mt-3" id="password"
                                                    name="password" oninput="valid_password(this)"
                                                    onkeyup="password_change()" />
                                                <i class="fas fa-eye-slash toggle-password" id="togglePassword"></i>
                                            </div>
                                            <span id="passwordError" style="color: red;"></span>
                                        </div>

                                        <div class="col-sm-2 mt-3 password-container">
                                            <label for="colFormLabelSm" class="col-form-label">Confirm Password</label>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="input-wrapper">
                                                <input type="password" class="form-control  mt-3"
                                                    onkeyup="password_change()" id="confirm_password"
                                                    name="confirm_password" />

                                                <i class="fas fa-eye-slash toggle-password" id="toggleConfirmPassword"></i>

                                            </div>
                                            <span id="passwordError" style="color: red;"></span>
                                            <small id="password_error" class="text-danger"
                                                style="display:none;">Passwords
                                                do not match</small>
                                            <small id="password_crct" class="text-success"
                                                style="display:none;">Passwords
                                                match</small>
                                        </div>
                            </form>
                        </div>


                        <div class="btn-float">
                            <button type="button" class="btn btn-danger"
                                onclick="window.location.href='{{ url('/firms_registration_list')}}'">Cancel</button>
                            <!-- <button type="submit" class="btn btn-primary">Submit</button> -->
                            <button type="button" class="btn btn-primary" onclick="authentication()">Submit</button>
                        </div>

                    </div>

                </div>
                </form>
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

togglePassword.addEventListener('click', function() {
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

toggleConfirmPassword.addEventListener('click', function() {
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
    var formData = new FormData();

    // Append form fields to the FormData object
    formData.append('academic_year', $('#academic_year').val());
    formData.append('firms', $('#firms').val());
    formData.append('firm_id', $('#hidden_firm_id').val());
    formData.append('company_reg_no', $('#company_reg_no').val());
    formData.append('name', $('#name').val());
    formData.append('aadhar_no', $(
        '#aadhar_no').val());
    formData.append('mobile', $('#mobile').val());
    formData.append('email', $(
        '#email').val());
    formData.append('contact_person', $('#contact_person').val());
    formData.append(
        'fax_no', $('#fax_no').val());
    formData.append('gstin', $('#gstin').val());
    formData.append('address_1',
        $('#address_1').val());
    formData.append('address_2', $('#address_2').val());
    formData.append('_token',
        '{{csrf_token()}}');

    // var aadhaar = $('#aadhaar_no').val();

    // // var validationError = $('#')

    // const isValidAadhaar = /^\d{12}$/.test(aadhaar);

    // if (!isValidAadhaar) {
    //     let validationError = '';

    //     if (aadhaar.length != 12) {
    //         validationError += 'Aadhaar Number must be exactly 12 characters long.\n';
    //     }
    //     if (/[^\d]/.test(aadhaar)) {
    //         validationError += 'Aadhaar Number must contain only numbers.\n';
    //     }

    //     sweetalert("aadhaar", validationError);
    //     return;
    // }
    $.ajax({
        type: "POST",
        url: "{{route('firms_registration.store')}}",
        data: formData,
        contentType: false, // Important for sending FormData
        processData: false, // Prevent jQuery from processing the data
        success: function(response) {

            if (response.unique_id) {
                var unique_id = response.unique_id;



                $('#main_unique_id').val(unique_id);


            } else {
                alert('Unique ID not returned');
            }
            document.querySelector('#tab2-link').click();
            sweetalert("create");
        },
        error: function(xhr) {
            console.log(xhr.responseText);
        }
    });

}



$(document).ready(function() {

    // Fetch the last firm number when the document is ready
    $.ajax({
        url: '{{ url("/get-last-firm") }}',
        method: 'GET',
        success: function(response) {
            // Update the Firm ID label with the new Firm ID
            $('#firm_id').text(response.firm_id);
            $('#hidden_firm_id').val(response.firm_id);
        },
        error: function(xhr, status, error) {
            console.error("Error fetching Firm ID:", error);
            $('#firm_id').text('Error loading Firm ID');
        }
    });
});

$(document).ready(function() {
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
            success: function(response) {
                // Update the Firm ID label with the new Firm ID
                $('#firm_id').text(response.firm_id);
                $('#hidden_firm_id').val(response.firm_id);
            },
            error: function(xhr, status, error) {
                console.error("Error fetching Firm ID:", error);
                $('#firm_id').text('Error loading Firm ID');
            }
        });
    }
});

document.querySelectorAll('.delete-form').forEach(form => {
    form.addEventListener('submit', function(e) {
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

        success: function(response) {
            const FirmsRegistrationSub = response.FirmsRegistrationSub;
           
            var file_name = response.attached_proof;
            var newRow = `
                 <tr>
                    <td>${s_nos}</td>
                    <td>${proof_type}</td> <!-- Type of ID Proof -->
                    <td>${proof_value}</td> <!-- Aadhaar No. or Licence No. -->
                    <td>
                        <a onclick="imageViews('${file_name}', '${proof_type}')">
                            <img src='public/uploads/download.png' width="40px" height="40px">
                        </a>
                    </td>
                    <td>
                    <a class="text-primary" title="Edit"  onclick="editDocument(${response.id}, '${response.proof_type}', '${proof_value}')"><i class="fas fa-edit"></i></a>
        <a href="#" class="text-danger" title="Delete" onclick="deleteProof(${response.FirmsRegistrationSub.id})"><i class="fas fa-trash-alt"></i></a>
                    
                    </td>
                </tr>
            `;
            // Append the new row to the tbody of the #sublist table
            $('#sublist tbody').append(newRow);

            // Reset form fields after successful submission
            $('#idproofSelect').val('');
            $('#aadhaar_num').val('');
            $('#licence_no').val('');
            $('#aadhaar_proof').val('');
            $('#licence_proof').val('');
        },
        error: function(xhr) {
            console.log(xhr.responseText); // Log any errors for debugging
            alert('An error occurred. Please try again.'); // Provide user feedback
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

    // Base directory wher your files are stored
    var aadhaarDirectory = 'public/uploads/firms_registration/aadhaar/';


    fileNames.forEach(function(fileName) {
        // Get the file extension
        var cFileName = fileName.split('.');
        var fileExtension = cFileName[cFileName.length - 1].toLowerCase();
        var filePath = `${baseDirectory}${fileName}`; // Construct the full path

        var baseDirectory;


        var filePath = `${baseDirectory}${fileName}`;

        if (fileExtension === 'jpg' || fileExtension === 'png' || fileExtension === 'jpeg') {
            // Open image in a new tab
            window.open(filePath, '_blank');
        } else if (fileExtension === 'pdf') {

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
        success: function(response) {
            const redirecturl = "{{route('firms_registration_list')}}";
            sweetalert("create", redirecturl);
        },
        error: function(xhr) {
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
    const allowedChars = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    // Filter out characters that are not in the allowedChars array
    input.value = input.value.split('').filter(char => allowedChars.includes(char)).join('');

    // Check if the length is exactly 12
    if (input.value.length >= 10) {
        input.value = input.value.slice(0, 10); 
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
    document.getElementById('fax_no').addEventListener('blur', validateFaxNo);
    document.getElementById('gstin').addEventListener('blur', validateGstin);
    document.getElementById('aadhar_no').addEventListener('blur', validateAadharNo);
    
    function validateFaxNo() {
    const faxNo = document.getElementById('fax_no').value;
    const faxPattern = /^[0-9-\s]+$/; // Only digits, dashes, and spaces
    const faxError = document.getElementById('fax_no_error');

    // Remove dashes and spaces for length validation
    const cleanedFaxNo = faxNo.replace(/[-\s]/g, '');

    if (!faxPattern.test(faxNo) || cleanedFaxNo.length !== 10) {
        faxError.textContent = "Give correct format (10 digits required)"; // Show error message
        return false;
    } else {
        faxError.textContent = ""; // Clear error message
    }
    return true;
}

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
            aadharError.textContent = "Aadhar number must be 12 digits"; // Show error message
            return false;
        } else {
            aadharError.textContent = ""; // Clear error message
        }
        return true;
    }
});
function fetchAadharToLoginID() {
    const aadharNo = document.getElementById('aadhar_no').value;
    const loginIdField = document.getElementById('login_id');
    
    // Set the value of the Login ID field to the Aadhar No
    loginIdField.value = aadharNo;
}

</script>


@endsection
