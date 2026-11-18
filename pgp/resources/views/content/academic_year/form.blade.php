@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')
    <div class="row mb-3 page-hed">
        <div class="col-12">
            <h4 class=""><span class="text-muted fw-light">Settings/</span>Academic year</h4>
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
                    <div class="d-flex justify-content-end">
                        <span class="text-danger fw-bold">* Mandatory Fields</span>
                    </div>

                    <form id="academic_year_form">
                        @csrf
                        @if(isset($academic_year))
                            @method('PUT')
                        @endif

                        <div class="row mb-3">

                            <div class="col-sm-2 mt-3">
                                <label class="form-label" for="academic_year">Academic Year<span
                                        class="text-danger fw-bold">*</span></label>
                            </div>
                            <div class="col-sm-4 mt-3">
                                <input type="text" class="form-control" id="academic_year" name="academic_year"
                                    value="{{ isset($academic_year) ? $academic_year->academic_year : '' }}" />
                            </div>

                            <div class="col-sm-2 mt-3">
                                <label class="form-label" for="description">Description</label>
                            </div>
                            <div class="col-sm-4 mt-3">
                                <textarea class="form-control" id="description" name="description"
                                    oninput="validatetextarea(this)">{{ isset($academic_year) ? $academic_year->description : '' }}</textarea>
                            </div>

                            <div class="col-sm-2 mt-3">
                                <label class="form-label" for="is_active">Status</label>
                            </div>
                            <div class="col-sm-4 mt-3">
                                <select class="form-select" name="is_active" id="is_active">
                                    <option value="1" {{ isset($academic_year) && $academic_year->is_active == 1 ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="0" {{ isset($academic_year) && $academic_year->is_active == 0 ? 'selected' : '' }}>
                                        Inactive</option>
                                </select>
                            </div>

                        </div>

                        <div class="btn-float">
                            <button type="button" class="btn btn-danger"
                                onclick="window.location.href='{{ route('academic_year') }}'">Cancel</button>
                            <button type="submit"
                                class="btn btn-primary">{{ isset($academic_year) ? 'Update' : 'Save' }}</button>

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
        $(document).ready(function () {


            $('#academic_year_form').submit(function (event) {
                event.preventDefault(); // Prevent the default form submission

                // Validate the form
                let isValid = validateForm();
                if (!isValid) {
                    return false;
                }

                // Gather form data
                var formData = {
                    academic_year: $('#academic_year').val(),
                    description: $('#description').val(),
                    is_active: $('#is_active').val(),
                    _token: '{{ csrf_token() }}'
                };


                // Determine method and URL based on whether $acc_year is set
                var method = "{{ isset($academic_year) ? 'PUT' : 'POST' }}";
                var url = "{{ isset($academic_year) ? route('academic_year.update', $academic_year->unique_id) : route('academic_year.store') }}";


                $.ajax({
                    url: url,
                    method: method,
                    data: formData,

                    success: function (response) {
                        Swal.fire({
                            title: 'Success!',
                            text: (method === 'POST' ? 'Added' :
                                'Updated') +
                                ' successfully!',
                            icon: 'success',
                            timer: 1500, // Time in milliseconds (1500ms = 1.5 seconds)
                            showConfirmButton: true, // Hide the confirm button

                        }).then(() => {
                            window.location.href = "{{ route('academic_year') }}";
                        });
                    },

                    error: function (xhr) {
                        console.log(xhr.responseText);
                        if (xhr.status === 422) { // Validation error status code
                            var errors = xhr.responseJSON.errors;
                            if (errors.academic_year_name) {
                                Swal.fire(
                                    'Warning',
                                    'academic year already exists.',
                                    'warning'
                                );
                            } else {
                                // Handle other validation errors
                                Swal.fire(
                                    'Warning',
                                    'Please check the form for errors.',
                                    'warning'
                                );
                            }
                        } else {
                            Swal.fire(
                                'Error!',
                                'An error occurred. Please try again.',
                                'error'
                            );
                        }
                    }
                });
            });
        });

        function validateForm() {
            let isValid = true;
            let academic_year = document.getElementById('academic_year');

            if (academic_year.value == '' ) {
                sweetalert('form_alert');
                // accerr.textContent = 'center name must not me empty';
                isValid = false;
            }

            return isValid;
        }
    </script>

@endsection