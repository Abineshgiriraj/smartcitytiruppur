@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Port Operating System/</span>Vehicle Creation</h4>
    </div>
</div>

<style>
    /* btn style */
    .btn-right {
        float: right;
    }
</style>
@php

    $get_cargo_options = get_cargo();

@endphp
<!-- Basic Layout -->
<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-body">
                <form id="vehicle_creation" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <!-- Vehicle Type (Select) -->
                        <label for="cargo" class="col-sm-2 col-form-label mt-3">Vehicle Type</label>
                        <div class="col-sm-4 mt-3">
                            <select class="form-select" name="cargo" id="cargo">
                                <option value="">Select Vehicle Type</option>
                                @foreach($get_cargo_options as $cargo)
                                    <option value="{{$cargo->unique_id}}">{{$cargo->cargo}}</option>
                                @endforeach
                            </select>
                            <div id="cargoError" class="text-danger"></div>
                        </div>

                        <!-- Vehicle Category (Input) -->
                        <label for="vehicle_type" class="col-sm-2 col-form-label mt-3">Vehicle Category</label>
                        <div class="col-sm-4 mt-3">
                            <input type="text" class="form-control" id="vehicle_type" name="vehicle_type">
                            <div id="vehicleError" class="text-danger"></div>
                        </div>

                        <!-- Status (Select) -->
                        <label for="status" class="col-sm-2 col-form-label mt-3">Status</label>
                        <div class="col-sm-4 mt-3">
                            <select class="form-select" name="status" id="status">
                                <option value="1">Active</option>
                                <option value="2">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="btn-float mt-3">
                        <button type="button" class="btn btn-danger"
                            onclick="window.location.href='{{ url('/vehicle_creation') }}'">Cancel</button>
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



    $(document).ready(function () {
        $('#vehicle_creation').on('submit', function (e) {
            e.preventDefault();

            var form_data = {
                cargo: $('#cargo').val(),
                vehicle_type: $('#vehicle_type').val(),
                status: $('#status').val(),
                _token: '{{csrf_token()}}'
            }
            if ((form_data.cargo != "") && (form_data.vehicle_type != '')) {

                $.ajax({
                    type: "POST",
                    url: '{{route("vehicle_creation.store")}}',
                    data: form_data,

                    success: function (response) {
                        const redirectUrl = "{{route('vehicle_creation')}}";
                        sweetalert("create", redirectUrl);

                    },
                    error: function (xhr) {
                        console.log(xhr.responseText);
                    }



                });
            } else {
                if (form_data.cargo == '') {
                    $('#cargoError').text('cargo is required.');
                } else {
                    $('#cargoError').text('');
                }
                if (form_data.vehicle_type == '') {
                    $('#vehicleError').text('Vehicle Type is required.');
                } else {
                    $('#vehicleError').text('');
                }
            }




        });
    });
</script>

@endsection