@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Person Type')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Settings/</span>Edit Person Type</h4>
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
                <form id="personTypeform">
                    @csrf
                    @method('PUT') <!-- Indicate this is an update -->
                    <input type="hidden" name="id" value="{{ $PersonType->id }}"> <!-- Hidden ID field -->

                    <div class="row mb-3">

                        <div class="col-4">
                            <div class="mb-3 mt-2 d-flex align-items-center">
                                <label class="form-label me-2" style="width:100px;" for="person_type">Person
                                    Type</label>
                                <input type="text" class="form-control" name="person_type" id="person_type"
                                    value="{{ old('person_type', $PersonType->person_type) }}" />
                            </div>
                            <div id="persontypeerror" class="text-danger"></div>
                        </div>
                        <div class="col-4">
                            <div class="mb-3 mt-2 d-flex align-items-center">
                                <label class="form-label" for="status">Status</label>
                                <select class="form-select" name="status" id="status">
                                    <option value="1" {{ $PersonType->status == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="2" {{ $PersonType->status == 2 ? 'selected' : '' }}>Inactive
                                    </option>
                                </select>
                            </div>

                        </div>
                    </div>

                    <div class="btn-float">
                        <button type="button" class="btn btn-danger"
                            onclick="window.location.href='{{ route('person_type') }}'">Cancel</button>
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

    document.getElementById('person_type').addEventListener('input', function (e) {
        var value = e.target.value;
        // Allow only alphabetic characters (A-Z, a-z)
        var regex = /^[A-Za-z]*$/;
        if (!regex.test(value)) {
            document.getElementById('persontypeerror').textContent = "Only alphabetic characters are allowed.";
            e.target.value = value.replace(/[^A-Za-z]/g, ''); // Remove non-alphabetic characters
        } else {
            document.getElementById('persontypeerror').textContent = ''; // Clear error message
        }
    });

    $('#personTypeform').submit(function (event) {
        event.preventDefault(); // Prevent the default form submission

        var person_type = $('#person_type').val();

        // Gather form data
        var formData = {
            person_type: $('#person_type').val(),
            status: $('#status').val(),
            _token: '{{ csrf_token() }}'
        };

        // Update URL dynamically
        var url = "{{ route('person_type_update', $PersonType->id) }}"; // Update route as necessary

        if (person_type !== '') {

            $.ajax({
                url: url,
                method: 'PUT',
                data: formData,

                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Person Type updated successfully!',
                        timer: 1500,
                    }).then(() => {
                        window.location.href = "{{ route('person_type') }}";
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
        } else {
            if (person_type === '') {
                sweetalert("form_alert");
                $('#persontypeerror').text('Person Type is required.');
            } else {
                $('#persontypeerror').text('');
            }
        }

    });
</script>

@endsection