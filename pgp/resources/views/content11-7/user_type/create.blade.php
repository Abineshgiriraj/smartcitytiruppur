@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')

<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Admin/</span>User Type</h4>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form id="usertypeForm" method="post" onsubmit="return submitForm(event);">
            @csrf
            @if(isset($user_types))
            @method('PUT')
            @endif

            <div class="row">
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="usertype">User Type</label>
                    <input type="text" class="form-control" id="usertype" name="usertype" />
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
                        onclick="window.location.href='{{ route("usertype.index")}}'">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
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
        _method: '{{ isset($user_types) ? "PUT " : "POST "}}'
    };

    let url = '{{ isset($user_types) ? route("usertype.update", $user_types->id) : route("usertype.store") }}';


    $.ajax({
        url: url,
        method: 'POST',
        data: formData,
        success: function(response) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'User ' + (formData._method === 'POST' ? 'created' : 'updated') +
                    ' successfully!',
            }).then(() => {
                window.location.href = "{{ url('/usertype') }}";
            });
        },
        error: function(xhr) {
            let errors = xhr.responseJSON.errors;
            if (errors && errors.usertype) {
                $('#usertypeError').text(errors.usertype[0]);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: errors.usertype[0],
                });
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
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'User Type is required.',
        });
        isValid = false;
    } else if (!/^[A-Za-z\s]+$/.test(usertypeInput.value.trim())) {
        usertypeError.textContent = 'User Type must contain only alphabetic characters.';
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'User Type must contain only alphabetic characters.',
        });
        isValid = false;
    } else if (usertypeInput.value.length < 3) {
        usertypeError.textContent = 'User Type must be at least 3 characters long.';
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'User Type must be at least 3 characters long.',
        });
        isValid = false;
    }

    return isValid;
}
</script>

@endsection