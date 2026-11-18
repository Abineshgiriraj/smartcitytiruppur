@extends('layouts/contentNavbarLayout')

@section('title', 'Staff Form')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4>
            <span class="text-muted fw-light">Entry Form /</span> Staff {{ isset($staff) ? 'Edit' : 'Creation' }}
        </h4>
    </div>
</div>

<style>
/* btn style */
.btn-right {
    float: right;
}
</style>
@php
$get_zone_name =get_zone();

$get_district_name = get_district();

$get_taluk_name = taluk_names();
$get_school_name =get_school_name();
$get_academic_year = academic_year();
$get_designation =get_designation();

@endphp

<!-- Basic Layout -->
<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-body">
                <form id="staffForm" method="post" onsubmit="return submitForm(event);">
                    @csrf


                    <div class="row mb-3">
                        <!-- Academic Year -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="acc_year">Academic Year</label>
                            <select class="form-select" id="acc_year" name="acc_year">
                                <option value="">Select Academic Year</option>
                                @foreach ($get_academic_year as $academic_year)
                                <option value="{{$academic_year->unique_id}}">
                                    {{$academic_year->acc_year}}
                                </option> @endforeach
                            </select>
                            <!-- <span id="emp_idError" class="text-danger"></span> -->
                        </div>
                        <!-- Employee Name -->
                        <!-- value="{{ $staff->emp_name ?? '' }}" -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="emp_name">Employee Name</label>
                            <input type="text" class="form-control" id="emp_name" name="emp_name" />
                            <span id="emp_nameError" class="text-danger"></span>
                        </div>
                        <!-- Employee ID -->
                        <!-- value="{{ $staff->emp_id ?? '' }}"  -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="emp_id">Employee ID</label>
                            <input type="text" class="form-control" id="emp_id" name="emp_id">
                            <span id="emp_idError" class="text-danger"></span>
                        </div>
                        <!-- Designation -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="designation">Designation</label>
                            <select class="form-select" id="designation" name="designation">
                                <option value="">Select Designation</option>
                                @foreach ($get_designation as $designation )
                                <option value="{{$designation->unique_id}}">
                                    {{$designation->designation}}
                                </option> @endforeach
                            </select>
                            <span id="designationError" class="text-danger"></span>
                        </div>
                        <!-- Zone -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="zone">Zone Name</label>
                            <select class="form-select select2" id="zone_name" name="zone_name">
                                <option value="">Select Zone</option>
                                @foreach ($get_zone_name as $zone )
                                <option value="{{$zone->unique_id}}">
                                    {{$zone->zone}}
                                </option>
                                @endforeach
                            </select>
                            <span id="zoneError" class="text-danger"></span>
                        </div>
                        <!-- District -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="district">District Name</label>
                            <select class="form-select" id="district_name" name="district_name">
                                <option value="selected">Select District</option>
                                @foreach ($get_district_name as $district)
                                <option value="{{$district->unique_id}}">
                                    {{$district->district}}
                                </option>
                                @endforeach
                            </select>
                            <span id="districtError" class="text-danger"></span>

                        </div>
                        <!-- Taluk -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="taluk_name">Taluk Name</label>
                            <select class="form-select" id="taluk_name" name="taluk_name">
                                <option value="selected">Select Taluk</option>

                                @foreach ($get_taluk_name as $taluk )
                                <option value="{{$taluk->unique_id}}">
                                    {{$taluk->taluk_name}}
                                </option> @endforeach
                            </select>
                            <span id="taluk_nameError" class="text-danger"></span>
                        </div>
                        <!-- School -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="school">School (or) Location Name</label>
                            <select class="form-select select2" id="school_name" name="school_name">
                                <option value="">Select School</option>

                            </select>
                            <span id="schoolError" class="text-danger"></span>
                        </div>
                        <!-- Date of Join -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="doj">Date of Join</label>
                            <input type="date" class="form-control" id="doj" name="doj"
                                value="{{ $staff->doj ?? '' }}" />
                            <span id="dojError" class="text-danger"></span>
                        </div>
                        <!-- Employee Contact Number -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="emp_contact">Employee Contact Number</label>
                            <input type="text" class="form-control" id="emp_contact" name="emp_contact"
                                value="{{ $staff->emp_contact ?? '' }}" />
                            <span id="emp_contactError" class="text-danger"></span>
                        </div>
                    </div>
                    <div class="btn-float">
                        <!-- <button class="btn btn-danger" type="button"
                            onclick="window.location.href='{{ url('/staff') }}'">Cancel</button>
                        <button type="submit" class="btn btn-primary">{{ isset($staff) ? 'Update' : 'Submit' }}</button>
                    </div>
                </form> -->
                        <button class="btn btn-danger" type="button"
                            onclick="window.location.href='{{ url('/staff') }}'">Cancel</button>
                        <button type="submit" class="btn btn-primary">{{ isset($staff) ? 'Update' : 'Submit' }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- <script>
function submitForm(event) {
    event.preventDefault();

    let isValid = validateForm();
    if (!isValid) {
        return false;
    }

    let formData = {
        acc_year: $('#acc_year').val(),
        emp_name: $('#emp_name').val(),
        emp_id: $('#emp_id').val(),
        designation: $('#designation').val(),
        district: $('#district').val(),
        zone: $('#zone').val(),
        taluk_name: $('#taluk_name').val(),
        school: $('#school').val(),
        doj: $('#doj').val(),
        emp_contact: $('#emp_contact').val(),
        _token: '{{ csrf_token() }}',
        _method: '{{ isset($staff) ? '
        PUT ' : '
        POST ' }}'
    };

    let url = '{{ isset($staff) ? url("staff/" . $staff->id . "/update") : url("staff/store") }}';


    $.ajax({
        url: url,
        method: 'POST', // Ensure method is POST for PUT/PATCH requests
        data: formData,
        success: function(response) {
            alert('Staff ' + (formData._method === 'POST' ? 'created' : 'updated') + ' successfully!');
            window.location.href = "{{ url('/staff') }}";
        },
        error: function(xhr) {
            let errors = xhr.responseJSON.errors;
            for (let field in errors) {
                $(`#${field}Error`).text(errors[field][0]);
            }
        }
    });

    return false;
}


</script> -->

<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>
<script>
$(document).ready(function() {


    $('#zone_name').on('change', function() {
        let zone_name = $('#zone_name').val();
        if (zone_name) {
            $.ajax({
                url: "{{route('get_districts')}}",
                type: "GET",
                data: {
                    'zone_name': zone_name
                },
                success: function(response) {
                    $('#district_name').empty();
                    $('#district_name').append('<option value="">Select District</option>');
                    $.each(response.districts, function(index, district) {
                        $('#district_name').append('<option value="' + district
                            .unique_id +
                            '">' + district.district + '</option> ')

                    });


                },
                error: function(xhr) {
                    console.log('Error:', xhr)
                },

            });

        } else {
            $('#district_name').empty();
            $('#district_name').append('<option value="">Select District</option>');
        }
    });

    $('#district_name').on('change', function() {


        let district = $('#district_name').val();
        if (district) {
            $.ajax({

                url: "{{route('get_taluk')}}",
                type: "GET",
                data: {
                    'district': district,
                },
                success: function(response) {
                    $('#taluk_name').empty();
                    $('#taluk_name').append('<option value ="">Select Taluk</option>');
                    $.each(response.taluk, function(index, taluk) {
                        $('#taluk_name').append('<option value="' + taluk
                            .unique_id +
                            '">' + taluk.taluk_name + '</option>');
                    });

                },
                error: function(xhr) {
                    console.log('Error', xhr);
                }


            });
        } else {
            $('#taluk_name').empty();
            $('#taluk_name').append('<option value="">Select Taluk</option>');

        }
    });


    $('#taluk_name').on('change', function() {


        let taluk = $('#taluk_name').val();
        if (taluk) {
            $.ajax({

                url: "{{route('get_schools')}}",
                type: "GET",
                data: {
                    'taluk': taluk,
                },
                success: function(response) {
                    $('#school_name').empty();
                    $('#school_name').append('<option value ="">Select School</option>');
                    $.each(response.school, function(index, school) {
                        $('#school_name').append('<option value="' + school
                            .unique_id +
                            '">' + school.school_name + '</option>');
                    });

                },
                error: function(xhr) {
                    console.log('Error', xhr);
                }


            });
        } else {
            $('#school_name').empty();
            $('#school_name').append('<option value="">Select Taluk</option>');

        }
    });





});


function submitForm(event) {
    event.preventDefault();

    let isValid = validateForm();
    if (!isValid) {
        return false;
    }

    let formData = {
        acc_year: $('#acc_year').val(),
        emp_name: $('#emp_name').val(),
        emp_id: $('#emp_id').val(),
        designation: $('#designation').val(),
        district: $('#district_name').val(),
        zone: $('#zone_name').val(),
        taluk_name: $('#taluk_name').val(),
        school: $('#school_name').val(),
        doj: $('#doj').val(),
        emp_contact: $('#emp_contact').val(),
        _token: '{{ csrf_token() }}',

    };

    // let url = '{{ isset($staff) ? url("staff/" . $staff->id) : url("staff") }}';
    if ((formData.acc_year != '') && (formData.emp_name != '') && (formData.emp_id != '') && (formData.designation !=
            '') && (
            formData
            .district != '') && (formData.zone != '') && (
            formData.taluk_name != '') && (formData.school != '') && (formData.doj != '') && (formData.emp_contact !=
            '')) {
        $.ajax({
            url: "{{route('staff.store')}}",
            method: 'POST',
            data: formData,
            success: function(response) {
                // alert('Staff Created successfully!');
                sweetalert('create');
                window.location.href = "{{ url('/staff') }}";
            },
            error: function(xhr) {
                let errors = xhr.responseJSON.errors;
                for (let field in errors) {
                    $(`#${field}Error`).text(errors[field][0]);
                }
            }
        });
    } else {
        sweetalert("form_alert");
    }

    return false;
}





function validateForm() {
    let isValid = true;
    let emp_contactInput = document.getElementById('emp_contact');

    // Clear previous error messages
    $('.text-danger').text('');

    // Validation logic
    $('#emp_name').each(function() {
        if ($(this).val().trim() === '') {
            $('#emp_nameError').text('Employee Name is required.');
            isValid = false;
        }
    });

    $('#emp_id').each(function() {
        if ($(this).val().trim() === '') {
            $('#emp_idError').text('Employee ID is required.');
            isValid = false;
        }
    });

    // $('# ').each(function() {
    //     if ($(this).val().trim() === '') {
    //         $('#acc_yearError').text('Academic year is required.');
    //         isValid = false;
    //     }
    // });

    $('#designation').each(function() {
        if ($(this).val().trim() === '') {
            $('#designationError').text('Designation is required.');
            isValid = false;
        }
    });

    $('#district_name').each(function() {
        if ($(this).val().trim() === '') {
            $('#districtError').text('District is required.');
            isValid = false;
        }
    });

    $('#zone_name').each(function() {
        if ($(this).val().trim() === '') {
            $('#zoneError').text('Zone is required.');
            isValid = false;
        }
    });

    $('#school_name').each(function() {
        if ($(this).val().trim() === '') {
            $('#schoolError').text('School is required.');
            isValid = false;
        }
    });

    $('#doj').each(function() {
        if ($(this).val().trim() === '') {
            $('#dojError').text('Date of join is required.');
            isValid = false;
        }
    });

    $('#emp_contact').each(function() {
        if ($(this).val().trim() === '') {
            $('#emp_contactError').text('Employee contact is required.');
            isValid = false;
        } else if (emp_contactInput.value.length > 10 || emp_contactInput.value.length < 10) {
            $('#emp_contactError').text('Mobile Number must be 10 characters.');
            isValid = false;
        } else if (!/^[0-9]+$/.test(emp_contactInput.value.trim())) {
            $('#emp_contactError').text('Mobile Number must contain only numeric characters.');
            isValid = false;
        }
    });

    // Additional validation logic as required

    return isValid;
}
</script>

<!-- <script>
    function submitForm(event) {
    event.preventDefault();

    let isValid = validateForm();
    if (!isValid) {
    return false;
    }

    let formData = {
    acc_year: $('#acc_year').val(),
    emp_name: $('#emp_name').val(),
    emp_id: $('#emp_id').val(),
    designation: $('#designation').val(),
    district: $('#district').val(),
    zone: $('#zone').val(),
    taluk_name: $('#taluk_name').val(),
    school: $('#school').val(),
    doj: $('#doj').val(),
    emp_contact: $('#emp_contact').val(),
    _token: '{{ csrf_token() }}',
    _method: '{{ isset($staff) ? '
        PUT ' : '
        POST ' }}'
    };

    let url = '{{ isset($staff) ? url("staff/" . $staff->id) : url("staff") }}'; // Adjusted URL

    $.ajax({
    url: url,
    method: 'POST', // Always use POST for AJAX; Laravel will handle the method via _method
    data: formData,
    success: function(response) {
    alert('Staff ' + (formData._method === 'POST' ? 'created' : 'updated') + ' successfully!');
    window.location.href = "{{ url('/staff') }}";
    },
    error: function(xhr) {
    let errors = xhr.responseJSON.errors;
    for (let field in errors) {
    $(`#${field}Error`).text(errors[field][0]);
    }
    }
    });

    return false;
    }

    function validateForm() {
    let isValid = true;
    let emp_contactInput = document.getElementById('emp_contact');

    // Clear previous error messages
    $('.text-danger').text('');

    // Validation logic
    if ($('#emp_name').val().trim() === '') {
    $('#emp_nameError').text('Employee Name is required.');
    isValid = false;
    }

    if ($('#emp_id').val().trim() === '') {
    $('#emp_idError').text('Employee ID is required.');
    isValid = false;
    }

    if ($('#acc_year').val().trim() === '') {
    $('#acc_yearError').text('Academic year is required.');
    isValid = false;
    }

    if ($('#designation').val().trim() === '') {
    $('#designationError').text('Designation is required.');
    isValid = false;
    }

    if ($('#district').val().trim() === '') {
    $('#districtError').text('District is required.');
    isValid = false;
    }

    if ($('#zone').val().trim() === '') {
    $('#zoneError').text('Zone is required.');
    isValid = false;
    }

    if ($('#school').val().trim() === '') {
    $('#schoolError').text('School is required.');
    isValid = false;
    }

    if ($('#doj').val().trim() === '') {
    $('#dojError').text('Date of join is required.');
    isValid = false;
    }

    if ($('#emp_contact').val().trim() === '') {
    $('#emp_contactError').text('Employee contact is required.');
    isValid = false;
    } else if (emp_contactInput.value.length !== 10) {
    $('#emp_contactError').text('Mobile Number must be 10 characters.');
    isValid = false;
    } else if (!/^[0-9]+$/.test(emp_contactInput.value.trim())) {
    $('#emp_contactError').text('Mobile Number must contain only numeric characters.');
    isValid = false;
    }

    return isValid;
    }
    </script> -->