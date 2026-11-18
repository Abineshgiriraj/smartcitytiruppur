@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')

<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Admin/</span>User Creation</h4>
    </div>
</div>
@php

$user_type_options = user_type();

@endphp

<div class="card">
    <div class="card-body">
        <form id="mainscreenForm" method="post" onsubmit="return submitForm(event);">
            @csrf
            @if(isset($user))
            @method('PUT')
            @endif

            <div class="row">
                <!-- Staff Name -->
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="staff_name">Staff Name</label>
                    <input type="text" class="form-control" id="staff_name" name="staff_name" />
                    <div id="staff_name_Error" class="text-danger"></div>
                </div>

                <!-- User Name -->
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="user_name">User Name</label>
                    <input type="text" class="form-control" id="user_name" name="user_name" />
                    <div id="user_name_Error" class="text-danger"></div>
                </div>

                <!-- Password -->
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" />
                    <div id="password_Error" class="text-danger"></div>
                </div>

                <!-- Confirm Password -->
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="password_confirmation">Confirm Password</label>
                    <input type="password" class="form-control" id="password_confirmation"
                        name="password_confirmation" />
                    <div id="password_confirmation_Error" class="text-danger"></div>
                </div>

                <!-- User Type -->
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="user_type">User Type</label>
                    <select class="form-select" id="user_type" name="user_type">

                        <option value="selected">Select user type
                        </option>
                        @foreach ($user_type_options as $user_type)
                        <option value="{{$user_type->unique_id}}">
                            {{$user_type->user_type}}
                        </option>
                        @endforeach
                    </select>
                    <div id=" user_type_Error" class="text-danger">
                    </div>
                </div>

                <!-- Mobile Number -->
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="mobile_number">Mobile</label>
                    <input type="text" class="form-control" id="mobile_number" name="mobile_number" />
                    <div id="mobile_number_Error" class="text-danger"></div>
                </div>

                <!-- Email ID -->
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="email_id">Email ID</label>
                    <input type="email" class="form-control" id="email_id" name="email_id" />
                    <div id="email_id_Error" class="text-danger"></div>
                </div>

                <!-- Description -->
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="remarks">Description</label>
                    <textarea class="form-control" id="remarks" name="remarks"></textarea>
                    <div id="remarksError" class="text-danger"></div>
                </div>

                <!-- Status -->
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="status">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    <div id="statusError" class="text-danger"></div>
                </div>
            </div>

            <div class="row">
                <div class="btn-float">
                    <button type="button" class="btn btn-danger"
                        onclick="window.location.href='{{ route("usercreation.index")}}'">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
<script>
function submitForm(event) {
    event.preventDefault();

    let formData = {
        staff_name: $('#staff_name').val(),
        user_name: $('#user_name').val(),
        password: $('#password').val(),
        password_confirmation: $('#password_confirmation').val(),
        user_type: $('#user_type').val(),
        mobile_number: $('#mobile_number').val(),
        email_id: $('#email_id').val(),
        remarks: $('#remarks').val(),
        status: $('#status').val(),
        _token: '{{ csrf_token() }}',
        _method: '{{ isset($user)? "PUT" :  "POST"}}'
    };

    let url = '{{ isset($user) ? route("usercreation.update ", $user->id) : route("usercreation.store") }}';


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
                window.location.href = '{{ route("usercreation.index") }}';
            });
        },
        error: function(xhr) {
            let errors = xhr.responseJSON.errors;
            $('.text-danger').html(''); // Clear any previous errors
            $.each(errors, function(field, messages) {
                $('#' + field + '_Error').html(messages[0]);
            });
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please correct the errors and try again.',
            });
        }
    });

    return false;
}
</script>

@endsection