@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Pass/</span> Vehicle Complaint Registration</h4>
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
                    <div class="row mb-3 com-form">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="basic-default-fullname">Vehicle No</label>
                            <input type="text" class="form-control" id="vehicle_no" name="vehicle_no" \ />
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="basic-default-fullname">Reg No</label>
                            <input type="text" class="form-control" id="reg_no" name="reg_no" />
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="basic-default-fullname">Reg Date</label>
                            <input type="date" class="form-control" id="reg_date" name="reg_date" />
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="basic-default-fullname">DL No</label>
                            <input type="text" class="form-control" id="reg_no" name="reg_no" />
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="basic-default-fullname">Driver Name</label>
                            <input type="text" class="form-control" id="reg_no" name="reg_no" />
                        </div>


                        <!-- <div class="col-md-4 mb-3">
                            <label class="form-label" for="basic-default-message">Description</label>
                            <textarea id="remarks" class="form-control"
                                name="remarks">{{ isset($acc_year) ? $acc_year->remarks : '' }}</textarea>
                        </div> -->

                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="basic-default-message">Status</label>
                            <select class="form-select" name="status" id="status">
                                <option value="">Select status</option>
                                <option value="active">Active</option>
                                <option value="inactive">In Active</option>

                            </select>
                        </div>

                        <div class=" btn-float">
                            <button type="button" class="btn btn-danger"
                                onclick="window.location.href='{{ route('vehicle_complaint_registration') }}'">Cancel</button>
                            <!-- <button type="submit" class="btn btn-primary">Submit</button> -->
                            <button type="submit"
                                class="btn btn-primary">{{ isset($acc_year) ? 'Update' : 'Submit' }}</button>
                        </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>


<script>
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
                text: 'Academic Year ' + (method === 'POST' ? 'added' : 'updated') +
                    ' successfully!',
                icon: 'success',
                timer: 1500, // Time in milliseconds (1500ms = 1.5 seconds)
                showConfirmButton: false, // Hide the confirm button
                willClose: () => {
                    // Redirect after the alert closes
                    window.location.href = "{{ route('academic_year_creation_list') }}";
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