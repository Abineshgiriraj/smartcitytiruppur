@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')


@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Entry Forms/</span> Bio-Metric Creation</h4>
    </div>
</div>
<style>
/* btn style */
.btn-right {
    float: right;
}

.valid {
    color: green;
}

.invalid {
    color: red;
}
</style>

@php
$get_zone_name =get_zone();
$get_distrcit_name = get_district();

$get_taluk_name = taluk_names();



$get_school_name =get_school_name();


$date =date('Y-m-d');

@endphp

<!-- Basic Layout -->
<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-body">
                <form id="biometricdevice" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="basic-default-fullname">Zone Name</label>
                            <select class="form-select santizeinput" id="zone" name="zone">
                                <option value="selected">Select Zone</option>
                                @foreach ($get_zone_name as $zone )
                                <option value="{{$zone->unique_id}}">
                                    {{$zone->zone}}
                                </option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="basic-default-fullname">District Name</label>
                            <select class="form-select santizeinput" id="district" name="district">
                                <option value="selected">Select District</option>
                                @foreach ( $get_distrcit_name as $district )
                                <option value="{{$district->unique_id}}">
                                    {{$district->district}}
                                </option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="basic-default-fullname">Taluk Name</label>
                            <select class="form-select santizeinput" id="taluk" name="taluk">
                                <option value="selected">Select Taluk</option>
                                @foreach ($get_taluk_name as $taluk )
                                <option value="{{$taluk->unique_id}}">
                                    {{$taluk->taluk_name}}
                                </option> @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">

                            <label class="form-label" for="basic-default-fullname">School Name</label>
                            <select class="form-select santizeinput" id="school_name" name="school_name">
                                <option value="selected">Select School</option>
                                @foreach ($get_school_name as $school)
                                <option value="{{$school->unique_id}}">
                                    {{$school->school_name}}
                                </option>
                                @endforeach
                            </select>
                            <div id="message"></div>

                        </div>
                        <div class="col-md-4 mb-3">

                            <label class="form-label" for="basic-default-fullname">Device ID</label>
                            <input type="text" class="form-control santizeinput" id="device_code" name="device_code" />
                        </div>
                        <div class="col-md-4 mb-3">

                            <label class="form-label" for="basic-default-fullname">Device Name</label>
                            <input type="text" class="form-control santizeinput" id="device_name" name="device_name" />
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="basic-default-message">Serial No</label>
                            <input class="form-control santizeinput" name="serial_no" id="serial_no" />
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="basic-default-message">IP Details</label>
                            <input class="form-control santizeip" name="ip_no" id="ip_no" />
                            <p id="ipError"></p>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="basic-default-message">User name</label>
                            <input class="form-control santizeinput" name="user_name" id="user_name" />
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="basic-default-message">Password</label>
                            <input class="form-control sanitizePassword" type="text" id="password" name="password" />
                            <p id="validationmessage"></p>
                        </div>
                        <div class=" col-md-4 mb-3">
                            <label class="form-label" for="basic-default-message">Create Date</label>
                            <input type="date" class="form-control" id="date" name="date" value="{{$date}}" />
                        </div>
                    </div>
                    <div class="btn-float">
                        <button type="button" class="btn btn-danger"
                            onclick="window.location.href='{{ url('/biometric_list') }}'">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</div>
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->


<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>

<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>


<script>
$(document).ready(function() {

    $('.santizeinput').on('input', function() {
        let value = $(this).val();
        let sanitizedValue = value.replace(/[^a-zA-Z0-9 - _]/g, ''); // Remove special characters
        $(this).val(sanitizedValue);
    });
    $('.santizeip').on('input', function() {
        let value = $(this).val();
        let sanitizedValue = value.replace(/[^0-9 .]/g, ''); // Remove special characters
        $(this).val(sanitizedValue);
    });



    $('.sanitizePassword').on('input', function() {
        let value = $(this).val();
        // Trim leading and trailing whitespace
        let trimmedValue = value.trim();
        // Allow only uppercase letters, lowercase letters, digits, and special characters
        let sanitizedValue = trimmedValue.replace(
            /[^a-zA-Z0-9!@#$%^&*()_]/g, '');
        $(this).val(sanitizedValue);


    });





    //dynamically fetching values from table
    $.ajax({
        url: "{{ route('districts') }}",
        type: "GET",
        success: function(data) {
            console.log('District data:', data);
            let districtSelect = $('#district');
            districtSelect.empty(); // Clear any existing options

            districtSelect.append(
                '<option value="">Select District</option>');
            $.each(data,
                function(key, district) {
                    console.log('Appending:', district);
                    districtSelect.append('<option value="' + district.district + '">' +
                        district.district + '</option>');

                });

        },
        error: function(xhr) {
            console.log(xhr.responseText);
            alert('An error occurred while fetching districts. Please try again.');
        }
    });


    $('#biometricdevice').on('submit', function(e) {
        e.preventDefault();

        // let ip_no = validateIPAddress($('#ip_no').val());
        // let password = validatePassword($('#password').val());


        let isValid = validateForm();
        console.log('Validation result:', isValid);
        if (!isValid) {
            return false;
        }


        var form_data = {
            zone: $('#zone').val(),
            district: $('#district').val(),
            taluk: $('#taluk').val(),
            school_name: $('#school_name').val(),
            device_code: $('#device_code').val(),
            device_name: $('#device_name').val(),
            serial_no: $('#serial_no').val(),
            ip_details: $('#ip_no').val(), // Corrected from ip_no to ip_details
            user_name: $('#user_name').val(),
            password: $('#password').val(),
            created_date: $('#date').val(), // Corrected from date to created_date
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString(),
            _token: '{{ csrf_token() }}'
        };


        console.log('Form Data:', form_data);


        if ((form_data.zone != '') && (form_data.district != '') && (form_data.taluk != '') &&
            (form_data.school_name != '') && (form_data.device_code != '') &&
            (form_data.device_name != '') && (form_data.serial_no != '') &&
            (form_data.ip_details != '') && (form_data.user_name != '') &&
            (form_data.password != '')) {

            $.ajax({
                url: "{{ route('biometric.store') }}",
                type: "POST",
                data: form_data,
                success: function(response) {
                    const redirectUrl = '{{ route("biometric_list") }}';
                    // console.log('Redirecting to:', redirectUrl);
                    sweetalert('create', redirectUrl);


                },
                error: function(xhr) {


                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON
                        .error) {

                        Swal.fire({
                            icon: 'info',
                            title: 'Already Exist!',
                            showConfirmButton: true,
                            timer: 1500,
                            timerProgressBar: true
                        });
                    }

                }
            });



        } else {
            sweetalert("form_alert");
        }
    });
});

// document.getElementById('ip_no').onkeyup = function() {
//     validateIPAddress(this.value);
// };

// function validateIPAddress(ip) {
//     const ipInput = document.getElementById('ip_no');
//     const validationMessage = document.getElementById('validationMessage');
//     // Regular expression to validate IP address
//     const ipPattern =
//         /^(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])\.(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])\.(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])\.(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])$/;

//     if (ipPattern.test(ip)) {
//         validationMessage.textContent = 'Valid IP address';
//         validationMessage.classList.remove('invalid');
//         validationMessage.classList.add('valid');
//     } else {
//         validationMessage.textContent = 'Invalid IP address';
//         validationMessage.classList.remove('valid');
//         validationMessage.classList.add('invalid');
//     }
// }


function validateForm() {
    let isValid = true;

    // Clear previous error messages
    $('.text-danger').text('');

    // IP Address validation
    let ipInput = document.getElementById('ip_no');
    let ipValidationMessage = document.getElementById('ipError');
    const ipPattern =
        /^(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])\.(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])\.(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])\.(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])$/;

    if (ipInput.value.trim() === '') {
        ipValidationMessage.textContent = 'IP Address is required.';
        isValid = false;
    } else if (!ipPattern.test(ipInput.value.trim())) {
        ipValidationMessage.textContent = 'Invalid IP address.';
        isValid = false;
    }

    // Password validation
    let passwordInput = document.getElementById('password');
    let passwordValidationMessage = document.getElementById('validationmessage');
    const passwordPattern = /^(?=.*[A-Z])(?=.*[!@#$%^&*()_+{}\[\]:;"'<>,.?\/\\|`~\-=\s])(?=.*[a-zA-Z0-9]).{8,}$/;

    if (passwordInput.value.trim() === '') {
        passwordValidationMessage.textContent = 'Password is required.';
        isValid = false;
    } else if (!passwordPattern.test(passwordInput.value.trim())) {
        passwordValidationMessage.textContent =
            'Password must be at least 8 characters, include one uppercase letter and one special character.';
        isValid = false;
    }

    return isValid;
}











// function validate_form() {

//     let is_valid = true;

document.getElementById('ip_no').onkeyup = function() {
    validateIPAddress(this.value);
};

document.getElementById('ip_no').onblur = function() {
    const ipInput = document.getElementById('ip_no');
    const validationMessage = document.getElementById('ipError');
    if (validationMessage.classList.contains('valid')) {
        validationMessage.textContent = '';
    }
};

{


    function validateIPAddress(ip) {
        const ipInput = document.getElementById('ip_no');
        const validationMessage = document.getElementById('ipError');
        // Regular expression to validate IP address
        const ipPattern =
            /^(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])\.(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])\.(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])\.(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])$/;

        if (ipPattern.test(ip)) {
            validationMessage.textContent = 'Valid IP address';
            validationMessage.classList.remove('invalid');
            validationMessage.classList.add('valid');
            return true;
        } else {
            validationMessage.textContent = 'Invalid IP address';
            validationMessage.classList.remove('valid');
            validationMessage.classList.add('invalid');
            return false;
        }
    }
}




document.getElementById('password').onkeyup = function() {
    validatePassword(this.value);
};

document.getElementById('password').onblur = function() {
    const password = document.getElementById('password');
    const validationmessage = document.getElementById('validationmessage');
    if (validationmessage.classList.contains('valid')) {
        validationmessage.textContent = '';

    }
};

function validatePassword() {
    const password = $('#password').val();
    const validationpassword = document.getElementById('validationmessage');

    const passwordPattern =
        /^(?=.*[A-Z])(?=.*[!@#$%^&*()_+{}\[\]:;"'<>,.?\/\\|`~\-=\s])(?=.*[a-zA-Z0-9]).{8,}$/;
    if (passwordPattern.test(password)) {
        validationpassword.textContent = '';
        // .Minimum 8 characters, at least one uppercase letter and one special characterssss // 
    } else {
        validationpassword.textContent = 'Enter a valid password.';
        validationpassword.classList.remove('valid');
        validationpassword.classList.add('invalid');
    }
}


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
                    $('#district').append(
                        '<option value="">Select District</option>');
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
                        .unique_id + '">' + school.school_name +
                        '</option>'
                    );
                });

            },
            error: function(xhr) {
                console.log('Error:', xhr);
            }

        });

    });



});
</script>

@endsection