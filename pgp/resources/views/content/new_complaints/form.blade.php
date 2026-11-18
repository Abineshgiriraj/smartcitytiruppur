@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Grievance Module / </span>New Grievance</h4>
    </div>
</div>

<style>
/* Section title style */
.section-title {
    font-size: 1.1rem;
    font-weight: bold;
    color: #495057;
    border-left: 4px solid #28a745;
    padding-left: 10px;
    margin-bottom: 15px;
}

/* Attachment grid */
.attachment-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 15px;
}

/* Card style */
.card {
    border-radius: 8px;
    border: 1px solid #dee2e6;
    box-shadow: 0 4px 8px rgba(0,0,0,0.03);
    padding: 20px;
    margin-bottom: 20px;
}

/* Form label styling */
.form-label-static {
    font-weight: 600;
    white-space: nowrap;
}

/* Fixed buttons */
.btn-float {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
}

textarea {
    min-height: 80px;
}
.section-title {
    color: #356106;
}
.form-label-static {
    color: black;
    font-weight: normal;
}

</style>
<?php
$get_source_name = get_source_name();
$get_priority = get_priority();
$get_dept_name = get_dept_name();
$get_zone_name = get_zone();
$get_ward_name = get_ward();
$location_name =get_location();
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="d-flex justify-content-end">
                        <span class="text-danger fw-bold">* Mandatory Fields</span>
                    </div>
            <div class="card-body">
                 
                <form id="academicYearForm">
                    @csrf
                    @if(isset($acc_year))
                    @method('PUT')
                    @endif

                    <!-- Personal Details -->
                    <div class="card">
                        
                        <div style="color: #027AFF; font-weight: bold;">Personal Details</div>
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-2"><label class="form-label-static">Name<span
                                        class="text-danger fw-bold">*</span> </label></div>
                            <div class="col-md-4"><input type="text" class="form-control" id ="name" name="name"></div>
                            <div class="col-md-2"><label class="form-label-static">Grievance ID </label></div>
                            <div class="col-md-4 form-static-value" style="font-weight: bold;" id="grievance_id"></div>
                               <input type="hidden" name="grievance_id" id="grievance_id_input">
                        </div>
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-2"><label class="form-label-static">Mobile No<span
                                        class="text-danger fw-bold">*</span></label></div>
                            <div class="col-md-4"><input type="text" class="form-control" id="mobile_no" name="mobile_no" maxlength="12"></div>
                            <div class="col-md-2"><label class="form-label-static">Email ID </label></div>
                            <div class="col-md-4"><input type="email" class="form-control" id="email_id" name="email"></div>
                        </div>
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-2"><label class="form-label-static">Address<span
                                        class="text-danger fw-bold">*</span></label></div>
                            <div class="col-md-10"><textarea class="form-control" name="address1" id="address1" style="width : 38%"></textarea></div>
                        </div>
                    </div>

                    <!-- Location Details -->
                <div class="card">
    <div style="color: #027AFF; font-weight: bold;">Location Details</div>

    <!-- Zone and Ward -->
    <div class="row mb-3 align-items-center">
        <div class="col-md-2">
            <label class="form-label-static">Zone<span class="text-danger fw-bold">*</span></label>
        </div>
        <div class="col-md-4">
            <select class="form-select" id="zone" name="zone">
                                    <option value="">Select Zone</option>
                                    @foreach ($get_zone_name as $get_zone)
                                    <option value="{{ $get_zone->unique_id }}"
                                        {{ isset($ward) && $ward->zone == $get_zone->unique_id ? 'selected' : '' }}>
                                        {{ $get_zone->zone }}
                                    </option>
                                    @endforeach
                                </select>
        </div>

        <div class="col-md-2">
            <label class="form-label-static">Ward<span class="text-danger fw-bold">*</span></label>
        </div>
        <div class="col-md-4">
            <div class="input-group">
            <select class="form-select" id="ward" name="ward[]" multiple>
                <option value="">Select Ward</option>
            </select>

            <div class="input-group-text">
                <input type="checkbox" class="form-check-input mt-0" id="selectAllWard">
                <label for="selectAllWard" class="mb-0 ms-1">All</label>
            </div>
        </div>
        </div>
    </div>

    <!-- Location and Door No -->
    <div class="row mb-3 align-items-center">
       <div class="col-md-2">
    <label class="form-label-static">Location <span class="text-danger fw-bold">*</span></label>
</div>
<div class="col-md-4">
    <div class="input-group">
        <select class="form-select select2" id="location" name="location[]" multiple>
            @foreach ($location_name as $get_location)
                <option value="{{ $get_location->unique_id }}"
                    {{ isset($location) && in_array($get_location->unique_id, (array) ($location->location ?? [])) ? 'selected' : '' }}>
                    {{ $get_location->location }}
                </option>
            @endforeach
        </select>
        <div class="input-group-text">
            <input type="checkbox" class="form-check-input mt-0" id="selectAllLocation">
            <label for="selectAllLocation" class="mb-0 ms-1">All</label>
        </div>
    </div>
</div>



        <div class="col-md-2">
            <label class="form-label-static">Door No</label>
        </div>
        <div class="col-md-4">
            <input type="text" class="form-control" id="door_no" name="door_no">
        </div>
    </div>

    <!-- Address and Same as Above -->
    <div class="row mb-3 align-items-center">
        <div class="col-md-2">
            <label class="form-label-static">Address<span class="text-danger fw-bold">*</span></label>
        </div>
        <div class="col-md-6 d-flex align-items-center gap-2">
    <textarea class="form-control" id="address2" name="address2" style="width: 65%; font-size: 14px;"></textarea>
    
    <input type="checkbox" id="sameAddress" class="form-check-input" style="transform: scale(1.3); cursor: pointer;">
    
    <label for="sameAddress" class="form-check-label" style="font-size: 12px; font-weight: 500;">
        Same as Above Address
    </label>
</div>

       
    </div>
</div>


                    <!-- Complaint Details -->
                    <div class="card">
<div style="color: #027AFF; font-weight: bold;">Grievance Details</div>

    <!-- Department & Complaint Category -->
    <div class="row mb-3 align-items-center">
        <div class="col-md-2">
            <label class="form-label-static">Source Name<span class="text-danger fw-bold">*</span></label>
        </div>
        <div class="col-md-4">
            <select class="form-select" id="source_name" name="source_name">
                  <option value="">Select Source</option>
                                    @foreach ($get_source_name as $get_source)
                                    <option value="{{ $get_source->unique_id }}"
                                        {{ isset($source_name) && $source_name->source == $get_source->unique_id ? 'selected' : '' }}>
                                        {{ $get_source->source_name}}
                                    </option>
                                    @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label-static">Department<span class="text-danger fw-bold">*</span></label>
        </div>
        <div class="col-md-4">
            <select class="form-select" id="department" name="department">
                <option value="">Select Department</option>
                                    @foreach ($get_dept_name as $get_department)
                                    <option value="{{ $get_department->unique_id }}"
                                        {{ isset($department) && $department->department == $get_department->unique_id ? 'selected' : '' }}>
                                        {{ $get_department->department}}
                                    </option>
                                    @endforeach
            </select>
        </div>

        
    </div>

    <!-- Source Name & Priority -->
    <div class="row mb-3 align-items-center">
        
<div class="col-md-2">
            <label class="form-label-static">Department Category<span class="text-danger fw-bold">*</span></label>
        </div>
        <div class="col-md-4">
            <select class="form-select" id="department_category" name="department_category">
                                <option value="">Department Category</option>
                            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label-static">Priority<span class="text-danger fw-bold">*</span></label>
        </div>
        <div class="col-md-4">
            <select class="form-select" id="priority" name="priority">
                <option value="">Select Priority</option>
                                    @foreach ($get_priority as $get_priority_name)
                                    <option value="{{ $get_priority_name->unique_id }}"
                                        {{ isset($priority) && $priority->source == $get_priority_name->unique_id ? 'selected' : '' }}>
                                        {{ $get_priority_name->priority}}
                                    </option>
                                    @endforeach
            </select>
        </div>
    </div>

    <!-- Attachments -->
    <div class="row mb-3 align-items-center">
        <div class="col-md-2">
            <label class="form-label-static">Attachment</label>
        </div>
        <div class="col-md-10">
<input type="file" class="form-control" name="attachments[]" id="attachments" multiple style="width:38%">
    </div>
    </div>

    <!-- Complaint Description (English) -->
    <div class="row mb-3">
        <div class="col-md-2">
            <label class="form-label-static">
                Grievance Description <br>(English)<span class="text-danger fw-bold">*</span>
            </label>
        </div>
        <div class="col-md-10">
            <textarea class="form-control" id="complaint_eng" name="complaint_en" rows="8"></textarea>
        </div>
    </div>

    <!-- Complaint Description (Tamil) -->
    <div class="row mb-3">
        <div class="col-md-2">
            <label class="form-label-static">
                Grievance Description <br>(Tamil)
            </label>
        </div>
        <div class="col-md-10">
            <textarea class="form-control" id="complaint_tam" name="complaint_ta" rows="8"></textarea>
        </div>
    </div>
</div>

</div>
                    <!-- Buttons -->
                    <div class="btn-float d-flex gap-2">
                            <a href="new_complaints" class="btn btn-secondary">Cancel</a>
  <button type="button" class="btn btn-primary" onclick="finalSave()">
                                    {{ isset($new_complaints) ? 'Update' : 'Save' }}
                                </button>
</div>

                </form>
            </div>
        </div>
    </div>
</div>


    <script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
    <script src="{{ asset('public/assets/js/common.js') }}"></script>
    <script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>



    <script>

        document.addEventListener('DOMContentLoaded', function () {
    fetch("{{ route('grievance.next') }}")
        .then(response => response.json())
        .then(data => {
            document.getElementById('grievance_id').textContent = data.grievance_id;
            document.getElementById('grievance_id_input').value = data.grievance_id;
        })
        .catch(err => console.error('Error fetching Grievance ID:', err));
});
        document.getElementById('sameAddress').addEventListener('change', function() {
    const address1 = document.getElementById('address1');
    const address2 = document.getElementById('address2');

    if (this.checked) {
        address2.value = address1.value;
        address2.readOnly = true; // Prevent manual editing when checked
    } else {
        address2.value = '';
        address2.readOnly = false;
    }
});
   
   $(document).ready(function() {
    $('#location').select2({
        placeholder: "Select Location"
    });

    // Handle select all
    $('#selectAllLocation').on('change', function() {
        if ($(this).is(':checked')) {
            // Select all options
            $('#location > option').prop("selected", true);
        } else {
            // Deselect all options
            $('#location > option').prop("selected", false);
        }
        $('#location').trigger('change'); // refresh select2
    });
});


    document.getElementById("department").addEventListener("change", function() {
        // alert("hiii");

        let deptId = this.value;
        // alert(deptId);
        let categorySelect = document.getElementById("department_category");
        categorySelect.innerHTML = '<option value="">Department Category</option>';

        let url = `{{ route('getDepartmentCategories', ':deptId') }}`;
        url = url.replace(':deptId', deptId);

        if (deptId) {
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    data.forEach(category => {
                        let option = document.createElement("option");
                        option.value = category.unique_id;
                        option.text = category.department_category;
                        categorySelect.appendChild(option);
                    });
                })
                .catch(error => console.error("Error fetching categories:", error));
        }
    });

$(document).ready(function() {
        $('#ward').select2({
            placeholder: "Select Ward",
            width: '80%'
        });

        $('#zone').on('change', function() {
            let zoneId = $(this).val();
            let wardSelect = $('#ward');
            let selectAllCheckbox = $('#selectAllWard');

            wardSelect.empty();
            selectAllCheckbox.prop('checked', false);

            if (zoneId) {
                let url = `{{ route('getZoneWardData', ':zoneId') }}`;
                url = url.replace(':zoneId', zoneId);

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            data.forEach(ward => {
                                wardSelect.append(new Option(ward.ward, ward.unique_id));
                            });
                        }
                        wardSelect.trigger('change');
                    })
                    .catch(error => console.error('Error:', error));
            }
        });

        $('#selectAllWard').on('change', function() {
            if (this.checked) {
                let allValues = [];
                $('#ward option').each(function() {
                    allValues.push($(this).val());
                });
                $('#ward').val(allValues).trigger('change'); // select all
            } else {
                $('#ward').val(null).trigger('change'); // deselect all
            }
        });
        $('#ward').on('change', function() {
            let total = $('#ward option').length;
            let selected = $(this).val() ? $(this).val().length : 0;
            $('#selectAllWard').prop('checked', total > 0 && selected === total);
        });
    });


    function finalSave() {
    var formData = new FormData();

    // Normal fields
    formData.append('name', $('#name').val());
    formData.append('mobile_no', $('#mobile_no').val());
    formData.append('email_id', $('#email_id').val());
    formData.append('address1', $('#address1').val());
    formData.append('zone', $('#zone').val());
    formData.append('ward', $('#ward').val());
    formData.append('location', $('#location').val());
    formData.append('door_no', $('#door_no').val());
    formData.append('address2', $('#address2').val());
    formData.append('source_name', $('#source_name').val());
    formData.append('department', $('#department').val());
    formData.append('department_category', $('#department_category').val());
    formData.append('priority', $('#priority').val());
    formData.append('complaint_eng', $('#complaint_eng').val());
    formData.append('complaint_tam', $('#complaint_tam').val());
    formData.append('status', $('#status').val() ? 1 : 0); // ✅ send boolean as 1/0
    formData.append('is_active', $('#is_active').val() ? 1 : 0);

    // Files
    var files = $('#attachments')[0].files;
    for (var i = 0; i < files.length; i++) {
        formData.append('attachments[]', files[i]);
    }

    var method = "{{ isset($new_complaints) ? 'PUT' : 'POST' }}";
    var url = "{{ isset($new_complaints) 
        ? route('new_complaints.update', $new_complaints->id ?? 0) 
        : route('new_complaints.store') }}";

    if (method === 'PUT') {
        formData.append('_method', 'PUT');
    }

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    });

    $.ajax({
        url: url,
        method: 'POST',
        data: formData,
        processData: false,   // ✅ important
        contentType: false,   // ✅ important
        success: function(response) {
            Swal.fire({
                title: 'Success!',
                text: 'New Grievance saved successfully!',
                icon: 'success',
                timer: 1500,
                showConfirmButton: true
            }).then(() => {
                window.location.href = "{{ route('new_complaints') }}";
            });
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                let errorMessage = errors ? Object.values(errors).join('<br>') : xhr.responseJSON.message;
                Swal.fire('Validation Error', errorMessage, 'warning');
            } else {
                Swal.fire('Error!', 'An error occurred. Please try again.', 'error');
                console.error("AJAX Error:", xhr.responseText);
            }
        }
    });
}

    </script>


    @endsection