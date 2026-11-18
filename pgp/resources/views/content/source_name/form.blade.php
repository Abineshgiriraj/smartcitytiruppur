@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
<h4 class=""><span class="text-muted fw-light">Settings /</span>Soure Name</h4>
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

                    <form id="source_name_form">
                        @csrf

                        <div class="row mb-3">

                            <div class="col-sm-2 mt-3">
                                <label class="form-label" for="source_name">Source Name<span
                                        class="text-danger fw-bold">*</span></label>
                            </div>
                            <div class="col-sm-4 mt-3">
                                <input type="text" class="form-control" id="source_name" name="source_name"
                                    value="{{ isset($source_name) ? $source_name->source_name : '' }}"
                                    oninput="validateInputName(this)" />
                            </div>

                            <div class="col-sm-2 mt-3">
                                <label class="form-label" for="description">Description</label>
                            </div>
                            <div class="col-sm-4 mt-3">
                                <textarea class="form-control" id="description" name="description"
                                    oninput="validatetextarea(this)">{{ isset($source_name) ? $source_name->description : '' }}</textarea>
                            </div>

                            <div class="col-sm-2 mt-3">
                                <label class="form-label" for="is_active">Status</label>
                            </div>
                            <div class="col-sm-4 mt-3">
                                <select class="form-select" name="is_active" id="is_active">
                                    <option value="1" {{ isset($source_name) && $source_name->is_active == 1 ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="0" {{ isset($source_name) && $source_name->is_active == 0 ? 'selected' : '' }}>
                                        Inactive</option>
                                </select>
                            </div>

                        </div>

                        <div class="btn-float">
                            <button type="button" class="btn btn-danger"
                                onclick="window.location.href='{{ route('source_name') }}'">Cancel</button>
                            <button type="submit"
                                class="btn btn-primary">{{ isset($source_name) ? 'Update' : 'Save' }}</button>

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
        $(document).ready(function () {


            $('#source_name_form').submit(function (event) {
                event.preventDefault(); // Prevent the default form submission

                
                // Gather form data
                var formData = {
                    source_name: $('#source_name').val(),
                    description: $('#description').val(),
                    is_active: $('#is_active').val(),
                    _token: '{{ csrf_token() }}'
                };


                // Determine method and URL based on whether $acc_year is set
                var method = "{{ isset($source_name) ? 'PUT' : 'POST' }}";
                var url = "{{ isset($source_name) ? route('source_name.update', $source_name->unique_id) : route('source_name.store') }}";


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
                            window.location.href = "{{ route('source_name') }}";
                        });
                    },

                    error: function (xhr) {
                        console.log(xhr.responseText);
                        if (xhr.status === 422) { // Validation error status code
                            var errors = xhr.responseJSON.errors;
                            if (errors.source_name) {
                                Swal.fire(
                                    'Warning',
                                    'Source name already exists.',
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

    </script>
@endsection