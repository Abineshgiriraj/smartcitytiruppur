@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Edit User Type')

@section('content')

<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Admin/</span>Edit User Type</h4>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form id="usertypeForm" method="post" onsubmit="return submitForm(event);">
            @csrf
            @method('PUT')
            <!-- Always use PUT method for updates -->

            <div class="row">
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="usertype">User Type</label>
                    <input type="text" class="form-control" id="usertype" name="usertype"
                        value="{{ old('usertype', $user_type->user_type) }}" />
                    <div id="usertypeError" class="text-danger"></div>
                </div>
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="status">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="1" {{ $user_type->is_active == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ $user_type->is_active == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                    <div id="statusError" class="form-text text-danger"></div>
                </div>
            </div>
            <div class="row">
                <div class="btn-float">
                    <button type="button" class="btn btn-danger"
                        onclick="window.location.href='{{ route('usertype.index') }}'">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function submitForm(event) {
    event.preventDefault();

    let isValid = validateForm();
    if (!isValid) {
        return false;
    }

    let formData = {
        usertype: $('#usertype').val(),
        status: $('#status').val(),
        _token: '{{ csrf_token() }}',
        _method: 'PUT'
    };

    let url = '{{ route("usertype.update", $user_type->id) }}';
    $.ajax({
        url: url,
        method: 'POST',
        data: formData,
        success: function(response) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'User updated successfully!',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = "{{ route('usertype.index') }}";
            });
        },
        error: function(xhr) {
            let errors = xhr.responseJSON.errors;
            if (errors.usertype) {
                $('#usertypeError').text(errors.usertype[0]);
            }
        }
    });

    return false;
}

function validateForm() {
    let isValid = true;
    let usertypeInput = document.getElementById('usertype');
    let usertypeError = document.getElementById('usertypeError');

    // Clear previous error messages
    usertypeError.textContent = '';

    // Validate usertype Name
    if (usertypeInput.value.trim() === '') {
        usertypeError.textContent = 'User Type is required.';
        isValid = false;
    } else if (!/^[A-Za-z0-9\s\-,()&]+$/.test(usertypeInput.value.trim())) {
        usertypeError.textContent = 'User Type must contain only alphabetic characters.';
        isValid = false;
    } else if (usertypeInput.value.length < 3) {
        usertypeError.textContent = 'User Type must be at least 3 characters long.';
        isValid = false;
    }

    return isValid;
}
</script>

@endsection