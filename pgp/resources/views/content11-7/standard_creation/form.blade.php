@extends('layouts/contentNavbarLayout')
@section('title', ' Vertical Layouts - Forms')
@section('content')


<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Master/</span>Standard</h4>
    </div>
</div>



<div class="card">
    <div class="card-body">
        <form id="standardform">
            @csrf
            <div class=" row">

                <div class="col-md-3 mb-3">
                    <label class="form-label" for="basic-default-fullname">School ID</label>
                    <input type="text" class="form-control" id="school_id" name="school_id" />
                </div>

                <div class="col-md-1 mb-3 mt-4">
                    <button type="button" id="goButton" class="btn btn-primary">Go</button>
                </div>

                <div id="school_names" class="col-md-3 mb-3" style="display:none;">
                    <label class="form-label" for="basic-default-fullname">School Name</label><br>
                    <label id="schoolNameLabel" class="form-label" for="basic-default-fullname"
                        style="font-weight:bold;font-size:16px">Government Boys School, Erode</label>
                </div>
                <div id="studentDetails" class="mt-4 mb-3" style="display:none;">
                    <div class=" card bg-lighter">
                        <div class="card-body">
                            <span class="fw-medium">School Details</span>
                            <hr>
                            <div class="row">
                                <div class="col-3 school-det">
                                    <p class="mb-2 small">School Name</p>
                                </div>
                                <div class="col-3 school-det">
                                    <p id="name" class="text-heading fw-medium">Govt Boys Higher Secondary School
                                    </p>
                                </div>
                                <div class="col-3 school-det">
                                    <p class="mb-2 small">School Address</p>
                                </div>
                                <div class="col-3 school-det">
                                    <p id="address" class="text-heading fw-medium">7/8,Govt Boys
                                        school,Karungalpalayam,Erode</p>
                                </div>

                                <div class="col-3 school-det">
                                    <p class="mb-2 small">Taluk</p>
                                </div>

                                <div class="col-3 school-det">
                                    <p id="taluk" class="text-heading fw-medium">Karungalpalayam</p>
                                </div>
                                <div class="col-3 school-det">
                                    <p class="mb-2 small">District </p>
                                </div>
                                <div class="col-3 school-det">
                                    <p id="district" class="text-heading fw-medium">Erode</p>
                                </div>

                                <div class="col-3 school-det">
                                    <p class="mb-2 small">Zone</p>
                                </div>
                                <div class="col-3 school-det">
                                    <p id="zones" class="text-heading fw-medium">North Zone</p>
                                </div>
                                <!-- <div class="col-3 school-det">
                                    <p class="mb-2 small">Group Name</p>
                                </div> -->
                                <!-- <div class="col-3 school-det">
                                    <p id="groupName" class="text-heading fw-medium">N/A</p>
                                </div>
                                <div class="col-3 school-det">
                                    <p class="mb-2 small">Mother Name</p>
                                </div>
                                <div class="col-3 school-det">
                                    <p id="motherName" class="text-heading fw-medium">GOMATHI</p>
                                </div>
                                <div class="col-3 school-det">
                                    <p class="mb-2 small">Class Section</p>
                                    <p id="classSection" class="text-heading fw-medium">B</p>
                                </div>
                                <div class="col-3 school-det">
                                    <p class="mb-2 small">Medium</p>
                                    <p id="medium" class="text-heading fw-medium">Tamil</p>
                                </div>
                                <div class="col-3 school-det">
                                    <p class="mb-2 small">School Name</p>
                                    <p id="schoolName1" class="text-heading fw-medium">GGHSS, VALAPPADI</p>
                                </div>
                                <div class="col-3 school-det">
                                    <p class="mb-2 small">School Block</p>
                                    <p id="schoolBlock1" class="text-heading fw-medium">Valappadi</p>
                                </div>
                                <div class="col-3 school-det">
                                    <p class="mb-2 small">School District</p>
                                    <p id="schoolDistrict1" class="text-heading fw-medium">SALEM</p>
                                </div> -->


                                <input type="hidden" class="form-control" id="udise_code" name="udise_code" required=""
                                    value="33081901402">
                                <input type="hidden" class="form-control" id="student_name" name="student_name"
                                    required="" value="BIRUNTHA R">
                                <input type="hidden" class="form-control" id="stu_dob" name="stu_dob" required=""
                                    value="2010-05-02">
                                <input type="hidden" class="form-control" id="current_std" name="current_std"
                                    required="" value="10">
                                <input type="hidden" class="form-control" id="group_name" name="group_name" value="">
                                <input type="hidden" class="form-control" id="class_sec" name="class_sec" value="B">
                                <input type="hidden" class="form-control" id="school_taluk" name="school_taluk"
                                    required="" value="Karungalapalayam">
                                <input type="hidden" class="form-control" id="school_name" name="school_name"
                                    required="" value="Govt Boys Higher Secondary School">
                                <input type="hidden" class="form-control" id="school_address" name="school_address"
                                    required="" value="7/8,Govt Boys school,Karungalpalayam,Erode">

                                <input type="hidden" class="form-control" id="school_zones" name="school_zones"
                                    value="North">
                                <input type="hidden" class="form-control" id="school_district" name="school_district"
                                    required="" value="Erode">
                                <input type="hidden" class="form-control" id="father_name" name="father_name"
                                    required="" value="RAMACHANDRAN">
                                <input type="hidden" class="form-control" id="mother_name" name="mother_name"
                                    required="" value="GOMATHI">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label" for="basic-default-fullname">Medium</label>
                    <select class="form-select" id="medium" name="medium">
                        <option value="">Select Medium type</option>
                        <option value="Tamil">Tamil</option>
                        <option value="English">English</option>
                    </select>
                </div>


                <div class="col-md-3 mb-3">
                    <label class="form-label" for="basic-default-fullname">Enter Standard</label>
                    <input type="text" class="form-control" id="standard" name="standard" />
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label" for="basic-default-message">Remarks</label>
                    <textarea id="remarks" class="form-control" name="remarks"></textarea>
                </div>
            </div>

            <div class="row">
                <div class="btn-float">
                    <button type="button" class="btn btn-danger"
                        onclick="window.location.href='{{ url('/standard_list') }}'">Cancel</button>
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
document.getElementById('goButton').addEventListener('click', function() {
    var school_id = document.getElementById('school_id').value;

    if (school_id.trim() === '') {
        alert('Please enter the School ID');
    } else {
        document.getElementById('studentDetails').style.display = 'block';
    }
});
$(document).ready(function() {



    $('#standardform').on('submit', function(e) {
        e.preventDefault();

        var formdata = {
            school_id: $('#school_id').val(),
            // school_: $('#schoolNameLabel').text(),
            school_name: $('#school_name').val(),
            school_address: $('#school_address').val(),
            school_taluk: $('#school_taluk').val(),
            school_district: $('#school_district').val(),
            school_zone: $('#school_zones').val(),
            medium: $('#medium').val(),
            standard: $('#standard').val(),
            remarks: $('#remarks').val(),
            created_at: new Date().toISOString(), // Example: current date-time
            updated_at: new Date().toISOString(),
            _token: '{{ csrf_token() }}'

        }
        // var requiredFields = ['school_id', 'school_name', 'school_address', 'school_taluk',
        //     'school_district', 'school_zone', 'medium', 'standard'
        // ];

        // var requiredFields = [];
        // $.each(formdata, function(key, value) {
        //     if (value === '') {
        //         missingFields.push(key.replace('_', ' '));
        //     }
        // });

        // if (missingFields.length > 0) {
        //     Swal.fire({
        //         title: 'Validation Error',
        //         text: 'Fill the fields: ' + missingFields.join(', '),
        //         icon: 'warning',
        //         showConfirmButton: true
        //     });
        //     return; // Exit the function if there are missing fields
        // }

        console.log('Form Data:', formdata);
        if ((formdata.school_id != '') && (formdata.school_name != '') && (formdata.school_address !=
                '') && (formdata.school_district != '') && (formdata.medium != '') && (formdata
                .standard != '')) {
            $.ajax({
                url: "{{route('standard_creation.store')}}",
                method: "POST",
                data: formdata,
                success: function(response) {
                    const redirectUrl = "{{route('standard_list')}}";
                    sweetalert('create', redirectUrl);
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        Swal.fire({
                            title: 'Already exist',
                            icon: 'info',
                            showConfirmButton: true,
                            timer: 1500,
                            timerProgressBar: true,
                        });
                    }
                }

            });

        } else {
            sweetalert('form_alert');
        }

    });

});
</script>
@endsection