@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Port Operating System/</span>Rate Master Approval</h4>
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

.sky-blue {
    border: none;
    /* Remove default border */
    border-top: 2px solid skyblue;
    /* Set the sky blue color */
    margin-bottom: 4px 0;
    /* Adjust margin as needed */
}
</style>

@php
$get_access_gate = get_access_gate();

$get_vehicle_type = get_vehicle_type();

$get_person_type = get_person_type();
@endphp
<!-- Basic Layout -->
<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-body">
                <form id="category_form">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <h4>Rate Approve Details</h4>
                            <hr class="sky-blue">
                        </div>

                        <div class="col-6">
                            <div class="mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="fullname">Access Gate</label>
                                    <span class="colon">:</span>
                                    <span class="value-col">
                                        @foreach ($get_access_gate as $access_gate)
                                        @if($access_gate->unique_id == $rate_master_approval->access_gate)
                                        {{$access_gate->access_gate}}
                                        @endif
                                        @endforeach
                                    </span>
                                </div>
                            </div>

                            <div class="mb-2">
                                <div class="label-container">

                                    <input type="hidden" id="main_unique_id"
                                        value="{{$rate_master_approval->unique_id}}">

                                    <label class="form-label label-col" for="email">Allowed Type</label>
                                    <span class="colon">:</span>
                                    <span class="value-col">{{$rate_master_approval->allowed_type}}</span>
                                </div>
                            </div>

                            <div class="mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="mobile">Vehicle Type</label>
                                    <span class="colon">:</span>
                                    <span class="value-col">
                                        @foreach ($get_vehicle_type as $vehicle_type)
                                        @if($vehicle_type->unique_id == $rate_master_approval->vehicle_person_type)
                                        {{$vehicle_type->vehicle_type}}
                                        @else
                                        {{'-'}}
                                        @endif
                                        @endforeach
                                    </span>
                                </div>
                            </div>

                            <div class="mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="aadhaar">Person Type</label>
                                    <span class="colon">:</span>
                                    <span class="value-col">
                                        @foreach ($get_person_type as $person_type)
                                        @if($person_type->unique_id == $rate_master_approval->vehicle_person_type)
                                        {{$person_type->person_type}}
                                        @endif
                                        @endforeach
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance">Validity</label>
                                    <span class="colon">:</span>
                                    <span class="value-col">{{$rate_master_approval->validity}}</span>
                                </div>
                            </div>

                            <div class="mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance-valid-from">Rate</label>
                                    <span class="colon">:</span>
                                    <span class="value-col">{{$rate_master_approval->rate}}</span>
                                </div>
                            </div>

                            <div class="mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance-valid-to">Per Hour</label>
                                    <span class="colon">:</span>
                                    <span class="value-col">{{$rate_master_approval->penalty_per_hour}}</span>
                                </div>
                            </div>

                            <div class="mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance-valid-to">Per Day</label>
                                    <span class="colon">:</span>
                                    <span class="value-col">{{$rate_master_approval->penalty_per_day}}</span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="mb-2">
                                <h4>Approval Officer Details</h4>
                                <hr class="sky-blue">
                            </div>

                            <div class="col-6">
                                <div class="mb-2">
                                    <div class="label-container">
                                        <label class="form-label label-col" for="fullname">User Name</label>
                                        <span class="colon">:</span>
                                        <span class="value-col">{{ session('user_name') }}</span>
                                        <input type="hidden" id="approved_id" value="{{session('user_id')}}">
                                    </div>
                                </div>



                                <div class="mb-2 mt-3">
                                    <div class="label-container">
                                        <label class="form-label label-col" for="fitness-certificate">Status</label>
                                        <span class="colon">:</span>
                                        <div class="col-5">
                                            <select class="form-select value-col" id="approve" name="approve"
                                                onchange="status_change()">
                                                <option value="selected">Change Status</option>
                                                <option value="0"
                                                    {{$rate_master_approval->approval_status == '0'? 'selected':''}}>
                                                    Pending
                                                </option>
                                                <option value="1"
                                                    {{$rate_master_approval->approval_status == '1'? 'selected':''}}>
                                                    Approved</option>
                                                <option value="2"
                                                    {{$rate_master_approval->approval_status == '2'? 'selected':''}}>
                                                    Rejected</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mt-3" id="reject_reason" style="display:none;">
                                    <div class="label-container">
                                        <label class="form-label label-col">Reject Reason</label>
                                        <span class="colon">:</span>
                                        <span class="value-col">
                                            <textarea class="form-control value-col" style="width:300px; height:70px;"
                                                id="reject"></textarea>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>


                    <div class="row">
                        <div class="btn-float">
                            <button type="button" class="btn btn-danger"
                                onclick="window.location.href='{{ route('rate_master_approval') }}'">Cancel</button>
                            <button type="button" onclick="status_approval()" class="btn btn-primary">Submit</button>
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


function status_change() {
    var status = $('#approve').val();


    if (status == 2) {
        $('#reject_reason').show();
    } else {
        $('#reject_reason').hide();
    }
}


function status_approval() {


    var status = $('#approve').val();

    var approved_by = $('#approved_id').val();

    var reject_reason = $('#reject').val();

    var main_unique_id = $('#main_unique_id').val();



    var data = {
        status: status,
        approved_by: approved_by,
        reject_reason: reject_reason,
        main_unique_id: main_unique_id,
        _token: "{{csrf_token()}}"
    }

    $.ajax({

        type: "POST",
        url: "{{route('rate_master_approval.status')}}",
        data: data,

        success: function(response) {

            const redirecturl = "{{route('rate_master_approval')}}";

            sweetalert("create", redirecturl);

        },

        error: function(xhr) {
            console.log(xhr.responseJSON);
        }
    });


}
</script>

@endsection