@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Settings/</span> Cargo Creation</h4>
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
                <form id="cargocreationform">
                    @csrf
                    @if(isset($acc_year))
                        @method('PUT')
                    @endif

                    <div class="row mb-3">
                        <!-- Vehicle Type (Input) -->
                        <label for="cargo" class="col-sm-2 col-form-label mt-3">Vehicle Type</label>
                        <div class="col-sm-4 mt-3">
                            <input type="text" class="form-control" name="cargo" id="cargo">
                            <div id="cargoError" class="text-danger"></div>
                        </div>

                        <!-- Status (Select) -->
                        <label for="status" class="col-sm-2 col-form-label mt-3">Status</label>
                        <div class="col-sm-4 mt-3">
                            <select class="form-select" name="status" id="status">
                                <option value="">Select Status</option>
                                <option value="1" selected>Active</option>
                                <option value="2">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="btn-float mt-3">
                        <button type="button" class="btn btn-danger" onclick="window.location.href='{{ route('cargo_creation') }}'">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
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
$('#cargocreationform').submit(function(event) {
    event.preventDefault(); // Prevent the default form submission

    var cargo = $('#cargo').val();

    // Validate the form
    // let isValid = validateForm();
    // if (!isValid) {
    //     return false;
    // }

    // Gather form data
    var formData = {
        cargo: $('#cargo').val(),
        status: $('#status').val(),
        _token: '{{ csrf_token() }}'
    };

    // Determine method and URL based on whether $acc_year is set
    var method = "POST";
    var url = "{{route('cargo_creation_store')}}";

    if (cargo !== '') {

        $.ajax({
            url: url,
            method: method,
            data: formData,

            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Cargo create successfully!',
                    timer: 1500, // Time in milliseconds (1500ms = 1.5 seconds)
                }).then(() => {
                    window.location.href = "{{ route('cargo_creation')}}";
                });
            },

            error: function(xhr) {
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
        if (cargo === '') {
            $('#cargoError').text('Cargo is required.');
            sweetalert("form_alert");
        } else {
            $('#cargoError').text('');
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