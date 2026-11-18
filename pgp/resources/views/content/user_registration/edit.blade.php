@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Settings /</span>Staff Registration</h4>
    </div>
</div>
<style>
    .btn-right {
        float: right;
    }
</style>
<?php
 $get_dept_name = get_dept_name();
    $get_designation_name = get_designation();
    $get_stage_name = get_stage();
    $get_dept_category = get_dept_category();
     $user_type_options = user_type();
?>

                <!-- Basic Layout -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="row">
    <div class="col-xl">
        <div class="row">
            <div class="col-xl">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-end">
                            <span class="text-danger fw-bold">* Mandatory Fields</span>
                        </div>
                        <div class="row mb-3 page-hed">
                            <div class="col-12">
                                <h4 class="mb-0">Staff Details</h4>
                            </div>
                        </div>

                        <form id="user_registration_form">
                            @csrf
                            <div class="row mb-3">
                                <input type="hidden" id="screen_unique_id" name="screen_unique_id" value="{{ $screen_unique_id }}">
                                <div class="col-sm-2 mt-3">
                                    <label class="form-label" for="name">Name<span class="text-danger fw-bold">*</span></label>
                                </div>
                                <div class="col-sm-4 mt-3">
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ isset($user_registration) ? $user_registration->name : '' }}">
                                </div>

                                <!-- Mobile -->
                                <div class="col-sm-2 mt-3">
                                    <label class="form-label" for="mobile_no">Mobile Number<span class="text-danger fw-bold">*</span></label>
                                </div>
                                <div class="col-sm-4 mt-3">
                                    <input type="text" class="form-control" id="mobile_no" name="mobile_no"
                                        maxlength="10" pattern="[0-9]{10}"
                                        value="{{ isset($user_registration) ? $user_registration->mobile_no : '' }}">
                                </div>

                                <!-- Alternative Mobile -->
                                <div class="col-sm-2 mt-3">
                                    <label class="form-label" for="amobile_no">Alternative Mobile Number<span class="text-danger fw-bold">*</span></label>
                                </div>
                                <div class="col-sm-4 mt-3">
                                    <input type="text" class="form-control" id="amobile_no" name="amobile_no"
                                        maxlength="10" pattern="[0-9]{10}"
                                        value="{{ isset($user_registration) ? $user_registration->amobile_no : '' }}">
                                </div>

                                <!-- Email -->
                                <div class="col-sm-2 mt-3">
                                    <label class="form-label" for="email_id">E-Mail ID</label>
                                </div>
                                <div class="col-sm-4 mt-3">
                                    <input type="email" class="form-control" id="email_id" name="email_id"
                                        value="{{ isset($user_registration) ? $user_registration->email_id : '' }}">
                                </div>
                            </div>

                            <hr>
                            <h6>Department Details</h6>
                            <div class="row mb-3">
                               <div class="col-sm-2 mt-3">
                                <label class="form-label" for="designation">Designation<span class="text-danger fw-bold">*</span></label>
                            </div>
                            <div class="col-sm-4 mt-3">
                                <select class="form-select" id="designation" name="designation">
                                    <option value="">Select Designation</option>
                                        @foreach ($get_designation_name as $get_designation)
                                    <option value="{{ $get_designation->unique_id }}"
                                    {{ isset($selected_designation) && $selected_designation == $get_designation->unique_id ? 'selected' : '' }}>
                                    {{ $get_designation->designation }}
                                     </option>
                                     @endforeach
                                </select>
                            </div>

                            <!-- Department -->
                            <div class="col-sm-2 mt-3">
                                <label class="form-label" for="department">Department<span class="text-danger fw-bold">*</span></label>
                            </div>
                            <div class="col-sm-4 mt-3">
                                 <select class="form-select" id="department" name="department">
                                <option value="">Department name</option>
                                @foreach ($get_dept_name as $get_department)
                                <option value="{{ $get_department->unique_id }}"
                                    {{ (isset($escalation) && $escalation->department == $get_department->unique_id) ? 'selected' : '' }}>
                                    {{ formatToCapitalizedWords($get_department->department) }}
                                </option>
                                @endforeach
                            </select>
                            </div>

                            <!-- Stage -->
                            <!-- <div class="col-sm-2 mt-3">
                                <label class="form-label" for="stage">Stage<span class="text-danger fw-bold">*</span></label>
                            </div>
                            <div class="col-sm-4 mt-3">
                                <select class="form-select" id="stage" name="stage">
                                     <option value="">Select Stage</option>
                                @foreach ($get_stage_name as $get_stage)
                                <option value="{{ $get_stage->unique_id }}"
                                    {{ isset($escalation) && $escalation->stage == $get_stage->unique_id ? 'selected' : '' }}>
                                    {{ $get_stage->stage }}
                                </option>
                                @endforeach
                                   
                                </select>
                            </div> -->

                            <!-- Department Category -->
                            <div class="col-sm-2 mt-3">
                                <label class="form-label" for="department_category">Department Category<span class="text-danger fw-bold">*</span></label>
                            </div>
                            <div class="col-sm-4 mt-3">
                                <select class="form-select" id="department_category" name="department_category">
                                    <option value="">Select Department Category</option>
                                   
                                </select>
                            </div>
                            <div class="col-12 text-end mt-3">
                            <button type="button" id="addBtn" class="btn btn-success" style="background-color:#027AFF; border-color:#027AFF;" onclick="saveSublist()">
                                Add
                            </button>
                        </div>
                        <div class="table-responsive mt-4" id="dataTable" >
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>S.No</th>
                                    <th>Staff</th>
                                    <th>Designation</th>
                                    <th>Department</th>
                                    <th>Department Category</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                            </div>
                            
                            <hr>
                            <div class="row mb-3">
                                <div class="row mb-3 page-hed">
                                <div class="col-12">
                                    <h4 class="mb-0">User Login Details</h4>
                                </div>
                            </div>

                            <!-- User Name -->
                            <div class="col-sm-2 mt-3">
                                <label class="form-label" for="user_name">User Name</label>
                            </div>
                            <div class="col-sm-4 mt-3">
                                <input type="text" class="form-control" id="user_name" name="user_name"
                                    value="{{ isset($user_registration) ? $user_registration->user_name : '' }}">
                            </div>
                            <div class="col-sm-2 mt-3">
                                <label class="form-label" for="user_type">User Type</label>
                            </div>
                            <div class="col-sm-4 mt-3">
                               <select class="form-select" id="user_type" name="user_type">

                        <option value="selected">Select user type
                        </option>
                        @foreach ($user_type_options as $user_type)
                        <option value="{{$user_type->unique_id}}">
                            {{$user_type->user_type}}
                        </option>
                        @endforeach
                    </select>
                            </div>

                            <!-- Password -->
                            <div class="col-sm-2 mt-3">
                                <label class="form-label" for="new_password">Password<span class="text-danger fw-bold">*</span></label>
                            </div>
                            <div class="col-sm-4 mt-3">
                                <div class="input-group">
                                    <input type="password" class="form-control" id="new_password" name="new_password"
                                        value="{{ isset($user_registration) ? $user_registration->new_password : '' }}">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password', this)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="col-sm-2 mt-3">
                                <label class="form-label" for="conf_password">Confirm Password<span class="text-danger fw-bold">*</span></label>
                            </div>
                            <div class="col-sm-4 mt-3">
                                <div class="input-group">
                                    <input type="password" class="form-control" id="conf_password" name="conf_password"
                                        value="{{ isset($user_registration) ? $user_registration->conf_password : '' }}">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('conf_password', this)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <small id="passwordHelp" class="text-danger"></small>
                            </div>

                            <!-- Buttons -->
                            <div class="btn-float mt-3">
                                <button type="button" class="btn btn-danger"
                                    onclick="window.location.href='{{ route('user_registration') }}'">Cancel</button>
                                <button type="button" class="btn btn-primary" onclick="saveUser()">
                                    {{ isset($user_registration) ? 'Update' : 'Submit' }}
                                </button>
                            </div>
                            </div>
                            
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


                <!-- Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<script>
                function saveUser() {
                    
            var formData = {
                screen_unique_id: $('#screen_unique_id').val(),
                name: $('#name').val(),
                mobile_no: $('#mobile_no').val(),
                amobile_no: $('#amobile_no').val(),
                email_id: $('#email_id').val(),
                user_name: $('#user_name').val(),
                user_type: $('#user_type').val(),
                new_password: $('#new_password').val(),
                conf_password: $('#conf_password').val()
            };

            var method = "{{ isset($user_registration) ? 'PUT' : 'POST' }}";
            var url = "{{ isset($user_registration) ? route('user_registration.update', $user_registration->unique_id) : route('user_registration.store') }}";

            if (method === 'PUT') {
                formData._method = 'PUT';
                method = 'POST';
            }

            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });

            $.ajax({
                url: url,
                method: method,
                data: formData,
                success: function(response) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'User ' + (method === 'POST' && !formData._method ? 'added' : 'updated') + ' successfully!',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: true
                    }).then(() => {
                        window.location.href = "{{ route('user_registration') }}";
                    });
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessage = Object.values(errors).join('<br>');
                        Swal.fire('Validation Error', errorMessage, 'warning');
                    } else {
                        Swal.fire('Error!', 'An error occurred. Please try again.', 'error');
                    }
                }
            });
        }

function togglePassword(fieldId, btn) {
                        let field = document.getElementById(fieldId);
                        let icon = btn.querySelector('i');
                        if (field.type === "password") {
                            field.type = "text";
                            icon.classList.replace('bi-eye', 'bi-eye-slash');
                        } else {
                            field.type = "password";
                            icon.classList.replace('bi-eye-slash', 'bi-eye');
                        }
                    }

                    function validatePasswords() {
                        let pass = document.getElementById("password").value;
                        let confirmPass = document.getElementById("confirm_password").value;
                        let helpText = document.getElementById("passwordHelp");

                        if (confirmPass.length > 0 && pass !== confirmPass) {
                            helpText.textContent = "Passwords do not match!";
                        } else {
                            helpText.textContent = "";
                        }
                    }

                    function toggleWardSelect(checkbox) {
                        let wardSelect = document.getElementById("ward");
                        if (checkbox.checked) {
                            wardSelect.disabled = true;
                            wardSelect.value = "";
                        } else {
                            wardSelect.disabled = false;
                        }
                    }
 document.getElementById("addBtn").addEventListener("click", function() {
        document.getElementById("dataTable").style.display = "block";
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

let editingRowId = null;

function saveSublist() {
    var formData = {
        screen_unique_id: $('#screen_unique_id').val(),
        name: $('#name').val(),
        designation : $('#designation').val(),
        department: $('#department').val(),
        department_category: $('#department_category').val(),
        is_active: 1
    };
// alert(formData);
    var url = "{{ route('user_registration_sub.store') }}";

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    });

    $.ajax({
        url: url,
        method: "POST",
        data: formData,
        success: function(response) {
            Swal.fire({
                title: 'Success!',
                text: editingRowId ? 'Staff Registration updated successfully!' : 'Staff Registration added successfully!',
                icon: 'success',
                timer: 1000,
                showConfirmButton: false
            });

            // second request for converting IDs ? names
            $.ajax({
                url: "{{ route('user.table.convert') }}",
                method: "POST",
                data: formData,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function(nameResponse) {
                    if (editingRowId) {
                        let row = $(`#row-${editingRowId}`);
                        row.attr("data-staff", formData.name)
                           .attr("data-designation", formData.designation)
                           .attr("data-department", formData.department)
                           .attr("data-category", formData.department_category);

                        row.find("td:eq(1)").text(nameResponse.name);
                        row.find("td:eq(2)").text(nameResponse.designation);
                        row.find("td:eq(3)").text(nameResponse.department);
                        row.find("td:eq(5)").text(nameResponse.department_category);

                        editingRowId = null;
                    } else {
                        let rowCount = $("#dataTable tbody tr").length + 1;
                        $("#dataTable").show();
                        $("#dataTable tbody").append(`
                            <tr id="row-${response.id}"
                                data-id="${response.id}"
                                data-staff="${formData.name}"
                                data-designation="${formData.designation}"
                                data-department="${formData.department}"
                                data-category="${formData.department_category}">
                                <td>${rowCount}</td>
                                <td>${nameResponse.name}</td>
                                <td>${nameResponse.designation}</td>
                                <td>${nameResponse.department}</td>
                                <td>${nameResponse.department_category}</td>
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

            // reset form
            $('#designation').val('');
            $('#department').val('');
            $('#department_category').val('');
        },
        error: function(xhr) {
            if (xhr.status === 422 && xhr.responseJSON.errors) {
                let errors = xhr.responseJSON.errors;
                let errorMessage = Object.values(errors).join('<br>');
                Swal.fire('Validation Error', errorMessage, 'warning');
            } else {
                let message = xhr.responseJSON && xhr.responseJSON.message ?
                    xhr.responseJSON.message : 'An error occurred. Please try again.';
                Swal.fire('Error!', message, 'error');
            }
        }
    });
}


   $(document).on("click", ".editBtn", function() {
    let row = $(this).closest("tr");
    editingRowId = row.data("id");

    $("#name").val(row.data("staff")).trigger("change");
    $("#designation").val(row.data("designation")).trigger("change");
    $("#department").val(row.data("department")).trigger("change");
    $("#department_category").val(row.data("category")).trigger("change");

    $('html, body').animate({
        scrollTop: $("#name").offset().top - 100
    }, 500);
});
</script>
@endsection