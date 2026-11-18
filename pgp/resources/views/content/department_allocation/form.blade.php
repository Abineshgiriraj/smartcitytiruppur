@extends('layouts/contentNavbarLayout')

@section('title', 'Department Allocation - Grievance Module')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4><span class="text-muted fw-light">Setting /</span> Department Allocation</h4>
    </div>
</div>

<style>
    /* Stage Flow */
    .stage-flow {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 30px;
        gap: 20px;
        flex-wrap: wrap;
    }

    .stage-box {
        padding: 12px 25px;
        background: #f8fafc;
        border: 2px solid #0d6efd;
        border-radius: 10px;
        cursor: pointer;
        font-weight: bold;
        transition: 0.3s;
    }

    .stage-box:hover,
    .stage-box.active {
        background: #0d6efd;
        color: #fff;
    }

    .stage-arrow {
        font-size: 22px;
        color: #0d6efd;
        font-weight: bold;
    }

    /* Stage Details Section */
    .stage-section {
        display: none;
        margin-top: 30px;
        padding: 20px;
        border: 1px solid #dce3f0;
        border-radius: 10px;
        background: #f9fbff;
    }

    /* Right Side Person Info */
    .stage-right {
        text-align: right;
        background: #eef5ff;
        border: 2px solid #0d6efd;
        border-radius: 12px;
        padding: 15px 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .stage-right strong {
        font-size: 18px;
        display: block;
        color: #0d6efd;
    }

    .stage-right small {
        color: #333;
        font-weight: 500;
    }

    /* Filters Row */
    .stage-filters {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 20px 0;
        gap: 20px;
        flex-wrap: nowrap;
    }

    .stage-filters label {
        font-weight: 500;
        margin-right: 8px;
    }

    .stage-filters select {
        min-width: 150px;
        display: inline-block;
    }

    /* Chart container */
    .chart-container {
        display: none;
        margin-top: 15px;
        padding: 20px;
        background: #fff;
        border-radius: 10px;
        border: 1px solid #dce3f0;
    }

    #zone.form-select,
    select[multiple] {
        height: auto;
        /* reset */
        min-height: calc(2.25rem + 2px);
        /* match single select height */
    }
</style>
<?php
$get_staff_name = get_staff_name();
$get_dept_name = get_dept_name();
$get_stage_name = get_stage();
$get_dept_category = get_dept_category();
$get_zone_name = get_zone();
$get_ward_name = get_ward();
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Main Layout -->
<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-body">
                <input type="hidden" id="screen_unique_id" name="screen_unique_id" value="{{ $screen_unique_id }}">

                <!-- Filters Section -->
                <form class="row g-3 align-items-center mb-3 d-flex justify-content-between">
                    <!-- Staff Name -->
                    <div class="col-md-4">
                        <label class="form-label">Staff Name</label>
                        <div class="d-flex">
                            <select class="form-select" id="staff_name" name="staff_name">
                                <option value="">Staff</option>
                                @foreach ($get_staff_name as $get_staff)
                                <option value="{{ $get_staff->unique_id }}"
                                    {{ (isset($department_allocation) && $department_allocation->name == $get_staff->unique_id) ? 'selected' : '' }}>
                                    {{ formatToCapitalizedWords($get_staff->name) }}
                                </option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-primary" id="goBtn">Go</button>
                        </div>
                    </div>

                    <div class="col-md-auto ms-auto text-end profile-left">

                    </div>
                    <input type="hidden" id="hidden_staff_id" name="staff_id">
    <input type="hidden" id="hidden_staff_name" name="staff_name_hidden">
    <input type="hidden" id="hidden_mobile_no" name="mobile_no">
    <input type="hidden" id="hidden_email_id" name="email_id">
   <input type="hidden" id="hidden_designation" name="designation">
<input type="hidden" id="hidden_department" name="department">
<input type="hidden" id="hidden_designation_id" name="designation_ids">
<input type="hidden" id="hidden_department_id" name="department_ids">

                </form>

                <!-- Hidden Section (Filters + Table) -->
                <div id="filtersAndTable" style="display:none;">
                    <form class="row g-3 align-items-center mb-3">
                         <!-- <div class="col-md-4">
                            <label class="form-label">Designation</label>
                            <select class="form-select" id="designation" name="designation">
                                <option value="">Designation name</option>
                            </select>
                        </div> -->
                         <!-- Stage -->
                          <div class="col-md-4">
                            <label class="form-label">Department</label>
                            <select class="form-select" id="department" name="department">
                                <option value="">Department name</option>
                            </select>
                        </div>
                        <!-- Category -->
                         <div class="col-md-4">
                            <label class="form-label">Department Category</label>
                            <select class="form-select" id="department_category" name="department_category">
                                <option value="">Department Category</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Stage</label>
                            <select class="form-select" id="stage" name="stage">
                                    <option value="">Select Stage</option>
                                    @foreach ($get_stage_name as $get_stage)
                                    <option value="{{ $get_stage->unique_id }}"
                                        {{ isset($stage) && $stage->stage == $get_stage->unique_id ? 'selected' : '' }}>
                                        {{ $get_stage->stage }}
                                    </option>
                                    @endforeach
                                </select>
                        </div>
                       
                        <div class="col-md-4">
                            <label class="form-label">Zone</label>
                            <div class="input-group">
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
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Ward</label>
                            <div class="input-group">
                                <select class="form-select" id="ward" name="ward[]" multiple>
                                    <option value="">Select Ward</option>
                                </select>


                                <div class="input-group-text">
                                    <input type="checkbox" class="form-check-input mt-0" id="selectAllWard"> All
                                </div>
                            </div>
                        </div>
                       <div class="col-md-4">
    <label for="days" class="form-label">Days</label>
    <input type="number" class="form-control" id="days" name="days">
</div>


                        <!-- Add Button -->
                        <div class="col-12 text-end mt-3">
                            <button type="button" id="addBtn" class="btn btn-success" style="background-color:#027AFF; border-color:#027AFF;" onclick="saveUser()">
                                Add
                            </button>
                        </div>
                    </form>

                    <!-- Table Section -->
                    <div class="table-responsive mt-4" id="dataTable" style="display: none;">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>S.No</th>
                                    <th>Staff</th>
                                    <th>Department</th>
                                    <th>Stage</th>
                                    <th>Category</th>
                                    <th>Zone</th>
                                    <th>Ward</th>
                                    <th>No.of Days</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <div class="btn-float">
                        <button type="button" class="btn btn-danger"
                            onclick="window.location.href='{{ route('department_allocation') }}'">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="finalSave()">
                                    {{ isset($department_allocation) ? 'Update' : 'Save' }}
                                </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    //go button
  document.getElementById("goBtn").addEventListener("click", function() {
    let staffId = document.getElementById("staff_name").value;

    if (!staffId) {
        alert("Please select a staff name first!");
        return;
    }

    // Show filters section
    document.getElementById("filtersAndTable").style.display = "block";

    // Trigger the staff selection change to populate department, stage, and designation
    let event = new Event('change');
    document.getElementById("staff_name").dispatchEvent(event);
});

    //add button
    document.getElementById("addBtn").addEventListener("click", function() {
        document.getElementById("dataTable").style.display = "block";
    });

// staff data get
document.getElementById("staff_name").addEventListener("change", function () {
    let staffId = this.value;

    if (staffId) {
        let url = `{{ route('getUserProfile', ['staffId' => '__staffId__']) }}`;
        url = url.replace('__staffId__', staffId);

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let user = data.data;

                    // ✅ Arrays are already provided by backend
                    let designations = Array.isArray(user.designations) ? user.designations : [];
                    let departments = Array.isArray(user.departments) ? user.departments : [];
                    let designationIds = Array.isArray(user.designation_ids) ? user.designation_ids : [];
                    let departmentIds = Array.isArray(user.department_ids) ? user.department_ids : [];

                    // Profile preview
                    let profileHtml = `
                        <h5>${user.name}</h5>
                        <p class="mb-0"><i class="bi bi-telephone"></i> ${user.mobile_no}</p>
                        <p class="mb-0"><i class="bi bi-envelope"></i> ${user.email_id ?? '-'}</p>
                        <p class="mb-0"><i class="bi bi-card-list"></i> 
                            Designation : <strong>${designations.join(", ")}</strong>
                        </p>
                        <p class="mb-0"><i class="bi bi-people-fill"></i> 
                            Department : <strong>${departments.join(", ")}</strong>
                        </p>
                    `;
                    document.querySelector(".profile-left").innerHTML = profileHtml;

                    // ✅ Store values in hidden inputs
                    document.getElementById("hidden_staff_id").value = staffId;
                    document.getElementById("hidden_staff_name").value = user.name;
                    document.getElementById("hidden_mobile_no").value = user.mobile_no;
                    document.getElementById("hidden_email_id").value = user.email_id ?? '';

                    document.getElementById("hidden_designation").value = designations.join(",");
                    document.getElementById("hidden_department").value = departments.join(",");
                    document.getElementById("hidden_designation_id").value = designationIds.join(",");
                    document.getElementById("hidden_department_id").value = departmentIds.join(",");
                } else {
                    document.querySelector(".profile-left").innerHTML = "<p>No profile found</p>";
                }
            })
            .catch(error => {
                console.error("Error fetching user profile:", error);
            });
    } else {
        document.querySelector(".profile-left").innerHTML = "";

        // Clear hidden inputs
        document.getElementById("hidden_staff_id").value = "";
        document.getElementById("hidden_staff_name").value = "";
        document.getElementById("hidden_mobile_no").value = "";
        document.getElementById("hidden_email_id").value = "";
        document.getElementById("hidden_designation").value = "";
        document.getElementById("hidden_department").value = "";
        document.getElementById("hidden_designation_id").value = "";
        document.getElementById("hidden_department_id").value = "";
    }
});

document.getElementById("staff_name").addEventListener("change", function() {
    //alert("gh");
    let staffId = this.value;

    if (!staffId) {
        // Clear selects and hidden inputs
        document.getElementById("department").innerHTML = '<option value="">Department name</option>';
        document.getElementById("department_category").innerHTML = '<option value="">Department Category</option>';

        document.getElementById("hidden_department").value = '';
        document.getElementById("hidden_department_category").value = '';
        return;
    }

    let url = `{{ route('getStaffData', ['staffId' => '__staffId__']) }}`.replace('__staffId__', staffId);

    fetch(url)
        .then(res => res.json())
        .then(res => {
            if (!res.success) return;

            let data = res.data;

            // Show filters
            document.getElementById("filtersAndTable").style.display = 'block';

            // --- Populate Department ---
            let deptSelect = document.getElementById("department");
            deptSelect.innerHTML = '';
            let deptSet = new Set();
            data.forEach(d => deptSet.add(JSON.stringify({id: d.department_id, name: d.department_name})));
            deptSet.forEach(item => {
                let dept = JSON.parse(item);
                let option = new Option(dept.name, dept.id);
                deptSelect.add(option);
            });
            if (deptSelect.options.length === 1) deptSelect.selectedIndex = 0;

           
            // --- Populate Department Category ---
            let categorySelect = document.getElementById("department_category");
            categorySelect.innerHTML = '';
            let catSet = new Set();
            data.forEach(c => catSet.add(JSON.stringify({id: c.department_category_id, name: c.department_category_name})));
            catSet.forEach(item => {
                let cat = JSON.parse(item);
                let option = new Option(cat.name, cat.id);
                categorySelect.add(option);
            });
            if (categorySelect.options.length === 1) categorySelect.selectedIndex = 0;
           
            // --- Populate hidden inputs ---
            document.getElementById("hidden_department").value = data[0].department_id;
            document.getElementById("hidden_department_category").value = data[0].department_category_id;

        })
        .catch(err => console.error(err));
});


    //department wise department category
    document.getElementById("department").addEventListener("change", function() {
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


    //zone wise ward
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

    //add and edit subblist
    let editingRowId = null;

    function saveUser() {
        var formData = {
            screen_unique_id : $('#screen_unique_id').val(),
            staff_name: $('#staff_name').val(),
            department: $('#department').val(),
            stage: $('#stage').val(),
            department_category: $('#department_category').val(),
            zone: $('#zone').val(),
            ward: $('#ward').val() || [],
            days: $('#days').val(),
            is_active: 1
        };
        //alert(formData);

        var url = "{{ route('department_allocation_sub.store') }}";

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        $.ajax({
            url: url,
            method: "POST",
            data: formData,
            success: function(response) {
                Swal.fire({
                    title: 'Success!',
                    text: editingRowId ? 'Department Allocation updated successfully!' : 'Department Allocation added successfully!',
                    icon: 'success',
                    timer: 1000,
                    showConfirmButton: false
                });

                $.ajax({
                    url: "{{ route('table.convert') }}",
                    method: "POST",
                    data: {
                        staff_name: formData.staff_name,
                        department: formData.department,
                        stage: formData.stage,
                        department_category: formData.department_category,
                        zone: formData.zone,
                        ward: formData.ward,
                        days: formData.days,
                    },
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(nameResponse) {
                        let wardDisplay = (nameResponse.wards && Array.isArray(nameResponse.wards)) ?
                            nameResponse.wards.join(", ") :
                            "";

                        if (editingRowId) {
                            let row = $(`#row-${editingRowId}`);
                            row.attr("data-staff", formData.staff_name)
                                .attr("data-department", formData.department)
                                .attr("data-stage", formData.stage)
                                .attr("data-category", formData.department_category)
                                .attr("data-zone", formData.zone)
                                .attr("data-wards", JSON.stringify(formData.ward))
                                .attr("data-days", formData.days);

                            row.find("td:eq(1)").text(nameResponse.name);
                            row.find("td:eq(2)").text(nameResponse.department);
                            row.find("td:eq(3)").text(nameResponse.stage);
                            row.find("td:eq(4)").text(nameResponse.department_category);
                            row.find("td:eq(5)").text(nameResponse.zone);
                            row.find("td:eq(6)").text(wardDisplay);
                            row.find("td:eq(7)").text(nameResponse.days);

                            editingRowId = null; // reset editing mode
                        } else {
                            let rowCount = $("#dataTable tbody tr").length + 1;
                            $("#dataTable").show();
                            $("#dataTable tbody").append(`
                            <tr id="row-${response.id}"
                                data-id="${response.id}"
                                data-staff="${formData.staff_name}"
                                data-department="${formData.department}"
                                data-stage="${formData.stage}"
                                data-category="${formData.department_category}"
                                data-zone="${formData.zone}"
                                data-wards='${JSON.stringify(formData.ward)}'
                                 data-days="${formData.days}">
                                <td>${rowCount}</td>
                                <td>${nameResponse.name}</td>
                                <td>${nameResponse.department}</td>
                                <td>${nameResponse.stage}</td>
                                <td>${nameResponse.department_category}</td>
                                <td>${nameResponse.zone}</td>
                                <td>${wardDisplay}</td>
                                <td>${nameResponse.days}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary editBtn" data-id="${response.id}">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                        `);
                        
                        }
                    }
                });

                
                $('#department').val('').trigger("change");
                $('#stage').val('').trigger("change");
                $('#department_category').val('').trigger("change");
                $('#zone').val('').trigger("change");
                $('#ward').val([]).trigger('change');
                $('#days').val('').trigger("change");
            },
            error: function(xhr) {
                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    let errors = xhr.responseJSON.errors;
                    let errorMessage = Object.values(errors).join('<br>');
                    Swal.fire('Validation Error', errorMessage, 'warning');
                } else {
                    let message = xhr.responseJSON && xhr.responseJSON.message ?
                        xhr.responseJSON.message :
                        'An error occurred. Please try again.';
                    Swal.fire('Error!', message, 'error');
                }
            }
        });
    }

    $(document).on("click", ".editBtn", function() {
        let row = $(this).closest("tr");
        editingRowId = row.data("id");

        let staff_id = row.data("staff");
        let dept_id = row.data("department");
        let stage_id = row.data("stage");
        let category_id = row.data("category");
        let zone_id = row.data("zone");
        let ward_ids = JSON.parse(row.attr("data-wards"));
        let days_id = row.data("days");

        $("#staff_name").val(staff_id).trigger("change");
        $("#department").val(dept_id).trigger("change");
        $("#stage").val(stage_id).trigger("change");
        $("#department_category").val(category_id).trigger("change");
        $("#zone").val(zone_id).trigger("change");
        $("#ward").val(ward_ids).trigger("change");
        $("#days").val(days_id).trigger("change");
        $('html, body').animate({
            scrollTop: $("#staff_name").offset().top - 100
        }, 500);
    });

    //final save
    function finalSave() {
    var formData = {
        screen_unique_id: $('#screen_unique_id').val(),
        staff_name: $('#hidden_staff_id').val(),
        mobile_no: $('#hidden_mobile_no').val(),
        designation: $('#hidden_designation_id').val()|| [],
        department: $('#hidden_department_id').val()|| [],
        is_active: $('#is_active').val()
    };

    console.log(formData);

    var method = "{{ isset($department_allocation) ? 'PUT' : 'POST' }}";
    var url = "{{ isset($department_allocation) 
        ? route('department_allocation.update', $department_allocation->id ?? 0) 
        : route('department_allocation.store1') }}";

    if (method === 'PUT') {
        formData._method = 'PUT';
        method = 'POST'; // Laravel requires POST with _method=PUT
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });

    $.ajax({
        url: url,
        method: method,
        data: formData,
        success: function(response) {
            Swal.fire({
                title: 'Success!',
                text: 'Department Allocation ' + (formData._method ? 'updated' : 'added') + ' successfully!',
                icon: 'success',
                timer: 1500,
                showConfirmButton: true
            }).then(() => {
                window.location.href = "{{ route('department_allocation') }}";
            });
        },
        error: function(xhr) {
    if (xhr.status === 422) {
        let errors = xhr.responseJSON.errors;
        if (errors) {
            // Laravel validation errors
            let errorMessage = Object.values(errors).join('<br>');
            Swal.fire('Validation Error', errorMessage, 'warning');
        } else {
            // Custom error from catch
            Swal.fire('Error', xhr.responseJSON.message || 'Validation failed', 'error');
        }
    } else {
        Swal.fire('Error!', 'An error occurred. Please try again.', 'error');
        console.error("AJAX Error:", xhr.responseText);
    }
}

    });
}

</script>

@endsection