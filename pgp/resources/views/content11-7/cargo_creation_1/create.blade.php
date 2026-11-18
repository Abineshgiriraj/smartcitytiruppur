@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Port Operating System/</span>New Cargo</h4>
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
                <form id="cargo_form">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-4">
                            <div class="mb-3 mt-2 d-flex align-items-center">
                                <label class="form-label me-2" style="width:100px;" for="cargo">Cargo :</label>
                                <input type="text" class="form-control" name="cargo" id="cargo">
                            </div>

                        </div>
                        <div class="col-4">
                            <div class="mb-3 mt-2 d-flex align-items-center">
                                <label class="form-label me-2" style="width:100px;" for="cargo">Status</label>
                                <select class="form-select">
                                    <option value="">Select Status</option>
                                    <option value="1">Active</option>
                                    <option value="2">InActive</option>
                                </select>
                            </div>

                        </div>
                        <div class=" btn-float">
                            <button type="button" class="btn btn-danger"
                                onclick="window.location.href='{{ url('/cargo_creation')}}'">Cancel</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}">
</script>
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


    // $('#cargo_form').on('submit', function(e) {

    //     e.preventDefault(); // Prevent the default form submission


    // })
</script>

@endsection