@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')
    <div class="row mb-3 page-hed">
        <div class="col-12">
            <h4 class=""><span class="text-muted fw-light">Settings/</span>Location</h4>
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

                    <form id="location_form">
                        @csrf
                        @if(isset($location))
                            @method('PUT')
                        @endif

                        <div class="row mb-3">
                            @if(isset($location))
                                <div class="col-sm-2 mt-3">
                                    <label class="form-label" for="location_id">Location ID<span
                                            class="text-danger fw-bold">*</span></label>
                                </div>
                                <div class="col-sm-4 mt-3">
                                    <h3>{{ isset($location) ? $location->location_id : '' }}</h3>
                                </div>
                            @endif
                            <div class="col-sm-2 mt-3">
                                <label class="form-label" for="location_name">Location Name<span
                                        class="text-danger fw-bold">*</span></label>
                            </div>
                            <div class="col-sm-4 mt-3">
                                <input type="text" class="form-control" id="location_name" name="location_name"
                                    value="{{ isset($location) ? $location->location_name : '' }}"
                                    oninput="validateInputName(this)" />
                            </div>

                            <div class="col-sm-2 mt-3">
                                <label class="form-label" for="type">Type<span class="text-danger fw-bold">*</span></label>
                            </div>
                            <div class="col-sm-4 mt-3">
                                <input type="text" class="form-control" id="type" name="type"
                                    value="{{ isset($location) ? $location->location_type : '' }}"
                                    oninput="validateInputName(this)" />
                            </div>

                            <div class="col-sm-2 mt-3">
                                <label class="form-label" for="type">Address<span
                                        class="text-danger fw-bold">*</span></label>
                            </div>
                            <div class="col-sm-4 mt-3">
                                <input type="text" class="form-control" id="address" name="address"
                                    value="{{ isset($location) ? $location->address : '' }}"
                                    oninput="validateAddress(this)" />
                            </div>

                            <div class="col-sm-2 mt-3">
                                <label class="form-label" for="type">City<span class="text-danger fw-bold">*</span></label>
                            </div>
                            <div class="col-sm-4 mt-3">
                                <input type="text" class="form-control" id="city" name="city"
                                    value="{{ isset($location) ? $location->city : '' }}"
                                    oninput="validateInputName(this)" />
                            </div>

                            <div class="col-sm-2 mt-3">
                                <label class="form-label" for="type">District<span class="text-danger fw-bold">*</span></label>
                            </div>
                            <div class="col-sm-4 mt-3">
                                <input type="text" class="form-control" id="district" name="district"
                                    value="{{ isset($location) ? $location->district : '' }}"
                                    oninput="validateInputName(this)" />
                            </div>

                            <div class="col-sm-2 mt-3">
                                <label class="form-label" for="type">State<span class="text-danger fw-bold">*</span></label>
                            </div>
                            <div class="col-sm-4 mt-3">
                                <input type="text" class="form-control" id="state" name="state"
                                    value="{{ isset($location) ? $location->state : '' }}"
                                    oninput="validateInputName(this)" />
                            </div>

                            <div class="col-sm-2 mt-3">
                                <label class="form-label" for="type">Pincode<span
                                        class="text-danger fw-bold">*</span></label>
                            </div>
                            <div class="col-sm-4 mt-3">
                                <input type="text" class="form-control" id="pincode" name="pincode" maxlength="6"
                                    value="{{ isset($location) ? $location->pincode : '' }}"
                                    oninput="validateInputNumber(this)" />
                            </div>

                            <div class="col-sm-2 mt-3">
                                <label class="form-label" for="type">Contact Person<span
                                        class="text-danger fw-bold">*</span></label>
                            </div>
                            <div class="col-sm-4 mt-3">
                                <input type="text" class="form-control" id="contact_person" name="contact_person"
                                    value="{{ isset($location) ? $location->contact_person : '' }}"
                                    oninput="validateInputName(this)" />
                            </div>

                            <div class="col-sm-2 mt-3">
                                <label class="form-label" for="type">Contact Number<span
                                        class="text-danger fw-bold">*</span></label>
                            </div>
                            <div class="col-sm-4 mt-3">
                                <input type="text" class="form-control" id="contact_number" name="contact_number"
                                    maxlength="10" value="{{ isset($location) ? $location->contact_number : '' }}"
                                    oninput="validateInputNumber(this)" />
                            </div>

                            <div class="col-sm-2 mt-3">
                                <label class="form-label" for="description">Description</label>
                            </div>
                            <div class="col-sm-4 mt-3">
                                <textarea class="form-control" id="description" name="description"
                                    oninput="validatetextarea(this)">{{ isset($location) ? $location->description : '' }}</textarea>
                            </div>

                            <div class="col-sm-2 mt-3">
                                <label class="form-label" for="status">Status</label>
                            </div>
                            <div class="col-sm-4 mt-3">
                                <select class="form-select" name="status" id="status">
                                    <option value="1" {{ isset($location) && $location->is_active == 1 ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="0" {{ isset($location) && $location->is_active == 0 ? 'selected' : '' }}>
                                        Inactive</option>
                                </select>
                            </div>

                        </div>

                        <div class="btn-float">
                            <button type="button" class="btn btn-danger"
                                onclick="window.location.href='{{ route('location') }}'">Cancel</button>
                            <button type="submit"
                                class="btn btn-primary">{{ isset($location) ? 'Update' : 'Save' }}</button>

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


            $('#location_form').submit(function (event) {
                event.preventDefault(); // Prevent the default form submission

                // Validate the form
                let isValid = validateForm();
                if (!isValid) {
                    return false;
                }

                // Gather form data
                var formData = {
                    location_name: $('#location_name').val(),
                    type: $('#type').val(),
                    address: $('#address').val(),
                    city: $('#city').val(),
                    district: $('#district').val(),
                    state: $('#state').val(),
                    pincode: $('#pincode').val(),
                    contactPerson: $('#contact_person').val(),
                    contactNum: $('#contact_number').val(),
                    description: $('#description').val(),
                    status: $('#status').val(),
                    _token: '{{ csrf_token() }}'
                };


                // Determine method and URL based on whether $acc_year is set
                var method = "{{ isset($location) ? 'PUT' : 'POST' }}";
                var url = "{{ isset($location) ? route('location.update', $location->unique_id) : route('location.store') }}";


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
                            window.location.href = "{{ route('location') }}";
                        });
                    },

                    error: function (xhr) {
                        console.log(xhr.responseText);
                        if (xhr.status === 422) { // Validation error status code
                            var errors = xhr.responseJSON.errors;
                            if (errors.location_name) {
                                Swal.fire(
                                    'Warning',
                                    'Location already exists.',
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
            let location_name = document.getElementById('location_name');
            let type = document.getElementById('type');

            if (location_name.value == '' || type.value == '') {
                sweetalert('form_alert');
                // accerr.textContent = 'center name must not me empty';
                isValid = false;
            }

            return isValid;
        }
    </script>

@endsection