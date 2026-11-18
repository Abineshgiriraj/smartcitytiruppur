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
                <form id="rate_master_edit" method="POST">
                    @csrf

                    <input type="hidden" id="rate_master_id" value="{{ $rate_master->id ?? '' }}">


                    <div class="row mb-3">
                        <label class="form-label col-sm-2" for="access_gate">Access Gate</label>
                        <div class="col-sm-4">
                            <select class="form-select" name="access_gate" id="access_gate">
                                <option value="">Select Access Gate</option>
                                @foreach ($get_access_gate as $access_gate)
                                    <option value="{{$access_gate->unique_id}}"
                                        {{$access_gate->unique_id == $rate_master->access_gate ? 'selected' : ''}}>
                                        {{$access_gate->access_gate}}
                                    </option>
                                @endforeach
                            </select>
                            <div id="usertypeError" class="text-danger"></div>
                        </div>

                        <label class="form-label col-sm-2" for="allowed_type">Allowed Type</label>
                        <div class="col-sm-4">
                            <select class="form-select" id="allowed_type" name="allowed_type"
                                onchange="allowed_types()">
                                <option value="">Select Allowed Type</option>
                                <option value="Vehicle" {{$rate_master->allowed_type == 'Vehicle' ? 'selected' : ''}}>
                                    Vehicle</option>
                                <option value="Person" {{$rate_master->allowed_type == 'Person' ? 'selected' : ''}}>
                                    Person</option>
                            </select>
                        </div>
                    </div>

                    <!-- <div id="vehicle" style="display:none;" class="row mb-3">
                        <label class="form-label col-sm-2" for="vehicle_type">Type of Vehicle</label>
                        <div class="col-sm-4">
                            <select class="form-select" id="vehicle_type" name="vehicle_type">
                                <option value="select">Select Vehicle</option>
                                @foreach ($get_vehicle_type as $vehicle_type)
                                    <option value="{{$vehicle_type->unique_id}}"
                                        {{$vehicle_type->unique_id == $rate_master->vehicle_type ? 'selected' : ''}}>
                                        {{$vehicle_type->cargo}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="person" style="display:none;" class="row mb-3">
                        <label class="form-label col-sm-2" for="person_type">Person Type</label>
                        <div class="col-sm-4">
                            <select class="form-select" id="person_type" name="person_type">
                                <option value="">Select Person Type</option>
                                @foreach ($get_person_type as $person_type)
                                    <option value="{{$person_type->unique_id}}"
                                        {{$person_type->unique_id == $rate_master->person_type ? 'selected' : ''}}>
                                        {{$person_type->person_type}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div> -->

                    <!-- Type Of Vehicle Section (Visible only if 'Vehicle' is selected) -->
                    <?php if ($rate_master->allowed_type === 'Vehicle'): ?>
                    <div class="row mb-3">
                        <label class="form-label col-sm-2" for="vehicle_type">Type Of Vehicle</label>
                        <div class="col-sm-4">
                            <select class="form-select" id="vehicle_type" name="vehicle_type">
                                <option value="select">Select Vehicle</option>
                                @foreach ($get_vehicle_type as $vehicle_type)
                                    <option value="{{$vehicle_type->unique_id}}" <?php        echo ($rate_master->vehicle_person_type == $vehicle_type->unique_id) ? 'selected' : ''; ?>>
                                        {{$vehicle_type->cargo}}
                                    </option>
                                @endforeach
                            </select>
                            <div id="usertypeError" class="text-danger"></div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Person Type Section (Visible only if 'Person' is selected) -->
                    <?php if ($rate_master->allowed_type === 'Person'): ?>
                    <div class="row mb-3">
                        <label class="form-label col-sm-2" for="access_gate">Access Gate</label>
                        <div class="col-sm-4">
                            <select class="form-select" id="person_type" name="person_type">
                                <option value="">Select Person Type</option>
                                @foreach ($get_person_type as $person_type)
                                    <option value="{{$person_type->unique_id}}" <?php echo ($rate_master->vehicle_person_type == $person_type->unique_id) ? 'selected' : ''; ?>>
                                        {{$person_type->person_type}}
                                    </option>
                                @endforeach
                            </select>
                            <div id="usertypeError" class="text-danger"></div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="row mb-3">
                        <label class="form-label col-sm-2" for="validity">Validity</label>
                        <div class="col-sm-4">
                            <select class="form-select" name="validity" id="validity">
                                <option value="">Select Validity Type</option>
                                <option value="Per Day" {{$rate_master->validity == 'Per Day' ? 'selected' : ''}}>Per
                                    Day</option>
                                <option value="Per Month" {{$rate_master->validity == 'Per Month' ? 'selected' : ''}}>
                                    Per Month</option>
                                <option value="Half Year" {{$rate_master->validity == 'Half Year' ? 'selected' : ''}}>
                                    Half Year</option>
                                <option value="Year" {{$rate_master->validity == 'Year' ? 'selected' : ''}}>Year
                                </option>
                            </select>
                        </div>

                        <label class="form-label col-sm-2" for="rate">Rate</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="rate" name="rate"
                                value="{{ $rate_master->rate }}">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="penalty_per_hour" class="col-sm-2 col-form-label">Penalty Per Hour</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="penalty_per_hour" name="penalty_per_hour"
                                value="{{ $rate_master->penalty_per_hour }}">
                            <div id="penaltyPerHourError" class="text-danger"></div>

                        </div>

                        <label for="penalty_per_day" class="col-sm-2 col-form-label">Penalty Per Day</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="penalty_per_day" name="penalty_per_day"
                                value="{{ $rate_master->penalty_per_day }}">
                            <div id="penaltyPerDayError" class="text-danger"></div>

                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="form-label col-sm-2" for="approval">Approval</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="approval" name="approval"
                                value="{{ $rate_master->approval }}">
                        </div>

                        <label class="form-label col-sm-2" for="status">Status</label>
                        <div class="col-sm-4">
                            <select class="form-select" id="status" name="status">
                                <option value="1" {{$rate_master->is_active == '1' ? 'selected' : ''}}>Active</option>
                                <option value="2" {{$rate_master->is_active == '2' ? 'selected' : ''}}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 text-end">
                            <button type="button" class="btn btn-danger"
                                onclick="window.location.href='{{ route('rate_master') }}'">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update</button>
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
        var person_type = $('#person').val();

        var vehicle_type = $('#vehicle').val();


        if (allowed_type == 'Vehicle') {
            $('#vehicle').show();
            $('#person').hide();

        } else if (allowed_type == 'Person') {
            $('#person').show();
            $('#vehicle').hide();

        }

    }

    $(document).ready(function () {

        // alert('');
        $('#rate_master_edit').on('submit', function (e) {

            e.preventDefault();
            var id = $('#rate_master_id').val(); // Get the ID from the hidden input


            var allowed_type = $('#allowed_type').val();


            if (allowed_type === 'Person') {

                var person_types = $('#person_type').val();
                var vehicle_types = 'none';

            } else if (allowed_type === 'Vehicle') {

                var vehicle_types = $('#vehicle_type').val();
                var person_types = 'none';
            }

            var form_data = {
                access_gate: $('#access_gate').val(),
                allowed_type: $('#allowed_type').val(),
                vehicle_type: vehicle_types,
                person_type: person_types,
                validity: $('#validity').val(),
                rate: $('#rate').val(),
                penalty_per_hour: $('#penalty_per_hour').val(),
                penalty_per_day: $('#penalty_per_day').val(),
                approval: $('#approval').val(),
                status: $('#status').val(),
                _token: '{{csrf_token()}}',
            }


            $.ajax({
                type: "POST",
                url: "{{route('rate_master.update', '')}}/" + id,
                data: form_data,
                success: function (response) {
                    const redirecturl = "{{route('rate_master')}}";
                    sweetalert("update", redirecturl);
                },
                error: function (xhr) {
                    console.log(xhr.responseText);
                }

            });
        });
    });
</script>

@endsection