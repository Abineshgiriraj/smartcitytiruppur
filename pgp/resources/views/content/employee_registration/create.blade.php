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
</style>
<!-- Basic Layout -->
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
                                <div class="row mb-3 mt-2">
                                    <label for="colFormLabelSm" class="col-sm-2 col-form-label">Full Name</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="fullName" name="fullName" />
                                    </div>

                                    <label for="nationalitySelect" class="col-sm-2 col-form-label mt-3">Employee
                                        ID</label>
                                    <div class="col-sm-4 mt-2">
                                        <input type="text" class="form-control" id="employee_id" name="employee_id">
                                    </div>
                                    <label for="countrySelect" class="col-sm-2 col-form-label mt-3">DOB</label>
                                    <div class="col-sm-4 mt-3">
                                        <input type="date" class="form-control" id="dob" name="dob">
                                    </div>

                                    <label for="colFormLabelSm" class="col-sm-2 col-form-label mt-3">Blood Group</label>
                                    <div class="col-sm-4 mt-3">
                                        <input type="text" class="form-control" id="blood_grp" name="blood_grp" />
                                    </div>
                                    <label for="colFormLabelSm" class="col-sm-2 col-form-label mt-3">Aadhaar No
                                    </label>
                                    <div class="col-sm-4 mt-3">
                                        <input type="text" id="aadhaar_no" name="aadhaar_no" class="form-select">
                                    </div>
                                    <label for="colFormLabelSm" class="col-sm-2 col-form-label mt-3">Email
                                        ID</label>
                                    <div class="col-sm-4 mt-3">
                                        <input type="email" class="form-control" id="email" name="email" />
                                    </div>
                                    <label for="colFormLabelSm" class="col-sm-2 col-form-label mt-3">Department
                                    </label>
                                    <div class="col-sm-4 mt-3">
                                        <select class="form-select" name="department" id="department">
                                            <option value="">Select Department</option>
                                            <option value="Staff">Staff</option>
                                            <option value="supervising">Supervising</option>
                                            <option value="Transport">Transport</option>
                                        </select>
                                    </div>
                                    <!-- <input type=" email" class="form-control" id="email"
                                            name="email" />
                                        -->
                                    <label for="colFormLabelSm" class="col-sm-2 col-form-label mt-3">Area
                                    </label>
                                    <div class="col-sm-4 mt-3">
                                        <input type="text" class="form-control" id="area" name="area" />
                                    </div>
                                    <label for="colFormLabelSm" class="col-sm-2 col-form-label mt-3">Validity Upto
                                    </label>
                                    <div class="col-sm-4 mt-3">
                                        <input type="date" class="form-control" id="area" name="area" />
                                    </div>
                                    <label for="colFormLabelSm" class="col-sm-2 col-form-label mt-3">RFID NO
                                    </label>
                                    <div class="col-sm-4 mt-3">
                                        <input type="text" class="form-control" id="area" name="area" />
                                    </div>
                                    <label for="colFormLabelSm" class="col-sm-2 col-form-label mt-3">Registration Number
                                    </label>
                                    <div class="col-sm-4 mt-3">
                                        <input type="text" class="form-control" id="area" name="area" />
                                    </div>
                                    <label for="colFormLabelSm" class="col-sm-2 col-form-label mt-3">Registration Date
                                    </label>
                                    <div class="col-sm-4 mt-3">
                                        <input type="date" class="form-control" id="area" name="area" />
                                    </div>
                                    <label for="colFormLabelSm" class="col-sm-2 col-form-label mt-3">Issuing Authority
                                    </label>
                                    <div class="col-sm-4 mt-3">
                                        <input type="text" class="form-control" id="area" name="area" />
                                    </div>
                                    <label for="colFormLabelSm" class="col-sm-2 col-form-label mt-3">Mobile Number
                                    </label>
                                    <div class="col-sm-4 mt-3">
                                        <input type="text" class="form-control" id="area" name="area" />
                                    </div>
                                    <label for="colFormLabelSm" class="col-sm-2 col-form-label mt-3">
                                        Address
                                    </label>
                                    <div class="col-sm-4 mt-3">
                                        <textarea class="form-control"></textarea>

                                    </div>
                                    <!-- </div> -->

                                </div>

                                <div class="row mb-3">


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

<script src=" {{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>


<script>
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

@endsection