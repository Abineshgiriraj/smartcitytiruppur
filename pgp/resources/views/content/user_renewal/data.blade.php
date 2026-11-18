@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Online Registration/</span> User Renewal</h4>
    </div>
</div>

<style>
/* btn style */
.btn-right {
    float: right;
}

.sublist {
    margin-left: 30px;
    width: 60px;
    height: 30px;
    margin-bottom: 30px;
}

.custom-hr {
    border-top: 3px solid #3498db;

    /* 3px thickness with a blue color */
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
                        <hr class="my-4 custom-hr">

                        <div class="row">
                            <div class="col-6">
                                <div class="row">
                                    <div class=" col-md-12 mb-2">
                                        <label class="form-label" for="fullname">Full Name
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            &nbsp;&nbsp;&nbsp; &nbsp;
                                            &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;Jeevan</label>

                                    </div>

                                    <div class="col-md-12 mb-2">
                                        <label class="form-label" for="email">Email ID
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            &nbsp;
                                            &nbsp;&nbsp;&nbsp;
                                            &nbsp;: &nbsp;
                                            &nbsp;jeeva123@gmail.com</label>
                                    </div>

                                    <div class="col-md-12 mb-2">
                                        <label class="form-label" for="mobile">Mobile Number
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;9876543210</label>
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
                                </div>
                            </div>
                            <!-- </div> -->
                            <div class="col-6">
                                <div class="row">
                                    <div class="col-md-12 mb-2">
                                        <label class="form-label" for="basic-default-fullname">Blood Group
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            &nbsp;&nbsp;&nbsp; &nbsp;
                                            :&nbsp;&nbsp;&nbsp; o+Ve</label>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <label class="form-label" for="basic-default-fullname">Company Name
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;
                                            &nbsp;ABC
                                        </label>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <label class="form-label"
                                            for="basic-default-fullname">Address&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            &nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;89,Kovil
                                            street,Kovilpatti(tk),Thoothukudi</label>
                                    </div>
                                </div>


                                <div class="col-md-12 mb-2">
                                    <label class="form-label" for="basic-default-fullname">User
                                        ID&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;&nbsp;&nbsp;J1234</label>
                                </div>
                            </div>
                        </div>
                        <hr class="my-4 custom-hr">

                        <!-- </div> -->

                        <!-- </div> -->

                        <div class="row mb-3">
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label" for="selected_value">Choose an Option:</label>
                                <select class="form-select" id="selected_value" name="selected_value"
                                    onchange="change_option()">
                                    <option value="">Select an option</option>
                                    <option value="year">Year</option>
                                    <option value="month">Month</option>
                                    <option value="date">Date</option>
                                </select>
                            </div>
                            <div id="dynamicFields" style="display:none;" class="col-md-3 mb-2"></div>
                            <div class="col-md-3">
                                <label class="form-label" for="basic-default-fullname">Access Area </label>
                                <input type="text" class="form-control" id="user_id" name="user_id" />

                            </div>

                            <div class="col-md-3">
                                <label class="form-label" for="basic-default-fullname">Fee</label>
                                <input type="text" class="form-control" id="user_id" name="user_id" />
                            </div>

                            <div class="btn-float">
                                <button type="button" class="btn btn-primary">Save</button>
                                <!-- <button type="submit" class="btn btn-primary">Submit</button> -->
                                <!-- <button type="submit" class="btn btn-primary"></button> -->
                            </div>




                        </div>



                        <!-- <div class="col-md-4 mb-3">
                            <label class="form-label" for="basic-default-message">Description</label>
                            <textarea id="remarks" class="form-control"
                                name="remarks">{{ isset($acc_year) ? $acc_year->remarks : '' }}</textarea>
                        </div> -->



                        <div class="row">
                            <table class="table table-bordered sublist">
                                <thead>
                                    <th>S.no</th>
                                    <th>Entry Date</th>
                                    <th>Validity Type(Month)</th>
                                    <th>Expired(Month) </th>
                                    <th>Approval verification</th>
                                    <th>Access Area Name</th>
                                    <th>Fees</th>
                                    <th>Payment</th>
                                </thead>
                                <tbody>
                                    <td>1</td>
                                    <td>20.09.2024</td>
                                    <td>November 2024</td>
                                    <td>December 2024</td>
                                    <td>Kumar</td>
                                    <td>Kovilpatti</td>
                                    <td>15000</td>
                                    <td>Completed</td>
                                </tbody>


                            </table>
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
</div>
</div>


<script src=" {{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>


<script>
function change_option() {
    // alert();
    var selectElement = document.getElementById('selected_value');
    var selected_value = selectElement.value;
    var dynamicFields = document.getElementById('dynamicFields');

    dynamicFields.innerHTML = '';
    if (selected_value) {

        dynamicFields.style.display = 'block';
        if (selected_value === 'year') {
            dynamicFields.innerHTML = `<div class="row">
        <div class="col-md-6 mb-2">
            <label for="from_year">From Year:</label>
            <input type="number" id="from_year" name="from_year" class="form-control" placeholder="YYYY" min="1900" max="2100" />
        </div>
        <div class="col-md-6 mb-2">
            <label for="to_year">To Year:</label>
            <input type="number" id="to_year" name="to_year" class="form-control" placeholder="YYYY" min="1900" max="2100" />
        </div>
    </div>`;
        } else if (selected_value === 'month') {
            dynamicFields.innerHTML = `<div class="row">
          <div class="col-md-6 mb-2">
            <label for="from_month">From Month:</label>
            <input type="month" id="from_month" name="from_month" class="form-control" />
            </div>

              <div class="col-md-6 mb-2">
        <label for="to_month" class="mt-2">To Month:</label>

            <input type="month" id="to_month" name="to_month" class="form-control" />
            </div>
            </div>
        `;
        } else if (selected_value === 'date') {
            dynamicFields.innerHTML = `<div class="row">
                      <div class="col-md-6 mb-2">
            <label for="from_date">From Date:</label> 
            <input type="date" id="from_date" name="from_date" class="form-control" value="date(Y-m-d)" />
            </div>
             <div class="col-md-6 mb-2">
            <label for="to_date" class="mt-2">To Date:</label>
            <input type="date" id="to_date" name="to_date" class="form-control" />
            </div>
            </div>
        `;
        }


    } else {
        dynamicFields.style.display = 'none'; // Hide the dynamic fields div if no option is selected
    }
}
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