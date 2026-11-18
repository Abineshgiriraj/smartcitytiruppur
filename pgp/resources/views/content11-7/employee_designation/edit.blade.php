@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Employee Designation')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Settings/</span> Edit Employee Designation</h4>
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
        <div class="card mb-4">
            <div class="card-body">
                <form id="employeedesignationform">
                    @csrf
                    @method('PUT') <!-- Indicate this is an update -->
                    <input type="hidden" name="id" value="{{ $employeeDesignation->id }}"> <!-- Hidden ID field -->

                    <div class="row mb-3">
                        <label for="colFormLabelSm" class="col-sm-2 col-form-label mt-3">Employee Designation</label>
                        <div class="col-sm-4 mt-3">
                            <input type="text" class="form-control" id="designation" name="designation"
                                value="{{ old('designation', $employeeDesignation->designation) }}" />
                            <div id="designationerror" class="text-danger"></div>
                        </div>

                        <label for="description" class="col-sm-2 col-form-label mt-3">Description</label>
                        <div class="col-sm-4 mt-3">
                            <textarea id="description" class="form-control"
                                name="description">{{ old('description', $employeeDesignation->description) }}</textarea>
                        </div>

                        <label for="StatusSelect" class="col-sm-2 col-form-label mt-3">Status</label>
                        <div class="col-sm-4 mt-3">
                            <select class="form-select" id="status" name="status">
                                <option value="1" {{ $employeeDesignation->status == 1 ? 'selected' : '' }}>Active
                                </option>
                                <option value="2" {{ $employeeDesignation->status == 2 ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                        </div>

                    </div>

                    <div class="btn-float">
                        <button type="button" class="btn btn-danger"
                            onclick="window.location.href='{{ route('employee_designation') }}'">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>

<script>
    $('#employeedesignationform').submit(function (event) {
        event.preventDefault(); // Prevent the default form submission

        var designation = $('#designation').val();

        // Gather form data
        var formData = {
            designation: $('#designation').val(),
            description: $('#description').val(),
            status: $('#status').val(),
            _token: '{{ csrf_token() }}'
        };

        // Update URL dynamically
        var url = "{{ route('employee_designation_update', $employeeDesignation->id) }}"; // Update route as necessary

        if (designation !== '') {

        $.ajax({
            url: url,
            method: 'PUT',
            data: formData,

            success: function (response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Designation updated successfully!',
                    timer: 1500,
                }).then(() => {
                    window.location.href = "{{ route('employee_designation') }}";
                });
            },

            error: function (xhr) {
                console.log(xhr.responseText);
                if (xhr.status === 422) { // Validation error status code
                    sweetalert("already");
                } else {
                    Swal.fire(
                        'Error!',
                        'An error occurred. Please try again.',
                        'error'
                    );
                }
            }
        });
    }else{
        if (designation === '') {
                sweetalert("form_alert");
                $('#designationerror').text('Designation is required.');
            } else {
                $('#designationerror').text('');
            }
    }
    });
</script>

@endsection