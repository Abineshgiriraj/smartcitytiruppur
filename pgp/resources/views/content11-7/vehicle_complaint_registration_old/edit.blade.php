@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Vehicle Complaint Registration')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4><span class="text-muted fw-light">Settings/</span>Edit Vehicle Complaint Registration</h4>
        <!-- <a href="{{ route('vehicle_complaint_registration_list') }}" class="btn btn-secondary">Back to List</a> -->
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form id="district_form"
            action="{{ route('vehicle_complaint_registration.update', $complaint_registration->id) }}" method="POST">
            @csrf
            @method('PUT')
            <!-- This method is required for updating resources -->
            <div class="row mb-3 com-form">
                <div class="col-md-4 mb-3">
                    <label for="complaint_type" class="form-label">Complaint Type</label>
                    <select class="form-select" id="complaint_type" name="complaint_type" onchange="allowed_types()">
                        @foreach ($complaintTypes as $type)
                        <option value="{{ $type->unique_id }}"
                            {{ $complaint_registration->complaint_type == $type->unique_id ? 'selected' : '' }}>
                            {{ $type->complaint_type }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 mb-3" id="personField" style="display:none;">
                    <label for="person" class="form-label">Person</label>
                    <input type="text" class="form-control" id="person" name="person"
                        value="{{ old('person', $complaint_registration->person) }}">
                </div>

                <div class="col-md-4 mb-3" id="vehicleNoField" style="display:none;">
                    <label for="vehicle_no" class="form-label">Vehicle No</label>
                    <input type="text" class="form-control" id="vehicle_no" name="vehicle_no"
                        value="{{ old('vehicle_no', $complaint_registration->vehicle_no) }}">
                </div>

                <div class="col-md-4 mb-3" id="userField" style="display:none;">
                    <label for="user" class="form-label">Firm User</label>
                    <input type="text" class="form-control" id="user" name="user"
                        value="{{ old('user', $complaint_registration->user) }}" />
                </div>

                <div class="col-md-4 mb-3" id="equipmentField" style="display:none;">
                    <label for="equipment" class="form-label">Equipment</label>
                    <input type="text" class="form-control" id="equipment" name="equipment"
                        value="{{ old('equipment', $complaint_registration->equipment) }}" />
                </div>

                <div class="col-md-4 mb-3" id="materialField" style="display:none;">
                    <label for="material" class="form-label">Material</label>
                    <input type="text" class="form-control" id="material" name="material"
                        value="{{ old('material', $complaint_registration->material) }}" />
                </div>

                <div class="col-md-4 mb-3" id="regNoField" style="display:none;">
                    <label for="reg_no" class="form-label">Reg No</label>
                    <input type="text" class="form-control" id="reg_no" name="reg_no"
                        value="{{ old('reg_no', $complaint_registration->reg_no) }}" />
                </div>

                <div class="col-md-4 mb-3" id="regDateField" style="display:none;">
                    <label for="reg_date" class="form-label">Reg Date</label>
                    <input type="date" class="form-control" id="reg_date" name="reg_date"
                        value="{{ old('reg_date', $complaint_registration->reg_date) }}" />
                </div>

                <div class="col-md-4 mb-3" id="dlNoField" style="display:none;">
                    <label for="dl_no" class="form-label">DL No</label>
                    <input type="text" class="form-control" id="dl_no" name="dl_no"
                        value="{{ old('dl_no', $complaint_registration->dl_no) }}" />
                </div>

                <div class="col-md-4 mb-3" id="nameField" style="display:none;">
                    <label for="name" class="form-label">Driver Name</label>
                    <input type="text" class="form-control" id="name" name="name"
                        value="{{ old('name', $complaint_registration->name) }}" />
                </div>

                <div class="col-md-4 mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="active" {{ $complaint_registration->status == 'active' ? 'selected' : '' }}>
                            Active
                        </option>
                        <option value="inactive" {{ $complaint_registration->status == 'inactive' ? 'selected' : '' }}>
                            Inactive
                        </option>
                    </select>
                </div>
            </div>

            <!-- <div class="btn-float">
                <button type="button" class="btn btn-danger"
                    onclick="window.location.href='{{ route('vehicle_complaint_registration_list') }}'">Cancel</button>
                <button type="submit"
                    class="btn btn-primary">{{ isset($complaint_registration) ? 'Update' : 'Submit' }}</button>
            </div> -->

            <div class="btn-float">
                <button type="button" class="btn btn-danger"
                    onclick="window.location.href='{{ route('vehicle_complaint_registration_list') }}'">Cancel</button>
                <button type="submit" class="btn btn-primary">Update </button>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>

<script>
function allowed_types() {
    var complaint_type = $('#complaint_type').val();
    // alert(complaint_type);


    // Hide all fields initially
    $('#personField, #vehicleNoField, #userField, #equipmentField, #materialField, #regNoField, #regDateField, #dlNoField, #nameField')
        .hide();

    // Show fields based on the selected complaint type
    if (complaint_type == '6701004426fc746864') {
        $('#personField').show();
        $('#regNoField').show();
        $('#regDateField').show();
    } else if (complaint_type == '670100869632051306') {
        $('#vehicleNoField').show();
        $('#regNoField').show();
        $('#regDateField').show();
        $('#dlNoField').show();
        $('#nameField').show();
    } else if (complaint_type == '6701046619b9c18585') {
        $('#userField').show();
        $('#regNoField').show();
        $('#regDateField').show();
    } else if (complaint_type == '670109b3c36bf25361') {
        $('#equipmentField').show();
        $('#regNoField').show();
        $('#regDateField').show();
    } else if (complaint_type == '67010a8da2b0f63483') {
        $('#regNoField').show();
        $('#regDateField').show();
        $('#materialField').show();
    }
}

// Call allowed_types on page load to set the initial state of fields
$(document).ready(function() {
    // $('#complaint_type').change(allowed_types);
    allowed_types();
});


$('#district_form').on('submit', function(event) {
    event.preventDefault(); // Prevent the default form submission
    // clearFields();
    let formData = $(this).serialize();

    $.ajax({
        url: $(this).attr('action'),
        method: "POST",
        data: formData,
        success: function(response) {
            const redirectUrl = '{{ route("vehicle_complaint_registration_list") }}';
            sweetalert('update', redirectUrl); // Redirect after successful addition
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                sweetalert('already');
            }
        }


    });

});
// // Function to clear relevant fields
// function clearFields() {
//     $('#personField, #vehicleNoField, #userField, #equipmentField, #materialField, #regNoField, #regDateField, #dlNoField, #nameField')
//         .find('input, select').val('');
// }
// });
</script>


@endsection