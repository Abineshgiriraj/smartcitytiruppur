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
                    <!-- <ul class="nav nav-pills nav-justified form-wizard-header ">
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
                    </ul> -->
                    <h3>Personl Details</h3>
                    <hr>
                    <div class="tab-content b-0 mb-0">
                        <div class="tab-pane active" id="basictab1">
                            <form id="firmsForm_edit" method="POST"
                                action="{{ route('firms.update', $firms_registration->id) }}">
                                @csrf
                                @method('PUT')
                                @if(isset($firms_registration))
                                <input type="hidden" id="screen_unique_id" name="screen_unique_id"
                                    value="{{ $firms_registration->screen_unique_id }}">
                                @endif

                                @if($firms_sublist_reg->isNotEmpty())
                                @foreach($firms_sublist_reg as $sublist)
                                <input type="hidden" name="sublist_id[]" value="{{ $sublist->form_unique_id }}">
                                @endforeach
                                @endif

                                <div class="row mb-3">
                                    <label for="acc_year" class="col-sm-2 col-form-label">A/C Year</label>
                                    <div class="col-sm-4">
                                        <select class="form-select" name="academic_year" id="academic_year">
                                            @foreach ($get_academic_year as $acc_year )
                                            <option value="{{$acc_year->unique_id}}"
                                                {{ $acc_year->unique_id == $firms_registration->academic_year ? 'selected' : ''}}>
                                                {{$acc_year->acc_year}}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-sm-2 mt-3">
                                        <label for="nationalitySelect" class="col-form-label">Type of Firms /
                                            Agent</label>
                                    </div>
                                    <div class="col-sm-4 mt-3">
                                        <select class="form-select" id="firms" name="firms">
                                            <option selected>Select Firms</option>
                                            @foreach ($get_firms_registration as $get_firms)
                                            <option value="{{$get_firms->unique_id}}"
                                                {{$get_firms->unique_id == $firms_registration->firms ? 'selected' : ''}}>
                                                {{$get_firms->company_name}}
                                            </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" id="firm_id" name="firm_id"
                                            value="{{$get_firms->unique_id}}">
                                    </div>

                                    <div class="col-sm-2 mt-3">
                                        <label class="col-form-label" for="firm_id">Firm ID:</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-12 col-form-label mt-3 firm-id-label">
                                            <b id="firm_id">{{ $firms_registration->firm_id}}</b>
                                        </label>
                                        <input type="hidden" id="hidden_firm_id" name="firm_id"
                                            value="{{ isset($firms_registration) ? $firms_registration->firm_id : '' }}">
                                    </div>


                                    <div class="col-sm-2 mt-3">
                                        <label for="name" class="col-form-label">Company Name</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control mt-3" id="name" name="name"
                                            oninput="validateCompanyName()"
                                            value="{{ old('name', $firms_registration->name ?? '') }}" required />
                                    </div>

                                    <div class="col-sm-2 mt-3">
                                        <label for="company_reg_no" class="col-form-label">Company Reg No.</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control mt-3" id="company_reg_no"
                                            oninput="validateCompanyRegNum()" name="company_reg_no"
                                            value="{{ old('company_reg_no', $firms_registration->company_reg_no ?? '') }}"
                                            required />
                                    </div>


                                    <div class="col-sm-2 mt-3">
                                        <label for="mobile" class="col-form-label">Mobile no</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="tel" class="form-control mt-3" id="mobile" name="mobile"
                                            oninput="valid_mobile_number(this)" maxlength="10"
                                            value="{{ old('mobile', $firms_registration->mobile ?? '') }}" required />
                                    </div>

                                    <div class="col-sm-2 mt-3">
                                        <label for="email" class="col-form-label">Email Id</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="email" class="form-control mt-3" id="email" name="email"
                                            oninput="mail_valid(this)" mailError
                                            value="{{ old('email', $firms_registration->email ?? '') }}" required />
                                        <span id="mailError" class="text-danger"></span>

                                    </div>

                                    <div class="col-sm-2 mt-3">
                                        <label for="aadhar_no" class="col-form-label">Contact Person Aadhar No</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control mt-3" id="aadhar_no" name="aadhar_no"
                                            oninput="validateAadhar(this)" max="12" min="12"
                                            value="{{ old('aadhar_no', $firms_registration->aadhar_no ?? '') }}"
                                            required />
                                        <span id="aadhar_no_error" class="text-danger"></span>

                                    </div>

                                    <div class="col-sm-2 mt-3">
                                        <label for="contact_person" class="col-form-label">Contact Person</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control mt-3" id="contact_person"
                                            name="contact_person" oninput="validateName()"
                                            value="{{ old('contact_person', $firms_registration->contact_person ?? '') }}"
                                            required />
                                    </div>

                                    <div class="col-sm-2 mt-3">
                                        <label for="fax_no" class="col-form-label">Phone No</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control mt-3" id="fax_no" name="fax_no"
                                            oninput="validateTelephoneNo()" maxlength="10"
                                            value="{{ old('fax_no', $firms_registration->fax_no ?? '') }}" />
                                    </div>

                                    <div class="col-sm-2 mt-3">
                                        <label for="gstin" class="col-form-label">GSTIN</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control mt-3" id="gstin" name="gstin"
                                            value="{{ old('gstin', $firms_registration->gstin ?? '') }}" />
                                    </div>

                                    <div class="col-sm-2 mt-3">
                                        <label for="address_1" class="col-form-label">Address 1</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <textarea class="form-control mt-3" id="address_1" name="address_1"
                                            oninput="validateAddress('address_1')"
                                            rows="1">{{ old('address_1', $firms_registration->address_1 ?? '') }}</textarea>
                                    </div>

                                    <div class="col-sm-2 mt-3">
                                        <label for="address_2" class="col-form-label">Address 2</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <textarea class="form-control mt-3" id="address_2" name="address_2"
                                            oninput="validateAddress('address_2')"
                                            rows="1">{{ old('address_2', $firms_registration->address_2 ?? '') }}</textarea>
                                    </div>
                                </div>

                                <!-- <div class="btn-float">
                                    <button type="button" class="btn btn-primary" onclick="submit_next()">Next</button>
                                </div> -->
                            </form>
                        <!-- </div>  -->
                        <h3>Document Details</h3>
                        <hr>

                        <div class="tab-pane" id="basictab2">
                            <form id="documentVerificationForm">
                                @csrf
                                @if(isset($acc_year))
                                @method('PUT')
                                @endif
                                <div class="row mb-3">
                                    <input type="hidden" id="screen_unique_id" name="screen_unique_id"
                                        value="{{$firms_registration->screen_unique_id}}">

                                    <label for="idproofSelect" class="col-sm-2 col-form-label">Type of ID proof</label>
                                    <div class="col-sm-4 fm">
                                        <select class="form-select" id="idproof" name="idproof"
                                            onchange="proof_change()">
                                            <option selected>Select ID Proof</option>
                                            <!-- <option value="aadhaar">Aadhaar</option> -->
                                            <option value="company">Company Licence</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- <div id="aadhar_id" style="display:none;" class="row mb-3 mt-3">
                                    <label for="aadhaar_num" class="col-sm-2 col-form-label">Aadhar No.</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="aadhaar_num" name="aadhaar_num" />
                                    </div>
                                    <label for="aadhaar_license" class="col-sm-2 col-form-label">ID Proof</label>
                                    <div class="col-sm-4">
                                        <input type="file" class="form-control" id="aadhaar_license"
                                            name="aadhaar_license" />
                                    </div>
                                </div> -->

                                <div id="licence_id" style="display:none;" class="row mb-3 mt-3">
                                    <label for="licence_no" class="col-sm-2 col-form-label">Company Licence No.</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="licence_no" name="licence_no" />
                                    </div>
                                    <label for="licence_proof" class="col-sm-2 col-form-label">ID Proof</label>
                                    <div class="col-sm-4">
                                        <input type="file" class="form-control" id="licence_proof" name="licence_proof"
                                            accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" />
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center mb-4">
                                    <button id="openTableButton submit_sublist" onclick="sublist_proof(event)"
                                        class="btn btn-primary">Add</button>
                                </div>

                                <div id="dataTableContainer" class="table-responsive mt-4">
                                    <table id="sublist" class="table table-striped table-bordered" style="width:100%">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>S.No</th>
                                                <th>Type of ID Proof</th>
                                                <th>Proof ID</th>
                                                <th>Document</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $s_no = 1; @endphp
                                            @if ($firms_sublist_reg && $firms_sublist_reg->count() > 0)
                                            @foreach ($firms_sublist_reg as $sublist)
                                            <tr>
                                                <td>{{ $s_no++ }}</td>
                                                <td>{{ $sublist->proof_type }}</td>
                                                <td>{{ $sublist->aadhar_licence}}</td>
                                                <td>
                                                    <a onclick="imageViews('{{ $sublist->attached_proof }}')">
                                                        <img src="{{ asset('/public/uploads/dwn.png') }}" width="40px"
                                                            height="40px">
                                                    </a>
                                                </td>
                                                <td>
                                                    <a class="text-primary" title="Edit"
                                                        onclick="editDocument('{{ $sublist->id }}', '{{ $sublist->proof_type }}', '{{ $sublist->aadhar_licence }}','{{ $sublist->attached_proof }}')"><i
                                                            class="fas fa-edit"></i></a>

                                                    <a href="#" class="text-danger" title="Delete"
                                                        onclick="deleteSublistItem('{{ $sublist->id }}')"
                                                        style="background:none; border:none; padding:0;"><i
                                                            class="fas fa-trash-alt"></i></a>

                                                </td>
                                            </tr>
                                            @endforeach
                                            @else
                                            <tr>
                                                <td colspan="5" class="text-center">No sublist record found.</td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>

                                <!-- <div class="btn-float">
                                    <button type="button" class="btn btn-primary"
                                        onclick="document.querySelector('#tab3-link').click();">Next</button>
                                </div> -->
                            </form>
                        </div>
                        <h3>Authenication</h3>
                        <hr>
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
                                            <input type="text" readonly class="form-control" name="login_id"
                                                id="login_id" value="{{$firms_registration->login_id}}">

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

                                                <i class="fas fa-eye-slash toggle-password"
                                                    id="toggleConfirmPassword"></i>

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
                            <button type="button" class="btn btn-primary" onclick="sub_authentication()">Update</button>
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


<script>
    function proof_change() {

        var proof = $('#idproof').val();

        if (proof === 'aadhaar') {
            $('#aadhar_id').show();
            $('#licence_id').hide();
        } else if (proof === 'company') {
            $('#licence_id').show();
            $('#aadhar_id').hide();
        }
    }



    function submit_next() {
        // e.preventDefault(); // Prevent the default form submission
        // alert($('#firms').val());
        $('#firm_id').parent().hide();
        console.log("Firm ID is stored and displayed hidden.");


        var aadhaar = $('#aadhar_no').val().trim();
        var mobile = $('#mobile').val().trim();
        var email = $('#email').val().trim();
        var faxNo = $('#fax_no').val().trim();
        var gst = $('#gstin').val().trim();
        var name = $('#name').val().trim();
        var contactPerson = $('#contact_person').val().trim();
        var address1 = $('#address_1').val().trim();
        var address2 = $('#address_2').val().trim();
        var academicYear = $('#academic_year').val().trim();
        var formData = new FormData();

        // Append form fields to the FormData object
        formData.append('academic_year', $('#academic_year').val());
        formData.append('firms', $('#firms').val());
        formData.append('firm_id', $('#hidden_firm_id').val());
        formData.append('company_reg_no', $('#company_reg_no').val());
        formData.append('name', $('#name').val());
        formData.append('aadhar_no', $('#aadhar_no').val());
        formData.append('mobile', $('#mobile').val());
        formData.append('email', $('#email').val());
        formData.append('contact_person', $('#contact_person').val());
        formData.append('fax_no', $('#fax_no').val());
        formData.append('gstin', $('#gstin').val());
        formData.append('address_1', $('#address_1').val());
        formData.append('address_2', $('#address_2').val());
        formData.append('_token', '{{csrf_token()}}');



        let validationErrors = [];

        // Validate required fields






        if (!aadhaar || !mobile || !email || !faxNo || !gst || !name || !contactPerson || !address1 || !address2 || !
            academicYear) {
            Swal.fire('Error', 'Please fill out all mandatory fields.', 'error');
            return;
        }



        // Aadhaar validation
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

        // Fax number validation (numeric only, up to 12 digits)
        if (!/^\d{1,12}$/.test(faxNo)) {
            validationErrors.push('Fax Number must be numeric and up to 12 digits.');
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
            url: "{{route('firms_registration.update',$firms_registration->id)}}",
            data: formData,
            contentType: false, // Important for sending FormData
            processData: false, // Prevent jQuery from processing the data
            success: function(response) {
                if (response.firm) {

                    var firm = response.firm;
                    var unique_id = firm.unique_id;


                    var login_id = firm.aadhar_no;



                    $('#screen_unique_id').val(unique_id);

                    $('#login_id').val(login_id);



                }
                document.querySelector('#tab2-link').click();
                sweetalert("create");
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });


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





    //     $(document).ready(function() {
    //     // Fetch the last firm number when the document is ready
    //     $.ajax({
    //         url: '{{ url("/get-last-firm-number") }}',
    //         method: 'GET',
    //         success: function(response) {
    //             // Update the Firm ID label with the new Firm ID
    //             $('#firm_id').text(response.firm_id);
    //             $('#hidden_firm_id').val(response.firm_id);
    //         },
    //         error: function(xhr, status, error) {
    //             console.error("Error fetching Firm ID:", error);
    //             $('#firm_id').text('Error loading Firm ID');
    //         }
    //     });
    // });

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


    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('fax_no').addEventListener('blur', validateFaxNo);
        document.getElementById('gstin').addEventListener('blur', validateGstin);
        document.getElementById('aadhar_no').addEventListener('blur', validateAadharNo);


        function validateFaxNo() {
            const faxInput = document.getElementById('fax_no');
            let faxNo = faxInput.value;

            const allowedChars = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

            // Filter out characters that are not in the allowedChars array
            faxNo = faxNo.split('').filter(char => allowedChars.includes(char)).join('');

            // Update the input value
            faxInput.value = faxNo;

            const faxPattern = /^[0-9\s]+$/; // Only digits and spaces allowed
            const faxError = document.getElementById('fax_no_error');

            // Remove spaces for length validation
            const cleanedFaxNo = faxNo.replace(/\s/g, '');

            // Validate fax number format and length
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
                aadharError.textContent = "Aadhar number must be exactly 12 digits";
                return false;
            } else {
                aadharError.textContent = "";
            }
            return true;
        }


    });


    document.addEventListener('DOMContentLoaded', function() {
        // Add event listener when second tab is clicked or shown
        document.querySelector('a[href="#basictab2"]').addEventListener('click', function() {
            // Get the value of the Aadhaar number from the first tab
            var aadhaarNo = document.getElementById('aadhar_no').value;

            // Set the value of the Aadhaar number in the second tab
            document.getElementById('adhaar').value = aadhaarNo;
        });
    });

    function sublist_proof(event) {
        event.preventDefault(); // Prevent default form submission

        var proof_type = $('#idproof').val();


        var proof_value = $('#licence_no').val();

        var screen_unique_id = $('#screen_unique_id').val();

        var licence_val_sub = $('#licence_val_sub').val();



        // Create FormData object to handle file uploads
        var formData = new FormData();
        formData.append('proof_type', proof_type);
        formData.append('screen_unique_id', screen_unique_id);
        formData.append('proof_value', proof_value);
        formData.append('_token', '{{csrf_token()}}'); // Add CSRF token for security

        // Handle file inputs (append only if the file is selected)
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
            url: '{{ route("firms_sublist.create") }}', // Ensure the route is correct
            // ,$firms_sublist_reg->id
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                loadTableData();

                if (response.message) {

                    Swal.fire('Success', response.message, 'success').then(() => {
                        // Redirect or further processing if necessary
                        document.querySelector('#tab2-link').click();
                    });
                }



            },
            error: function(xhr) {
                console.log(xhr.responseText);
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        alert(value[0]);
                    });
                } else {
                    alert('An error occurred. Please try again.');
                }
            }
        });
    }




    function loadTableData() {
        var screen_unique_id = $('#screen_unique_id').val();

        $.ajax({
            type: "GET",
            url: '{{ route("firm_sublist_data.index") }}',
            data: {
                screen_unique_id: screen_unique_id, // Pass the parameter directly
                _token: '{{ csrf_token() }}' // Include CSRF token for security
            },
            // Ensure this route fetches the latest sublist data
            success: function(data) {
                // Clear the current table body
                $('#sublist tbody').empty();

                // Populate the table with new data
                data.forEach(function(item, index) {



                    var Directory = '{{ asset("public/uploads/firms_registration/licence/") }}';

                    var newRow = `
                <tr id="row-${item.unique_id}">
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
                        <a href="#" class="text-danger" title="Delete" onclick="deleteSublistItem('${item.unique_id}')">
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
            error: function(xhr) {
                console.log(xhr.responseText); // Log any errors for debugging
            }
        });
    }

    function imageViews(file_name, proof_type) {
        // Split the file names by commas (in case there are multiple)
        var fileNames = file_name.split(',');

        // Base directory wher your files are stored
        var baseDirectory = '/harbour/public/uploads/firms_registration/licence/';

        // var baseDirectory = '{{ asset("public/uploads/firms_registration/licence/") }}';

        fileNames.forEach(function(fileName) {
            // Get the file extension
            var cFileName = fileName.split('.');
            var fileExtension = cFileName[cFileName.length - 1].toLowerCase();
            var filePath = `${baseDirectory}${fileName}`; // Construct the full path


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
                    url: '{{ route("firm_registration.delete", ":id") }}'.replace(':id', id),
                    method: 'POST', // Assuming you're using PUT to update the record
                    data: {
                        is_delete: 1,
                        _token: '{{ csrf_token() }}' // Include CSRF token
                    },
                    success: function(response) {
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
                    error: function() {
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


    function deleteSublistItem(id) {

        alert(id);
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
                    method: 'POST', // Assuming you're using PUT to update the record
                    data: {
                        is_delete: 1,
                        _token: '{{ csrf_token() }}' // Include CSRF token
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Deleted!', response.message, 'success').then(() => {
                                // Remove the corresponding row from the table
                                $('#sublist tr').filter(function() {
                                    return $(this).find('a[onclick*="' +
                                        id +
                                        '"]').length > 0;
                                }).remove();

                                // Optional: Check if the table is empty and show a message
                                if ($('#sublist tbody tr').length === 0) {
                                    $('#sublist tbody').html(
                                        '<tr><td colspan="5" class="text-center">No sublist records found.</td></tr>'
                                    );
                                }
                            });
                        } else {
                            Swal.fire(
                                'Error!',
                                response.message || 'Failed to delete the record.',
                                'error'
                            );
                        }
                    },
                    error: function() {
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
        alert(password);
        var confirm_password = $('#confirm_password').val();

        alert(confirm_password);
        var screen_unique_id = $('#unique_id').val();

        // Password validation checks
        const hasUpperCase = /[A-Z]/.test(password);
        const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);
        const minLength = password.length >= 8;

        if (!login_id && !password && !confirm_password && !screen_unique_id) {
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
            return false;
        }

        var data = {
            login_id: login_id,
            password: password,
            confirm_password: confirm_password,
            screen_unique_id: screen_unique_id,
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

    function sub_authentication() {

        // Get values from input fields
        var login_id = $('#login_id').val();
        var password = $('#password').val();
        var confirm_password = $('#confirm_password').val();

        var screen_unique_id = $('#unique_id').val();


        const hasUpperCase = /[A-Z]/.test(password);
        const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);
        const minLength = password.length >= 8;

        // if (!password && !confirm_password) {
        //     return; // No action required
        // }

        let validationError = '';

        // Only validate if password and confirm_password are not null
        if (password || confirm_password) {
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

            // If there are any validation errors, show them using SweetAlert2
            if (validationError) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: validationError,
                    confirmButtonText: 'OK'
                });
                return; // Prevent further execution
            }
        }

        // Password validation checks
        // const hasUpperCase = /[A-Z]/.test(password);
        // const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);
        // const minLength = password.length >= 8;

        // if (!login_id && !password && !confirm_password && !screen_unique_id) {
        //     sweetalert("form_alert");
        // }

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
        //     return false;
        // }

        var data = {
            login_id: login_id,
            password: password,
            confirm_password: confirm_password,
            screen_unique_id: screen_unique_id,
            _token: '{{csrf_token()}}'
        };

        $.ajax({
            type: "POST",
            url: "{{route('firms_authentication.update', $firms_registration->unique_id)}}",
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
    }

    function editDocument(id, proof_type, aadhar_licence, attached_proof) {
        // Set the certificate type dropdown

        alert(attached_proof);
        $('#idproof').val(proof_type);

        // const proofLink = '/harbour/public/uploads/' + attached_proof;

        // Set other fields based on the type of certificate
        if (proof_type == 'company') {
            // alert('ji');
            $('#licence_id').show();
            $('#licence_no').val(aadhar_licence);
            $('#licence_proof').html(attached_proof);




        }
    }
</script>


@endsection$