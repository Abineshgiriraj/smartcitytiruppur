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
        <form id="mainscreenForm">
            @csrf


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
                        <option value="{{$user_type->unique_id}}">
                            {{$user_type->user_type}}
                        </option> @endforeach
                    </select>
                    <div id="user_type_Error" class="form-text text-danger"></div>
                </div>
                <div class="mb-3 col-md-6">
                    <label class="form-label" for="main_screen">Main Screen</label>
                    <select name="main_screen" id="main_screen" onchange="permui_val()" class="form-select">
                        <option value="selected">Select Main screen</option>

                        @foreach($main_screen_options as $main_screen)
                        <option value="{{$main_screen->unique_id}}">
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

            <!-- ///////////////////////////////////////////////////////////////////////////////// -->

            <!-- <div class="col-12" id="perm_ui">
                <div class="card-box">

                </div>
            </div>
            <div class=" row mt-2 justify-content-md-center" id="view_permissions" style="display: none;">
                <div class="col-12">
                    <div id="DataTables_Table_0_processing" class="dt-processing card" role="status"
                        style="display: none;">
                        <div>
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                        </div>
                    </div>
                    <table class="user_permission_datatable table dt-responsive nowrap w-100 dataTable dtr-inline"
                        id="DataTables_Table_0" style="width: 1067px;">
                        <colgroup>
                            <col data-dt-column="0" style="width: 2.537rem;">
                            <col data-dt-column="1" style="width: 13.696rem;">
                            <col data-dt-column="2" style="width: 5.754rem;">
                            <col data-dt-column="3" style="width: 6.847rem;">
                            <col data-dt-column="4" style="width: 8.950rem;">
                            <col data-dt-column="5" style="width: 6.259rem;">
                            <col data-dt-column="6" style="width: 8.367rem;">
                            <col data-dt-column="7" style="width: 7.272rem;">
                            <col data-dt-column="8" style="width: 7.071rem;">
                        </colgroup>
                        <thead>
                            <tr role="row">
                                <th data-dt-column="0" class="td-text-center dt-orderable-none" rowspan="1" colspan="1">
                                    <span class="dt-column-title">#</span><span class="dt-column-order"></span>
                                </th>
                                <th data-dt-column="1" class="td-text-center dt-orderable-none" rowspan="1" colspan="1">
                                    <span class="dt-column-title">Screen</span><span class="dt-column-order"></span>
                                </th>
                                <th data-dt-column="2" class="td-text-center dt-orderable-none" rowspan="1" colspan="1">
                                    <span class="dt-column-title">
                                        <button onclick="check_all('all',this)" type="button" data-id="all"
                                            data-check="checked"
                                            class="btn btn-sm btn-soft-info btn-rounded waves-effect waves-light table-bt"
                                            value="unchecked">All</button>
                                    </span><span class="dt-column-order"></span>
                                </th>
                                <th data-dt-column="3" class="td-text-center dt-orderable-none" rowspan="1" colspan="1">
                                    <span class="dt-column-title">
                                        <button onclick="check_all('add',this)" data-check="unchecked" type="button"
                                            data-id="add"
                                            class="btn btn-sm btn-soft-info btn-rounded waves-effect waves-light table-bt"
                                            value="unchecked">Add</button>
                                    </span><span class="dt-column-order"></span>
                                </th>
                                <th data-dt-column="4" class="td-text-center dt-orderable-none" rowspan="1" colspan="1">
                                    <span class="dt-column-title">
                                        <button onclick="check_all('update',this)" data-check="unchecked" type="button"
                                            data-id="update"
                                            class="btn btn-sm btn-soft-info btn-rounded waves-effect waves-light table-bt"
                                            value="unchecked">Update</button>
                                    </span><span class="dt-column-order"></span>
                                </th>
                                <th data-dt-column="5" class="td-text-center dt-orderable-none" rowspan="1" colspan="1">
                                    <span class="dt-column-title">
                                        <button onclick="check_all('list',this)" data-check="unchecked" type="button"
                                            data-id="list"
                                            class="btn btn-sm btn-soft-info btn-rounded waves-effect waves-light table-bt"
                                            value="unchecked">List</button>
                                    </span><span class="dt-column-order"></span>
                                </th>
                                <th data-dt-column="6" class="td-text-center dt-orderable-none" rowspan="1" colspan="1">
                                    <span class="dt-column-title">
                                        <button onclick="check_all('delete',this)" data-check="unchecked" type="button"
                                            data-id="delete"
                                            class="btn btn-sm btn-soft-info btn-rounded waves-effect waves-light table-bt"
                                            value="unchecked">Delete</button>
                                    </span><span class="dt-column-order"></span>
                                </th>
                                <th data-dt-column="7" class="td-text-center dt-orderable-none" rowspan="1" colspan="1">
                                    <span class="dt-column-title">
                                        <button onclick="check_all('view',this)" data-check="unchecked" type="button"
                                            data-id="view"
                                            class="btn btn-sm btn-soft-info btn-rounded waves-effect waves-light table-bt"
                                            value="unchecked">View</button>
                                    </span><span class="dt-column-order"></span>
                                </th>
                                <th data-dt-column="8" class="td-text-center dt-orderable-none" rowspan="1" colspan="1">
                                    <span class="dt-column-title">
                                        <button onclick="check_all('Print',this)" data-check="unchecked" type="button"
                                            data-id="Print"
                                            class="btn btn-sm btn-soft-info btn-rounded waves-effect waves-light table-bt"
                                            value="unchecked">Print</button>
                                    </span><span class="dt-column-order"></span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="dtr-control td-text-center" tabindex="0">1</td>
                                <td class="td-text-center">Application</td>
                                <td class="td-text-center">
                                    <span class="toggle-button toggle-button--tuuli">
                                        <input class="all" onclick="check_all('screen',this)" id="all" type="checkbox">
                                        <label for="all"></label>
                                        <div class="toggle-button__icon"></div>
                                    </span>
                                </td>
                                <td class="td-text-center">
                                    <span class="toggle-button toggle-button--tuuli">
                                        <input onclick="check_me(this)" class="all-checkbox add all screen allcheck"
                                            data-main="" data-section="" data-screen="" data-action="5f8807ff00bf726601"
                                            id="5f8807ff00bf726601" type="checkbox">
                                        <label for="5f8807ff00bf726601"></label>
                                        <div class="toggle-button__icon"></div>
                                    </span>
                                </td>
                                <td class="td-text-center">
                                    <span class="toggle-button toggle-button--tuuli">
                                        <input onclick="check_me(this)" class="all-checkbox update all screen allcheck"
                                            data-main="" data-section="" data-screen="" data-action="5f88082b25ec031952"
                                            id="5f88082b25ec031952" type="checkbox">
                                        <label for="5f88082b25ec031952"></label>
                                        <div class="toggle-button__icon"></div>
                                    </span>
                                </td>
                                <td class="td-text-center">
                                    <span class="toggle-button toggle-button--tuuli">
                                        <input onclick="check_me(this)" class="all-checkbox list all screen allcheck"
                                            data-main="" data-section="" data-screen="" data-action="5f88083fd50a344823"
                                            id="5f88083fd50a344823" type="checkbox">
                                        <label for="5f88083fd50a344823"></label>
                                        <div class="toggle-button__icon"></div>
                                    </span>
                                </td>
                                <td class="td-text-center">
                                    <span class="toggle-button toggle-button--tuuli">
                                        <input onclick="check_me(this)" class="all-checkbox delete all screen allcheck"
                                            data-main="" data-section="" data-screen="" data-action="5f8808504271036738"
                                            id="5f8808504271036738" type="checkbox">
                                        <label for="5f8808504271036738"></label>
                                        <div class="toggle-button__icon"></div>
                                    </span>
                                </td>
                                <td class="td-text-center">
                                    <span class="toggle-button toggle-button--tuuli">
                                        <input onclick="check_me(this)" class="all-checkbox view all screen allcheck"
                                            data-main="" data-section="" data-screen="" data-action="5f880860d6b32a9009"
                                            id="5f880860d6b32a9009" type="checkbox">
                                        <label for="5f880860d6b32a9009"></label>
                                        <div class="toggle-button__icon"></div>
                                    </span>
                                </td>
                                <td class="td-text-center">
                                    <span class="toggle-button toggle-button--tuuli">
                                        <input onclick="check_me(this)" class="all-checkbox Print all screen allcheck"
                                            data-main="" data-section="" data-screen="" data-action="5f88087e2f446d9"
                                            id="5f88087e2f446d9" type="checkbox">
                                        <label for="5f88087e2f446d9"></label>
                                        <div class="toggle-button__icon"></div>
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div> -->



            <!-- ///////////////////////////////////////////////////////////////////////////////// -->

            <div class="row">
                <div class="btn-float">
                    <button type="button" class="btn btn-danger"
                        onclick="window.location.href='{{ route("user_permission.index") }}' ">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>


<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>
<script>
// document.addEventListener('DOMContentLoaded', function() {
//     var mainScreenSelect = document.getElementById('main_screen');
//     var viewPermissionsDiv = document.getElementById('view_permissions');

//     mainScreenSelect.addEventListener('change', function() {
//         if (mainScreenSelect.value) {
//             viewPermissionsDiv.style.display = 'block';
//         } else {
//             viewPermissionsDiv.style.display = 'none';
//         }
//     });
// });








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

$('#mainscreenForm').on('submit', function(event) {


    event.preventDefault();


    var user_type = $('#user_type').val();
    var main_screen = $('#main_screen').val();


    var selectedPermission = [];

    $('input[name="checkboxvalue[]"]:checked').each(function() {

        var user_screen_id = $(this).data('screen');
        var action_id = $(this).data('action');
        var main_screen_id = $(this).data('main');



        selectedPermission.push({
            "user_screen_id": user_screen_id,
            "action_id": action_id,
            "main_screen_id": main_screen_id

        });
    });

    var formdata = {

        user_type: user_type,
        permissions: selectedPermission,
        _token: '{{ csrf_token() }}',
        created_at: new Date().toISOString(), // Example: current date-time
        updated_at: new Date().toISOString(),

        // main_screen: main_screen,
        // user_screen_id: $('#user_screen').val(),
        // action_id: $('#action_id').val(),
    }

    $.ajax({
        type: "POST",
        url: "{{route('userpermission.store')}}",
        data: formdata,
        success: function(response) {
            // alert('ad');
            const redirectUrl = '{{ route("user_permission.index") }}';
            sweetalert('create', redirectUrl);

        },
        error: function(xhr) {
            console.log(data);
            console.error("Error occurred:", xhr.responseText);
        }

    });

});



function check_all(class_name = "", this_obj = "") {

    if (this_obj.type == "button") {

        is_check = $(this_obj).val();

        if (is_check == "unchecked") {
            $('.' + class_name).each(function() {
                $(this).prop('checked', true); // checks it
            });
            $(this_obj).attr("data-check", "checked");
            $(this_obj).val("checked");
        } else {

            $('.' + class_name).each(function() {
                $(this).prop('checked', false); // checks it
            });
            $(this_obj).attr("data-check", "unchecked");
            $(this_obj).val("unchecked");
        }
    } else {
        if (this_obj.checked) {
            $('.' + class_name).each(function() {
                $(this).prop('checked', true); // checks it
            });
        } else {
            $('.' + class_name).each(function() {
                $(this).prop('checked', false); // Un Checks it
            });
        }
    }
}

function check_me(class_name = "") {
    var is_value = 1;

    if (class_name) {
        $('.allcheck-' + class_name).each(function() {
            if (!this.checked) {
                is_value *= 0;
            }
        });

        if (is_value) {
            $("#all" + class_name).prop('checked', true);
        } else {
            $("#all" + class_name).prop('checked', false);
        }
    }
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
            // console.log(user_screen_name);


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