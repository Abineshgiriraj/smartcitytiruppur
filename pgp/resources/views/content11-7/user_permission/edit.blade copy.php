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
        <form id="mainscreenForm"  method="post" action="{{route('userpermission.update', $user_permission->main_screen_id)}}" autocomplete="off">
            @csrf
            @method('PUT')


            <!-- method=" post" onsubmit="return submitForm(event);"> -->
            <!-- @csrf
             
            @if(isset($zone))
                @method('PUT')
            @endif -->

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
                        </option> 
                        @endforeach
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
                <a href="{{ URL::previous() }}"><button type="button" class="btn btn-danger waves-effect waves-light">Cancel</button></a>
                    <!-- <button type="button" class="btn btn-danger"
                        onclick=" ">Cancel</button> -->
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
    <!-- window.location.href='{{ route('user_permission.index') }}' -->
</div>


<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>
<script>

function permui_val() {

    // alert();

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

// $('#mainscreenForm').on('submit', function(event) {


//     event.preventDefault();


//     var user_type = $('#user_type').val();
//     var main_screen = $('#main_screen').val();


//     var selectedPermission = [];

//     $('input[name="checkboxvalue[]"]:checked').each(function() {

//         var user_screen_id = $(this).data('screen');
//         var action_id = $(this).data('action');
//         var main_screen_id = $(this).data('main');



//         selectedPermission.push({
//             "user_screen_id": user_screen_id,
//             "action_id": action_id,
//             "main_screen_id": main_screen_id

//         });
//     });

//     var formdata = {

//         user_type: user_type,
//         permissions: selectedPermission,
//         _token: '{{ csrf_token() }}',
//         created_at: new Date().toISOString(), // Example: current date-time
//         updated_at: new Date().toISOString(),

//         // main_screen: main_screen,
//         // user_screen_id: $('#user_screen').val(),
//         // action_id: $('#action_id').val(),
//     }

//     $.ajax({
//         type: "POST",
//         url: "{{route('userpermission.store')}}",
//         data: formdata,
//         success: function(response) {
//             // alert('ad');
//             const redirectUrl = '{{ route("user_permission.index") }}';
//             sweetalert('create', redirectUrl);

//         },
//         error: function(xhr) {
//             console.log(data);
//             console.error("Error occurred:", xhr.responseText);
//         }

//     });

// });

function check_all(groupClass, element) {
        const isChecked = element.getAttribute('data-check') === 'checked';
        const checkboxes = document.querySelectorAll('.' + groupClass);
        
        checkboxes.forEach((checkbox) => {
            checkbox.checked = !isChecked;
        });
        
        // Toggle state for next click
        element.setAttribute('data-check', isChecked ? 'unchecked' : 'checked');
    }

    // Toggle the main "Check All" based on individual checks
    function check_me(screenId, element) {
        const allCheckboxes = document.querySelectorAll('.allcheck-' + screenId);
        const allChecked = Array.from(allCheckboxes).every(cb => cb.checked);
        
        const mainCheckbox = document.getElementById('checkall' + screenId);
        mainCheckbox.checked = allChecked;
    }

function createRows(response) {

    var len = 0;
    $('#empTable tbody').empty(); // Empty <tbody>
    if (response['data'] != null) {
        len = response['data'].length;
    }
    $('#left').show();
    if (len > 0) {
        for (var i = 0; i < len; i++) {

            var user_screen_name = response['data'][i].user_screen_name;
            console.log(user_screen_name);


            var tr_str = "<tr>" +
                "<td>" + user_screen_name + "</td>" + "<form><th id='head'>" +
                '<input type="checkbox" id="selectall" />' + "</th>" +
                "<th id='head'>" + '<input type="checkbox" class="checkSingle" name="sample[]" />' +
                "</th>" +
                "<th id='head'>" + '<input type="checkbox" class="checkSingle" name="sample[]" />' +
                "</th>" +
                "<th id='head'>" + '<input type="checkbox"class="checkSingle" name="sample[]" />' +
                "</th>" +
                "<th id='head'>" + '<input type="checkbox" class="checkSingle" name="sample[]" />' +
                "</th></form>"
            "</tr>";
            $("#selectall").change(function() {

                if (this.checked) {
                    //     if($(".checkSingle1")){
                    //   $(".checkSingle").each(function(){
                    //     this.checked=true;
                    //   });
                    // }else if($(".checkSingle2")){
                    //   $(".checkSingle").each(function(){
                    //     this.checked=true;
                    //   });
                    // }else if($(".checkSingle3")){
                    //   $(".checkSingle").each(function(){
                    //     this.checked=true;
                    //   });
                    // }
                    $(".checkSingle").each(function() {
                        this.checked = true;
                    });
                } else {
                    $(".checkSingle").each(function() {
                        this.checked = false;
                    })
                }
            });
            // $('#selectall').click(function() { $(this.form.elements).filter(':checkbox').prop('checked', this.checked);
            // });
            $("#empTable tbody").append(tr_str);

        }
    } else {
        var tr_str = "<tr>" +
            "<td align='center' colspan='4'>No record found.</td>" +
            "</tr>";

        $("#empTable tbody").append(tr_str);
    }

}
</script>




<!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@1s1"></script>
<script>
    function submitForm(event) {
        event.preventDefault();

        let isValid = validateForm();
        if (!isValid) {
            return false;
        }

        let formData = {
            zone: $('#zone').val(),
            remarks: $('#remarks').val(),
            _token: '{{ csrf_token() }}',
            _method: '{{ isset($zone) ? 'PUT' : 'POST' }}'
        };

        let url = '{{ isset($zone) ? route('zone.update', $zone->id) : route('zone.store') }}';

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            success: function (response) {
                alert('Zone ' + (formData._method === 'POST' ? 'created' : 'updated') + ' successfully!');
                window.location.href = "{{ url('/zone') }}";
            },
            error: function (xhr) {
                let errors = xhr.responseJSON.errors;
                if (errors.zone) {
                    $('#zoneError').text(errors.zone[0]);
                }
            }
        });

        return false;
    }

    function validateForm() {
        let isValid = true;
        let zoneInput = document.getElementById('zone');
        let zoneError = document.getElementById('zoneError');

        // Clear previous error messages
        zoneError.textContent = '';

        // Validate Zone Name
        if (zoneInput.value.trim() === '') {
            zoneError.textContent = 'Zone Name is required.';
            isValid = false;
        } else if (!/^[A-Za-z\s]+$/.test(zoneInput.value.trim())) {
            zoneError.textContent = 'Zone Name must contain only alphabetic characters.';
            isValid = false;
        } else if (zoneInput.value.length < 3) {
            zoneError.textContent = 'Zone Name must be at least 3 characters long.';
            isValid = false;
        }


        return isValid;
    }
</script> -->

@endsection