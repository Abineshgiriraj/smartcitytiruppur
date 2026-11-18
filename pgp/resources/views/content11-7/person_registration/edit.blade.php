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

hr {

    height: 2px;
    background-color: skyblue;
}

.new-card h5 {
    color: #5859ae;
}
</style>

@php

$get_designations = get_person_type();

$get_academic_year = academic_year();




$get_firms_registration = get_firm_company();


@endphp
<!-- Basic Layout -->

<!-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body new-card">
                <div id="basicwizard">

                    <h3>Personl Details</h3>

                    <div class="">
                        <div class="" id="">
                            <form id="person_registration_edit">
                                @csrf
                                @method('PUT')
                                @if(isset($person_registration) && $person_registration)
                                <input type="hidden" name="id" id="reg_unique_id"
                                    value="{{ $person_registration->form_unique_id }}">
                                @endif

                                @if(isset($person_sublist_reg_first) && $person_sublist_reg_first)
                                <input type="hidden" name="sublist_id"
                                    value="{{ $person_sublist_reg_first->form_unique_id }}">
                                @endif
                                <!-- </form> -->


                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-sm-5 ">
                                                <label for="colFormLabelSm" class="col-form-label"> Designation</label>
                                            </div>
                                            <div class="col-sm-7">

                                                <select class="form-select mb-2" id="designation_select"
                                                    name="designation_select">
                                                    <option selected>Select Designation</option>
                                                    @foreach ($get_designations as $designation)
                                                    <option value="{{ $designation->unique_id }}"
                                                        {{ $designation->unique_id == $person_registration->designation ? 'selected' : '' }}>
                                                        {{ $designation->person_type }}
                                                    </option>
                                                    @endforeach
                                                </select>

                                            </div>

                                            <div class="row mb-3" style="display:none;">
                                                <label for="register_number" class="col-sm-2 col-form-label">Register
                                                    Number</label>
                                                <div class="col-sm-4">
                                                    <label class="col-form-label"><b
                                                            id="register_number">{{ isset($register_number) ? $register_number : 'Loading...' }}</b></label>
                                                    <input type="hidden" id="hidden_register_number"
                                                        name="register_number" value="">
                                                </div>

                                                <div class="col-sm-4" style="display:none;">
                                                    <select class="form-select" name="academic_year" id="academic_year">
                                                        @foreach ($get_academic_year as $acc_year)
                                                        <option value="{{$acc_year->unique_id}}">{{$acc_year->acc_year}}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-sm-5 align-self-center">
                                                <label for="colFormLabelSm" class="col-form-label">Full Name</label>
                                            </div>
                                            <div class="col-sm-7">
                                                <input type="text" class="form-control mb-2" id="full_name"
                                                    name="full_name" value="{{$person_registration->full_name}}" />
                                            </div>
                                            <div class="col-sm-5 align-self-center">
                                                <label for="colFormLabelSm" class="col-form-label">Blood Group</label>
                                            </div>
                                            <div class="col-sm-7">
                                                <select class="form-select mb-2" id="blood_group" name="blood_group">
                                                    <option selected>Select Blood Group</option>
                                                    <option value="A+"
                                                        {{$person_registration->blood_group == 'A+' ? 'selected' : ''}}>
                                                        A+</option>
                                                    <option value="A-"
                                                        {{$person_registration->blood_group == 'A-' ? 'selected' : ''}}>
                                                        A-</option>
                                                    <option value="B+"
                                                        {{$person_registration->blood_group == 'B+' ? 'selected' : ''}}>
                                                        B+
                                                    </option>
                                                    <option value="B-"
                                                        {{$person_registration->blood_group == 'B-' ? 'selected' : ''}}>
                                                        B-
                                                    </option>
                                                    <option value="O+"
                                                        {{$person_registration->blood_group == 'O+' ? 'selected' : ''}}>
                                                        O+
                                                    </option>
                                                    <option value="O-"
                                                        {{$person_registration->blood_group == 'O-' ? 'selected' : ''}}>
                                                        O-
                                                    </option>
                                                    <option value="AB+"
                                                        {{$person_registration->blood_group == 'AB+' ? 'selected' : ''}}>
                                                        AB+
                                                    </option>
                                                    <option value="AB-"
                                                        {{$person_registration->blood_group == 'AB-' ? 'selected' : ''}}>
                                                        AB-
                                                    </option>
                                                </select>

                                            </div>

                                            <div class="col-sm-5 align-self-center">
                                                <label for="colFormLabelSm" class="col-form-label">Nationality</label>
                                            </div>
                                            <div class="col-sm-7">
                                                <select class="form-select mb-2" id="nationality" name="nationality">
                                                    <option value="">Select Nationality</option>
                                                    <option value="Indian"
                                                        {{$person_registration->nationality == 'Indian' ? 'selected' :''}}>
                                                        Indian</option>
                                                    <option value="Foreign"
                                                        {{$person_registration->nationality == 'Foreign' ? 'selected' :''}}>
                                                        Foreign</option>
                                                    <option value="Indian-origin"
                                                        {{$person_registration->nationality == 'Indian-origin' ? 'selected' :''}}>
                                                        Indian Origin</option>
                                                </select>
                                            </div>



                                            <div class="col-sm-5 align-self-center">
                                                <label for="colFormLabelSm" class="col-form-label">Address</label>
                                            </div>
                                            <div class="col-sm-7">
                                                <textarea class="form-control mb-3" oninput="validateAddress('address')"
                                                    id="address" name="address"
                                                    rows="2">{{$person_registration->address}}</textarea>
                                                <span id="address_error" style="color: red;"></span>

                                                <input type="hidden" id="address_old"
                                                    value="{{$person_registration->address}}">
                                                <!--
                                                    Error message span -->
                                            </div>

                                            <div class="row mb-3" id="upload">
                                                <div class="col-sm-5 align-self-center">
                                                    <label for="colFormLabelSm" class="col-form-label">Upload
                                                        Photo</label>
                                                </div>

                                                <div class="col-sm-7">
                                                    <input type="file" class="form-control mb-2" id="photo" name="photo"
                                                        value="{{$person_registration->image_upload}}"
                                                        onchange="previewImage()" />

                                                    <input type="hidden" id="old_image" name="old_image"
                                                        value="{{$person_registration->image_upload}}">
                                                </div>
                                                <div class="col-sm-2 mt-3">
                                                    <img id="imagePreview"
                                                        src="{{ asset('public/uploads/person_registration/' .$person_registration->image_upload) }}"
                                                        alt="Image Preview" style="max-width: 200px;"
                                                        {{ $person_registration->image_upload ? '' : 'display:none;' }} />
                                                </div>
                                            </div>

                                            <div class="row mb-3" id="snap" style="display:none;">
                                                <div class="col-sm-5 align-self-center">
                                                    <label for="colFormLabelSm" class="col-form-label">Preview
                                                        Photo</label>
                                                </div>
                                                <div class="col-sm-4 text-center">
                                                    <img src="resources/views/content/person_registration/13.png"
                                                        alt="..." class="img-thumbnail"
                                                        style="border: 5    px solid grey; max-width: 100%; height: auto;">
                                                    <div class="mt-3">
                                                        <button type="button" onclick="web_cam()"
                                                            class="btn btn-primary">Snap</button>
                                                    </div>
                                                </div>
                                            </div>






                                            <div id="upload" style="display:none;">
                                                <div class="col-sm-5 align-self-center">
                                                    <label for="photo" class="col-sm-2 col-form-label">Upload
                                                        Photo</label>
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
                                                    <label for="photoPreview" class="col-form-label">Preview
                                                        Photo</label>
                                                </div>
                                                <div class="col-sm-7">
                                                    <img src="resources/views/content/person_registration/13.png"
                                                        alt="..." class="img-thumbnail"
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
                                                    oninput="valid_aadhar_number(this)" name="aadhaar_no"
                                                    value="{{ old('aadhaar_no', $person_registration->aadhaar_no)  }}" />
                                                <!-- <span id="aadhaarError" style="color:red;"></span> -->
                                                <span id="aadhaar_error" class="error-message"
                                                    style="color: red; display: none;"></span>
                                            </div>
                                            <div class="col-sm-5 align-self-center">
                                                <label for="colFormLabelSm" class="col-form-label">Mobile No</label>
                                            </div>
                                            <div class="col-sm-7">
                                                <input type="tel" class="form-control mb-2" id="mobile" name="mobile"
                                                    oninput="valid_mobile_number(this)" minlength="10" maxlength="13"
                                                    value="{{$person_registration->mobile}}" />
                                                <!-- <span id="mobileError" style="color:red;"></span> -->
                                                <span id="mobile_error" class="error-message"
                                                    style="color: red; display: none;"></span>
                                            </div>
                                            <div class="col-sm-5 align-self-center">
                                                <label for="colFormLabelSm" class="col-form-label">Email ID</label>
                                            </div>
                                            <div class="col-sm-7">
                                                <input type="email" class="form-control mb-2" id="email_id"
                                                    oninput="mail_valid(this)" name="email_id"
                                                    value="{{$person_registration->email_id}}" />
                                                <span id=" mailError" style="color:red;"></span>

                                            </div>

                                            <div class="col-sm-5 align-self-center">
                                                <label for="colFormLabelSm" class="col-form-label">Country</label>
                                            </div>
                                            <div class="col-sm-7">
                                                <select class="form-select mb-2" id="country" name="country">
                                                    <option value="">Select</option>
                                                    <option value="india"
                                                        {{$person_registration->country == 'india' ? 'selected' : ''}}>
                                                        India
                                                    </option>
                                                    <option value=" usa"
                                                        {{$person_registration->country == 'usa' ? 'selected' : ''}}>
                                                        United States</option>
                                                    <option value="uk"
                                                        {{$person_registration->country == 'uk' ? 'selected' : ''}}>
                                                        United
                                                        Kingdom</option>
                                                    <option value="canada"
                                                        {{$person_registration->country == 'canada' ? 'selected' : ''}}>
                                                        Canada
                                                    </option>
                                                    <option value="australia"
                                                        {{$person_registration->country == 'australia' ? 'selected' : ''}}>
                                                        Australia
                                                    </option>
                                                    <option value="germany"
                                                        {{$person_registration->country == 'germany' ? 'selected' : ''}}>
                                                        Germany
                                                    </option>
                                                    <option value="france"
                                                        {{$person_registration->country == 'france' ? 'selected' : ''}}>
                                                        France
                                                    </option>

                                                    <option value="japan"
                                                        {{$person_registration->country == 'japan' ? 'selected' : ''}}>
                                                        Japan
                                                    </option>
                                                    <option value="china"
                                                        {{$person_registration->country == 'china' ? 'selected' : ''}}>
                                                        China
                                                    </option>
                                                    <option value="brazil"
                                                        {{$person_registration->country == 'brazil' ? 'selected' : ''}}>
                                                        Brazil
                                                    </option>
                                                    <option value="south-africa"
                                                        {{$person_registration->country == 'south-africa' ? 'selected' : ''}}>
                                                        South
                                                        Africa</option>
                                                    <option value="indian-origin"
                                                        {{$person_registration->country == 'indian-origin' ? 'selected' : ''}}>
                                                        Indian
                                                        Origin</option>
                                                </select>

                                            </div>

                                            <div class="col-sm-5 align-self-center">
                                                <label for="colFormLabelSm" class="col-form-label">Photo</label>
                                            </div>
                                            <div class="col-sm-7">
                                                <select class="form-select" name="photo_pic" id="photo_pic"
                                                    onchange="image_upload()">
                                                    <option value="">Select </option>
                                                    <option value="upload_pic"
                                                        {{$person_registration->photo_pic == 'upload_pic' ? 'selected' : ''}}>
                                                        Upload
                                                    </option>
                                                    <option value="snap_pic"
                                                        {{$person_registration->photo_pic == 'snap_pic' ? 'selected' : ''}}>
                                                        Snap</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>




                                <!-- Hidden ID field -->


                                <div class="row mb-3">
                                    <div class="" style="display:none;"> <label for="acc_year"
                                            class="col-sm-2 col-form-label">Academic Year</label>
                                        <div class="col-sm-4">
                                            <select class="form-select" name="academic_year" id="academic_year">
                                                <option value="">Select Academic Year</option>

                                                @foreach ($get_academic_year as $acc_year )
                                                <option value="{{$acc_year->unique_id}}"
                                                    {{ $acc_year->unique_id == $person_registration->academic_year ? 'selected' : ''}}>
                                                    {{$acc_year->acc_year}}
                                                </option>


                                                @endforeach
                                            </select>
                                        </div>
                                    </div>




                                    <!-- onclick="document.querySelector ('#tab2-link').click();" -->
                            </form>
                        </div> <!-- end tab-pane -->
                        <hr>

                        <h3>Document Details</h3>

                        <div class="" id="">
                            <form id="documentVerificationForm">
                                @csrf
                                @if(isset($acc_year))
                                @method('PUT')
                                @endif

                                <div class="row mb-3">
                                    <input type="hidden" id="main_unique_id" name="main_unique_id"
                                        value="{{$person_registration->form_unique_id}}">
                                    <input type="hidden" id="licence_val_sub" name="licence_val_sub"
                                        value="{{$person_sublist_reg_first->expiry_date}}">
                                    <label for="idproof" class="col-sm-2 col-form-label">Type of ID proof</label>
                                    <!-- <div class="col-sm-4">
                                        <select class="form-select" id="idproof" name="idproof"
                                            onchange="proof_change()">
                                            <option selected>Select ID Proof</option>
                                            <option value="Aadhaar">Aadhaar</option>
                                            <option value="Driving licence">Driving Licence</option>
                                        </select>
                                    </div> -->
                                    <div class="col-sm-4">
                                        <select class="form-select" id="idproof" name="idproof"
                                            onchange="proof_change()">
                                            <option selected>Select ID Proof</option>
                                            <option value="Aadhaar">Aadhaar</option>
                                            <option value="Driving Licence">Driving Licence</option>
                                        </select>
                                    </div>

                                    <div id="aadhar_id" style="display:none;" class="row mb-3 mt-3"> <label
                                            for="colFormLabelSm" class="col-sm-2 col-form-label">
                                            Aadhar
                                            No.
                                        </label>
                                        <div class="col-sm-4">
                                            <input type="text" readonly class="form-control" id="aadhaar_num"
                                                name="aadhaar_num" />
                                        </div>


                                        <label for="colFormLabelSm" class="col-sm-2 col-form-label">ID
                                            Proof</label>
                                        <div class="col-sm-4">
                                            <input type="file" class="form-control" id="aadhaar_proof"
                                                name="aadhaar_proof" />
                                        </div>
                                    </div>


                                    <!-- <div id="licence_id" style="display:none;"> -->

                                    <div id="licence_id" style="display:none;" class="row mb-3 mt-3">
                                        <label for="licence" class="col-sm-2 col-form-label">Licence No.</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" id="licence_no" name="licence_no" />
                                        </div>

                                        <label for="licence_proof" class="col-sm-2 col-form-label">ID Proof</label>
                                        <div class="col-sm-4">
                                            <input type="file" class="form-control" id="licence_proof"
                                                name="licence_proof" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" />
                                        </div>

                                        <label for="licence_expire" class="col-sm-2 col-form-label mt-3 driver">Licence
                                            Expire Date</label>
                                        <div class="col-sm-4 mt-3 driver">
                                            <input type="date" class="form-control" id="expiry_date"
                                                name="expiry_date" />
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
                                                <th>Aadhar No./Licence No.</th>
                                                <th>Licence Expiry</th>
                                                <th>Document</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $s_no = 1; @endphp
                                            @if ($person_sublist_reg && $person_sublist_reg->count() > 0)
                                            @foreach ($person_sublist_reg as $sublist)
                                            <tr>
                                                <td>{{ $s_no++ }}</td>
                                                <td>{{ $sublist->proof_type }}</td>
                                                <td>{{ $sublist->aadhar_licence }}</td>
                                                <td>
                                                    <a
                                                        onclick="imageeditViews('{{ $sublist->attached_proof }}', '{{ $sublist->proof_type }}')">

                                                        <!-- /harbour/public/uploads -->
                                                        <img src="{{ asset('/public/uploads/dwn.png') }}" width="40px"
                                                            height="40px">
                                                    </a>
                                                </td>
                                                <td>
                                                    <!-- <a class="text-primary" title="Edit"
                                                        onclick="editSublistItem('{{ $sublist->form_unique_id }}','{{ $sublist->proof_type }}','{{ $sublist->aadhar_licence }}','{{ $sublist->licence_expiry }}')"><i
                                                            class="fas fa-edit"></i></a> -->
                                                    <a class="text-primary" title="Edit"
                                                        onclick="editDocument('{{ $sublist->form_unique_id }}', '{{ $sublist->proof_type }}', '{{ $sublist->aadhar_licence }}', '{{ $sublist->licence_expiry }}')">
                                                        <i class="fas fa-edit"></i>
                                                    </a>


                                                    <a href="#" class="text-danger" title="Delete"
                                                        onclick="deleteSublistItem('{{ $sublist->id }}')"><i
                                                            class="fas fa-trash-alt"></i></a>
                                                </td>
                                            </tr>
                                            @endforeach
                                            @else
                                            <tr>
                                                <td colspan="6" class="text-center">No sublist records found.</td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>





                            </form>
                        </div> <!-- End Tab 2 -->






                        <!-- Include Font Awesome for the eye icon -->
                        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
                            rel="stylesheet">

                        <div class="btn-float">
                            <button type="button" class="btn btn-danger"
                                onclick="window.location='{{ route('person_registration') }}'">Cancel</button>
                            <button type="button" onclick="submit_function()" class="btn btn-primary">Update</button>
                            <!-- onclick="window.location='{{ route('user_registration_data') }}'" -->
                        </div>
                        </form>
                    </div> <!-- End Tab 3 -->








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

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">


<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>

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
    } else {
        // If no file is selected, keep the previous image visible
        preview.src =
            "{{ asset('/harbour/uploads/person_registration/' . $person_registration->image_upload) }}";
        preview.style.display = 'block';
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


function previewImage() {
    const file = document.getElementById('photo').files[0];
    const preview = document.getElementById('imagePreview');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(file);
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




function authentication() {
    // alert();
    // Get values from input fields
    var user_id = $('#login_id').val();
    var password = $('#password').val();
    var confirm_password = $('#confirm_password').val();
    var main_unique_id = $('#reg_unique_id').val();
    var rf_id = $('#rf_id').val();
    // Password validation checks
    // const hasUpperCase = /[A-Z]/.test(password);
    // const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);
    // const minLength = password.length >= 8;

    if (!user_id) {
        sweetalert("form_alert");
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

    var data = {
        user_id: user_id,
        // password: password,
        // confirm_password: confirm_password,
        main_unique_id: main_unique_id,
        rf_id: rf_id,
        _token: '{{csrf_token()}}'
    };

    $.ajax({
        type: "POST",
        url: "{{route('person_authentication.update',$person_registration->id)}}",
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



function submit_function() {
    // Create a new FormData object
    var formData = new FormData();

    // Get values from the input fields


    var main_unique_id = $('#reg_unique_id').val();

    var academic_year = $('#academic_year').val();
    var name = $('#full_name').val();
    var address = $('#address').val();
    var address_old = $('#address_old').val();

    if (address != '') {
        var address_updt = $('#address').val();
    } else {
        var address_updt = $('#address_old').val();
    }

    var register_number = $('#register_number').val(); // Ensure this is included in your validation
    var designation = $('#designation_select').val();
    var aadhaar = $('#aadhaar_no').val();
    var licence_validity_date = $('#expiry_date').val();
    var nationality = $('#nationality').val();
    var country = $('#country').val();
    var mobile = $('#mobile').val();
    var blood_group = $('#blood_group').val();
    var email_id = $('#email_id').val();
    var port_type = $('#port_type').val();

    // alert(port_type);

    // Append form fields to the FormData object
    formData.append('academic_year', academic_year);
    formData.append('name', name);
    formData.append('register_number', register_number); // Append this value
    formData.append('designation', designation);
    formData.append('aadhaar_no', aadhaar);
    formData.append('address', address_updt);


    formData.append('licence_validity_date', licence_validity_date);
    formData.append('nationality', nationality);
    formData.append('country', country);
    formData.append('mobile', mobile);
    formData.append('blood_group', blood_group);
    formData.append('email_id', email_id);
    formData.append('port_type', port_type);
    formData.append('photo_pic', $('#photo_pic').val());
    formData.append('_token', '{{csrf_token()}}');

    // // Validation checks
    // if (!academic_year || !name || !register_number || !designation ||
    //     !aadhaar || !nationality || !country || !mobile || !blood_group || !email_id) {
    //     Swal.fire({
    //         icon: 'warning',
    //         title: 'Form Incomplete',
    //         text: 'Please fill out all fields.',
    //         confirmButtonText: 'OK'
    //     });
    //     return;
    // }

    // Aadhaar validation
    const isValidAadhaar = /^\d{12}$/.test(aadhaar);
    if (!isValidAadhaar) {
        let validationError = 'Aadhaar Number must be exactly 12 digits and contain only numbers.';
        Swal.fire({
            icon: 'error',
            title: 'Invalid Aadhaar Number',
            text: validationError,
            confirmButtonText: 'OK'
        });
        return;
    }

    // Mobile validation
    const isValidMobile = /^\d{10,13}$/.test(mobile);
    if (!isValidMobile) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Mobile Number',
            text: 'Mobile Number must be between 10 to 13 digits long and contain only numbers.',
            confirmButtonText: 'OK'
        });
        return;
    }

    // Email validation
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

    // File handling
    var photo = document.getElementById('photo').files[0]; // Get the uploaded photo
    var old_image = $('#old_image').val(); // Assuming this holds the old image path or filename

    if (photo) {
        formData.append('photo', photo);
    } else {
        formData.append('previous_photo', old_image); // Use the old image path if no new photo is uploaded
    }

    // AJAX request to submit the form
    $.ajax({
        type: "POST",
        url: "{{ route('person_registration.update', $person_registration->id) }}", // Ensure the ID is passed correctly
        data: formData,
        contentType: false, // Important for sending FormData
        processData: false, // Prevent jQuery from processing the data
        success: function(response) {

            const redirecturl = "{{route('person_registration')}}";

            sweetalert("update", redirecturl);
        },
        error: function(xhr) {
            console.error(xhr.responseText); // Log any errors
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while updating the registration.',
                confirmButtonText: 'OK'
            });
        }
    });
}



function deleteSublistItem(form_unique_id) {
    // Use SweetAlert for confirmation
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: "{{ route('sublist.delete', ':form_unique_id') }}".replace(':form_unique_id',
                    form_unique_id),
                data: {
                    _token: '{{ csrf_token() }}' // Include CSRF token for security
                },
                success: function(response) {
                    // Show success message with auto-close
                    Swal.fire({
                        title: 'Deleted!',
                        text: response.message,
                        icon: 'success',
                        timer: 2000, // Auto-close after 2 seconds
                        timerProgressBar: true,
                        showConfirmButton: false
                    });

                    // Remove the corresponding row from the table
                    $('#sublist tr').filter(function() {
                        return $(this).find('a[onclick*="' + form_unique_id + '"]').length >
                            0;
                    }).remove();

                    // Optional: Check if the table is empty and show a message
                    if ($('#sublist tbody tr').length === 0) {
                        $('#sublist tbody').html(
                            '<tr><td colspan="5" class="text-center">No sublist records found.</td></tr>'
                        );
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    // Show error message
                    Swal.fire('Error!', 'An error occurred while deleting the item: ' + xhr
                        .responseText, 'error');
                }
            });
        }
    });
}

document.getElementById('idproof').addEventListener('change', function() {
    const aadhaarInput = document.getElementById('aadhaar_no');
    const proofNoInput = document.getElementById('aadhaar_num');

    // Check if Aadhaar is selected
    if (this.value === 'Aadhaar') {
        proofNoInput.value = aadhaarInput.value; // Set the proof_no value to the Aadhaar number
        proofNoInput.readOnly = true; // Set the input to readonly
    } else {
        proofNoInput.value = ''; // Clear the proof_no field if other option is selected
        proofNoInput.readOnly = false; // Make it editable
    }
});



// Initialize s_no based on the existing rows in the table
// let s_no = $('#sublist tbody tr').length + 1; // Set the initial serial number
// $s_no = 0;

// function sublist_proof(event) {
//     event.preventDefault();

//     // Get form values
//     var proof_type = $('#idproof').val();
//     var aadhaar_num = $('#aadhaar_num').val();
//     var licence_no = $('#licence_no').val();
//     var main_unique_id = $('#main_unique_id').val();
//     var licence_val_sub = $('#licence_val_sub').val();
//     var proof_value = aadhaar_num !== '' ? aadhaar_num : licence_no;

//     // Create FormData object to handle file uploads
//     var formData = new FormData();
//     formData.append('proof_type', proof_type);
//     formData.append('main_unique_id', main_unique_id);
//     formData.append('proof_value', proof_value);
//     formData.append('licence_val_sub', licence_val_sub);
//     formData.append('_token', '{{csrf_token()}}'); // Add CSRF token for security

//     // Handle file inputs (append only if the file is selected)
//     var aadhaar_proof = document.getElementById('aadhaar_proof').files[0];
//     var licence_proof = document.getElementById('licence_proof').files[0];

//     if (aadhaar_proof) {
//         formData.append('aadhaar_proof', aadhaar_proof);
//     }

//     if (licence_proof) {
//         formData.append('licence_proof', licence_proof);
//     }
//     // AJAX request to submit form data
//     $.ajax({
//         type: "POST",
//         url: '{{route("person_sublist.create")}}', // Update the route
//         data: formData,
//         contentType: false, // Important for file upload
//         processData: false, // Prevent jQuery from processing the data
//         success: function(response) {
//             var s_no = $('#sublist tbody tr').length + 1;
//             // Check if the response contains the necessary data
//             if (response && response.attached_proof) {
//                 var file_name = response.attached_proof;

//                 // Create a new row with the current serial number
//                 var newRow = `
//                     <tr>
//                         <td>${s_no}</td> <!-- Use current value of s_no -->
//                         <td>${response.proof_type}</td>
//                         <td>${response.aadhar_licence}</td>
//                         <td>
//                             <a onclick="imageViews('${file_name}', '${proof_type}')">
//                                 <img src='public/uploads/dwn.png' width="40px" height="40px">
//                             </a>
//                         </td>
//                         <td>
//                             <a class="text-primary" title="Edit" onclick="editDocument(${response.id}, '${response.proof_type}', '${response.aadhar_licence}', '${licence_val_sub}')">
//                                 <i class="fas fa-edit"></i>
//                             </a>
//                             <a href="#" class="text-danger" title="Delete" onclick="deleteSublistItem(${response.id})">
//                                 <i class="fas fa-trash-alt"></i>
//                             </a>
//                         </td>
//                         <td>${licence_val_sub ?? 'N/A'}</td>
//                     </tr>
//                 `;

//                 // Append the new row to the tbody of the #sublist table
//                 $('#sublist tbody').append(newRow);

//                 // Increment the serial number for the next entry
//                 // s_no++;

//                 // Clear input fields after successful submission
//                 $('#idproof').val('');
//                 $('#aadhaar_num').val('');
//                 $('#licence_no').val('');
//                 $('#aadhaar_proof').val('');
//                 $('#licence_proof').val('');
//             } else {
//                 console.error('Response does not contain attached proof:', response);
//             }
//         },
//         error: function(xhr) {
//             console.error('Error occurred during AJAX request:', xhr
//                 .responseText); // Log any errors for debugging
//         }
//     });
// }

function sublist_proof(event) {
    event.preventDefault();

    // Get form values
    var proof_type = $('#idproof').val();
    var aadhaar_num = $('#aadhaar_num').val();
    var licence_no = $('#licence_no').val();
    var main_unique_id = $('#main_unique_id').val();
    var licence_val_sub = $('#licence_val_sub').val();

    // var expiry_date = $('#expiry_date').val();



    if (licence_val_sub != '') {

        var expiry_date = $('#licence_val_sub').val();

    } else {
        var expiry_date = $('#expiry_date').val();

    }


    // Set default for non-existing values
    var proof_value = aadhaar_num !== '' ? aadhaar_num : licence_no;

    // Create FormData object to handle file uploads
    var formData = new FormData();
    formData.append('proof_type', proof_type);
    formData.append('main_unique_id',
        main_unique_id);
    formData.append('proof_value', proof_value);
    formData.append('expiry_date',
        expiry_date);
    formData.append('_token', '{{ csrf_token() }}'); // Add CSRF token for security

    // Handle file inputs (append only if the file is selected)
    var aadhaar_proof = document.getElementById('aadhaar_proof').files[0];
    var licence_proof = document.getElementById('licence_proof').files[0];
    if (aadhaar_proof) formData.append('aadhaar_proof', aadhaar_proof);
    if (licence_proof) formData.append('licence_proof', licence_proof);
    // if (licence_proof) formData.append('licence_proof', licence_proof);

    // AJAX request to submit form data
    $.ajax({
        type: "POST",
        url: '{{ route("person_sublist.create") }}', // Update the route
        data: formData,
        contentType: false, // Important for file upload
        processData: false, // Prevent jQuery from processing the data
        success: function(response) {
            // Reload the table after a successful submission
            loadTableData();
        },
        error: function(xhr) {
            console.log(xhr.responseText); // Log any errors for debugging
        }
    });
}

$(document).ready(function() {
    // Your code here
    loadTableData();
});

// Function to load table data
function loadTableData() {
    var main_unique_id = $('#main_unique_id').val();

    $.ajax({
        type: "GET",
        url: '{{ route("personSublist.index") }}',
        data: {
            main_unique_id: main_unique_id, // Pass the parameter directly
            _token: '{{ csrf_token() }}' // Include CSRF token for security
        },
        // Ensure this route fetches the latest sublist data
        success: function(data) {
            // Clear the current table body
            $('#sublist tbody').empty();
            // Populate the table with new data
            data.forEach(function(item, index) {

                // if(item.proof_type == 'Aadhaar'){
                //     var Directory = '{{ asset("public/uploads/person_registration/aadhaar/") }}';
                // }else{
                // var Directory = '{{ asset("public/uploads/person_registration/licence/") }}';
                // } 
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
                        <a class="text-primary" title="Edit" onclick="editDocument('${item.unique_id}', '${item.proof_type}', '${item.aadhar_licence}', '${item.expiry_date}')">
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


//-------------------------image and pdf view download function------///////

function editDocument(unique_id, proof_type, aadhar_licence, expiry_date) {
    // Set the certificate type dropdown
    $('#idproof').val(proof_type);
    alert(unique_id);
    alert(proof_type);
    alert(aadhar_licence);
    alert(expiry_date);

    // Reset all fields
    $('#aadhaar_num').val('').hide();
    $('#licence_no').val('').hide();
    $('#expiry_date').val('').hide();


    if (proof_type == 'Aadhaar') {
        $('#aadhaar_num').val(aadhar_licence);
        $('#licence_id').hide();
        $('#aadhar_id').show();
        $('#aadhaar_num').show();
        $('#licence_no').hide();

        // aadhaar_num

    } else if (proof_type == 'Driving Licence') {
        $('#licence_id').show();
        $('#aadhar_id').hide();
        $('#licence_no').val(aadhar_licence);
        $('#expiry_date').val(expiry_date);
        $('#licence_no').show();
        $('#expiry_date').show();
        $('#aadhaar_num').hide();


    }
}


// deleteProof

function deleteProof(form_unique_id) {
    alert(form_unique_id);
    // Use SweetAlert for confirmation
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: "{{ route('sublist.delete', ':unique_id') }}".replace(':unique_id',
                    form_unique_id),

                data: {
                    _token: '{{ csrf_token() }}' // Include CSRF token for security
                },
                success: function(response) {
                    // Show success message with auto-close
                    Swal.fire({
                        title: 'Deleted!',
                        text: response.message,
                        icon: 'success',
                        timer: 2000, // Auto-close after 2 seconds
                        timerProgressBar: true,
                        showConfirmButton: false
                    });

                    // Remove the corresponding row from the table
                    $('#sublist tr').filter(function() {
                        return $(this).find('a[onclick*="' + form_unique_id + '"]')
                            .length >
                            0;
                    }).remove();

                    // Optional: Check if the table is empty and show a message
                    if ($('#sublist tbody tr').length === 0) {
                        $('#sublist tbody').html(
                            '<tr><td colspan="5" class="text-center">No sublist records found.</td></tr>'
                        );
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    // Show error message
                    Swal.fire('Error!', 'An error occurred while deleting the item: ' + xhr
                        .responseText, 'error');
                }
            });
        }
    });
}


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

function view(fileName) {


    // alert('view');

    // var aadhaarDirectory = '{{ asset("public/uploads/person_registration/aadhaar/") }}';
    // var licenceDirectory = '{{ asset("public/uploads/person_registration/licence/") }}';

    var url = "{{ asset('public/uploads/person_registration/aadhaar/') }}" + fileName;


    window.open(url, '_blank', "width=600,height=480,toolbar=no,menubar=no,resizable=yes");

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



function valid_mobile_number(input) {
    var nationality = document.getElementById('nationality')
        .value; // Get nationality from the input element
    const allowedChars = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '+'];

    // Filter out characters that are not in the allowedChars array
    input.value = input.value.split('').filter(char => allowedChars.includes(char)).join('');

    const errorSpan = document.getElementById('mobileError');
    const mobileValue = input.value.trim();

    // Check based on nationality
    if (nationality.toLowerCase() === 'indian') {
        // For India, check for exactly 10 digits
        if (mobileValue.length > 10) {
            input.value = mobileValue.slice(0, 10); // Limit input to 10 digits
            errorSpan.textContent = 'For Indian nationality, mobile number should be exactly 10 digits.';
        } else if (mobileValue.length !== 10) {
            errorSpan.textContent = 'For Indian nationality, mobile number should be exactly 10 digits.';
        } else {
            errorSpan.textContent = '';
        }
    } else {
        // For foreign nationality, check for exactly 13 digits
        if (mobileValue.length > 13) {
            input.value = mobileValue.slice(0, 13); // Limit input to 13 digits
            errorSpan.textContent = 'For foreign nationality, mobile number should be exactly 13 digits.';
        } else if (mobileValue.length !== 13) {
            errorSpan.textContent = 'For foreign nationality, mobile number should be exactly 13 digits.';
        } else {
            errorSpan.textContent = '';
        }
    }
}

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

// Example usage
// var appFileName = "file1.jpg,file2.pdf,file3.xlsx,file4.doc"; // Replace this with actual data
// document.getElementById('yourElementId').innerHTML = imageViews(appFileName);

function download_pdf(fileName) {

    // alert('ji');
    var pdfUrl = "{{ asset('public/uploads/person_registration/aadhaar/') }}" + fileName;

    // var pdfUrl = "public/uploads/person_registration/aadhaar/" + fileName;
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

    // alert('licence');
    const designation = document.getElementById('designation_select').value;
    const licenceProofFields = document.getElementsByClassName('licence_valididty_date');

    for (let i = 0; i < licenceProofFields.length; i++) {
        if (designation === '66fe76256d3a792499') {
            licenceProofFields[i].style.display = 'block';
        } else {
            licenceProofFields[i].style.display = 'none';
        }
    }
}

$(document).ready(function() {
    // editSublistItem();
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


// function editSublistItem(form_unique_id, proof_type, aadhar_licence, licence_expiry) {
//     // Set the certificate type dropdown
//     $('#idproof').val(proof_type);
//     alert(aadhar_licence);
//     // Set other fields based on the type of certificate
//     if (proof_type == 'Aadhaar') {
//         $('#aadhaar_num').val(aadhar_licence);
//         $('#licence_id').hide();
//         $('#aadhar_id').show();

//     } else if (proof_type == 'Driving Licence') {
//         $('#licence_id').show();
//         $('#aadhar_id').hide();
//         $('#licence_no').val(aadhar_licence);
//         $('#expiry_date').val(licence_expiry);


//     }
// }



// function editSublistItem(form_unique_id, proof_type, aadhar_licence, licence_expiry) {
//     console.log("Editing item with ID:", form_unique_id);
//     console.log("Proof Type:", proof_type);
//     console.log("Aadhaar License:", aadhar_licence);
//     console.log("License Expiry:", licence_expiry);

//     // Set the certificate type dropdown
//     $('#idproof').val(proof_type);

//     // Show/hide fields and set values based on the proof type
//     if (proof_type === 'Aadhaar') {
//         $('#aadhaar_num').val(aadhar_licence); // Set Aadhaar number
//         $('#aadhar_id').show(); // Show Aadhaar section
//         $('#licence_id').hide(); // Hide Driving License section

//         // Clear Driving License fields if previously filled
//         $('#licence_no').val('');
//         $('#expiry_date').val('');
//         console.log("Aadhaar section displayed.");
//     } else if (proof_type === 'Driving Licence') {
//         $('#licence_no').val(aadhar_licence); // Set Driving License number
//         $('#expiry_date').val(licence_expiry); // Set Driving License expiry date
//         $('#licence_id').show(); // Show Driving License section
//         $('#aadhar_id').hide(); // Hide Aadhaar section

//         // Clear Aadhaar fields if previously filled
//         $('#aadhaar_num').val('');
//         console.log("Driving Licence section displayed.");
//     } else {
//         console.log("No valid proof type selected.");
//     }
// }
</script>

</script>


@endsection