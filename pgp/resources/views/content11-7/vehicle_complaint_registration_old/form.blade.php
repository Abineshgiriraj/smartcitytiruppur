@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Complaint/</span> Vehicle Complaint Registration</h4>
    </div>
</div>

<style>
/* btn style */
.btn-right {
    float: right;
}
</style>
@php
$get_complaint_type =get_complaint_type();

//$get_person_type = get_person_type();
@endphp
<!-- Basic Layout -->
<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-body">
                <form id="vehicle_form" method="POST" action="{{ route('vehicle_complaint_registration.store') }}">
                    @csrf
                    @if(isset($complaint_registration))
                    @method('PUT')
                    @endif


                    <div class="row mb-3 com-form">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="complaint_type">Complaint Type</label>
                            <select class="form-select" id="complaint_type" name="complaint_type"
                                onchange="allowed_types()">
                                <option value="">
                                    Select </option>
                                @foreach ($get_complaint_type as $complaint_type)
                                <option value="{{$complaint_type->unique_id}}">
                                    {{$complaint_type->complaint_type}}
                                </option>
                                @endforeach
                            </select>
                            <span id="vec_nameError" class="text-danger"></span>
                        </div>

                        <div class="col-md-4 mb-3" style="display:none;">
                            <label class="form-label" for="person">Person</label>
                            <input type="text" class="form-control" id="person" name="person"
                                value="{{ isset($complaint_registration) ? $complaint_registration->person : '' }}" />
                        </div>
                        <div class="col-md-4 mb-3" style="display:none;">
                            <label class="form-label" for="vehicle_no">Vehicle No</label>
                            <input type="text" class="form-control" id="vehicle_no" name="vehicle_no"
                                value="{{ isset($complaint_registration) ? $complaint_registration->vehicle_no : '' }}" />

                        </div>
                        <div class="col-md-4 mb-3" style="display:none;">
                            <label class="form-label" for="user">Firm User</label>
                            <input type="text" class="form-control" id="user" name="user"
                                value="{{ isset($complaint_registration) ? $complaint_registration->user : '' }}" />
                        </div>
                        <div class="col-md-4 mb-3" style="display:none;">
                            <label class="form-label" for="equipment">Equipment</label>
                            <input type="text" class="form-control" id="equipment" name="equipment"
                                value="{{ isset($complaint_registration) ? $complaint_registration->equipment : '' }}" />
                        </div>
                        <div class="col-md-4 mb-3" style="display:none;">
                            <label class="form-label" for="equipment">Material</label>
                            <input type="text" class="form-control" id="material" name="material"
                                value="{{ isset($complaint_registration) ? $complaint_registration->material : '' }}" />
                        </div>
                        <div class="col-md-4 mb-3" style="display:none;">
                            <label class="form-label" for="reg_no">Reg No</label>
                            <input type="text" class="form-control" id="reg_no" name="reg_no"
                                value="{{ isset($complaint_registration) ? $complaint_registration->reg_no : '' }}" />
                            <span id="district_nameError" class="text-danger"></span>
                        </div>
                        <div class="col-md-4 mb-3" style="display:none;">
                            <label class="form-label" for="reg_date">Reg Date</label>
                            <input type="date" class="form-control" id="reg_date" name="reg_date"
                                value="{{ isset($complaint_registration) ? $complaint_registration->reg_date : '' }}" />
                        </div>
                        <div class="col-md-4 mb-3" style="display:none;">
                            <label class="form-label" for="dl_no">DL No</label>
                            <input type="text" class="form-control" id="dl_no" name="dl_no"
                                value="{{ isset($complaint_registration) ? $complaint_registration->dl_no : '' }}" />
                        </div>
                        <div class="col-md-4 mb-3" style="display:none;">
                            <label class="form-label" for="name">Driver Name</label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="{{ isset($complaint_registration) ? $complaint_registration->name : '' }}" />
                        </div>

                        <!-- <div class="col-md-4 mb-3">
                            <label class="form-label" for="basic-default-message">Description</label>
                            <textarea id="remarks" class="form-control"
                                name="remarks">{{ isset($acc_year) ? $acc_year->remarks : '' }}</textarea>
                        </div> -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="basic-default-message">Status</label>
                            <select class="form-select" name="status" id="status">
                                <option value="">Select Status</option>
                                <option value="active"
                                    {{ isset($complaint_registration) && $complaint_registration->status == 'active' ? 'selected' : '' }}>
                                    Active</option>
                                <option value="inactive"
                                    {{ isset($complaint_registration) && $complaint_registration->status == 'inactive' ? 'selected' : '' }}>
                                    Inactive</option>
                            </select>
                        </div>
                        <div class="btn-float">
                            <button type="button" class="btn btn-danger"
                                onclick="window.location.href='{{ route('vehicle_complaint_registration_list') }}'">Cancel</button>

                            <button type="submit"
                                class="btn btn-primary">{{ isset($complaint_registration) ? 'Update' : 'Submit' }}</button>
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
$(document).ready(function() {
    allowed_types();


    $('#vehicle_form').on('submit', function(e) {
        e.preventDefault();

        // Validate form
        let isValid = validateForm();
        if (!isValid) {
            return false;
        }

        // Gather form data
        let formData = {
            complaint_type: $('#complaint_type').val(),
            person: $('#person').val(),
            // alert(complaint_type);
            vehicle_no: $('#vehicle_no').val(),
            user: $('#user').val(),
            equipment: $('#equipment').val(),
            reg_no: $('#reg_no').val(),
            reg_date: $('#reg_date').val(),
            dl_no: $('#dl_no').val(),
            name: $('#name').val(),
            material: $('#material').val(),
            status: $('#status').val(),
            _token: '{{ csrf_token() }}', // CSRF token for security
        };

        // Perform AJAX request
        $.ajax({
            url: "{{ route('vehicle_complaint_registration.store') }}", // URL to store the school
            method: 'POST', // Always use POST, Laravel will handle the method type through _method
            data: formData,
            success: function(response) {
                sweetalert('create');
                window.location.href = "{{ route('vehicle_complaint_registration_list') }}";
                // SweetAlert2 success message
                // Swal.fire({
                //     title: 'Success!',
                //     text: 'School created successfully!',
                //     icon: 'success',
                //     timer: 1000, // 1 second timer
                //     showConfirmButton: false // Automatically close after 1 second
                // }).then(() => {
                //     // Redirect after success
                //     
                // });
            },
            error: function(xhr) {
                // SweetAlert2 error message
                let errors = xhr.responseJSON.errors;
                for (let field in errors) {
                    $(`#${field}Error`).text(errors[field][0]);
                }

                Swal.fire({
                    title: 'Error!',
                    text: 'Please correct the errors and try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });

        return false; // Prevent default form submission
    });
});
// });

// });


function validateForm() {
    let isValid = true;

    // Clear previous error messages
    $('.text-danger').text('');

    // Validation logic
    // if ($('#reg_no').val().trim() === '') {
    //     $('#district_nameError').text('Reg No is required.');
    //     isValid = false;
    // }

    if ($('#complaint_type').val().trim() === '') {

        $('#vec_nameError').text('Complaint Type is required.');
        isValid = false;
    }



    return isValid;
}
// Event listeners to clear error messages on input
$('#complaint_type').on('input', function() {
    if ($(this).val().trim() !== '') {
        $('#vec_nameError').text(''); // Clear the error message when user types
    }
});

function allowed_types() {
    var complaint_type = $('#complaint_type').val();
    // alert();
    // alert(complaint_type);
    // Hide all input fields initially
    // $('.col-md-4.mb-3').hide();
    // Show fields based on the selected complaint type

    if (complaint_type == '6701004426fc746864') {
        $('#person').closest('.col-md-4.mb-3').show();
        $('#vehicle_no').closest('.col-md-4.mb-3').hide();
        $('#user').closest('.col-md-4.mb-3').hide();
        $('#equipment').closest('.col-md-4.mb-3').hide();
        $('#dl_no').closest('.col-md-4.mb-3').hide();
        $('#name').closest('.col-md-4.mb-3').hide();
        $('#reg_no').closest('.col-md-4.mb-3').show();
        $('#reg_date').closest('.col-md-4.mb-3').show();
        $('#equipment').closest('.col-md-4.mb-3').hide();
        $('#material').closest('.col-md-4.mb-3').hide();

    } else if (complaint_type == '670100869632051306') {
        // Show all fields if another complaint type is selected
        $('#person').closest('.col-md-4.mb-3').hide();
        $('#vehicle_no').closest('.col-md-4.mb-3').show();
        $('#user').closest('.col-md-4.mb-3').hide();
        $('#equipment').closest('.col-md-4.mb-3').hide();
        $('#reg_no').closest('.col-md-4.mb-3').show();
        $('#reg_date').closest('.col-md-4.mb-3').show();
        $('#dl_no').closest('.col-md-4.mb-3').show();
        $('#name').closest('.col-md-4.mb-3').show();
        $('#material').closest('.col-md-4.mb-3').hide();

    } else if (complaint_type == '6701046619b9c18585') {
        // Show all fields if another complaint type is selected
        $('#person').closest('.col-md-4.mb-3').hide();
        $('#vehicle_no').closest('.col-md-4.mb-3').hide();
        $('#user').closest('.col-md-4.mb-3').show();
        $('#equipment').closest('.col-md-4.mb-3').hide();
        $('#reg_no').closest('.col-md-4.mb-3').show();
        $('#reg_date').closest('.col-md-4.mb-3').show();
        $('#dl_no').closest('.col-md-4.mb-3').hide();
        $('#name').closest('.col-md-4.mb-3').hide();
        $('#material').closest('.col-md-4.mb-3').hide();
    } else if (complaint_type == '670109b3c36bf25361') {
        // Show all fields if another complaint type is selected
        $('#person').closest('.col-md-4.mb-3').hide();
        $('#vehicle_no').closest('.col-md-4.mb-3').hide();
        $('#user').closest('.col-md-4.mb-3').hide();
        $('#equipment').closest('.col-md-4.mb-3').show();
        $('#reg_no').closest('.col-md-4.mb-3').show();
        $('#reg_date').closest('.col-md-4.mb-3').show();
        $('#dl_no').closest('.col-md-4.mb-3').hide();
        $('#name').closest('.col-md-4.mb-3').hide();
        $('#material').closest('.col-md-4.mb-3').hide();
    } else if (complaint_type == '67010a8da2b0f63483') {
        // Show all fields if another complaint type is selected
        $('#person').closest('.col-md-4.mb-3').hide();
        $('#vehicle_no').closest('.col-md-4.mb-3').hide();
        $('#user').closest('.col-md-4.mb-3').hide();
        $('#equipment').closest('.col-md-4.mb-3').hide();
        $('#reg_no').closest('.col-md-4.mb-3').show();
        $('#reg_date').closest('.col-md-4.mb-3').show();
        $('#dl_no').closest('.col-md-4.mb-3').hide();
        $('#name').closest('.col-md-4.mb-3').hide();
        $('#material').closest('.col-md-4.mb-3').show();

    } else {

        $('#person').closest('.col-md-4.mb-3').hide();
        $('#vehicle_no').closest('.col-md-4.mb-3').show();
        $('#user').closest('.col-md-4.mb-3').hide();
        $('#equipment').closest('.col-md-4.mb-3').hide();
        $('#reg_no').closest('.col-md-4.mb-3').show();
        $('#reg_date').closest('.col-md-4.mb-3').show();
        $('#dl_no').closest('.col-md-4.mb-3').show();
        $('#name').closest('.col-md-4.mb-3').show();
        $('#material').closest('.col-md-4.mb-3').hide();

    }

}
</script>

@endsection