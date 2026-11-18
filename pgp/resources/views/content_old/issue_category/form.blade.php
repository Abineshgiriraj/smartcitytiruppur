@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')
    <div class="row mb-3 page-hed">
        <div class="col-12">
            <h4 class=""><span class="text-muted fw-light">Settings/</span>Issue Category</h4>
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

                    <form id="designation_form">
                        @csrf
                        @if(isset($designation_id))
                            @method('PUT')
                        @endif

                        <div class="row mb-3">
                            <div class="col-sm-2 mt-3">
                                <label class="form-label" for="designation_name">QC Type<span
                                        class="text-danger fw-bold">*</span></label>
                            </div>
                           <div class="col-sm-4 mt-3">
  <select class="form-control" id="designation_name" name="designation_name">
    <option selected disabled>Select QC</option>
    <option value="physical_qc">Physical QC</option>
    <option value="technical_qc">Technical QC</option>
  </select>
</div>


                            <div class="col-sm-2 mt-3">
                                <label class="form-label" for="description">Issue Category<span
                                        class="text-danger fw-bold">*</span></label>
                            </div>
                            <div class="col-sm-4 mt-3">
                                <input type="text" class="form-control" id="description" name="description">
                            </div>

                            <div class="col-sm-2 mt-3">
                                <label class="form-label" for="status">Status</label>
                            </div>
                            <div class="col-sm-4 mt-3">
                                <select class="form-select" name="status" id="status">
                                    <option value="1" {{ isset($designation_id) && $designation_id->is_active == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ isset($designation_id) && $designation_id->is_active == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                        </div>

                        <div class="btn-float">
                            <button type="button" class="btn btn-danger"
                                onclick="window.location.href='{{ route('issue_category') }}'">Cancel</button>
                            <button type="submit"
                                class="btn btn-primary">{{ isset($designation_id) ? 'Update' : 'Save' }}</button>
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
            // alert();
            $('#designation_form').submit(function (event) {
                event.preventDefault(); // Prevent the default form submission

                // Validate the form
                let isValid = validateForm();
                if (!isValid) {
                    return false;
                }

                // Gather form data
                var formData = {
                    designation_name: $('#designation_name').val(),
                    description: $('#description').val(),
                    status: $('#status').val(),
                    _token: '{{ csrf_token() }}'
                };


                // Determine method and URL based on whether $acc_year is set
                var method = "{{ isset($designation_id) ? 'PUT' : 'POST' }}";
                var url = "{{ isset($designation_id) ? route('designation.update', $designation_id->unique_id) : route('designation.store') }}";
                $.ajax({
                    url: url,
                    method: method,
                    data: formData,

                    success: function (response) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Designation ' + (method === 'POST' ? 'added' :
                                'updated') +
                                ' successfully!',
                            icon: 'success',
                            timer: 1500, // Time in milliseconds (1500ms = 1.5 seconds)
                            showConfirmButton: true, // Hide the confirm button

                        }).then(() => {
                            window.location.href = "{{ route('designation') }}";
                        });
                    },

                    error: function (xhr) {
                        console.log(xhr.responseText);
                        if (xhr.status === 422) { // Validation error status code
                            var errors = xhr.responseJSON.errors;
                            // Handle other validation errors
                            Swal.fire(
                                'Warning',
                                'Please check the form for errors.',
                                'warning'
                            );
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
            let designation = document.getElementById('designation_name');

            if (designation.value == '') {
                sweetalert('form_alert');
                isValid = false;
            }

            return isValid;
        }
    </script>

@endsection