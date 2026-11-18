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
<!-- Basic Layout -->
<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-body">
                <form id="category_form">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label me-2" for="access_gate">Access Gate
                            </label>

                            <select class="form-select">
                                <option value="">Select Access Gate</option>

                                <option value="1">Inside/Outside</option>
                                <option value="2">Custom Bound Area</option>
                                <option value="3">Zone A & B</option>


                            </select>
                        </div>
                        <div class="col-3">
                            <label class="form-label me-2" for="access_gate">Allowed Type
                            </label>

                            <select class="form-select">
                                <option value="">Select Allowed Type</option>

                                <option value="1">Vehicle</option>
                                <option value="2">Person</option>


                            </select>
                        </div>
                        <div class="col-3">
                            <label class="form-label me-2" for="access_gate"> Type of Vehicle
                            </label>

                            <select class="form-select">
                                <option value="">Select Vehicle Type</option>

                                <option value="1">Light</option>
                                <option value="2">Heavy</option>
                                <option value="3">Equipment</option>



                            </select>
                        </div>
                        <div class="col-3">
                            <label class="form-label me-2" for="access_gate">Person Type
                            </label>

                            <select class="form-select">
                                <option value="">Select Person Type</option>

                                <option value="1">Port User</option>
                                <option value="2">Person</option>
                                <option value="3">Visitors</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3">
                            <label class="form-label me-2" for="access_gate">Validity
                            </label>

                            <select class="form-select">
                                <option value="selected">Select Validity Type</option>
                                <option value="1">Per Day</option>
                                <option value="2">Per Month</option>
                                <option value="3">Half Year</option>
                                <option value="4">Year</option>

                            </select>
                        </div>


                        <div class="col-3">
                            <label class="form-label me-2" for="access_gate">Rate
                            </label>
                            <input type="text" class="form-control" id="rate" name="rate">
                        </div>
                        <div class="col-3">
                            <label class="form-label me-2" for="access_gate">Approval
                            </label>
                            <input type="text" class="form-control" id="rate" name="rate">
                        </div>
                        <div class="col-3">
                            <label class="form-label" for="vehicle_type">
                                Status
                            </label>
                            <select class="form-select">
                                <option value="1">Active</option>
                                <option value="2">IN Active</option>
                            </select>
                        </div>
                    </div>



                    <div class="row">
                        <div class="btn-float">
                            <button type="button" class="btn btn-danger"
                                onclick="window.location.href='{{ url('/rate_master')}}'">Cancel</button>
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
</script>

@endsection