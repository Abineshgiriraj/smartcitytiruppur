@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')

<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Admin/</span>Main User Screen</h4>
    </div>
</div>

@php
$get_screen_type = get_screen_type();
@endphp

<div class="card">
    <div class="card-body">
        <form id="mainUserScreenForm" method="post" onsubmit="return submitForm(event);">
            @csrf
            @if(isset($mainUserScreen))
            @method('PUT')
            @endif

            <div class="row">
                <div class="fm col-md-4">
                    <label class="form-label" for="screen_type">Screen Type</label>
                    <select name="screen_type" id="screen_type" class="form-select">
                        @foreach ( $get_screen_type as $screen_type)
                        <option value="{{$screen_type->unique_id}}">
                            {{formatToCapitalizedWords($screen_type->type_name)}}
                        </option>
                        @endforeach
                    </select>
                    <div id="screen_typeError" class="form-text text-danger"></div>
                </div>
                <div class="fm col-md-4">
                    <label class="form-label" for="screen_name">Screen Name</label>
                    <input type="text" class="form-control" id="screen_name" name="screen_name" />
                    <div id="screen_nameError" class="text-danger"></div>
                </div>
                <div class="fm col-md-4">
                    <label class="form-label" for="orderno">Order No</label>
                    <input type="text" class="form-control" id="orderno" name="orderno" />
                    <div id="ordernoError" class="text-danger"></div>
                </div>
                <div class="fm col-md-4">
                    <label class="form-label" for="icon">Icon</label>
                    <input type="text" class="form-control" id="icon" name="icon" />
                    <div id="iconError" class="text-danger"></div>
                </div>
                <div class="fm col-md-4">
                    <label class="form-label" for="remarks">Description</label>
                    <textarea class="form-control" name="remarks" id="remarks"></textarea>
                    <div id="remarksError" class="text-danger"></div>
                </div>
                <div class="fm col-md-4">
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
                        onclick="window.location.href='{{ route('mainuserscreen.index') }}'">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
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

        let isValid = validateForm();
        if (!isValid) {
            return false;
        }

        let formData = {
            screen_type: $('#screen_type').val(),
            screen_name: $('#screen_name').val(),
            orderno: $('#orderno').val(),
            icon: $('#icon').val(),
            remarks: $('#remarks').val(),
            status: $('#status').val(),
            _token: '{{ csrf_token() }}',
            _method: '{{ isset($mainUserScreen) ? "PUT " : "POST "}}'
        };

        let url =
            '{{ isset($mainUserScreen) ? route("mainuserscreen.update", $mainUserScreen->id) : route("mainuserscreen.store") }}';


        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            success: function(response) {
                // Swal.fire('Success!', 'Main User Screen ' + (formData._method === 'POST' ? 'created' :
                //     'updated') + ' successfully!', 'success').then(() => {
                // window.location.href = "{{ route('mainuserscreen.index') }}";


                const redirectUrl = "{{route('mainuserscreen.index')}}";
                sweetalert('create', redirectUrl);

            },

            error: function(xhr) {
                let errors = xhr.responseJSON.errors;
                if (errors.screen_type) {
                    $('#screen_typeError').text(errors.screen_type[0]);
                }
                if (errors.screen_name) {
                    $('#screen_nameError').text(errors.screen_name[0]);
                }
                if (errors.orderno) {
                    $('#ordernoError').text(errors.orderno[0]);
                }
                if (errors.icon) {
                    $('#iconError').text(errors.icon[0]);
                }
                if (errors.remarks) {
                    $('#remarksError').text(errors.remarks[0]);
                }
                if (errors.status) {
                    $('#statusError').text(errors.status[0]);
                }
            }
        });

        return false;
    }

    function validateForm() {
        let isValid = true;

        // Clear previous error messages
        $('.text-danger').text('');

        // Validate form fields
        // Add your validation logic here...

        return isValid;
    }
</script>

@endsection