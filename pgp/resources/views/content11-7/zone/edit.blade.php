@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')

<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Common Module/</span> Zone creation</h4>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form id="zoneForm" method="POST" action="{{ route('zone.update', $zone->id) }}" method="POST">
            <!-- onsubmit="return submitForm(event);" -->
            @csrf
            @method('PUT')

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
                </div>
            </div>
            <div class="row">
                <div class="btn-float">
                    <button type="button" class="btn btn-danger"
                        onclick="window.location.href='{{ url('/zone') }}'">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>

<script>
$('#zoneForm').on('submit', function(event) {
    // alert();
    event.preventDefault(); // Prevent the default form submission

    let formData = $(this).serialize();

    $.ajax({
        url: $(this).attr('action'),
        method: "POST",
        data: formData,
        success: function(response) {
            const redirectUrl = '{{ route("zone.index") }}';
            sweetalert('update', redirectUrl); // Redirect after successful addition
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                sweetalert('already');
            }
        }


    });

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