@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Port Operating System/</span>Equipment Complaint Approval</h4>
    </div>
</div>

<style>
    /* btn style */
    .btn-right {
        float: right;
    }

    .label-container {
        display: flex;
        align-items: center;
        /* Center vertically */
        width: 100%;
        /* Full width for proper alignment */
    }

    .label-col {
        width: 200px;
        /* Fixed width for labels */
        margin-right: 10px;
        /* Space between label and colon */
    }

    .colon {
        width: 20px;
        /* Fixed width for colon */
        text-align: center;
        /* Center the colon */
    }

    .value-col {
        flex: 3;
        /* Take the remaining space */
        text-align: left;
        /* Align the value to the left */
    }
</style>
<!-- Basic Layout -->
<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-body">
                <form id="category_form">
                    @csrf
                    <div class="row">
                        <div class="col-6">
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <div class="label-container">
                                        <label class="form-label label-col" for="fullname">Vehicle No
                                        </label>
                                        <span class="colon">:</span>
                                        <span class="value-col">TN 34 JK 1234</span>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-2">
                                    <div class="label-container">
                                        <label class="form-label label-col" for="email">Reg. No </label>
                                        <span class="colon">:</span>
                                        <span class="value-col">R1234</span>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-2">
                                    <div class="label-container">
                                        <label class="form-label label-col" for="mobile">Reg. Date</label>
                                        <span class="colon">:</span>
                                        <span class="value-col">20-09-2024</span>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-2">
                                    <div class="label-container">
                                        <label class="form-label label-col" for="aadhaar">DL No</label>
                                        <span class="colon">:</span>
                                        <span class="value-col">D123</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="col-md-12 mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance">Driver Name
                                    </label>
                                    <span class="colon">:</span>
                                    <span class="value-col">Suresh</span>
                                </div>
                            </div>

                            <div class="col-md-12 mb-2 mt-3">
                                <div class="label-container">
                                    <label class="form-label label-col" for="fitness-certificate">
                                        Status</label>
                                    <span class="colon">:</span>
                                    <!-- <span class="value-col"></span> -->
                                    <div class="col-5"><select class="form-select value-col" id="approve"
                                            name="approve">
                                            <option value="selected">Change Status</option>
                                            <option value=" Pending">Pending</option>
                                            <option value="Approved">Approved</option>
                                            <option value="Rejected">Rejected</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance">Penalty
                                    </label>
                                    <span class="colon">:</span>
                                    <span class="value-col">1200</span>
                                </div>
                            </div>

                            <div class="col-md-12 mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance">Reason
                                    </label>
                                    <span class="colon">:</span>
                                    <span class="value-col"></span>
                                    <textarea class="form-control" style="width: 60%; height: 60px;"></textarea>
                                </div>
                            </div>


                        </div>
                    </div>






                    <div class="row">
                        <div class="btn-float">
                            <button type="button" class="btn btn-danger"
                                onclick="window.location.href='{{ route("equipment_complaint_approval")}}'">Cancel</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
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