@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">User Firms/</span>User Registration</h4>
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
                            <form id="personalDetailsForm">
                                @csrf
                                @if(isset($acc_year))
                                @method('PUT')
                                @endif
                                <!-- <h5>Personal Information</h5> -->
                                <div class="row mb-3">
                                    <label for="colFormLabelSm" class="col-sm-2 col-form-label">Full Name</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="fullName" name="fullName" />
                                    </div>

                                    <label for="nationalitySelect"
                                        class="col-sm-2 col-form-label mt-3">Nationality</label>
                                    <div class="col-sm-4 mt-3">
                                        <select class="form-select" id="nationalitySelect" name="nationality">
                                            <option selected>Select Nationality</option>
                                            <option value="indian">Indian</option>
                                            <option value="foreign">Foreign</option>
                                            <option value="indian-origin">Indian Origin</option>
                                        </select>
                                    </div>
                                    <label for="countrySelect" class="col-sm-2 col-form-label mt-3">Country</label>
                                    <div class="col-sm-4 mt-3">
                                        <select class="form-select" id="countrySelect" name="country">
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
                                    <label for="DesignationSelect"
                                        class="col-sm-2 col-form-label mt-3">Designation</label>
                                    <div class="col-sm-4 mt-3">
                                        <select class="form-select" id="DesignationSelect" name="Designation"
                                            onchange="toggleLicenceFields()">
                                            <option selected>Select Designation</option>
                                            <option value="Manager">Manager</option>
                                            <option value="Employee">Employee</option>
                                            <option value="Staff">Staff</option>
                                            <option value="Sales Manager">Sales Manager</option>
                                            <option value="Driver">Driver</option>
                                            <option value="Cleaner">Cleaner</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>

                                    <label for="colFormLabelSm" class="col-sm-2 col-form-label mt-3">Mobile</label>
                                    <div class="col-sm-4 mt-3">
                                        <input type="text" class="form-control" id="Mobile" name="Mobile" />
                                    </div>
                                    <label for="colFormLabelSm" class="col-sm-2 col-form-label mt-3">Blood Group</label>
                                    <div class="col-sm-4 mt-3">
                                        <select class="form-select" id="blood_groupSelect" name="blood_group">
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
                                    <label for="colFormLabelSm" class="col-sm-2 col-form-label mt-3">Email ID</label>
                                    <div class="col-sm-4 mt-3">
                                        <input type="email" class="form-control" id="email" name="email" />
                                    </div>

                                </div>




                                <div class="btn-float">
                                    <button type="button" class="btn btn-primary"
                                        onclick="document.querySelector('#tab2-link').click();">Next</button>
                                </div>

                            </form>
                        </div> <!-- end tab-pane -->

                        <div class="tab-pane" id="basictab2">
                            <form id="documentVerificationForm">
                                @csrf
                                @if(isset($acc_year))
                                @method('PUT')
                                @endif

                                <div class="row mb-3">

                                    <label for="idproofSelect" class="col-sm-2 col-form-label">Type of ID proof</label>
                                    <div class="col-sm-4">
                                        <select class="form-select" id="idproofSelect" name="idproof">
                                            <option selected>Select ID Proof</option>
                                            <option value="aadhaar">Aadhaar</option>
                                            <option value="driving_licence">Driving Licence</option>
                                        </select>
                                    </div>

                                    <label for="colFormLabelSm" class="col-sm-2 col-form-label">Aadhar No./Licence
                                        No.</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="adhaar" name="adhaar" />
                                    </div>

                                    <label for="colFormLabelSm" class="col-sm-2 col-form-label mt-3 driver"
                                        style="display: none;">Licence Expire Date</label>
                                    <div class="col-sm-4 mt-3 driver" style="display: none;">
                                        <input type="date" class="form-control" id="licence_expire"
                                            name="licence_expire" />
                                    </div>

                                    <label for="colFormLabelSm" class="col-sm-2 col-form-label mt-3">ID Proof</label>
                                    <div class="col-sm-4 mt-3">
                                        <input type="file" class="form-control" id="id_proof" name="id_proof" />
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
                                    <button id="openTableButton" class="btn btn-primary">Add</button>
                                </div>



                                <div id="dataTableContainer" class="table-responsive mt-4">
                                    <table id="example" class="table table-striped table-bordered" style="width:100%">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>S.No</th>
                                                <th>Type of ID Proof</th>
                                                <th>Aadhar No./Licence No.</th>

                                                <th>Licence Expiry</th>
                                                <th>Document</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>Aadhar Card</td>
                                                <td>5432 6789 9832</td>

                                                <td>01.03.2023</td>
                                                <td id="pdf" onclick="pdf()" target="_blank"><img
                                                        src="resources/views/content/person_registration/PDF.png"
                                                        alt="PDF" style="width: 24px; height: 24px;">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>Driving Licence</td>
                                                <td>5432 6789 9832</td>

                                                <td>02.09.2024</td>
                                                <td id="pdf" onclick="pdf()" target="_blank"><img
                                                        src="resources/views/content/person_registration/PDF.png"
                                                        alt="PDF" style="width: 24px; height: 24px;">
                                                </td>
                                            </tr>
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
                                            <input type="text" class="form-control" name="login_id" id="login_id">

                                        </div>
                                        <div class="col-sm-2 mt-3 password-container">
                                            <label for="colFormLabelSm" class="col-form-label">Password</label>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="input-wrapper">
                                                <input type="password" class="form-control  mt-3" id="password"
                                                    name="password" />
                                                <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                                            </div>
                                        </div>

                                        <div class="col-sm-2 mt-3 password-container">
                                            <label for="colFormLabelSm" class="col-form-label">Confirm Password</label>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="input-wrapper">
                                                <input type="password" class="form-control  mt-3"
                                                    onkeyup="password_change()" id="confirm_password"
                                                    name="confirm_password" />

                                                <i class="fas fa-eye toggle-password" id="toggleConfirmPassword"></i>


                                            </div>
                                            <small id="password_error" class="text-danger"
                                                style="display:none;">Passwords
                                                do not match</small>
                                            <small id="password_crct" class="text-success"
                                                style="display:none;">Passwords
                                                match</small>
                                        </div>
                                    </div>


                                    <!-- Add more fields... -->
                                </div>
                                <div class="btn-float">
                                    <button type="button" class="btn btn-danger"
                                        onclick="window.location='{{ route('person_registration') }}'">Cancel</button>
                                    <button type="button" class="btn btn-primary">Submit</button>
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

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>



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
</script>


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
    const designation = document.getElementById('DesignationSelect').value;
    const licenceProofFields = document.getElementsByClassName('driver');

    for (let i = 0; i < licenceProofFields.length; i++) {
        if (designation === 'Driver') {
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
</script>

</script>

@endsection