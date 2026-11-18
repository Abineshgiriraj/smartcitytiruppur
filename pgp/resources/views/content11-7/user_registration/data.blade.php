@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Online Registration/</span> Data Registration</h4>
    </div>
</div>

<style>
/* btn style */
.btn-right {
    float: right;
}
</style>
<!-- Basic Layout -->
<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-body">
                <form id="academicYearForm">
                    @csrf
                    @if(isset($acc_year))
                    @method('PUT')
                    @endif
                    <div class="row mb-3">
                        <h5>Personal Information</h5>

                        <div class="col-md-12 mb-2">
                            <label class="form-label" for="fullname">Full Name :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp; &nbsp;
                                &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;Jeevan</label>

                        </div>

                        <div class="col-md-12 mb-2">
                            <label class="form-label" for="email">Email ID:
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;
                                &nbsp;&nbsp;&nbsp; &nbsp;
                                &nbsp;jeeva123@gmail.com</label>
                        </div>

                        <div class="col-md-12 mb-2">
                            <label class="form-label" for="mobile">Mobile Number
                                :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;
                                &nbsp;9876543210</label>
                            <!-- <input type="text" class="form-control" id="mobile" name="mobile" /> -->
                        </div>

                        <div class="col-md-12 mb-2">
                            <label class="form-label" for="aadhaar">Aadhaar Number :&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;90876543219</label>
                        </div>

                        <div class="col-md-12 mb-2">
                            <label class="form-label" for="basic-default-fullname">Driving License No :
                                &nbsp;&nbsp; &nbsp; DHIH3455</label>
                            <!-- <input type="text" class="form-control" id="designation" name="designation" /> -->
                        </div>
                        <div class="col-md-12 mb-2">
                            <label class="form-label" for="basic-default-fullname">Blood Group : &nbsp;&nbsp;
                                &nbsp;o+Ve</label>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label class="form-label" for="basic-default-fullname">Company Name : ABC </label>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label class="form-label" for="basic-default-fullname">Address : 89,Kovil
                                street,Kovilpatti(tk),Thoothukudi</label>
                        </div>
                    </div>


                    <div class="col-md-12 mb-2">
                        <label class="form-label" for="basic-default-fullname">User ID : J1234</label>
                    </div>



                    <div class="row mb-3">
                    </div>
                    <div class="row mb-3">
                        <h5>Document Upload</h5>
                        <div class="col-md-3">
                            <label class="form-label" for="basic-default-fullname">Aadhar Doc Upload : </label>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label" for="basic-default-fullname">Driving License</label>
                            <input type="file" class="form-control" id="user_id" name="user_id" />
                        </div>

                        <div class="col-md-3">
                            <label class="form-label" for="basic-default-fullname">Photo</label>
                            <input type="file" class="form-control" id="passwrd" name="passwrd" />
                        </div>

                    </div>



                    <!-- <div class="col-md-4 mb-3">
                            <label class="form-label" for="basic-default-message">Description</label>
                            <textarea id="remarks" class="form-control"
                                name="remarks">{{ isset($acc_year) ? $acc_year->remarks : '' }}</textarea>
                        </div> -->

            </div>
            <div class="btn-float">
                <button type="button" class="btn btn-danger"
                    onclick="window.location='{{ route('user_registration') }}'">Cancel</button>
                <!-- <button type="submit" class="btn btn-primary">Submit</button> -->
                <!-- <button type="submit" class="btn btn-primary"></button> -->
            </div>
            </form>
        </div>
    </div>
</div>
</div>


<script src=" {{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>


<script>
@if(!isset($acc_year))
// Get current date
var today = new Date();

// Format the current date as YYYY-MM (required format for type="month")
var year = today.getFullYear();
var month = (today.getMonth() + 1).toString().padStart(2, '0'); // Months are 0-indexed, so we add 1

var currentMonthYear = year + '-' + month;

// Set the value of the input if not editing
document.getElementById('from_year').value = currentMonthYear;
document.getElementById('to_year').value = currentMonthYear;
@endif
$('#academicYearForm').submit(function(event) {

    event.preventDefault(); // Prevent the default form submission

    // Validate the form
    let isValid = validateForm();
    if (!isValid) {
        return false;
    }

    // Gather form data
    var formData = {
        from_year: $('#from_year').val(),
        to_year: $('#to_year').val(),
        acc_year: $('#acc_year').val(),
        remarks: $('#remarks').val(),
        _token: '{{ csrf_token() }}'
    };

    // Determine method and URL based on whether $acc_year is set
    var method = "{{ isset($acc_year) ? 'PUT' : 'POST' }}";
    var url =
        "{{ isset($acc_year) ? route('academic_year_creation.update', $acc_year->id) : route('academic_year_creation.store') }}";

    $.ajax({
        url: url,
        method: method,
        data: formData,

        success: function(response) {
            Swal.fire({
                title: 'Success!',
                text: 'Academic Year ' + (method === 'POST' ? 'added' :
                        'updated') +
                    ' successfully!',
                icon: 'success',
                timer: 1500, // Time in milliseconds (1500ms = 1.5 seconds)
                showConfirmButton: false, // Hide the confirm button
                willClose: () => {
                    // Redirect after the alert closes
                    window.location.href =
                        "{{ route('academic_year_creation_list') }}";
                }
            });
        },

        error: function(xhr) {
            console.log(xhr.responseText);
            if (xhr.status === 422) { // Validation error status code
                var errors = xhr.responseJSON.errors;
                if (errors.acc_year) {
                    Swal.fire(
                        'Warning',
                        'Academic year already exists.',
                        'warning'
                    );
                } else {
                    // Handle other validation errors
                    Swal.fire(
                        'Warning',
                        'Please check the form for errors.',
                        'warning'
                    );
                }
            } else {
                Swal.fire(
                    'Error!',
                    'An error occurred. Please try again.',
                    'error'
                );
            }
        }
    });
});

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