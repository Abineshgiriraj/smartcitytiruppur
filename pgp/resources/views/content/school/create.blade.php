@extends('layouts/contentNavbarLayout')

@section('title', 'School Form')

@section('content')

<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4>
            <span class="text-muted fw-light">Entry Form /</span> School {{ isset($school) ? 'Edit' : 'Creation' }}
        </h4>
    </div>
</div>
@php
$get_zone_name =get_zone();
$get_district_name =get_district();

@endphp

<style>
/* btn style */
.btn-right {
    float: right;
}
</style>

<!-- Basic Layout -->
<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-body">
                <form id="schoolForm">
                    @csrf
                    <!-- @if(isset($school))
                    @method('PUT')
                    <input type="hidden" name="id" value="{{ $school->id }}">
                    @endif -->

                    <div class="row mb-3">

                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="zone_name">Zone Name</label>
                            <select class="form-select" id="zone_name" name="zone_name">
                                <option value="selected">Select Zone</option>
                                @foreach($get_zone_name as $zone)
                                <option value="{{$zone->unique_id}}">
                                    {{$zone->zone}}
                                </option>
                                @endforeach
                            </select>
                            <span id="zone_nameError" class="text-danger"></span>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="district_name">District Name</label>
                            <select class="form-select" id="district_name" name="district_name">
                                <option value="selected">Select District</option>
                                @foreach($get_district_name as $district)
                                <option value="{{$district->unique_id}}">
                                    {{$district->district}}
                                </option>
                                @endforeach
                            </select>
                            <span id="district_nameError" class="text-danger"></span>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="taluk_name">Taluk Name</label>
                            <select class="form-select" id="taluk_name" name="taluk_name">

                                <!-- Add more options as needed -->
                            </select>
                            <span id="taluk_nameError" class="text-danger"></span>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="school_name">School (or) Location Name</label>
                            <input type="text" class="form-control" id="school_name" name="school_name"
                                value="{{ $school->school_name ?? '' }}" />
                            <span id="school_nameError" class="text-danger"></span>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="remark">Remarks</label>
                            <textarea class="form-control" id="remark"
                                name="remark">{{ $school->remark ?? '' }}</textarea>
                            <!-- <span id="remarkError" class="text-danger"></span> -->
                        </div>
                    </div>
                    <div class="btn-float">
                        <button class="btn btn-danger" type="button"
                            onclick="window.location.href='{{route('school.index')}}'">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>

<script>
$(document).ready(function() {
    $('#schoolForm').on('submit', function(e) {
        e.preventDefault();

        // Validate form
        let isValid = validateForm();
        if (!isValid) {
            return false;
        }

        // Gather form data
        let formData = {
            district_name: $('#district_name').val(),
            zone_name: $('#zone_name').val(),
            taluk_name: $('#taluk_name').val(),
            school_name: $('#school_name').val(),
            remark: $('#remark').val(),
            _token: '{{ csrf_token() }}', // CSRF token for security
        };

        // Perform AJAX request
        $.ajax({
            url: "{{ route('school.store') }}", // URL to store the school
            method: 'POST', // Always use POST, Laravel will handle the method type through _method
            data: formData,
            success: function(response) {
                sweetalert('create');
                window.location.href = "{{ route('school.index') }}";
                // SweetAlert2 success message
                // Swal.fire({
                //     title: 'Success!',
                //     text: 'School created successfully!',
                //     icon: 'success',
                //     timer: 1000, // 1 second timer
                //     showConfirmButton: false // Automatically close after 1 second
                // }).then(() => {
                //     // Redirect after success
                //     
                // });
            },
            error: function(xhr) {
                // SweetAlert2 error message
                let errors = xhr.responseJSON.errors;
                for (let field in errors) {
                    $(`#${field}Error`).text(errors[field][0]);
                }

                Swal.fire({
                    title: 'Error!',
                    text: 'Please correct the errors and try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });

        return false; // Prevent default form submission
    });
});

$(document).ready(function() {

    $('#zone_name').on('change', function() {

        let zone_id = $('#zone_name').val();

        console.log('Selected Zone ID:', zone_id);

        if (zone_id) {
            $.ajax({
                url: "{{route('get_district')}}",
                type: "GET",
                data: {
                    'zone_id': zone_id
                },
                success: function(response) {
                    console.log(response);
                    $('#district_name').empty();
                    $('#district_name').append(
                        '<option value="">Select District</option>');
                    $.each(response.districts, function(index, district) {
                        $('#district_name').append('<option value="' +
                            district
                            .unique_id + '">' + district.district +
                            '</option>');
                    });
                },
                error: function(xhr) {
                    console.log('Error:', xhr);
                }


            });

        } else {
            $('#district_name').empty();
            $('#district_name').append('<option value="">Select District</option>');
        }
        // Clear taluk options
        // $('#taluk_name').empty();
        // $('#taluk_name').append('<option value="">Select Taluk</option>');


    });


    $('#district_name').on('change', function() {
        let district_name = $(this).val();

        if (district_name) {
            $.ajax({
                url: "{{ route('get_taluk') }}",
                type: "GET",
                data: {
                    'district': district_name
                },
                success: function(response) {
                    $('#taluk_name').empty();
                    $('#taluk_name').append('<option value="">Select Taluk</option>');
                    $.each(response.taluk, function(index, taluk) {
                        console.log(taluk); // Log each taluk object
                        console.log(taluk
                            .unique_id); // Log unique_id to see if it exists
                        console.log(taluk.taluk);
                        $('#taluk_name').append('<option value="' + taluk
                            .unique_id + '">' + taluk.taluk_name +
                            '</option>');
                    });
                },
                error: function(xhr) {
                    console.error('Error:', xhr);
                }
            });
        }
    });


});


function validateForm() {
    let isValid = true;

    // Clear previous error messages
    $('.text-danger').text('');

    // Validation logic
    if ($('#district_name').val().trim() === '') {
        $('#district_nameError').text('District Name is required.');
        isValid = false;
    }

    if ($('#zone_name').val().trim() === '') {

        $('#zone_nameError').text('Zone Name is required.');
        isValid = false;
    }

    if ($('#taluk_name').val().trim() === '') {
        $('#taluk_nameError').text('Taluk Name is required.');
        isValid = false;
    }

    if ($('#school_name').val().trim() === '') {
        $('#school_nameError').text('School Name is required.');
        isValid = false;
    }

    return isValid;
}
</script>