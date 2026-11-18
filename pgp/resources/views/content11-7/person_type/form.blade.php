@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Settings/</span>Person Type</h4>
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
                    @if(isset($acc_year))
                        @method('PUT')
                    @endif
                    <div class="row mb-3">
                        <div class="col-4">
                            <div class="mb-3 mt-2 d-flex align-items-center">
                                <label class="form-label me-2" style="width:100px;" for="person_type">Person </label>
                                <input type="text" class="form-control" name="person_type" id="person_type">
                            </div>
                            <div id="persontypeerror" class="text-danger"></div>
                        </div>

                        <div class="col-4">
                            <div class="mb-3 mt-2 d-flex align-items-center">
                                <label class="form-label me-2 mt-2" style="width:100px;" for="status">Status</label>
                                <select class="form-select" name="status" id="status">
                                    <option value="">Select Status</option>
                                    <option value="1" selected>Active</option>
                                    <option value="2">InActive</option>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="btn-float">
                        <button type="button" class="btn btn-danger"
                            onclick="window.location.href='{{ route('person_type') }}'">Cancel</button>
                        <!-- <button type="submit" class="btn btn-primary">Submit</button> -->
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <!-- {{ isset($acc_year) ? 'Update' : 'Submit' }} -->
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

        // Validate the form
        // let isValid = validateForm();
        // if (!isValid) {
        //     return false;
        // }

        var regex = /^[A-Za-z]*$/;
        var value = $('#person_type').val();

        // Gather form data
        var formData = {
            person_type: $('#person_type').val(),
            status: $('#status').val(),
            _token: '{{ csrf_token() }}'
        };

        var person_type = $('#person_type').val();

        // Determine method and URL based on whether $acc_year is set
        var method = "POST";
        var url = "{{route('person_type_store')}}";

        if (person_type !== '') {

            $.ajax({
                url: url,
                method: method,
                data: formData,

                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Person create successfully!',
                        timer: 1500, // Time in milliseconds (1500ms = 1.5 seconds)
                    }).then(() => {
                        window.location.href = "{{ route('person_type')}}";
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
        }else {
            if (person_type === '') {
                $('#persontypeerror').text('Person Type is required.');
                sweetalert("form_alert");
            } else {
                $('#persontypeerror').text('');
            }
        }
    });

    function validateForm() {
        let isValid = true;
        let accyear = document.getElementById('acc_year');
        let accerr = document.getElementById('acc_err');

        if (accyear.value.trim() === '') {
            accerr.textContent = 'Academic Year must not me empty';
            isValid = false;
        }

        return isValid;
    }
</script>

@endsection