@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')

<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Settings/</span>Complaint Type</h4>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form id="complainttypeForm" method="post" onsubmit="return submitForm(event);">
            @csrf
            @if(isset($complaint_types))
                @method('PUT')
            @endif

            <div class="row">
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="complaint_type">Complaint Type</label>
                    <input type="text" class="form-control" id="complaint_type" name="complaint_type" />
                    <div id="usertypeError" class="text-danger"></div>
                </div>
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="status">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    <div id="statusError" class="form-text text-danger"></div>
                </div>
            </div>
            <div class="row">
                <div class="btn-float">
                    <button type="button" class="btn btn-danger"
                        onclick="window.location.href='{{ route("complaint_type.index")}}'">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->


<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>

<script>
    function submitForm(event) {
        event.preventDefault();

        // Validate the form before proceeding
        let isValid = validateForm();
        if (!isValid) {
            return false; // Stop submission if validation fails
        }

        // Gather form data
        let formData = {
            complaint_type: $('#complaint_type').val(),
            status: $('#status').val(),
            _token: '{{ csrf_token() }}',
            _method: '{{ isset($complaint_types) ? "PUT" : "POST" }}' // Correctly handle method
        };

        // Determine the URL for the request
        let url =
            '{{ isset($complaint_types) ? route("complaint_type.update", $complaint_types->id) : route("complaint_type.store") }}';

        // Send AJAX request
        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            success: function (response) {
                // Show success alert with automatic close
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '{{ isset($complaint_types) ? "Complaint Type updated successfully!" : "Complaint Type created successfully!" }}',
                    timer: 3000, // Close after 3 seconds
                    timerProgressBar: true, // Optional: show progress bar
                    willClose: () => {
                        // Redirect after the alert is closed
                        window.location.href = "{{ url('/complaint_type_list') }}";
                    }
                });
            },
            error: function (xhr) {
                let errors = xhr.responseJSON.errors;
                // Check for specific error messages and display them
                if (errors && errors.complaint_type) {
                    $('#usertypeError').text(errors.complaint_type[
                        0]); // Display error in the specified element
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: errors.complaint_type[0], // Display error in SweetAlert
                    });
                } else {
                    // Handle generic error
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'An unexpected error occurred. Please try again.',
                    });
                }
            }
        });

        return false; // Prevent default form submission
    }


    function validateForm() {
        let isValid = true;
        let usertypeInput = document.getElementById('complaint_type');
        let usertypeError = document.getElementById('usertypeError');

        // Clear previous error messages
        usertypeError.textContent = '';

        // Validate usertype Name
        if (usertypeInput.value.trim() === '') {
            usertypeError.textContent = 'Complaint Type is required.';
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Complaint Type is required.',
            });
            isValid = false;
        } else if (!/^[A-Za-z\s]+$/.test(usertypeInput.value.trim())) {
            usertypeError.textContent = 'Complaint Type must contain only alphabetic characters.';
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Complaint Type must contain only alphabetic characters.',
            });
            isValid = false;
        } else if (usertypeInput.value.length < 3) {
            usertypeError.textContent = 'Complaint Type must be at least 3 characters long.';
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Complaint Type must be at least 3 characters long.',
            });
            isValid = false;
        }

        return isValid;
    }
</script>

@endsection