@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')
    <div class="row mb-3 page-hed">
        <div class="col-12">
            <h4 class=""><span class="text-muted fw-light">Settings/</span>Zone</h4>
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

                    <form id="zone_form">
                        @csrf
                        @if(isset($zone))
                            @method('PUT')
                        @endif

                        <div class="row mb-3">

                            <div class="col-sm-2 mt-3">
                                <label class="form-label" for="zone">Zone Name<span
                                        class="text-danger fw-bold">*</span></label>
                            </div>
                            <div class="col-sm-4 mt-3">
                                <input type="text" class="form-control" id="zone" name="zone"
                                    value="{{ isset($zone) ? $zone->zone : '' }}"
                                    oninput="validateInputName(this)" />
                            </div>

                            <div class="col-sm-2 mt-3">
                                <label class="form-label" for="description">Description</label>
                            </div>
                            <div class="col-sm-4 mt-3">
                                <textarea class="form-control" id="description" name="description"
                                    oninput="validatetextarea(this)">{{ isset($zone) ? $zone->description : '' }}</textarea>
                            </div>

                            <div class="col-sm-2 mt-3">
                                <label class="form-label" for="is_active">Status</label>
                            </div>
                            <div class="col-sm-4 mt-3">
                                <select class="form-select" name="is_active" id="is_active">
                                    <option value="1" {{ isset($zone) && $zone->is_active == 1 ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="0" {{ isset($zone) && $zone->is_active == 0 ? 'selected' : '' }}>
                                        Inactive</option>
                                </select>
                            </div>

                        </div>

                        <div class="btn-float">
                            <button type="button" class="btn btn-danger"
                                onclick="window.location.href='{{ route('zone') }}'">Cancel</button>
                            <button type="submit"
                                class="btn btn-primary">{{ isset($zone) ? 'Update' : 'Save' }}</button>

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


            $('#zone_form').submit(function (event) {
                event.preventDefault(); // Prevent the default form submission

                // Validate the form
                let isValid = validateForm();
                if (!isValid) {
                    return false;
                }

                // Gather form data
                var formData = {
                    zone: $('#zone').val(),
                    description: $('#description').val(),
                    is_active: $('#is_active').val(),
                    _token: '{{ csrf_token() }}'
                };


                // Determine method and URL based on whether $acc_year is set
                var method = "{{ isset($zone) ? 'PUT' : 'POST' }}";
                var url = "{{ isset($zone) ? route('zone.update', $zone->unique_id) : route('zone.store') }}";


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
                            window.location.href = "{{ route('zone') }}";
                        });
                    },

                    error: function (xhr) {
                        console.log(xhr.responseText);
                        if (xhr.status === 422) { // Validation error status code
                            var errors = xhr.responseJSON.errors;
                            if (errors.zone_name) {
                                Swal.fire(
                                    'Warning',
                                    'zone already exists.',
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
            let zone = document.getElementById('zone');

            if (zone.value == '' ) {
                sweetalert('form_alert');
                // accerr.textContent = 'center name must not me empty';
                isValid = false;
            }

            return isValid;
        }
    </script>

@endsection