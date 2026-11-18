@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Port Operating System/</span>New Cotainer</h4>
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
                    <div class="col-sm-2 mt-3">  <label class="form-label me-2" for="transaction_type">Transaction</label></div>
                    <div class="col-sm-4 mt-3">
                    <select class="form-select me-" name="transaction_type" id="transaction_type"
                                onchange="updateLabel()">
                                <option value="transaction_type_1">Export</option>
                                <option value="transaction_type_2">Import</option>
                            </select>
                        </div>

                        <div class="col-sm-2 mt-3">
                            <label class="form-label me-2" id="dynamic_label" for="category_type">Export No (EIR No)</label></div>
                            <div class="col-sm-4 mt-3">
                            <input type="text" class="form-control me-4" id="category_type" name="category_type" />
                        </div>
                        <div class="col-sm-2 mt-3">
                            <label class="form-label md-2" id="dynamic_label_1" for="export_name">Export Name</label></div>
                            <div class="col-sm-4 mt-3">
                            <input type="text" class="form-control md-4" id="export_name" name="export_name" />
                        </div>
                        <div class="col-sm-2 mt-3">
                            <label class="form-label me-2" for="description">Address</label></div>
                            <div class="col-sm-4 mt-3">
                            <textarea id="description" class="form-control me-4" name="description"></textarea>
                        </div>
                        <div class="col-sm-2 mt-3">
                            <label class="form-label me-2" for="cargo_name">Name Of Container</label></div>
                            <div class="col-sm-4 mt-3">
                            <input type="text" class="form-control me-4" id="cargo_name" name="cargo_name" />
                        </div>
                        <div class="col-sm-2 mt-3">
                            <label class="form-label me-2" for="package">No. Of Package</label></div>
                            <div class="col-sm-4 mt-3">
                            <input type="text" class="form-control me-4" id="package" name="package" />
                        </div>
                        <div class="col-sm-2 mt-3">
                            <label class="form-label me-2" for="weight">Total Weight</label></div>
                            <div class="col-sm-4 mt-3">
                            <input type="text" class="form-control me-4" id="weight" name="weight" />
                        </div>
                        <div class="col-sm-2 mt-3">
                            <label class="form-label me-2" for="vessel_name">Vessel Name</label></div>
                            <div class="col-sm-4 mt-3">
                            <input type="text" class="form-control me-4" id="vessel_name" name="vessel_name" />
                            </div>
                        <div class="col-sm-2 mt-3">
                            <label class="form-label me-2" for="serial_no">Vessel Serial No</label></div>
                            <div class="col-sm-4 mt-3">
                            <input type="text" class="form-control me-4" id="serial_no" name="serial_no" />
                        </div>
                        <div class="col-sm-2 mt-3">
                            <label class="form-label me-2" for="agent_type">Agent Type</label></div>
                            <div class="col-sm-4 mt-3">
                            <select class="form-select me-4" name="agent_type" id="agent_type">
                                <option value="agent_1">Stevedore</option>
                                <option value="agent_2">Ship Agent</option>
                                <option value="agent_3">Custom Broker</option>
                                <option value="agent_4">CFS (Container Freight Station)
                                </option>
                            </select>
                        </div>
                        <div class="col-sm-2 mt-3">
                            <label class="form-label me-2" for="serial_no">Agent Details</label></div>
                            <div class="col-sm-4 mt-3">
                            <textarea type="text" class="form-control me-4" id="serial_no" name="serial_no" ></textarea>
                        </div>
                        <div class="col-sm-2 mt-3">
                            <label class="form-label me-2" for="serial_no">Port of Origin/Destination</label></div>
                            <div class="col-sm-4 mt-3">
                            <input type="text" class="form-control me-4" id="serial_no" name="serial_no" />
                        </div>
                    </div>
                    <div class="btn-float">
                        <button type="button" class="btn btn-danger"
                            onclick="window.location.href='{{ url('/container_list')}}'">Cancel</button>
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
            dynamicLabel.textContent = "Export No (EIR No)";
            dynamicLabel_1.textContent = 'Export Name';
        } else {
            dynamicLabel.textContent = "Import No (EIR No)";
            dynamicLabel_1.textContent = 'Import Name';
        }
    }
</script>

@endsection
