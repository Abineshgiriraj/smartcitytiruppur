@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Port Operating System/</span>Document Master</h4>
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
                            <label class="form-label me-2" for="transaction_type">Category Type
                            </label>
                            <select class="form-select" for="category_type">
                                <option value="">Select Category</option>
                                <option value="firm">Firm</option>
                                <option value="users">Users</option>
                                <option value="person">Person</option>
                                <option value="driver">Driver</option>
                                <option value="vehicle">Vehicle</option>
                                <option value="cargo">Cargo</option>
                                <option value="container">Container</option>
                            </select>
                        </div>
                        <div class="col-3">
                            <label class="form-label">Type of Proof</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-3">
                            <label class="form-label" for="status">
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
                                onclick="window.location.href='{{ route('document_master')}}'">Cancel</button>
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