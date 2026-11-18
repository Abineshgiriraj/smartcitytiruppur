@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')

<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Admin/</span>User Permission</h4>
    </div>
</div>

@php

$user_type_options = user_type();

$main_screen_options = user_main_screen();


@endphp
<div class="card">
    <div class="card-body">
        <form id="mainscreenForm" method="post" autocomplete="off">
            @csrf

            <div class="row">
                <div class="mb-3 col-md-6">
                    <label class="form-label" for="user_type">User Type</label>
                    <select name="user_type" id="user_type" class="form-select">
                        <option value="selected">Select User Type</option>

                        @foreach($user_type_options as $user_type)
                        <option value="{{$user_type->unique_id}}"
                            {{$user_type->unique_id == $user_permission->user_type ? 'selected' : ''}}>
                            {{$user_type->user_type}}
                        </option>
                        @endforeach
                    </select>
                    <div id="user_type_Error" class="form-text text-danger"></div>
                </div>
                <div class="mb-3 col-md-6">
                    <label class="form-label" for="main_screen">Main Screen</label>
                    <select name="main_screen" id="main_screen" onchange="permui_val()" class="form-select">
                        <option value="selected">Select Main screen</option>

                        @foreach($main_screen_options as $main_screen)
                        <option value="{{$main_screen->unique_id}}"
                            {{$main_screen->unique_id == $main_screen->screen_name ? 'selected' : ''}}>
                            {{$main_screen->screen_name}}
                        </option> @endforeach
                    </select>
                    <div id="main_screen_Error" class="form-text text-danger"></div>
                </div>
            </div>

            <div class="col-12" id="perm_ui">
                <div class="card-box">

                </div>
            </div>


            <div class="row">
                <div class="btn-float">
                    <button type="button" class="btn btn-danger"
                        onclick="window.location.href='{{ route("user_permission.index") }}' ">Cancel</button>
                    <button type="submit" class="btn btn-primary" onclick="submitForm(event)">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>


<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>
<script>

function  permui_val() {

    

    var user_type = $('#user_type').val();
    var main_screen = $('#main_screen').val();

    $.ajax({
        type: 'GET',
        url: "{{route('userpermission.getdata')}}",
        data: {
            "user_type": user_type,
            "main_screen": main_screen,
        },
        success: function(data) {
            // alert("sucess");
            $("#perm_ui").html(data);
        },
        error: function(xhr) {
            console.log('error', data);
        }


    });

}

function submitForm(event) {
    // alert("fgh");
    event.preventDefault();

    let user_type = $('#user_type').val();
    let main_screen = $('#main_screen').val();
    let selectedPermissions = [];

    $('input[name="checkboxvalue[]"]:checked').each(function() {
        let user_screen_id = $(this).data('screen');
        let action_id = $(this).data('action');
        let main_screen_id = $(this).data('main');

        selectedPermissions.push({
            main: main_screen_id,
            section: main_screen_id, // Use actual section ID if different
            screen: user_screen_id,
            action: action_id
        });
    });

    let formData = {
        user_type: user_type,
        main_screen: main_screen,
        permissions: selectedPermissions,
        _token: '{{ csrf_token() }}'
    };

    $.ajax({
        type: "PUT",
        url: "{{route('userpermission.update', $user_permission->main_screen_id)}}", 
        data: formData,
        success: function(response) {
            const redirectUrl = '{{ route("user_permission.index") }}';
            sweetalert('create');
        },
        error: function(xhr) {
            console.error("Error occurred:", xhr.responseText);
        }
    });
}

function check_all(groupClass, element) {
    const isChecked = element.getAttribute('data-check') === 'checked';
    document.querySelectorAll('.' + groupClass).forEach((checkbox) => {
        checkbox.checked = !isChecked;
    });

    // also update the row "all" checkbox (if it's a row-level toggle)
    if (element.id.startsWith("checkall")) {
        element.checked = !isChecked;
    }

    element.setAttribute('data-check', isChecked ? 'unchecked' : 'checked');
}

function check_me(screenId, element) {
    const allCheckboxes = document.querySelectorAll('.allcheck-' + screenId);
    const allChecked = Array.from(allCheckboxes).every(cb => cb.checked);
    const rowAll = document.getElementById('checkall' + screenId);

    if (rowAll) {
        rowAll.checked = allChecked;
        rowAll.setAttribute('data-check', allChecked ? 'checked' : 'unchecked');
    }
}



</script>





@endsection