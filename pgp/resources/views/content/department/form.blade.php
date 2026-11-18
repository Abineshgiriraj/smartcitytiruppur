@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Settings /</span>Designation</h4>
    </div>
</div>
<style>
    /* btn style */
    .btn-right {
        float: right;
    }
</style>
<!-- Basic Layout -->
<div class="row">
    <div class="col-xl">
        <div class="row">
            <div class="col-xl">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-end">
                            <span class="text-danger fw-bold">* Mandatory Fields</span>
                        </div>

                        <form id="Department_form">
                            <div class="row mb-3">
                                <div class="col-sm-2 mt-3">
                                    <label class="form-label" for="Department_name">Department<span
                                            class="text-danger fw-bold">*</span></label>
                                </div>
                                <div class="col-sm-4 mt-3">
                                    <input type="text" class="form-control" id="department" name="department"
                                        value="{{ isset($department) ? $department->department : '' }}"
                                        oninput="validateInputName(this)" />
                                </div>
                                <div class="col-sm-2 mt-3">
                                    <label class="form-label" for="description">Description</label>
                                </div>
                                <div class="col-sm-4 mt-3">
                                    <textarea class="form-control" id="description" name="description"
                                        oninput="validatetextarea(this)">{{ isset($department) ? $department->description : '' }}</textarea>
                                </div>

                                <div class="col-sm-2 mt-3">
                                    <label class="form-label" for="is_active">Status</label>
                                </div>
                                <div class="col-sm-4 mt-3">
                                    <select class="form-select" name="is_active" id="is_active">
                                        <option value="1" {{ isset($location) && $location->is_active == 1 ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="0" {{ isset($location) && $location->is_active == 0 ? 'selected' : '' }}>
                                            Inactive</option>
                                    </select>
                                </div>

                            </div>

                            <div class="btn-float">
                                <button type="button" class="btn btn-danger"
                                    onclick="window.location.href='{{ route('department') }}'">Cancel</button>
                                <button type="submit"
                                    class="btn btn-primary">{{ isset($department) ? 'Update' : 'Save' }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>

<script>
    $(document).ready(function() {
        // alert();
        $('#Department_form').submit(function(event) {
            event.preventDefault(); // Prevent the default form submission

            // Validate the form
            let isValid = validateForm();
            if (!isValid) {
                return false;
            }

            // Gather form data
            var formData = {
                department: $('#department').val(),
                description: $('#description').val(),
                is_active: $('#is_active').val(),
                _token: '{{ csrf_token() }}'
            };

            // Determine method and URL based on whether $acc_year is set
            var method = "{{ isset($department) ? 'PUT' : 'POST' }}";
            var url = "{{ isset($department) ? route('department.update', $department->unique_id) : route('department.store') }}";
            $.ajax({
                url: url,
                method: method,
                data: formData,

                success: function(response) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'department Name ' + (method === 'POST' ? 'added' :
                                'updated') +
                            ' successfully!',
                        icon: 'success',
                        timer: 1500, // Time in milliseconds (1500ms = 1.5 seconds)
                        showConfirmButton: true, // Hide the confirm button

                    }).then(() => {
                        window.location.href = "{{ route('department') }}";
                    });
                },

                error: function(xhr) {
                    if (xhr.status === 422) { // Validation error status code
                            Swal.fire('Warning','department already exists.', 'warning');
                    } else {
                        Swal.fire('Error!', 'An error occurred. Please try again.', 'error');
                    }
                }
            });
        });
    });

    function validateForm() {
        let isValid = true;
        let department = document.getElementById('department');
        // let category_name = document.getElementById('category_name');

        if (department.value == '') {
            Swal.fire(
                'Error!',
                'Fill the mandatory fileds',
                'error'
            );
            isValid = false;
        }

        return isValid;
    }
</script>
@endsection