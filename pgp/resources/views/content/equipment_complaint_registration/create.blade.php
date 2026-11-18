@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')

<style>
    .input {
        margin-left: 125px;
    }

    .pad1 {
        margin-left: 110px;
    }
</style>

<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Complaint /</span>Equipment Complaint Registration</h4>
    </div>
</div>



<div class="card">
    <div class="card-body">
        <form id="usertypeForm" method="post" onsubmit="return submitForm(event);">
            @csrf
            @if(isset($user_types))
            @method('PUT')
            @endif

            <div id="basicwizard">
                <!-- <ul class="nav nav-pills nav-justified form-wizard-header mb-4">
                    <li class="nav-item">
                        <a href="#vehiclereg" data-bs-toggle="tab" class="nav-link rounded-0 pt-2 pb-2 active">
                            <span class="d-sm-inline">Person Complaint Registration Details</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#document" data-bs-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                            <span class="d-sm-inline">Document Details</span>
                        </a>
                    </li>
                </ul> -->


                <div class="tab-content b-0 mb-0">
                    <div class="tab-pane active" id="vehiclereg">


                        <div class="row">
                            <!-- Vehicle Registration Number -->
                            <div class="col-md-6">
                                <div class="mb-3 mt-2 d-flex align-items-center">
                                    <label class="form-label me-3" style="width: 250px;" for="vech_reg_no">Vehicle
                                        NO</label>
                                    <input type="text" class="form-control" id="firms_user_name"
                                        name="firms_user_name" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3 mt-2 d-flex align-items-center">
                                    <label class="form-label me-3" style="width: 250px;" for="vech_reg_no">Reg.
                                        No</label>
                                    <input type="text" class="form-control" id="reg_no" name="reg_no" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 mt-2 d-flex align-items-center">
                                    <label class="form-label me-3" style="width: 250px;" for="vech_reg_no">Reg.Date
                                    </label>
                                    <input type="text" class="form-control" id="reg_no" name="reg_no" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 mt-2 d-flex align-items-center">
                                    <label class="form-label me-3" style="width: 250px;" for="vech_reg_no">DL NO
                                    </label>
                                    <input type="text" class="form-control" id="reg_date" name="reg_date" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3 mt-2 d-flex align-items-center">
                                    <label class="form-label me-3" style="width: 250px;" for="vech_reg_no">Driver
                                        Name</label>
                                    <input type="text" class="form-control" id="contact_person" name="contact_person" />
                                </div>
                            </div>






                        </div>



                        <div class="btn-float">
                            <button type="button" class="btn btn-danger"
                                onclick="window.location.href='{{ route('equipment_complaint_registration') }}'">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    </form>
</div>
</div>





<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}">
</script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>


<script>
    function change() {

        var fitness = $('#fitness').val();
        var insurance = $('#insurance').val();
        var puc = $('#puc').val();
        var rc = $('#rc').val();


        var selected_element = document.getElementById('selected_value');

        var selected_value = selected_element.value;


        // var dynamic_values = $('#dynamic_values').val();



        if (selected_value === 'Fitness') {

            // alert('1');
            $('#fitness').show();
            $('#add').show();
            $('#insurance').hide();
            $('#puc').hide();
            $('#rc').hide();

        } else if (selected_value === 'Insurance') {

            $('#insurance').show();
            $('#add').show();

            $('#puc').hide();
            $('#rc').hide();
            $('#fitness').hide();

        } else if (selected_value === 'PUC') {
            $('#puc').show();
            $('#add').show();

            $('#insurance').hide();
            $('#rc').hide();
            $('#fitness').hide();

        } else if (selected_value === 'RC') {
            $('#rc').show();
            $('#add').show();

            $('#insurance').hide();
            $('#puc').hide();
            $('#fitness').hide();
        }

    }

    function pdf() {

        var url = "resources/views/content/vehicle_registration/testpdf1.pdf";
        window.location.href = url;
    }
</script>

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
</script>

@endsection