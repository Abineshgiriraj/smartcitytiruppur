@extends('layouts/contentNavbarLayout')

@section('title', 'Edit User Screen')

@section('content')

<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Admin/</span>User Screen</h4>
    </div>
</div>



<div class="card">
    <div class="card-body">
        <form id="mainscreenForm" method="POST"
            action="{{ isset($user_screen) ? route('userscreen.update', $user_screen->id) : route('userscreen.store') }}"
            onsubmit="return submitForm(event);">
            @csrf
            @if(isset($user_screen))
            @method('PUT')
            @endif

            @php
            $get_main_Screen = user_main_screen();
            $get_sub_screen = user_sub_screen();
            $get_screen_action = user_actions();
            $get_screen_action = user_action_list($get_screen_action, $user_screen->action);
            @endphp

            <div class="row">
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="mainscreen">Main Screen</label>
                    <select name="mainscreen" id="mainscreen" class="form-select">
                        <option value="">Select Main Screen</option>
                        @foreach ($get_main_Screen as $main_screen)
                        <option value="{{ $main_screen->unique_id }}"
                            {{ (isset($user_screen) && $user_screen->main_screen == $main_screen->unique_id) ? 'selected' : '' }}>
                            {{ formatToCapitalizedWords($main_screen->screen_name) }}
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
                        <option value="{{ $sub_screen->unique_id }}"
                            {{ (isset($user_screen) && $user_screen->sub_screen == $sub_screen->unique_id) ? 'selected' : '' }}>
                            {{ formatToCapitalizedWords($sub_screen->sub_screen_name) }}
                        </option>
                        @endforeach
                    </select>
                    <div id="subscreenError" class="form-text text-danger"></div>
                </div>


                <div class="mb-3 col-md-3">
                    <label class="form-label" for="screen_name">Screen Name</label>
                    <input type="text" class="form-control" id="screen_name" name="screen_name"
                        value="{{ isset($user_screen) ? $user_screen->screen_name : '' }}" />
                    <div id="screen_nameError" class="text-danger"></div>
                </div>

                <div class="mb-3 col-md-3">
                    <label class="form-label" for="folder_name">Folder Name</label>
                    <input type="text" class="form-control" id="folder_name" name="folder_name"
                        value="{{ isset($user_screen) ? $user_screen->folder_name : '' }}" />
                    <div id="folder_nameError" class="text-danger"></div>
                </div>

                <div class="mb-3 col-md-3">
                    <label class="form-label" for="orderno">Order No</label>
                    <input type="text" class="form-control" id="orderno" name="orderno"
                        value="{{ isset($user_screen) ? $user_screen->order_no : '' }}" />
                    <div id="ordernoError" class="text-danger"></div>
                </div>

                <div class="mb-3 col-md-3">
                    <label class="form-label" for="icon">Icon</label>
                    <input type="text" class="form-control" id="icon" name="icon"
                        value="{{ isset($user_screen) ? $user_screen->icon : '' }}" />
                    <div id="iconError" class="text-danger"></div>
                </div>

                

                <div class="mb-3 col-md-3">
                    <label class="form-label" for="remarks">Description</label>
                    <textarea class="form-control" name="remarks"
                        id="remarks">{{ isset($user_screen) ? $user_screen->remarks : '' }}</textarea>
                    <div id="remarksError" class="text-danger"></div>
                </div>
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="status">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="1" {{ isset($user_screen) && $user_screen->is_active ? 'selected' : '' }}>Active
                        </option>
                        <option value="0" {{ isset($user_screen) && !$user_screen->is_active ? 'selected' : '' }}>
                            Inactive</option>
                    </select>
                    <div id="statusError" class="form-text text-danger"></div>
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
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function submitForm(event) {
        event.preventDefault();

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
            // sub_screen_icon: $('#sub_screen_icon').val(),
            screen_name: $('#screen_name').val(),
            folder_name: $('#folder_name').val(),
            orderno: $('#orderno').val(),
            icon: $('#icon').val(),
            status: $('#status').val(),
            remarks: $('#remarks').val(),
            checkaction: actions,
            _token: '{{ csrf_token() }}',
            _method: '{{ isset($user_screen) ? "PUT " : "POST " }}'
        };

        let url = '{{ isset($user_screen) ? route("userscreen.update", $user_screen->id) : route("userscreen.store") }}';

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            success: function(response) {
                Swal.fire('Success!', 'User Screen ' + (formData._method === 'POST' ? 'created' : 'updated') +
                    'successfully!', 'success').then(() => {
                    window.location.href = "{{ route('userscreen.index') }}";
                });
            },
            error: function(xhr) {
                let errors = xhr.responseJSON.errors;
                if (errors.mainscreen) {
                    $('#mainscreenError').text(errors.mainscreen[0]);
                }
                if (errors.subscreen) {
                    $('#subscreenError').text(errors.subscreen[0]);
                }
                // if (errors.sub_screen_icon) {
                //     $('#sub_Screen_icon_Error').text(errors.sub_screen_icon[0]);
                // }
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

    document.addEventListener("DOMContentLoaded", function() {
        const allCheckbox = document.getElementById('all');
        const actionCheckboxes = document.querySelectorAll('.action_check');

        function updateAllCheckbox() {
            const allChecked = [...actionCheckboxes].every(cb => cb.checked);
            allCheckbox.checked = allChecked;
        }

        allCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            actionCheckboxes.forEach(cb => cb.checked = isChecked);
        });

        actionCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateAllCheckbox);
        });

        updateAllCheckbox();
    });
</script>

<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert2.js')}}"></script>
<!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->

@endsection