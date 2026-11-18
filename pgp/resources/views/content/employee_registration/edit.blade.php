@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Common Module/</span> User Registration</h4>
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
                        <h5>Personal Information </h5>
                        <div class="col-md-3">
                            <label class="form-label" for="basic-default-fullname">Full Name</label>
                            <input type="text" class="form-control" id="designation" name="designation" />
                        </div>

                        <div class="col-md-3">
                            <label class="form-label" for="basic-default-fullname">Email ID</label>
                            <input type="text" class="form-control" id="designation" name="designation" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="basic-default-fullname">Mobile Number</label>
                            <input type="text" class="form-control" id="designation" name="designation" />
                        </div>

                        <div class="col-md-3">
                            <label class="form-label" for="basic-default-fullname">Aadhaar Number</label>
                            <input type="text" class="form-control" id="designation" name="designation" />
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label" for="basic-default-fullname">Driving License No</label>
                            <input type="text" class="form-control" id="designation" name="designation" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="basic-default-fullname">Blood Group</label>
                            <input type="text" class="form-control" id="designation" name="designation" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="basic-default-fullname">Company Name</label>
                            <input type="text" class="form-control" id="designation" name="designation" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="basic-default-fullname">Address</label>
                            <textarea id="address" class="form-control" name="address"></textarea>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <h5>Authentication</h5>

                        <div class="col-md-3">
                            <label class="form-label" for="basic-default-fullname">User ID</label>
                            <input type="text" class="form-control" id="user_id" name="user_id" />
                        </div>

                        <div class="col-md-3">
                            <label class="form-label" for="basic-default-fullname">Password</label>
                            <input type="text" class="form-control" id="passwrd" name="passwrd" />
                        </div>
                    </div>
                    <div class="row mb-3">
                    </div>
                    <div class="row mb-3">
                        <h5>Document Upload</h5>
                        <div class="col-md-3">
                            <label class="form-label" for="basic-default-fullname">Aadhar Doc Upload</label>
                            <input type="file" class="form-control" id="user_id" name="user_id" />
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
                <button type="button" class="btn btn-primary"
                    onclick="window.location='{{ route('user_registration_data') }}'">Submit</button>
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
