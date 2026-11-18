@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Settings/</span>Port Issue Center</h4>
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
                <!-- method="POST" action="{{ route('center_name.store') }}" -->
                <form id="center_form" >
                @csrf
                    @if(isset($acc_year))
                    @method('PUT')
                    @endif

                    <div class="row mb-3">
                        <div class="col-sm-2 mt-3">
                            <label class="form-label" for="center_name">Center Name</label>
                        </div>
                        <div class="col-sm-4 mt-3">
                            <input type="text" class="form-control" id="center_name" name="center_name"
                                value="{{ isset($port_center) ? $port_center->center_name : '' }}" />
                        </div>
                        <div class="col-sm-2 mt-3">
                            <label class="form-label" for="short_name">Short Name</label>
                        </div>
                        <div class="col-sm-4 mt-3">
                            <input type="text" class="form-control" id="short_name" name="short_name"
                                value="{{ isset($port_center) ? $port_center->short_name : '' }}" />
                        </div>
                        <div class="col-sm-2 mt-3">
                            <label class="form-label" for="description">Description</label>
                        </div>
                        <div class="col-sm-4 mt-3">
                            <textarea class="form-control" id="description"
                                name="description">{{ isset($port_center) ? $port_center->description : '' }}</textarea>
                        </div>
                        <div class="col-sm-2 mt-3">
                            <label class="form-label" for="status">Status</label>
                        </div>
                        <div class="col-sm-4 mt-3">
                            <select class="form-select" name="status" id="status">
                                <option value="active"
                                    {{ isset($port_center) && $port_center->status == 'active' ? 'selected' : '' }}>
                                    Active</option>
                                <option value="inactive"
                                    {{ isset($port_center) && $port_center->status == 'inactive' ? 'selected' : '' }}>
                                    Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="btn-float">
                        <button type="button" class="btn btn-danger"
                            onclick="window.location.href='{{ route('center_name_list') }}'">Cancel</button>
                            <button type="submit"
                            class="btn btn-primary">{{ isset($port_center) ? 'Update' : 'Submit' }}</button>
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
    // alert();
    $('#center_form').submit(function(event) {
        event.preventDefault(); // Prevent the default form submission

        // Validate the form
        let isValid = validateForm();
        if (!isValid) {
            return false;
        }

        // Gather form data
        var formData = {
            center_name: $('#center_name').val(),
            short_name: $('#short_name').val(),
            description: $('#description').val(),
            // remarks: $('#remarks').val(),
            status: $('#status').val(),
            
            _token: '{{ csrf_token() }}'
        };

       
        // Determine method and URL based on whether $acc_year is set
        var method = "{{ isset($port_center) ? 'PUT' : 'POST' }}";
        var url =
            "{{ isset($port_center) ? route('center_name.update', $port_center->id) : route('center_name.store') }}";

        $.ajax({
            url: url,
            method: method,
            data: formData,

            success: function(response) {
                Swal.fire({
                    title: 'Success!',
                    text: 'Center Name ' + (method === 'POST' ? 'added' : 'updated') +
                        ' successfully!',
                    icon: 'success',
                    timer: 1500, // Time in milliseconds (1500ms = 1.5 seconds)
                    showConfirmButton: false, // Hide the confirm button
                    willClose: () => {
                        // Redirect after the alert closes
                        window.location.href = "{{ route('center_name_list') }}";
                    }
                });
            },

            error: function(xhr) {
                console.log(xhr.responseText);
                if (xhr.status === 422) { // Validation error status code
                    var errors = xhr.responseJSON.errors;
                    if (errors.center_name) {
                        Swal.fire(
                            'Warning',
                            'Center year already exists.',
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
});

function validateForm() {
        let isValid = true;
        let accyear = document.getElementById('center_name');
        let accerr = document.getElementById('acc_err');

        if (accyear.value.trim() === '') {
            accerr.textContent = 'center name must not me empty';
            isValid = false;
        }

        return isValid;
    }

</script>

@endsection