@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')

<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Admin/</span>User Screen</h4>
    </div>
</div>

@php

$get_main_Screen = user_main_screen();

$get_sub_screen = user_sub_screen();

$get_screen_action = user_actions();
$get_screen_action = user_action_list($get_screen_action);

@endphp

<div class="card">
    <div class="card-body">
        <form id="mainscreenForm" method="POST" action="{{ route('userscreen.store') }}"
            onsubmit="return submitForm(event);">
            @csrf
            @if(isset($user_Screen))
            @method('PUT')
            @endif

            <div class="row">
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="mainscreen">Main Screen</label>
                    <select name="mainscreen" id="mainscreen" class="form-select">
                        <option value="">Select Main Screen</option>
                        @foreach ($get_main_Screen as $main_screen)
                        <option value="{{$main_screen->unique_id}}">
                            {{formatToCapitalizedWords($main_screen->screen_name)}}
                        </option>
                        @endforeach
                    </select>
                    <div id="mainscreenError" class="form-text text-danger"></div>
                </div>
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="subscreen">Sub Screen</label>
                    <select name="subscreen" id="subscreen" class="form-select">
                        <option value="">Select Sub Screen</option>
                        @foreach ($get_sub_screen as $sub_screen)
                        <option value="{{$sub_screen->unique_id}}">
                            {{formatToCapitalizedWords($sub_screen->sub_screen_name)}}
                        </option>
                        @endforeach
                    </select>
                    <div id="subscreenError" class="form-text text-danger"></div>
                </div>
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="sub_screen_icon">Sub Screen Icon</label>
                    <input type="text" class="form-control" id="sub_screen_icon" name="sub_screen_icon" />
                    <div id="sub_Screen_icon_Error" class="text-danger"></div>
                </div>
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="screen_name">Screen Name</label>
                    <input type="text" class="form-control" id="screen_name" name="screen_name" />
                    <div id="screen_nameError" class="text-danger"></div>
                </div>
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="folder_name">Folder Name</label>
                    <input type="text" class="form-control" id="folder_name" name="folder_name" />
                    <div id="folder_nameError" class="text-danger"></div>
                </div>
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="orderno">Order No</label>
                    <input type="text" class="form-control" id="orderno" name="orderno" />
                    <div id="ordernoError" class="text-danger"></div>
                </div>
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="icon">Icon</label>
                    <input type="text" class="form-control" id="icon" name="icon" />
                    <div id="iconError" class="text-danger"></div>
                </div>
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="status">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    <div id="statusError" class="form-text text-danger"></div>
                </div>
                <div class="mb-3 col-md-12">
                    <label class="form-label" for="remarks">Description</label>
                    <textarea type="text" class="form-control" name="remarks" id="remarks"></textarea>
                    <div id="remarksError" class="text-danger"></div>
                </div>
            </div>

            <!-- Actions Section -->
            <div class="row">
                <div class="col-md-6">
                    <h6 class="header-title" style="color: #343a40;">Actions</h6>
                </div>
                <div class="form-group">
                    <ul class="ks-cboxtags">
                        <!-- Display the generated checkboxes -->
                        {!! $get_screen_action !!}
                    </ul>
                </div>
            </div>

            <div class="row">
                <div class="btn-float">
                    <button type="button" class="btn btn-danger"
                        onclick="window.location.href='{{ route('userscreen.index') }}'">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function submitForm(event) {
        event.preventDefault();

        // let isValid = validateForm();
        // if (!isValid) {
        //     return false;
        // }

        var add_comma = false;
        var actions = "";
        $('input[name="check_actions"]:checked').each(function() {
            if (add_comma) {
                actions += ",";
            } else {
                add_comma = true;
            }
            actions += this.value;
        });

        let formData = {
            mainscreen: $('#mainscreen').val(),
            subscreen: $('#subscreen').val(),
            sub_screen_icon: $('#sub_screen_icon').val(),
            screen_name: $('#screen_name').val(),
            folder_name: $('#folder_name').val(),
            orderno: $('#orderno').val(),
            icon: $('#icon').val(),
            status: $('#status').val(),
            remarks: $('#remarks').val(),
            checkaction: actions,
            _token: '{{ csrf_token() }}',
            _method: '{{ isset($user_Screen) ? "PUT " : " POST " }}'
        };

        let url = '{{ isset($user_Screen) ? route("userscreen.update", $user_Screen->id) : route("userscreen.store") }}';


        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            success: function(response) {
                // Swal.fire('Success!', 'User Screen ' + (formData._method === 'POST' ? 'created' : 'updated') +
                //     ' successfully!', 'success').then(() => {
                const redirectUrl = "{{ route('userscreen.index') }}";
                sweetalert('create', redirectUrl);

            },
            error: function(xhr) {
                let errors = xhr.responseJSON.errors;
                if (errors.mainscreen) {
                    $('#mainscreenError').text(errors.mainscreen[0]);
                }
                if (errors.subscreen) {
                    $('#subscreenError').text(errors.subscreen[0]);
                }
                if (errors.sub_screen_icon) {
                    $('#sub_Screen_icon_Error').text(errors.sub_screen_icon[0]);
                }
                if (errors.screen_name) {
                    $('#screen_nameError').text(errors.screen_name[0]);
                }
                if (errors.folder_name) {
                    $('#folder_nameError').text(errors.folder_name[0]);
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

        // Add your validation logic here for each field
        if ($('#mainscreen').val() === '') {
            $('#mainscreenError').text('Main Screen is required.');
            isValid = false;
        }
        if ($('#screen_name').val() === '') {
            $('#screen_nameError').text('Screen Name is required.');
            isValid = false;
        }

        return isValid;
    }
</script>


<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert2.js')}}"></script>



<script>
    document.addEventListener("DOMContentLoaded", function() {
        const allCheckbox = document.getElementById('all');
        const actionCheckboxes = document.querySelectorAll('.action_check');

        // Function to update "All" checkbox status
        function updateAllCheckbox() {
            const allChecked = [...actionCheckboxes].every(cb => cb.checked);
            allCheckbox.checked = allChecked;
        }

        // Toggle all checkboxes when "All" is clicked
        allCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            actionCheckboxes.forEach(cb => cb.checked = isChecked);
        });

        // Deselect "All" if any individual checkbox is unchecked
        actionCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateAllCheckbox);
        });

        // Initial update of "All" checkbox based on individual checkboxes
        updateAllCheckbox();
    });
</script>
@endsection