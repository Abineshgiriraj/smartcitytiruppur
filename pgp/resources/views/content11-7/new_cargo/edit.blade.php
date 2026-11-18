@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Registration/</span>Cargo/Container</h4>
    </div>
</div>

<style>
    /* btn style */
    .btn-right {
        float: right;
    }
</style>
<!-- Basic Layout -->
<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-body">
                <form id="category_form">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-sm-2 mt-3"> <label class="form-label me-2"
                                for="transaction_type">Transaction</label></div>
                        <div class="col-sm-4 mt-3">
                            <select class="form-select me-" name="transaction_type" id="transaction_type"
                                onchange="updateLabel()">
                                <option value="1" {{$cargo_ei->transaction_type == 1 ? 'selected' : '' }}>Export</option>
                                <option value="2" {{$cargo_ei->transaction_type == 2 ? 'selected' : '' }}>Import</option>
                            </select>
                        </div>

                        <div class="col-sm-2 mt-3">
                            <label class="form-label me-2" id="dynamic_label" for="category_type">Export No (IA/EA
                                No)</label>
                        </div>
                        <div class="col-sm-4 mt-3">
                            <input type="text" class="form-control me-4" id="exp_imp_no" name="exp_imp_no"
                                value="{{$cargo_ei->exp_imp_no}}" />
                        </div>
                        <div class="col-sm-2 mt-3">
                            <label class="form-label md-2" id="dynamic_label_1" for="export_name">Export Name</label>
                        </div>
                        <div class="col-sm-4 mt-3">
                            <input type="text" class="form-control md-4" id="exp_imp_name" name="exp_imp_name"
                                value="{{$cargo_ei->exp_imp_name}}" />
                        </div>
                        <div class="col-sm-2 mt-3">
                            <label class="form-label me-2" for="description">Address</label>
                        </div>
                        <div class="col-sm-4 mt-3">
                            <textarea id="address" class="form-control me-4"
                                name="address">{{$cargo_ei->address}}</textarea>
                        </div>
                        <div class="col-sm-2 mt-3">
                            <label class="form-label me-2" for="cargo_name">Name Of The Cargo</label>
                        </div>
                        <div class="col-sm-4 mt-3">
                            <input type="text" class="form-control me-4" id="cargo_name" name="cargo_name"
                                value="{{$cargo_ei->cargo_name}}" />
                        </div>
                        <div class="col-sm-2 mt-3">
                            <label class="form-label me-2" for="package">No. Of Package</label>
                        </div>
                        <div class="col-sm-4 mt-3">
                            <input type="text" class="form-control me-4" id="package_qty" name="package_qty"
                                value="{{$cargo_ei->package_qty}}" />
                        </div>
                        <div class="col-sm-2 mt-3">
                            <label class="form-label me-2" for="weight">Total Weight</label>
                        </div>
                        <div class="col-sm-4 mt-3">
                            <input type="text" class="form-control me-4" id="total_weight" name="total_weight"
                                value="{{$cargo_ei->total_weight}}" />
                        </div>
                        <div class="col-sm-2 mt-3">
                            <label class="form-label me-2" for="vessel_name">Vessel Name</label>
                        </div>
                        <div class="col-sm-4 mt-3">
                            <input type="text" class="form-control me-4" id="vessel_name" name="vessel_name"
                                value="{{$cargo_ei->vessel_name}}" />
                        </div>
                        <div class="col-sm-2 mt-3">
                            <label class="form-label me-2" for="serial_no">Vessel Serial No</label>
                        </div>
                        <div class="col-sm-4 mt-3">
                            <input type="text" class="form-control me-4" id="vessel_sr_no" name="vessel_sr_no"
                                value="{{$cargo_ei->vessel_sr_no}}" />
                        </div>
                        <div class="col-sm-2 mt-3">
                            <label class="form-label me-2" for="agent_type">Agent Type</label>
                        </div>
                        <div class="col-sm-4 mt-3">
                            <select class="form-select me-4" name="agent_type" id="agent_type">
                                <option value="Stevedore" {{$cargo_ei->agent_type == "Stevedore" ? 'selected' : '' }}>
                                    Stevedore</option>
                                <option value="Ship Agent" {{$cargo_ei->agent_type == "Ship Agent" ? 'selected' : '' }}>
                                    Ship Agent</option>
                                <option value="Custom Broker" {{$cargo_ei->agent_type == "Custom Broker" ? 'selected' : '' }}>Custom Broker</option>
                                <option value="CFS (Container Freight Station)" {{$cargo_ei->agent_type == "CFS (Container Freight Station)" ? 'selected' : '' }}>CFS (Container Freight Station)
                                </option>
                            </select>
                        </div>
                        <div class="col-sm-2 mt-3">
                            <label class="form-label me-2" for="serial_no">Agent Details</label>
                        </div>
                        <div class="col-sm-4 mt-3">
                            <textarea type="text" class="form-control me-4" id="agent_details"
                                name="agent_details">{{$cargo_ei->agent_details}}</textarea>
                        </div>
                        <div class="col-sm-2 mt-3">
                            <label class="form-label me-2" for="serial_no">Port of Origin/Destination</label>
                        </div>
                        <div class="col-sm-4 mt-3">
                            <input type="text" class="form-control me-4" id="org_destination" name="org_destination"
                                value="{{$cargo_ei->org_destination}}" />
                        </div>
                    </div>
                    <div class="btn-float">
                        <button type="button" class="btn btn-danger"
                            onclick="window.location.href='{{ url('/new_cargo')}}'">Cancel</button>
                        <button type="submit" class="btn btn-primary"
                            onclick="submit_form()">{{isset($cargo_ei) ? 'Update' : 'Submit'}}</button>
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

    function submit_form() {
        event.preventDefault();
        var transaction_type = $("#transaction_type").val();
        var exp_imp_no = $("#exp_imp_no").val();
        var exp_imp_name = $("#exp_imp_name").val();
        var address = $("#address").val();
        var cargo_name = $("#cargo_name").val();
        var package_qty = $("#package_qty").val();
        var total_weight = $("#total_weight").val();
        var vessel_name = $("#vessel_name").val();
        var vessel_sr_no = $("#vessel_sr_no").val();
        var agent_type = $("#agent_type").val();
        var agent_details = $("#agent_details").val();
        var org_destination = $("#org_destination").val();


        if ((transaction_type && exp_imp_no && exp_imp_name && address && cargo_name && package_qty && total_weight && vessel_name && vessel_sr_no && agent_type && agent_details && org_destination) != '') {



            var data = {
                transaction_type: transaction_type,
                exp_imp_no: exp_imp_no,
                address: address,
                exp_imp_name: exp_imp_name,
                cargo_name: cargo_name,
                package_qty: package_qty,
                total_weight: total_weight,
                vessel_name: vessel_name,
                vessel_sr_no: vessel_sr_no,
                agent_type: agent_type,
                agent_details: agent_details,
                org_destination: org_destination,

                _token: '{{ csrf_token() }}',
                _method: '{{ isset($cargo_ei) ? "PUT" : "POST"}}'
            };

            let url = '{{ isset($cargo_ei) ? route("cargo_ei.update", $cargo_ei->id) : route("cargo_ei.store") }}';


            $.ajax({
                url: url,
                method: 'POST',
                data: data,
                success: function (response) {

                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: (data._method === 'POST' ? 'Registered' : 'updated') +
                            ' successfully!',
                    }).then(() => {
                        window.location.href = "{{ url('/new_cargo') }}";
                    });
                },
                error: function (xhr) {
                    let errors = xhr.responseJSON.errors;
                    if (errors && errors.usertype) {
                        $('#usertypeError').text(errors.usertype[0]);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: errors.usertype[0],
                        });
                    }
                }
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Fill Out All Mandatory Fields',
            });
        }
    }


    function updateLabel() {
        const transactionType = document.getElementById('transaction_type').value;
        const dynamicLabel = document.getElementById('dynamic_label');
        const dynamicLabel_1 = document.getElementById('dynamic_label_1');

        if (transactionType === '1') {
            dynamicLabel.textContent = "Export No (IA/EA No)";
            dynamicLabel_1.textContent = 'Export Name';
        } else {
            dynamicLabel.textContent = "Import No (IA/EA No)";
            dynamicLabel_1.textContent = 'Import Name';
        }
    }
</script>

@endsection
