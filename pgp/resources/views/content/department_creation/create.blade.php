@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')

<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Settings/</span>Port Department</h4>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form id="port_department" method="post">
            @csrf
            @if(isset($port_department))
            @method('PUT')
            @endif

            <div class=" row">
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="department">Department</label>
                    <input type="text" class="form-control" id="department" name="department" />
                    <div id="departmentError" class="text-danger"></div>
                </div>
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="description">Description</label>
                    <textarea name="description" id="description" class="form-control">

                    </textarea>
                    <!-- <div id="statusError" class="form-text text-danger"></div> -->
                </div>

                <div class="mb-3 col-md-3">
                    <label class="form-label" for="status">Active Status</label>
                    <select class="form-control" id="status" name="status">
                        <option value="Active">Active</option>
                        <option value="In Active">In Active</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="btn-float">
                    <button type="button" class="btn btn-danger"
                        onclick="window.location.href='{{ route('department') }}'">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>

<script>
// function validateForm() {
//     let isValid = true;
//     let usertypeInput = document.getElementById('usertype');
//     let usertypeError = document.getElementById('usertypeError');

//     // Clear previous error messages
//     usertypeError.textContent = '';

//     // Validate usertype Name
//     if (usertypeInput.value.trim() === '') {
//         usertypeError.textContent = 'User Type is required.';
//         Swal.fire({
//             icon: 'error',
//             title: 'Validation Error',
//             text: 'User Type is required.',
//         });
//         isValid = false;
//     } else if (!/^[A-Za-z\s]+$/.test(usertypeInput.value.trim())) {
//         usertypeError.textContent = 'User Type must contain only alphabetic characters.';
//         Swal.fire({
//             icon: 'error',
//             title: 'Validation Error',
//             text: 'User Type must contain only alphabetic characters.',
//         });
//         isValid = false;
//     } else if (usertypeInput.value.length < 3) {
//         usertypeError.textContent = 'User Type must be at least 3 characters long.';
//         Swal.fire({
//             icon: 'error',
//             title: 'Validation Error',
//             text: 'User Type must be at least 3 characters long.',
//         });
//         isValid = false;
//     }

//     return isValid;
// }
$('#port_department').on('submit', function(e) {

    var department = $('#department').val();

    // alert(dear)

    e.preventDefault();

    var formdata = {
        department: $('#department').val(),
        description: $('#description').val(),
        _token: '{{csrf_token()}}',
    }

    if (formdata.department !== '') {

        $.ajax({
            url: '{{route("department.store")}}',
            type: "POST",
            data: formdata,

            success: function(reaponse) {
                const redirectUrl = "{{route('department')}}";
                sweetalert("create", redirectUrl);
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });

    } else {
        if (formdata.department === '') {
            $('#departmentError').text('Deparmtent is required.');
        } else {
            $('#departmentError').text('');
        }
    }
    if (formdata.department === '') {
        sweetalert("form_alert");
    }

});
</script>

@endsection