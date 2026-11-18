@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Access IN & OUT/</span> Person Access Center</h4>
    </div>
</div>

<style>
/* btn style */
.btn-right {
    float: right;
}
</style>
<!-- Basic Layout -->
<form id="academicYearForm">
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-body">
                    @csrf
                    @if(isset($acc_year))
                    @method('PUT')
                    @endif
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label" for="rfid_no">Center Name - Gate 1</label>
                            <label class="form-label" for="rfid_no">Center ID - gate001</label>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label" for="rfid_no">RFID No</label>
                            <input type="text" class="form-control" id="rfid_no" name="rfid_no" />
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-primary mt-4" onclick="get_div()">Go</button>
                        </div>
                    </div>
                </div>
            </div>
            </br>
            <div class="card mb-4" style="display:none;" id="style-div">
                <div class="card-body">
                    <div class="row mb-3">
                        <h5>Personal Information </h5>
                        <div class="col-md-3">
                            <h6>Full Name</h6>
                            <label class="form-label" for="basic-default-fullname">Monish</label>
                        </div>

                        <div class="col-md-3">
                            <h6>E-mail ID</h6>
                            <label class="form-label" for="basic-default-fullname">monish@gmail.com</label>
                        </div>
                        <div class="col-md-3">
                            <h6>Mobile Number</h6>
                            <label class="form-label" for="basic-default-fullname">9867536271</label>
                        </div>

                        <div class="col-md-3">
                            <h6>Aadhar Number</h6>
                            <label class="form-label" for="basic-default-fullname">1234 4321 1234</label>
                        </div>
                    </div>
                    </br>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <h6>Driving License No</h6>
                            <label class="form-label" for="basic-default-fullname">TN-36983011</label>
                        </div>
                        <div class="col-md-3">
                            <h6>Blood Group</h6>
                            <label class="form-label" for="basic-default-fullname">O +ve</label>
                        </div>
                        <div class="col-md-3">
                            <h6>Company Name</h6>
                            <label class="form-label" for="basic-default-fullname">Ascent</label>
                        </div>
                        <div class="col-md-3">
                            <h6>Address</h6>
                            <label class="form-label" for="basic-default-fullname">76\21, test street, chennai</label>
                        </div>
                    </div>
                    </br>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <h6>Access Area</h6>
                            <label class="form-label" for="basic-default-fullname">Admin Office</label>
                        </div>
                        <div class="col-md-3">
                            <h6>valid Upto</h6>
                            <label class="form-label" for="basic-default-fullname">10th March 2024 - 07:00 PM</label>
                        </div>
                    </div>
                    </br>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <h6>Check-IN</h6>
                            <label class="form-label" for="basic-default-fullname">10:00 AM</label>
                        </div>
                        <div class="col-md-3">
                            <h6>Check-Out</h6>
                            <label class="form-label" for="basic-default-fullname">01:00 PM</label>
                        </div>
                        <div class="col-md-3">
                            <h6>Total Time</h6>
                            <label class="form-label" for="basic-default-fullname">03 Hours</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</form>


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

function get_div() {
    var div = document.getElementById("style-div");
    if (div) {
        div.style.display = "block"; // Set the div to display as block
    }
}
</script>

@endsection