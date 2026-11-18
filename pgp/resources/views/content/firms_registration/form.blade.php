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
<!-- Basic Layout -->
<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
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
                                <span class="d-sm-inline">Authenication </span>
                            </a>
                        </li>
                    </ul>


                    <div class="tab-content b-0 mb-0">
                        <div class="tab-pane active" id="basictab1">
                            <form id="academicYearForm">
                                @csrf
                                @if(isset($acc_year))
                                @method('PUT')
                                @endif


                                <div class="row mb-3">
                                    <div class="col-sm-2 mt-3">
                                        <label for="nationalitySelect" class="col-form-label">Type of Firms /
                                            Agent</label>
                                    </div>
                                    <div class="col-sm-4 mt-3">
                                        <select class="form-select" id="nationalitySelect" name="nationality">
                                            <option selected>Select Firms</option>
                                            <option value="Stevedore">Stevedore</option>
                                            <option value="Ship Agent">Ship Agent</option>
                                            <option value="Custom Broker">Custom Broker</option>
                                            <option value="CFS">CFS (Container Freight Station)</option>
                                        </select>
                                    </div>

                                    <div class="col-sm-2 mt-3">
                                        <label class="col-form-label" for="basic-default-fullname">Firm ID:</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-12 col-form-label mt-3"><b>20240900001</b></label>
                                    </div>

                                    <div class="col-sm-2 mt-3">
                                        <label for="colFormLabelSm" class="col-form-label">Company Reg No.</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control mt-3" id="company_reg_no"
                                            name="company_reg_no" />
                                    </div>

                                    <div class="col-sm-2 mt-3">
                                        <label for="colFormLabelSm" class="col-form-label">Name</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control mt-3" id="name" name="name" />
                                    </div>

                                    <div class="col-sm-2 mt-3">
                                        <label for="colFormLabelSm" class="col-form-label">Mobile no</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control mt-3" id="mobile" name="mobile" />
                                    </div>

                                    <div class="col-sm-2 mt-3">
                                        <label for="colFormLabelSm" class="col-form-label">Email Id</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="email" class="form-control mt-3" id="email" name="email" />
                                    </div>

                                    <div class="col-sm-2 mt-3">
                                        <label for="colFormLabelSm" class="col-form-label">Contact Person</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control mt-3" id="contact_person"
                                            name="contact_person" />
                                    </div>

                                    <div class="col-sm-2 mt-3">
                                        <label for="colFormLabelSm" class="col-form-label">FAX No</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control mt-3" id="fax_no" name="fax_no" />
                                    </div>
                                    <div class="col-sm-2 mt-3">
                                        <label for="colFormLabelSm" class="col-form-label">GSTIN</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control mt-3" id="gstin" name="gstin" />
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
                                    <button type="button" class="btn btn-primary"
                                        onclick="document.querySelector('#tab2-link').click();">Next</button>
                                </div>

                        </div>

                        <div class="tab-pane" id="basictab2">

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
                                        <input type="password" class="form-control  mt-3" onkeyup="password_change()"
                                            id="confirm_password" name="confirm_password" />

                                        <i class="fas fa-eye toggle-password" id="toggleConfirmPassword"></i>


                                    </div>
                                    <small id="password_error" class="text-danger" style="display:none;">Passwords
                                        do not match</small>
                                    <small id="password_crct" class="text-success" style="display:none;">Passwords
                                        match</small>
                                </div>
                            </div>
                        </div>
                        <div class="btn-float">
                            <button type="button" class="btn btn-danger"
                                onclick="window.location.href='{{ url('/firms_registration_list')}}'">Cancel</button>
                            <!-- <button type="submit" class="btn btn-primary">Submit</button> -->
                            <button type="submit"
                                class="btn btn-primary">{{ isset($acc_year) ? 'Update' : 'Submit' }}</button>
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

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables JS -->


    <script>
    const passwordField = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');

    togglePassword.addEventListener('click', function() {
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);

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
    </script>


    @endsection