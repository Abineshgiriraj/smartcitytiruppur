@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')

<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Common Module/</span> Zone creation</h4>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form id="zoneForm" method="post">
            @csrf
            @if(isset($zone))
            @method('PUT')
            @endif

            <div class="row">
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="zone">Zone Name</label>
                    <input type="text" class="form-control" id="zone" name="zone"
                        value="{{ old('zone', $zone->zone ?? '') }}" />
                    <div id="zoneError" class="text-danger"></div>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label" for="remarks">Remarks</label>
                    <textarea id="remarks" class="form-control"
                        name="remarks">{{ old('remarks', $zone->remarks ?? '') }}</textarea>
                    <div id="remarksError" class="text-danger"></div>
                </div>
            </div>
            <div class="row">
                <div class="btn-float">
                    <button type="button" class="btn btn-danger"
                        onclick="window.location.href='{{ url('/zone') }}'">Cancel</button>
                    <!-- <button type=" submit"
                        class="btn btn-primary">{{ isset($zone) ? 'Update' : 'Submit' }}</button> -->
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>


<!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>


<script>
// new change


$('#zoneForm').on('submit', function(e) {
    e.preventDefault(); // Prevent the default form submission
    // alert($('#zone').val());
    var formData = {
        zone: $('#zone').val(),
        remarks: $('#remarks').val(),


        created_at: new Date().toISOString(), // Example: current date-time
        updated_at: new Date().toISOString(),
        _token: '{{ csrf_token() }}'
    };

    console.log('Form Data:', formData); // Log the form data

    if ((formData.zone != '') && (formData.district != '')) {

        $.ajax({
            url: "{{ route('zone.store') }}",
            method: "POST",
            data: formData,
            success: function(response) {

                const redirectUrl = '{{ route("zone.index") }}';
                sweetalert('create', redirectUrl);
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Already Exist!',
                        showConfirmButton: true,
                        timer: 1000,
                        timerProgressBar: true
                    });
                }

            }

        });
    } else {
        sweetalert("form_alert");

    }
});






function validateForm() {
    let isValid = true;
    let zoneInput = document.getElementById('zone');
    let zoneError = document.getElementById('zoneError');

    // Clear previous error messages
    zoneError.textContent = '';

    // Validate Zone Name
    if (zoneInput.value.trim() === '') {
        zoneError.textContent = 'Zone Name is required.';
        isValid = false;
    } else if (!/^[A-Za-z\s]+$/.test(zoneInput.value.trim())) {
        zoneError.textContent = 'Zone Name must contain only alphabetic characters.';
        isValid = false;
    } else if (zoneInput.value.length < 3) {
        zoneError.textContent = 'Zone Name must be at least 3 characters long.';
        isValid = false;
    }


    return isValid;
}
</script>

@endsection