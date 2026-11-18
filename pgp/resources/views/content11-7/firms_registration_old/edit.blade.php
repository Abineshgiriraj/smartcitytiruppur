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
        <form id="firmsForm_edit" method="POST" action="{{ route('firms.update', $firms_registration->unique_id) }}">
            @csrf
            @method('PUT')
            @if(isset($firms_registration))
                <input type="hidden" id="unique_id" name="unique_id" value="{{ $firms_registration->unique_id }}">
            @endif

            @if($firms_sublist_reg->isNotEmpty())
                @foreach($firms_sublist_reg as $sublist)
                    <input type="hidden" name="sublist_id[]" value="{{ $sublist->form_unique_id }}">
                @endforeach
            @endif

            <div class="row mb-3">
                <div class="col-sm-2 mt-3">
                    <label for="firms" class="col-form-label">Type of Firms / Agent</label>
                </div>
                <div class="col-sm-4 mt-3">
                    <select class="form-select" id="firms" name="firms" required>
                        <option selected disabled>Select Firms</option>
                        <option value="Stevedore" {{ (isset($firms_registration) && $firms_registration->firms == 'Stevedore') ? 'selected' : '' }}>Stevedore</option>
                        <option value="Ship Agent" {{ (isset($firms_registration) && $firms_registration->firms == 'Ship Agent') ? 'selected' : '' }}>Ship Agent</option>
                        <option value="Custom Broker" {{ (isset($firms_registration) && $firms_registration->firms == 'Custom Broker') ? 'selected' : '' }}>Custom Broker</option>
                        <option value="CFS" {{ (isset($firms_registration) && $firms_registration->firms == 'CFS') ? 'selected' : '' }}>CFS (Container Freight Station)</option>
                    </select>
                </div>

                <div class="col-sm-2 mt-3">
                    <label class="col-form-label" for="firm_id">Firm ID:</label>
                </div>
                <div class="col-sm-4">
                    <label class="col-sm-12 col-form-label mt-3 firm-id-label">
                        <b id="firm_id">{{ $firms_registration->firm_id}}</b>
                    </label>
                    <input type="hidden" id="hidden_firm_id" name="firm_id" value="{{ isset($firms_registration) ? $firms_registration->firm_id : '' }}">
                </div>

                <div class="col-sm-2 mt-3">
                    <label for="company_reg_no" class="col-form-label">Company Reg No.</label>
                </div>
                <div class="col-sm-4">
                    <input type="text" class="form-control mt-3" id="company_reg_no" name="company_reg_no" value="{{ old('company_reg_no', $firms_registration->company_reg_no ?? '') }}" required />
                </div>

                <div class="col-sm-2 mt-3">
                    <label for="name" class="col-form-label">Company Name</label>
                </div>
                <div class="col-sm-4">
                    <input type="text" class="form-control mt-3" id="name" name="name" value="{{ old('name', $firms_registration->name ?? '') }}" required />
                </div>

                <div class="col-sm-2 mt-3">
                    <label for="mobile" class="col-form-label">Mobile no</label>
                </div>
                <div class="col-sm-4">
                    <input type="tel" class="form-control mt-3" id="mobile" name="mobile" maxlength="15" value="{{ old('mobile', $firms_registration->mobile ?? '') }}" required />
                </div>

                <div class="col-sm-2 mt-3">
                    <label for="email" class="col-form-label">Email Id</label>
                </div>
                <div class="col-sm-4">
                    <input type="email" class="form-control mt-3" id="email" name="email" value="{{ old('email', $firms_registration->email ?? '') }}" required />
                </div>

                <div class="col-sm-2 mt-3">
                    <label for="aadhar_no" class="col-form-label">Aadhar No</label>
                </div>
                <div class="col-sm-4">
                    <input type="number" class="form-control mt-3" id="aadhar_no" name="aadhar_no" maxlength="12" value="{{ old('aadhar_no', $firms_registration->aadhar_no ?? '') }}" required />
                </div>

                <div class="col-sm-2 mt-3">
                    <label for="contact_person" class="col-form-label">Contact Person</label>
                </div>
                <div class="col-sm-4">
                    <input type="text" class="form-control mt-3" id="contact_person" name="contact_person" value="{{ old('contact_person', $firms_registration->contact_person ?? '') }}" required />
                </div>

                <div class="col-sm-2 mt-3">
                    <label for="fax_no" class="col-form-label">FAX No</label>
                </div>
                <div class="col-sm-4">
                    <input type="number" class="form-control mt-3" id="fax_no" name="fax_no" value="{{ old('fax_no', $firms_registration->fax_no ?? '') }}" />
                </div>

                <div class="col-sm-2 mt-3">
                    <label for="gstin" class="col-form-label">GSTIN</label>
                </div>
                <div class="col-sm-4">
                    <input type="text" class="form-control mt-3" id="gstin" name="gstin" value="{{ old('gstin', $firms_registration->gstin ?? '') }}" />
                </div>

                <div class="col-sm-2 mt-3">
                    <label for="address_1" class="col-form-label">Address 1</label>
                </div>
                <div class="col-sm-4">
                    <textarea class="form-control mt-3" id="address_1" name="address_1" rows="1">{{ old('address_1', $firms_registration->address_1 ?? '') }}</textarea>
                </div>

                <div class="col-sm-2 mt-3">
                    <label for="address_2" class="col-form-label">Address 2</label>
                </div>
                <div class="col-sm-4">
                    <textarea class="form-control mt-3" id="address_2" name="address_2" rows="1">{{ old('address_2', $firms_registration->address_2 ?? '') }}</textarea>
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
            <input type="text" id="main_unique_id" name="main_unique_id" value="{{$firms_registration->unique_id}}">

            <label for="idproofSelect" class="col-sm-2 col-form-label">Type of ID proof</label>
            <div class="col-sm-4 fm">
                <select class="form-select" id="idproof" name="idproof" onchange="proof_change()">
                    <option selected>Select ID Proof</option>
                    <option value="aadhaar">Aadhaar</option>
                    <option value="company">Company Licence</option>
                </select>
            </div>
        </div>

        <div id="aadhar_id" style="display:none;" class="row mb-3 mt-3">
            <label for="aadhaar_num" class="col-sm-2 col-form-label">Aadhar No.</label>
            <div class="col-sm-4">
                <input type="text" class="form-control" id="aadhaar_num" name="aadhaar_num" />
            </div>
            <label for="aadhaar_license" class="col-sm-2 col-form-label">ID Proof</label>
            <div class="col-sm-4">
                <input type="file" class="form-control" id="aadhaar_license" name="aadhaar_license" />
            </div>
        </div>

        <div id="licence_id" style="display:none;" class="row mb-3 mt-3">
            <label for="licence_no" class="col-sm-2 col-form-label">Company Licence No.</label>
            <div class="col-sm-4">
                <input type="text" class="form-control" id="licence_no" name="licence_no" />
            </div>
            <label for="licence_proof" class="col-sm-2 col-form-label">ID Proof</label>
            <div class="col-sm-4">
                <input type="file" class="form-control" id="licence_proof" name="licence_proof" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" />
            </div>
        </div>
        <div class="d-flex justify-content-center mb-4">
                                    <button id="openTableButton submit_sublist" onclick="sublist_proof(event)" class="btn btn-primary">Add</button>
                                </div>
        <div id="dataTableContainer" class="table-responsive mt-4">
            <table id="sublist" class="table table-striped table-bordered" style="width:100%">
                <thead class="thead-light">
                    <tr>
                        <th>S.No</th>
                        <th>Type of ID Proof</th>
                        <th>Aadhar No</th>
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
                        <td>{{ $sublist->aadhar_licence }}</td>
                        <td>
                            <a onclick="imageViews('{{ $sublist->attached_proof }}', '{{ $sublist->proof_type }}')">
                                <img src="{{ asset('uploads/firms_registration/.png') }}" width="40px" height="40px">
                            </a>
                        </td>
                        <td>
                        <button type="button" class="btn btn-warning"
        onclick="editDocument({{ $sublist->id }}, '{{ $sublist->proof_type }}', '{{ $sublist->aadhar_licence }}')">
        <i class="fas fa-edit"></i>
    </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteSublistItem('{{ $sublist->form_unique_id }}')">Delete</button>
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="5" class="text-center">No sublist records found.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="btn-float">
            <button type="button" class="btn btn-primary" onclick="document.querySelector('#tab3-link').click();">Next</button>
        </div>
    </form>
</div>
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
                                            <input type="text" class="form-control" name="login_id" id="login_id" value="{{$firms_registration->login_id}}">

                                        </div>
                                        <div class="col-sm-2 mt-3 password-container">
                                            <label for="colFormLabelSm" class="col-form-label">Password</label>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="input-wrapper">
                                                <input type="password" class="form-control  mt-3" id="password"
                                                    name="password" oninput="valid_password(this)"  onkeyup="password_change()" />
                                                <i class="fas fa-eye toggle-password" id="togglePassword" ></i>
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

                                                <i class="fas fa-eye toggle-password" id="toggleConfirmPassword"></i>

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

    var proof = $('#idproofSelect').val();

    if (proof === 'aadhaar') {
        $('#aadhar_id').show();
        $('#licence_id').hide();
    } else if (proof === 'company') {
        $('#licence_id').show();
        $('#aadhar_id').hide();
    }
}



    function submit_next()
{
        // e.preventDefault(); // Prevent the default form submission
        // alert($('#firms').val());
        $('#firm_id').parent().hide();
        console.log("Firm ID is stored and displayed hidden.");
        var formData = new FormData();

// Append form fields to the FormData object
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
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const firmId = this.getAttribute('data-id');
        if (confirm('Are you sure you want to delete this firm?')) {
            fetch(this.action, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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

document.addEventListener('DOMContentLoaded', function () {
    // Add event listener when second tab is clicked or shown
    document.querySelector('a[href="#basictab2"]').addEventListener('click', function() {
        // Get the value of the Aadhaar number from the first tab
        var aadhaarNo = document.getElementById('aadhar_no').value;

        // Set the value of the Aadhaar number in the second tab
        document.getElementById('adhaar').value = aadhaarNo;
    });
});


function sublist_proof(event) {
    event.preventDefault();
    // Get form values
    var proof_type = $('#idproof').val();
    var aadhaar_num = $('#aadhaar_num').val();
    var licence_no = $('#licence_no').val();
    var main_unique_id = $('#main_unique_id').val();
    var licence_val_sub = $('#licence_val_sub').val();
    var proof_value = aadhaar_num !== '' ? aadhaar_num : licence_no;

    // Create FormData object to handle file uploads
    var formData = new FormData();
    formData.append('proof_type', proof_type);
    formData.append('main_unique_id', main_unique_id);
    formData.append('proof_value', proof_value);
    formData.append('_token', '{{csrf_token()}}'); // Add CSRF token for security

    // Handle file inputs (append only if the file is selected)
    var aadhaar_proof = document.getElementById('aadhaar_proof').files[0];
    var licence_proof = document.getElementById('licence_proof').files[0];

    if (aadhaar_proof) {
        formData.append('aadhaar_proof', aadhaar_proof);
    }

    if (licence_proof) {
        formData.append('licence_proof', licence_proof);
    }

    // AJAX request to submit form data
    $.ajax({
        type: "POST",
        url: '{{route("person_sublist.create")}}', // Update the route
        data: formData,
        contentType: false, // Important for file upload
        processData: false, // Prevent jQuery from processing the data

        success: function(response) {
          if (response.message) {
                    // Show success message (SweetAlert or any other method)
                    Swal.fire('Success', response.message, 'success');
          }
            var file_name = response.attached_proof;
            var proof_type = response.proof_type;
            var newRow = `
                <tr>
                    <td>${s_no++}</td>
                    <td>${response.proof_type}</td>
                    <td>${response.aadhar_licence}</td>
                    <td>${licence_val_sub}</td>
                    <td>
                        <a onclick ="imageViews('${file_name}','${proof_type}')">
                            <img src='public/uploads/download.png' width="40px" height="40px">
                        </a>
                    </td>
                    <td>
                        <button class="btn btn-danger btn-sm" onclick="deleteProof(${response.id})">Delete</button>
                    </td>
                </tr>
            `;

            // Append the new row to the tbody of the #sublist table
            sublistTable.row.add($(newRow)).draw(); // Use DataTables API to add a row

            // Optionally, reset form fields after successful submission
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

// function sublist_proof(event) {
//   alert("jiiiiii");
//     event.preventDefault(); // Prevent default form submission

//     // Get form values
//     var main_unique_id = $('#main_unique_id').val();
//     var proof_type = $('#idproof').val(); // Ensure this matches your HTML
//     var adhaar = $('#adhaar').val();
//     var aadhar_licence = $('#aadhar_licence').val(); // Ensure this ID exists

//     // Set proof_value to the first non-empty value
//     var proof_value = adhaar !== '' ? adhaar : aadhar_licence;
//     var s_no = 1;

// var s_nos = s_no++;
//     // Create FormData object to handle file uploads
//     var formData = new FormData();
//     formData.append('main_unique_id',main_unique_id);
//     formData.append('proof_type', proof_type);
//     formData.append('aadhaar_license', aadhar_licence); // Corrected to use 'aadhaar_license'
//     formData.append('_token', '{{ csrf_token() }}'); // Add CSRF token for security

//     // Handle file inputs
//     var id_proof = document.getElementById('id_proof').files[0]; // Update to correct ID for file input
//     if (id_proof) {
//         formData.append('attached_proof', id_proof);
//     }

//     // AJAX request to submit form data
//     $.ajax({
//         type: "POST",
//         url: '{{ route("firms_sublist.create") }}', // Ensure the route is correct
//         data: formData,
//         contentType: false,
//         processData: false,
//         success: function(response) {
//             if (response && response.FirmsRegistrationSub) {
//                 var newRow = `
//                     <tr>
//                         <td></td>
//                         <td>${response.proof_type}</td>
//                         <td>${proof_value}</td>
//                         <td>
//                             <a onclick="imageViews('${response.attached_proof}', '${response.proof_type}')">
//                                 <img src='public/uploads/download.png' width="40px" height="40px">
//                             </a>
//                         </td>
//                         <td>
//                             <button class="btn btn-danger btn-sm" onclick="deleteProof(${response.FirmsRegistrationSub.id})">Delete</button>
//                         </td>
//                     </tr>
//                 `;

//                 // Append the new row to the tbody of the #sublist table
//                 $('#sublist tbody').append(newRow);

//                 // Reset form fields after successful submission

//                 $('#idproofSelect').val('');
//                 $('#adhaar').val('');
//                 $('#id_proof').val('');
//                 $('#aadhar_licence').val('');
//             }
//         },
//         error: function(xhr) {
//             console.log(xhr.responseText); // Log any errors for debugging
//             if (xhr.responseJSON && xhr.responseJSON.errors) {
//                 // Show validation errors
//                 $.each(xhr.responseJSON.errors, function(key, value) {
//                     alert(value[0]); // Display the first error message
//                 });
//             } else {
//                 alert('An error occurred. Please try again.'); // Generic error message
//             }
//         }
//     });
// }

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
    var main_unique_id = $('#unique_id').val();

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

function sub_authentication() {
    // Get values from input fields
    var login_id = $('#login_id').val();
    var password = $('#password').val();
    var confirm_password = $('#confirm_password').val();
    var main_unique_id = $('#unique_id').val();

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
        url: "{{route('firms_authentication.update',$firms_registration->unique_id)}}",
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


function editDocument(id, proof_type, aadhar_licence, licence_expiry) {
    // Set the certificate type dropdown
    $('#idproof').val(proof_type);

    // Set other fields based on the type of certificate
    if (proof_type == 'aadhaar') {
        $('#aadhaar_num').val(aadhar_licence);

        $('#aadhar_id').show();

    } else if (proof_type == 'driving_licence') {
      $('#licence_id').show();
      $('#licence_no').val(aadhar_licence);

        $('#licence_expire').val(licence_expiry);


    }
}

    </script>


    @endsection
