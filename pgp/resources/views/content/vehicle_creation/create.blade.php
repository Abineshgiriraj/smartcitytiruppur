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
<!-- Basic Layout -->
<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-body">
                <form id="category_form">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label me-2" for="transaction_type">Purpose of
                                Cargo</label>

                            <select class="form-select me-2" name="cargo" id="cargo" onchange="updateLabel()">
                                <option value="Cargo1">Cargo1</option>
                                <option value="Cargo1">Cargo2</option>
                            </select>
                        </div>

                        <div class="col-3">
                            <label class="form-label" for="vehicle_type">
                                Vehicle Type
                            </label>
                            <input type="text" class="form-control" id="vehicle_type" name="vehicle_type">
                        </div>
                        <div class="col-3">
                            <label class="form-label" for="vehicle_type">
                                Status
                            </label>
                            <select class="form-select" name="status" id="status">
                                <option value="1">Active</option>
                                <option value="2">In Active</option>
                            </select>
                        </div>
                    </div>



                    <div class="btn-float">
                        <button type="button" class="btn btn-danger"
                            onclick="window.location.href='{{ url('/vehicle_creation')}}'">Cancel</button>
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
</script>

@endsection