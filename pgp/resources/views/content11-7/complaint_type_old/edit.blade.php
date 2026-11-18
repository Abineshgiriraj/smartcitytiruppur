@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Edit User Type')

@section('content')

<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Settings/</span>Edit Complaint Type</h4>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form id="complainttypeForm" method="post" onsubmit="return submitForm(event);">
            @csrf
            @method('PUT')
            <!-- Always use PUT method for updates -->

            <div class="row">
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="complaint_type">Complaint Type</label>
                    <input type="text" class="form-control" id="complaint_type" name="complaint_type"
                        value="{{ old('complaint_type', $complaint_type->complaint_type) }}" />
                    <div id="usertypeError" class="text-danger"></div>
                </div>
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="status">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="1" {{ $complaint_type->is_active == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ $complaint_type->is_active == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                    <div id="statusError" class="form-text text-danger"></div>
                </div>
            </div>
            <div class="row">
                <div class="btn-float">
                    <button type="button" class="btn btn-danger"
                        onclick="window.location.href='{{ route('complaint_type.index') }}'">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>

<script>
function submitForm(event) {
    event.preventDefault();

    let isValid = validateForm();
    if (!isValid) {
        return false;
    }

    let formData = {
        complaint_type: $('#complaint_type').val(),
        status: $('#status').val(),
        _token: '{{ csrf_token() }}',
        _method: 'PUT'
    };

    let url = '{{ route("complaint_type.update", $complaint_type->id) }}';
    $.ajax({
        url: url,
        method: 'POST',
        data: formData,
        success: function(response) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Complaint Type updated successfully!',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = "{{ route('complaint_type.index') }}";
            });
        },
        error: function(xhr) {
            let errors = xhr.responseJSON.errors;
            if (errors.complaint_type) {
                $('#usertypeError').text(errors.complaint_type[0]);
            }
        }
    });

    return false;
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
        isValid = false;
    } else if (!/^[A-Za-z\s]+$/.test(usertypeInput.value.trim())) {
        usertypeError.textContent = 'Complaint must contain only alphabetic characters.';
        isValid = false;
    } else if (usertypeInput.value.length < 3) {
        usertypeError.textContent = 'Complaint Type must be at least 3 characters long.';
        isValid = false;
    }

    return isValid;
}
</script>

@endsection