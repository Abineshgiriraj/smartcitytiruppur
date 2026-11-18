@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Complaint/</span>Firm User Complaint Registration</h4>
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
                        <label for="fullName" class="col-sm-2 col-form-label">Firm User Name</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="fullName" name="fullName" />
                        </div>

                        <label for="email" class="col-sm-2 col-form-label mt-3">Email ID</label>
                        <div class="col-sm-4 mt-3">
                            <input type="email" class="form-control" id="email" name="email" />
                        </div>

                        <label for="regDate" class="col-sm-2 col-form-label mt-3">Reg Date</label>
                        <div class="col-sm-4 mt-3">
                            <input type="text" class="form-control" id="regDate" name="regDate" />
                        </div>

                        <label for="regNo" class="col-sm-2 col-form-label mt-3">Reg No</label>
                        <div class="col-sm-4 mt-3">
                            <input type="text" class="form-control" id="regNo" name="regNo" />
                        </div>

                        <label for="contactPerson" class="col-sm-2 col-form-label mt-3">Contact Person</label>
                        <div class="col-sm-4 mt-3">
                            <input type="text" class="form-control" id="contactPerson" name="contactPerson" />
                        </div>

                        <label for="mobileNo" class="col-sm-2 col-form-label mt-3">Mobile No</label>
                        <div class="col-sm-4 mt-3">
                            <input type="text" class="form-control" id="mobileNo" name="mobileNo" />
                        </div>

                        <label for="address1" class="col-sm-2 col-form-label mt-3">Address 1</label>
                        <div class="col-sm-4 mt-3">
                            <textarea class="form-control" id="address1" name="address1"></textarea>
                        </div>

                        <label for="address2" class="col-sm-2 col-form-label mt-3">Address 2</label>
                        <div class="col-sm-4 mt-3">
                            <textarea class="form-control" id="address2" name="address2"></textarea>
                        </div>

                        <label for="faxNo" class="col-sm-2 col-form-label mt-3">FAX No</label>
                        <div class="col-sm-4 mt-3">
                            <input type="text" class="form-control" id="faxNo" name="faxNo" />
                        </div>

                        <label for="gstNo" class="col-sm-2 col-form-label mt-3">GST No</label>
                        <div class="col-sm-4 mt-3">
                            <input type="text" class="form-control" id="gstNo" name="gstNo" />
                        </div>

                        <div class="btn-float mt-3">
                            <button type="button" class="btn btn-danger"
                                onclick="window.location.href='{{ route('firm_user_complaint_registration') }}'">Cancel</button>
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