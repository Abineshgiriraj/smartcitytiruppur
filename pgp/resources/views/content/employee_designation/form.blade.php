@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Settings/</span> Employee Designation</h4>
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
                <form id="employeedesignationform">
                    @csrf
                    @if(isset($acc_year))
                    @method('PUT')
                    @endif
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label" for="basic-default-fullname">Employee Designation</label>
                            <input type="text" class="form-control" id="designation" name="designation" />
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="basic-default-message">Description</label>
                            <textarea id="description" class="form-control" name="description"></textarea>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="basic-default-message">Status</label>
                            <select class="form-select" name="status" id="status">
                                <option value="">Select Status</option>
                                <option value="1">Active</option>
                                <option value="2">InActive</option>
                            </select>
                        </div>
                    </div>
                    <div class="btn-float">
                        <button type="button" class="btn btn-danger"
                            onclick="window.location.href='{{ route('employee_designation') }}'">Cancel</button>
                        <!-- <button type="submit" class="btn btn-primary">Submit</button> -->
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <!-- {{ isset($acc_year) ? 'Update' : 'Submit' }} -->
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

$('#employeedesignationform').submit(function(event) {
    event.preventDefault(); // Prevent the default form submission

    // Validate the form
    // let isValid = validateForm();
    // if (!isValid) {
    //     return false;
    // }

    // Gather form data
    var formData = {
        designation: $('#designation').val(),
        description: $('#description').val(),
        _token: '{{ csrf_token() }}'
    };

    // Determine method and URL based on whether $acc_year is set
    var method = "POST";
    var url = "{{route('employee_designation_store')}}";

    $.ajax({
        url: url,
        method: method,
        data: formData,

        success: function(response) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Designation create successfully!',
                timer: 1500, // Time in milliseconds (1500ms = 1.5 seconds)
            }).then(() => {
                window.location.href = "{{ route('employee_designation')}}";
            });
        },

        error: function(xhr) {
            console.log(xhr.responseText);
            if (xhr.status === 422) { // Validation error status code
                var errors = xhr.responseJSON.errors;
                if (errors.designation) {
                    Swal.fire(
                        'Warning',
                        'Designation already exists.',
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