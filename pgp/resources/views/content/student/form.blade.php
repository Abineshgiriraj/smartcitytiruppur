@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Entry Form /</span> Student Creation</h4>
    </div>
</div>
<style>
/* btn style */
.btn-right {
    float: right;
}
</style>
<!-- Basic Layout -->

<div class="card mb-4">
    <div class="card-body">
        <div id="loadingMessage"
            style="display: none; color:red; top: 10px; right: 10px; background-color: #f1f1f1; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
            Loading...
        </div>

        <form id="student_creation" class="was-validated" method="post" action="{{ route('student.store') }}"
            autocomplete="off">
            <!-- method="post" action="{{ route('student.store') }}"
            autocomplete="off" -->
            @csrf
            <div class="row mb-3">
                <div class="col-md-3 mb-2">
                    <label class="form-label" for="emis_no">EMIS No.</label>
                    <input type="text" class="form-control" id="emis_no" name="emis_no" required />
                </div>
                <div class="col-md-2 mt-4">
                    <button type="button" id="goButton" class="btn btn-primary">Go</button>
                </div>
            </div>

            <div id="studentDetails" class="mt-4" style="display:none;">
                <div class="card bg-lighter">
                    <div class="card-body">
                        <span class="fw-medium">Student Details</span>
                        <hr>
                        <div class="row">
                            <div class="col-3 school-det">
                                <p class="mb-2 small">Student Name</p>
                            </div>
                            <div class="col-3 school-det">
                                <p id="studentName" class="text-heading fw-medium"></p>
                            </div>
                            <div class="col-3 school-det">
                                <p class="mb-2 small">UDISE Code</p>
                            </div>
                            <div class="col-3 school-det">
                                <p id="udiseCode" class="text-heading fw-medium"></p>
                            </div>

                            <div class="col-3 school-det">
                                <p class="mb-2 small">Date of Birth</p>
                            </div>

                            <div class="col-3 school-det">
                                <p id="dob" class="text-heading fw-medium"></p>
                            </div>
                            <div class="col-3 school-det">
                                <p class="mb-2 small">Current Standard </p>
                            </div>
                            <div class="col-3 school-det">
                                <p id="currentStandard" class="text-heading fw-medium"></p>
                            </div>

                            <div class="col-3 school-det">
                                <p class="mb-2 small">Father Name</p>
                            </div>
                            <div class="col-3 school-det">
                                <p id="fatherName" class="text-heading fw-medium"></p>
                            </div>
                            <div class="col-3 school-det">
                                <p class="mb-2 small">Group Name</p>
                            </div>
                            <div class="col-3 school-det">
                                <p id="groupName" class="text-heading fw-medium"></p>
                            </div>
                            <div class="col-3 school-det">
                                <p class="mb-2 small">Mother Name</p>
                            </div>
                            <div class="col-3 school-det">
                                <p id="motherName" class="text-heading fw-medium"></p>
                            </div>
                            <div class="col-3 school-det">
                                <p class="mb-2 small">Class Section</p>
                                <p id="classSection" class="text-heading fw-medium"></p>
                            </div>
                            <div class="col-3 school-det">
                                <p class="mb-2 small">Medium</p>
                                <p id="medium" class="text-heading fw-medium"></p>
                            </div>
                            <div class="col-3 school-det">
                                <p class="mb-2 small">School Name</p>
                                <p id="schoolName1" class="text-heading fw-medium"></p>
                            </div>
                            <div class="col-3 school-det">
                                <p class="mb-2 small">School Block</p>
                                <p id="schoolBlock1" class="text-heading fw-medium"></p>
                            </div>
                            <div class="col-3 school-det">
                                <p class="mb-2 small">School District</p>
                                <p id="schoolDistrict1" class="text-heading fw-medium"></p>
                            </div>


                            <input type="hidden" class="form-control" id="udise_code" name="udise_code" required />
                            <input type="hidden" class="form-control" id="student_name" name="student_name" required />
                            <input type="hidden" class="form-control" id="stu_dob" name="stu_dob" required />
                            <input type="hidden" class="form-control" id="current_std" name="current_std" required />
                            <input type="hidden" class="form-control" id="group_name" name="group_name" />
                            <input type="hidden" class="form-control" id="class_sec" name="class_sec" />
                            <input type="hidden" class="form-control" id="schl_medium" name="schl_medium" required />
                            <input type="hidden" class="form-control" id="school_name" name="school_name" required />
                            <input type="hidden" class="form-control" id="school_block" name="school_block" required />
                            <input type="hidden" class="form-control" id="zone" name="zone" value="North" />
                            <input type="hidden" class="form-control" id="school_district" name="school_district"
                                required />
                            <input type="hidden" class="form-control" id="father_name" name="father_name" required />
                            <input type="hidden" class="form-control" id="mother_name" name="mother_name" required />
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3 mb-3">
                    <label class="form-label" for="basic-default-fullname">Biometric ID</label>
                    <select class="form-select" id="biometric_id" name="biometric_id">
                        <option value="">Select Biometric Id</option>
                        <option value="Chennai">B1123</option>
                        <option value="Salem">B234</option>

                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label" for="basic-default-fullname">Device ID</label>
                    <select class="form-select" id="district_id" name="district_id">
                        <option value="">Select Device Id</option>
                        <option value="Chennai">D1123</option>
                        <option value="Salem">D674</option>

                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label" for="school_id">School ID</label>
                    <input type="text" class="form-control" id="school_id" name="school_id" required />
                </div>
            </div>
            <div class="btn-float" id="btn_div" style="display:none;">
                <button class="btn btn-danger" onclick="window.location.href='{{ url('/student') }}'"
                    type="button">Cancel
                </button>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
            <!-- onclick="create()"  -->


        </form>
    </div>

    <script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>

    <script>
    $(document).ready(function() {
        $('#goButton').click(function() {
            let emis_val = $('#emis_no').val();

            // Build the URL with the emis_val
            let url = "{{ url('/student') }}/" + emis_val;
            $('#loadingMessage').show();

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.dataStatus && response.result.length > 0) {
                        let student = response.result[
                            0]; // Get the first student data object

                        // Update the HTML elements with the response data
                        $('#studentName').text(student.name || 'N/A');
                        $('#dob').text(student.dob || 'N/A');
                        $('#currentStandard').text(student.class_studying_id || 'N/A');
                        $('#groupName').text(student.group_name || 'N/A');
                        $('#medium').text(student.MEDINSTR_DESC || 'N/A');
                        // $('#schoolName').text(student.school_name || 'N/A');
                        // $('#schoolBlock').text(student.block_name || 'N/A');
                        // $('#schoolDistrict').text(student.district_name || 'N/A');

                        $('#schoolName1').text(student.school_name || 'N/A');
                        $('#schoolBlock1').text(student.block_name || 'N/A');
                        $('#schoolDistrict1').text(student.district_name || 'N/A');


                        $('#udiseCode').text(student.udise_code || 'N/A');
                        $('#classSection').text(student.class_section || 'N/A');
                        $('#fatherName').text(student.father_name || 'N/A');
                        $('#motherName').text(student.mother_name || 'N/A');


                        $('#student_name').val(student.name);
                        $('#stu_dob').val(student
                            .dob || '');
                        $('#current_std').val(student
                            .class_studying_id || '');
                        $('#group_name').val(student
                            .group_name || '');
                        $('#schl_medium').val(student
                            .MEDINSTR_DESC || '');
                        $('#school_name').val(student
                            .school_name || '');
                        $('#school_block').val(student
                            .block_name || '');
                        $('#school_district').val(student
                            .district_name || '');

                        $('#udise_code').val(student.udise_code || '');
                        $(
                            '#class_sec').val(student.class_section || '');
                        $(
                            '#father_name').val(student.father_name || '');
                        $(
                            '#mother_name').val(student.mother_name || '');

                        $('#studentDetails')
                            .show();
                        $('#btn_div')
                            .show(); // Ensure the details container is visible
                    } else {
                        $('#response').html('<p>No data found</p>');
                    }
                },
                error: function(xhr, status, error) {
                    $('#response').html('<p>Error: ' + error + '</p>');
                },
                complete: function() {
                    // Hide the loading message
                    $('#loadingMessage').hide();
                }
            });
        });
    });


    function create() {
        $(this).html('Sending..');
        var uniqueId = uuid.v4();
        // var formData = $('#student_creation').serializeArray();
        // alert(formData);
        // var formData = {
        //     emis_no:
        // }
        alert('hi');

        $.ajax({
            data: $.param(formData),
            url: "{{ route('student.store') }}",
            type: "POST",
            dataType: 'json',
            success: function(data) {
                console.log(data);
                $('#student_creation').trigger("reset");
                window.location.href = "{{ route('student.index') }}";
            },
            error: function(data) {
                console.log('Error:', data);
                $('#zone_create').html('Save Changes');
            }
        });
    }
    // document.getElementById('goButton').addEventListener('click', function() {
    //     var emis_no = document.getElementById('emis_no').value;

    //     if (emis_no.trim() === '') {
    //         alert('Please enter the EMIS number');
    //     } else {
    //         document.getElementById('studentDetails').style.display = 'block';
    //     }

    // });
    // document.getElementById('school_go').addEventListener('click', function() {
    //     var school_id = document.getElementById('school_id').value;

    //     if (school_id.trim() === '') {
    //         alert('Please enter the School ID');
    //     } else {
    //         document.getElementById('school_details').style.display = 'block';
    //     }
    // });
    </script>
    @endsection