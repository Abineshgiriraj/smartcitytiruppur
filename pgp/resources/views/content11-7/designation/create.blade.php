@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')

<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4><span class="text-muted fw-light">Common Module/</span> Designation Creation</h4>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form id="designationForm" method="post" onsubmit="return submitForm(event);">
            @csrf

            <div class="row">
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="designation">Designation Name</label>
                    <input type="text" class="form-control" id="designation" name="designation"
                        value="{{ old('designation') }}" />
                    <div id="designationError" class="text-danger"></div>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label" for="remark">Remarks</label>
                    <textarea id="remark" class="form-control" name="remark">{{ old('remark') }}</textarea>
                    <!-- <div id="remarkError" class="text-danger"></div> -->
                </div>
            </div>
            <div class="row">
                <div class="btn-float">
                    <button type="button" class="btn btn-danger"
                        onclick="window.location.href='{{ route('designation.index') }}'">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>


<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>
<script>
function submitForm(event) {
    event.preventDefault();

    let isValid = validateForm();
    if (!isValid) {
        return false;
    }

    let formData = {
        designation: $('#designation').val(),
        remark: $('#remark').val(),
        _token: '{{ csrf_token() }}'
    };

    let url = '{{ route("designation.store") }}';


    $.ajax({
        url: url,
        method: 'POST',
        data: formData,
        success: function(response) {
            sweetalert('create');
            // Swal.fire('Success', 'Designation created successfully!', 'success')
            //     .then(() => {
            window.location.href = "{{ route('designation.index') }}";
            // });
        },
        error: function(xhr) {
            let errors = xhr.responseJSON.errors;
            console.log(errors);
            if (errors.designation) {
                $('#designationError').text(errors.designation[0]);
            }
            // if (errors.remark) {
            //     $('#remarkError').text(errors.remark[0]);
            // }
        }
    });

    return false;
}

function validateForm() {
    let isValid = true;
    let designationInput = document.getElementById('designation');
    let designationError = document.getElementById('designationError');
    // let remark = document.getElementById('remark');
    // let remarkError = document.getElementById('remarkError');

    // Clear previous error messages
    designationError.textContent = '';
    // remarkError.textContent = '';

    // Validate Designation Name
    if (designationInput.value.trim() === '') {
        designationError.textContent = 'Designation Name is required.';
        isValid = false;
    } else if (!/^[A-Za-z\s]+$/.test(designationInput.value.trim())) {
        designationError.textContent = 'Designation Name must contain only alphabetic characters.';
        isValid = false;
    } else if (designationInput.value.length < 3) {
        designationError.textContent = 'Designation Name must be at least 3 characters long.';
        isValid = false;
    }

    // Validate Remarks
    // if (remark.value.trim() === '') {
    //     remarkError.textContent = 'Remarks is required.';
    //     isValid = false;
    // } else if (!/^[A-Za-z\s]+$/.test(remark.value.trim())) {
    //     remarkError.textContent = 'Remarks must contain only alphabetic characters.';
    //     isValid = false;
    // }

    return isValid;
}
</script>

@endsection