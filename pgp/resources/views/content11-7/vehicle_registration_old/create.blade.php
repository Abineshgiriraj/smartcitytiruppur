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
        <h4 class=""><span class="text-muted fw-light">User Firms/</span>Vehicle Registration</h4>
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
                <ul class="nav nav-pills nav-justified form-wizard-header mb-4">
                    <li class="nav-item">
                        <a href="#vehiclereg" data-bs-toggle="tab" class="nav-link rounded-0 pt-2 pb-2 active">
                            <span class="d-sm-inline">Vehicle Details</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#document" data-bs-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                            <span class="d-sm-inline">Document Details</span>
                        </a>
                    </li>
                </ul>


                <div class="tab-content b-0 mb-0">
                    <div class="tab-pane active" id="vehiclereg">


                        <div class="row">
                            <!-- Vehicle Registration Number -->
                            <div class="col-md-6">
                                <div class="mb-3 mt-2 d-flex align-items-center">
                                    <label class="form-label me-3" style="width: 250px;" for="vech_reg_no">Vehicle
                                        Registration
                                        Number:</label>
                                    <input type="text" class="form-control" id="vech_reg_no" name="vech_reg_no" />
                                </div>
                            </div>

                            <!-- Vehicle Type -->
                            <div class="col-md-6">
                                <div class="mb-3 mt-2 d-flex align-items-center">
                                    <label class="form-label me-3" style="width: 250px;" for="status">Vehicle
                                        Type:</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="1">Heavy</option>
                                        <option value="2">Light</option>
                                        <option value="3">Two-Wheeler</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Owner's Name -->
                            <div class="col-md-6">
                                <div class="mb-3 mt-2 d-flex align-items-center">
                                    <label class="form-label me-3" style="width: 250px;" for="owner_name">Owner's
                                        Name:</label>
                                    <input type="text" class="form-control" id="owner_name" name="owner_name" />
                                </div>
                            </div>

                            <!-- Contact Number -->
                            <div class="col-md-6">
                                <div class="mb-3 mt-2 d-flex align-items-center">
                                    <label class="form-label me-3" style="width: 250px;" for="contact_number">Contact
                                        Number:</label>
                                    <input type="text" class="form-control" id="contact_number" name="contact_number" />
                                </div>
                            </div>

                            <!-- Insurance Details -->


                            <!-- Insurance Valid From -->


                            <!-- Insurance Valid To -->


                            <!-- Insurance Certificate Upload -->


                            <!-- Fitness Certificate -->


                            <!-- Fitness Valid From -->



                        </div>


                        <div class="row">
                            <div class="btn-float">
                                <button type="button" class="btn btn-primary"
                                    onclick="$('.nav-pills a[href=\'#document\']').tab('show')">Next</button>
                            </div>
                        </div>
                    </div>
                    <!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->

                    <!-- documment tab -->
                    <div class="tab-pane" id="document">

                        <!-- Fitness Certificate Upload -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3 mt-2 d-flex align-items-center">

                                    <label class="form-label me-3" style="width:250px;">Certificate Type</label>
                                    <select class="form-select" onchange="change()" name="selected_value"
                                        id="selected_value" for="select_option">
                                        <option value="selected">Select </option>
                                        <option value="Fitness">Fitness</option>
                                        <option value="Insurance">Insurance</option>
                                        <option value="PUC">PUC</option>
                                        <option value="RC">RC</option>
                                    </select>
                                </div>
                            </div>
                            <div id="fitness" style="display:none;">
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="mb-3 mt-2 d-flex align-items-center">
                                            <label class="form-label me-3" style="width: 250px;"
                                                for="fitness_certificate">Fitness
                                                Certificate:</label>
                                            <input type="text" class="form-control" id="fitness_certificate"
                                                name="fitness_certificate" />
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3 mt-2 d-flex align-items-center">
                                            <label class="form-label me-3" style="width: 250px;"
                                                for="fitness_valid_to">Fitness
                                                Valid
                                                Up To:</label>
                                            <input type="date" class="form-control" id="fitness_valid_to"
                                                name="fitness_valid_to" />
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3 mt-2 d-flex align-items-center">
                                            <label class="form-label me-3" style="width: 250px;"
                                                for="fitness_certificate_upload">Fitness
                                                Certificate Upload:</label>
                                            <input type="file" class="form-control" id="fitness_certificate_upload"
                                                name="fitness_certificate_upload" />
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div id="insurance" style="display:none;">
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="mb-3 mt-2 d-flex align-items-center">
                                            <label class="form-label me-3" style="width: 250px;"
                                                for="insurance_details">Insurance
                                                Details:</label>
                                            <input type="text" class="form-control" id="insurance_details"
                                                name="insurance_details" />
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3 mt-2 d-flex align-items-center">
                                            <label class="form-label me-3" style="width: 250px;"
                                                for="insurance_valid_to">Insurance
                                                valid UpTo
                                                :</label>
                                            <input type="date" class="form-control" id="insurance_valid_to"
                                                name="insurance_valid_to" />
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3 mt-2 d-flex align-items-center">
                                            <label class="form-label me-3" style="width: 250px;"
                                                for="insurance_certificate">Insurance
                                                Certificate Upload:</label>
                                            <input type="file" class="form-control" id="insurance_certificate"
                                                name="insurance_certificate" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="puc" style="display:none;">
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="mb-3 mt-2 d-flex align-items-center">
                                            <label class="form-label me-3" style="width: 250px;"
                                                for="puc_certificate">PUC
                                                Certificate:</label>
                                            <input type="text" class="form-control" id="puc_certificate"
                                                name="puc_certificate" />
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3 mt-2 d-flex align-items-center">
                                            <label class="form-label me-3" style="width: 250px;" for="puc_valid_to">PUC
                                                Valid UP
                                                To:</label>
                                            <input type="date" class="form-control" id="puc_valid_to"
                                                name="puc_valid_to" />
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3 mt-2 d-flex align-items-center">
                                            <label class="form-label me-3" style="width: 250px;"
                                                for="puc_certificate_upload">PUC
                                                Certificate Upload:</label>
                                            <input type="file" class="form-control" id="puc_certificate_upload"
                                                name="puc_certificate_upload" />
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div id="rc" style="display:none;">
                                <div class="col-md-6">
                                    <div class="mb-3 mt-2 d-flex align-items-center">
                                        <label class="form-label me-3" style="width: 250px;"
                                            for="rc_upload">Registration
                                            Certificate
                                            (RC) Upload:</label>
                                        <input type="file" class="form-control" id="rc_upload" name="rc_upload" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 text-end">
                                <button type="button  btn-lg" id="add" class="btn btn-primary">Add</button>

                            </div>
                        </div>
                        <div class="row">

                        </div>

                        <div class="row mt-4">
                            <div id="dataTableContainer" class="table-responsive mt-4">

                                <table id="example" class="table table-striped table-bordered" style="width:100%">

                                        <thead class="thead-light">
                                            <th>S.No</th>
                                            <th>Certificate Type</th>
                                            <th>Certificate NO</th>
                                            <th>Valid Up To</th>
                                            <th>Attached Proof</th>
                                        </thead>
                                        <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class=" row">
                            <div class="btn-float">
                                <button type="button" class="btn btn-danger"
                                    onclick="window.location.href='{{ route('vehicle_registration') }}'">Cancel</button>
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
$('#add').on('click', function() {
    var selectedType = $('#selected_value').val();
    var newRow = `
        <div class="document-row">
            <input type="hidden" name="documents[][certificate_type]" value="${selectedType}" />
            <input type="text" class="form-control" name="documents[][certificate_no]" placeholder="Certificate No" />
            <input type="date" class="form-control" name="documents[][valid_up_to]" placeholder="Valid Up To" />
            <input type="file" class="form-control" name="documents[][attached_proof]" />
        </div>
    `;
    $('#dataTableContainer tbody').append(newRow);
});


   function submitForm(event) {
    event.preventDefault(); // Prevent the default form submission

    // Collect main form data
    var formData = new FormData(document.getElementById('usertypeForm'));

    // Loop through each document and append it to formData
    $('.document-row').each(function() {
        var certificateType = $(this).find('.certificate-type').val();
        var certificateNo = $(this).find('.certificate-no').val();
        var validUpTo = $(this).find('.valid-up-to').val();
        var attachedProof = $(this).find('.attached-proof')[0].files[0];

        formData.append('documents[][certificate_type]', certificateType);
        formData.append('documents[][certificate_no]', certificateNo);
        formData.append('documents[][valid_up_to]', validUpTo);
        if (attachedProof) {
            formData.append('documents[][attached_proof]', attachedProof);
        }
    });

    // Send the data using AJAX
    $.ajax({
        url: '{{ route('vehicle_registration.store') }}',
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            // Handle the response
            console.log(response);
        },
        error: function(xhr) {
            // Handle the error
            console.error(xhr.responseText);
        }
    });
}

</script>

@endsection
