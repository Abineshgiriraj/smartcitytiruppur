@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Category')

<head>
    <!-- Add other head elements here -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Common Module/</span> Biometric Creation </h4>
    </div>
</div>

<style>
.btn-right {
    float: right;
}
</style>
@php
$get_zone_name=get_zone();

$get_academic_year =academic_year();

$get_distrcit_name =get_district();
$get_taluk_name = taluk_names();

$get_school_name =get_school_name();

@endphp

<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-body">
                <form id="biometric_update" action="{{ route('biometric.update', $biometric->id) }}" method="POST">
                    @csrf
                    <div class="row mb-3 com-form">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="zone">Zone</label>
                            <select class="form-select santizeinput" name="zone" id="zone">
                                @foreach($get_zone_name as $zone)
                                <option value="{{$zone->unique_id}}"
                                    {{$zone->unique_id ==$biometric->zone ? 'selected' : ''}}>
                                    {{$zone->zone}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="district">District</label>
                            <select class="form-select santizeinput" name="district" id="district">
                                @foreach($get_distrcit_name as $district)
                                <option value="{{$district->unique_id}}"
                                    {{$district->unique_id == $biometric->district ? 'selected' : ''}}>
                                    {{$district->district}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="taluk">Taluk</label>
                            <select class="form-select santizeinput" name="taluk" id="taluk">
                                @foreach($get_taluk_name as $taluk)
                                <option value="{{$taluk->unique_id}}"
                                    {{$taluk->unique_id == $biometric->taluk ? 'selected' : ''}}>
                                    {{$taluk->taluk_name}}
                                </option>
                                @endforeach

                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="school_name">School Location</label>
                            <select class="form-select santizeinput" id="school_name" class="form-control santizeinput"
                                name="school_name">
                                @foreach($get_school_name as $school)
                                <option value="{{$school->unique_id}}"
                                    {{$school->unique_id == $biometric->school_location ? 'selected' : ''}}>
                                    {{$school->school_name}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="device_code">Device Code</label>
                            <input type="text" id="device_code" class="form-control santizeinput" name="device_code"
                                value="{{ $biometric->device_code }}" />
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="device_name">Device Name</label>
                            <input type="text" id="device_name" class="form-control santizeinput" name="device_name"
                                value="{{ $biometric->device_name }}" />
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="serial_no">Serial No</label>
                            <input type="text" id="serial_no" class="form-control santizeinput" name="serial_no"
                                value="{{ $biometric->serial_no }}" />
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="ip_no">IP Details</label>
                            <input type="text" id="ip_no" class="form-control santizeinput" name="ip_details"
                                value="{{ $biometric->ip_details }}" />
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="user_name">User Name</label>
                            <input type="text" id="user_name" class="form-control santizeinput" name="user_name"
                                value="{{ $biometric->user_name }}" />
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="password">Password</label>
                            <input type="text" id="password" class="form-control santizepassword" name="password"
                                value="{{ $biometric->password }}" />
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="created_date">Created Date</label>
                            <input type="date" id="created_date" class="form-control" name="created_date"
                                value="{{ $biometric->created_date }}" />
                        </div>
                        <input type="hidden" name="updated_at" value="{{ now() }}">
                    </div>
                    <div class="btn-float">
                        <button type="button" class="btn btn-danger"
                            onclick="window.location.href='{{ route('biometric_list') }}'">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Include necessary CSS files -->
<link rel="stylesheet" href="{{ asset('public/assets/css/bootstrap.min.css') }}" />
<link rel="stylesheet" href="{{ asset('public/assets/css/dataTables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('public/assets/css/responsive.bootstrap.min.css') }}" />
<link rel="stylesheet" href="{{ asset('public/assets/css/core.css') }}" />

<!-- Include necessary JS files -->
<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('public/assets/js/dataTables.js') }}"></script>
<script src="{{ asset('public/assets/js/dataTables.bootstrap5.js') }}"></script>
<script src="{{ asset('public/assets/js/dataTables.responsive.js') }}"></script>
<script src="{{ asset('public/assets/js/responsive.bootstrap5.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>

<script>
$(document).ready(function() {


    $('.santizeinput').on('input', function() {
        let value = $(this).val();
        let sanitizedValue = value.replace(/[^a-zA-Z0-9 - _]/g, ''); // Remove special characters
        $(this).val(sanitizedValue);
    });

    $('.santizepassword').on('input', function() {
        let value = $(this).val();
        let sanitizedValue = value.replace(/[^a-zA-Z0-9 - _ @]/g, ''); // Remove special characters
        $(this).val(sanitizedValue);
    });

    $('#biometric_update').on('submit', function(event) {
        event.preventDefault(); // Prevent default form submission

        let formdata = $(this).serialize();

        $.ajax({
            url: $(this).attr('action'),
            method: "POST",
            data: formdata,
            success: function(response) {
                const redirectUrl = '{{ route("biometric_list") }}';
                sweetalert('update', redirectUrl); // Ensure sweetalert function is defined
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    sweetalert('already'); // Handle validation errors
                } else {
                    // Handle other error responses
                    const errorMsg = xhr.responseJSON?.error ||
                        'An unexpected error occurred.';
                    Swal.fire('Error!', errorMsg,
                    'error'); // Display a SweetAlert for errors
                }
            }
        });
    });

});

$(document).ready(function() {


    $('#zone').on('change', function() {

        let zone_id = $('#zone').val();
        if (zone_id) {
            $.ajax({
                url: "{{route('zone_district')}}",
                type: "GET",
                data: {
                    'zone_id': zone_id
                },
                success: function(response) {
                    $('#district').empty();
                    $('#district').append('<option value="">Select District</option>');
                    $.each(response.district, function(index, district) {
                        $('#district').append('<option value="' + district
                            .unique_id + '">' + district.district +
                            '</option>');
                    });
                },
                error: function(xhr) {
                    console.log('Error:', xhr);
                }



            });
        } else {
            $('#district').empty();
            $('#district').append(' < option value = "" > Select District < /option>');
        }



    });

    $('#district').on('change', function() {

        let district_id = $('#district').val();
        if (district_id) {
            $.ajax({
                url: "{{route('taluk')}}",
                type: "GET",
                data: {
                    'district_id': district_id
                },
                success: function(response) {
                    $('#taluk').empty();
                    $('#taluk').append('<option value="">Select taluk</option>');
                    $.each(response.taluk, function(index, taluk) {
                        $('#taluk').append('<option value="' + taluk
                            .unique_id + '">' + taluk.taluk_name +
                            '</option>');
                    });
                },
                error: function(xhr) {
                    console.log('Error:', xhr);
                }



            });
        } else {
            $('#taluk').empty();
            $('#taluk').append(' < option value = "" > Select District < /option>');
        }
    });

    $('#taluk').on('change', function() {

        let taluk_id = $('#taluk').val();
        $.ajax({
            url: "{{route('school')}}",
            type: "GET",
            data: {
                'taluk_id': taluk_id
            },
            success: function(response) {
                $('#school_name').empty();
                $('#school_name').append('<option value="">Select School</option>');
                $.each(response.school, function(index, school) {
                    $('#school_name').append('<option value="' + school
                        .unique_id + '">' + school.school_name + '</option>');
                });

            },
            error: function(xhr) {
                console.log('Error:', xhr);
            }

        });

    });



});
</script>
<!-- <script>
    $(document).ready(function() {
        @if(session('success'))
        sweetalert('update', '{{ url('
            biometric_list ') }}');
        @endif
        @if(session('error'))
        sweetalert('error', '', '', 'Something went wrong');
        @endif
        @if(session('form_alert'))
        sweetalert('form_alert');
        @endif
    });
</script> -->




@endsection