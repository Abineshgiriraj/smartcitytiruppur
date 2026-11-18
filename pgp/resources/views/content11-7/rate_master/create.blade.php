@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Port Operating System/</span>Rate Master</h4>
    </div>
</div>

<style>
    /* btn style */
    .btn-right {
        float: right;
    }
</style>

@php
$get_vehicle_type = get_vehicle_type();

$get_person_type = get_person_type();

$get_access_gate = get_access_gate();
@endphp
<!-- Basic Layout -->
<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-body">
                <form id="rate_master" method="POST">
                    @csrf

                    <div class="row mb-3">

                        <label for="access_gate" class="col-sm-2 col-form-label mt-3">Access Gate</label>
                        <div class="col-sm-4 mt-3">
                            <select class="form-select" name="access_gate" id="access_gate">
                                <option value="">Select Access Gate</option>
                                @foreach ($get_access_gate as $access_gate)
                                <option value="{{$access_gate->unique_id}}">{{$access_gate->access_gate}}</option>
                                @endforeach
                            </select>
                            <div id="access_gateError" class="text-danger"></div>
                        </div>

                        <label for="allowed_type" class="col-sm-2 col-form-label mt-3">Allowed Type</label>
                        <div class="col-sm-4 mt-3">
                            <select class="form-select" id="allowed_type" name="allowed_type"
                                onchange="allowed_types()">
                                <option value="">Select Allowed Type</option>
                                <option value="Vehicle">Vehicle</option>
                                <option value="Person">Person</option>
                            </select>
                            <div id="allowedtypeError" class="text-danger"></div>
                        </div>

                        <label for="vehicle_type" class="col-sm-2 col-form-label mt-3 vehicle"
                            style="display:none;">Type Of Vehicle</label>
                        <div class="col-sm-4 mt-3 vehicle" style="display:none;">
                            <select class="form-select" class="vehicle_type" id="vehicle_type">
                                <option value="select">Select Vehicle</option>
                                @foreach ($get_vehicle_type as $vehicle_type)
                                <option value="{{$vehicle_type->unique_id}}">{{$vehicle_type->cargo}}
                                </option>
                                @endforeach
                            </select>

                            <div id="vehicleError" class="text-danger"></div>

                        </div>

                        <label for="person_type" class="col-sm-2 col-form-label mt-3 person"
                            style="display:none;">Person Type</label>
                        <div class="col-sm-4 mt-3 person" style="display:none;">
                            <select class="form-select" id="person_type" name="person_type">
                                <option value="">Select Person Type</option>
                                @foreach ($get_person_type as $person_type)
                                <option value=" {{$person_type->unique_id}}">{{$person_type->person_type}}
                                </option>
                                @endforeach
                            </select>
                            <div id="personError" class="text-danger"></div>

                        </div>

                        <label for="validity" class="col-sm-2 col-form-label mt-3">Validate</label>
                        <div class="col-sm-4 mt-3">
                            <select class="form-select" name="validity" id="validity">
                                <option value="selected">Select Validity Type</option>
                                <option value="Per Day">Per Day</option>
                                <option value="Per Month">Per Month</option>
                                <option value="Half Year">Half Year</option>
                                <option value="Year">Year</option>
                            </select>

                            <div id="validityError" class="text-danger"></div>

                        </div>

                        <label for="rate" class="col-sm-2 col-form-label mt-3">Rate</label>
                        <div class="col-sm-4 mt-3">
                            <input type="text" class="form-control" id="rate" name="rate">
                            <div id="rateError" class="text-danger"></div>

                        </div>

                        <label for="penalty_per_hour" class="col-sm-2 col-form-label mt-3">Penalty Per Hour</label>
                        <div class="col-sm-4 mt-3">
                            <input type="text" class="form-control" id="penalty_per_hour" name="penalty_per_hour">
                            <div id="penaltyPerHourError" class="text-danger"></div>

                        </div>

                        <label for="penalty_per_day" class="col-sm-2 col-form-label mt-3">Penalty Per Day</label>
                        <div class="col-sm-4 mt-3">
                            <input type="text" class="form-control" id="penalty_per_day" name="penalty_per_day">
                            <div id="penaltyPerDayError" class="text-danger"></div>

                        </div>

                        <label for="approval" class="col-sm-2 col-form-label mt-3">Approval</label>
                        <div class="col-sm-4 mt-3">
                            <input type="text" class="form-control" id="approval" name="approval">
                            <div id="approvalError" class="text-danger"></div>

                        </div>

                        <label for="status" class="col-sm-2 col-form-label mt-3">Status</label>
                        <div class="col-sm-4 mt-3">
                            <select class="form-select" id="status" name="status">
                                <option value="1">Active</option>
                                <option value="2">In Active</option>
                            </select>
                        </div>

                    </div>


                    <div class="row">
                        <div class="btn-float">
                            <button type="button" class="btn btn-danger"
                                onclick="window.location.href='{{ route('rate_master')}}'">Cancel</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
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
    function updateLabel() {
        const transactionType = document.getElementById('transaction_type').value;
        const dynamicLabel = document.getElementById('dynamic_label');
        const dynamicLabel_1 = document.getElementById('dynamic_label_1');

        if (transactionType === 'transaction_type_1') {
            dynamicLabel.textContent = "Export No (IA/EA No)";
            dynamicLabel_1.textContent = 'Export Name';
        } else {
            dynamicLabel.textContent = "Import No (IA/EA No)";
            dynamicLabel_1.textContent = 'Import Name';
        }
    }

    function allowed_types() {

        var allowed_type = $('#allowed_type').val();
        var person_type = $('.person').val();
        var vehicle_type = $('.vehicle').val();


        if (allowed_type == 'Vehicle') {
            $('.vehicle').show();
            $('.person').hide();

        } else if (allowed_type == 'Person') {
            $('.person').show();
            $('.vehicle').hide();
        } else {
            $('.person').hide();
            $('.vehicle').hide();
        }

    }

    $(document).ready(function() {

        // alert('');
        $('#rate_master').on('submit', function(e) {

            e.preventDefault();

            var allowed_type = $('#allowed_type').val();




            var form_data = {
                access_gate: $('#access_gate').val(),
                allowed_type: $('#allowed_type').val(),
                vehicle_type: $('#vehicle_type').val(),
                person_type: $('#person_type').val(),
                validity: $('#validity').val(),
                rate: $('#rate').val(),
                penalty_per_hour: $('#penalty_per_hour').val(),
                penalty_per_day: $('#penalty_per_day').val(),
                approval: $('#approval').val(),
                status: $('#status').val(),
                _token: '{{csrf_token()}}',
            }

            // Check if all required fields are filled
            if (form_data.access_gate && form_data.allowed_type &&
                (form_data.allowed_type === 'Vehicle' ? form_data.vehicle_type : true) &&
                (form_data.allowed_type === 'Person' ? form_data.person_type : true) &&
                form_data.validity && form_data.rate && form_data.penalty_per_hour && form_data
                .penalty_per_day && form_data.approval) {


                $.ajax({
                    type: "POST",
                    url: "{{route('rate_master.store')}}",
                    data: form_data,
                    success: function(response) {
                        const redirecturl = "{{route('rate_master')}}";
                        sweetalert("create", redirecturl);
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }

                });
            } else {
                if (form_data.access_gate === '') {
                    $('#access_gateError').text('Access Gate is required.');
                } else {
                    $('#access_gateError').text('');
                }

                if (form_data.allowed_type === '') {
                    $('#allowedtypeError').text('Allowed Type is required.');
                } else {
                    $('#allowedtypeError').text('');
                }

                if (form_data.allowed_type === 'Vehicle' && !form_data.vehicle_type) {
                    $('#vehicleError').text('Vehicle Type is required.');
                } else {
                    $('#vehicleError').text('');
                }

                if (form_data.allowed_type === 'Person' && !form_data.person_type) {
                    $('#personError').text('Person Type is required.');
                } else {
                    $('#personError').text('');
                }
                if (form_data.validity === '') {

                    $('#validityError').text('Validity is required.');
                } else {
                    $('#validityError').text('');
                }
                if (form_data.rate === '') {
                    $('#rateError').text('Rate is required.');
                } else {
                    $('#rateError').text('');
                }
                if (form_data.penalty_per_hour === '') {
                    $('#penaltyPerHourError').text('Penalty Per Hour is required.');
                } else {
                    $('#penaltyPerHourError').text('');
                }
                if (form_data.penalty_per_day === '') {
                    $('#penaltyPerDayError').text('Penalty Per Day is required.');
                } else {
                    $('#penaltyPerDayError').text('');
                }
                if (form_data.approval === '') {
                    $('#approvalError').text('Approval is required.');
                } else {
                    $('#approvalError').text('');
                }
            }

            if ((form_data.access_gate === '') && (form_data.allowed_type === '') && (form_data
                    .vehicle_types ===
                    '') && (form_data.person_types === '') && (form_data.validity === '') && (form_data
                    .approval === '')) {
                sweetalert("form_alert");

            }
        });
    });
</script>

@endsection